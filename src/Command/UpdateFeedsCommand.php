<?php

namespace App\Command;

use App\Entity\Feed;
use App\Service\FeedFetcher;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param FeedFetcher $feedFetcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        FeedFetcher $feedFetcher
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->feedFetcher = $feedFetcher;
    }


    protected function configure()
    {
        $this->setName('app:update:feeds');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->lock('cron.lock', true);

        /** @var Feed $feed */
        foreach ($this->feedFetcher as $feed) {
            $errors = $this->validator->validate($feed);
            if ($errors->count() > 0) {
                throw new \RuntimeException((string)$errors);
            }

            $this->entityManager->transactional(
                function (EntityManagerInterface $entityManager) use ($feed) {
                    $entityManager->merge($feed);
                }
            );
        }

        $this->release();
    }
}
