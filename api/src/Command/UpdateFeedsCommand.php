<?php

namespace App\Command;

use App\Command\Exception\ValidationException;
use App\Entity\Feed;
use App\Repository\FeedRepository;
use App\Service\FeedFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateFeedsCommand extends Command
{
    use LockableTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ValidatorInterface */
    private $validator;

    /** @var FeedFetcher */
    private $feedFetcher;

    /** @var FeedRepository */
    private $feedRepository;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param FeedFetcher $feedFetcher
     * @param FeedRepository $feedRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        FeedFetcher $feedFetcher,
        FeedRepository $feedRepository,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->feedFetcher = $feedFetcher;
        $this->feedRepository = $feedRepository;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this->setName('app:update:feeds');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;
        $this->lock('cron.lock');

        $this->entityManager->transactional(
            function (EntityManagerInterface $entityManager) {
                foreach ($this->feedRepository->findAllExceptByUrls($this->feedFetcher->getFeedUrls()) as $feed) {
                    $entityManager->remove($feed);
                }
            }
        );

        /** @var Feed $feed */
        foreach ($this->feedFetcher as $feed) {
            $errors = $this->validator->validate($feed);
            if ($errors->count() > 0) {
                $this->logger->error(new ValidationException($errors), ['feedUrl' => $feed->getUrl()]);
                $exitCode = 1;
                continue;
            }

            $this->entityManager->transactional(
                function (EntityManagerInterface $entityManager) use ($feed) {
                    /** @var Feed|null $persistedFeed */
                    $persistedFeed = $this->feedRepository->find($feed->getUrl());
                    if ($persistedFeed) {
                        $entityManager->remove($persistedFeed);
                        $entityManager->flush();
                    }

                    $entityManager->persist($feed);
                }
            );
        }

        $this->release();

        return $exitCode;
    }
}
