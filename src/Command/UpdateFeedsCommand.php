<?php

namespace App\Command;

use App\Entity\Feed;
use App\Entity\Item;
use App\Repository\FeedRepository;
use App\Repository\ItemRepository;
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

    /** @var FeedRepository */
    private $feedRepository;

    /** @var ItemRepository */
    private $itemRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param FeedFetcher $feedFetcher
     * @param FeedRepository $feedRepository
     * @param ItemRepository $itemRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        FeedFetcher $feedFetcher,
        FeedRepository $feedRepository,
        ItemRepository $itemRepository
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->feedFetcher = $feedFetcher;
        $this->feedRepository = $feedRepository;
        $this->itemRepository = $itemRepository;
    }

    protected function configure()
    {
        $this->setName('app:update:feeds');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->lock('cron.lock', true);

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
                throw new \RuntimeException((string)json_encode($errors));
            }

            $this->entityManager->transactional(
                function (EntityManagerInterface $entityManager) use ($feed) {
                    foreach ($this->getOrphanedItems($feed) as $orphanedItem) {
                        $entityManager->remove($orphanedItem);
                    }
                    $entityManager->merge($feed);
                }
            );
        }

        $this->release();
    }

    /**
     * @param Feed $feed
     * @return Item[]
     */
    private function getOrphanedItems(Feed $feed): array
    {
        $itemIds = [];
        foreach ($feed->getItems() as $item) {
            $itemIds[] = $item->getPublicId();
        }

        return $this->itemRepository->findAllExceptByIds($feed, $itemIds);
    }
}
