<?php

namespace App\Command;

use App\Command\Exception\ValidationException;
use App\Entity\Feed;
use App\Repository\FeedRepository;
use App\Service\FeedFetcher;
use Doctrine\ORM\EntityManager;
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

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private FeedFetcher $feedFetcher,
        private FeedRepository $feedRepository,
        private LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:update:feeds');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;
        $this->lock('cron.lock');

        $this->entityManager->wrapInTransaction(
            function (EntityManagerInterface $entityManager): void {
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

            $this->entityManager->wrapInTransaction(
                function (EntityManagerInterface $entityManager) use ($feed): void {
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
