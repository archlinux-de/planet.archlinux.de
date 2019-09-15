<?php

namespace App\Tests\Command;

use App\Command\Exception\ValidationException;
use App\Command\UpdateFeedsCommand;
use App\Entity\Feed;
use App\Entity\Item;
use App\Repository\FeedRepository;
use App\Repository\ItemRepository;
use App\Service\FeedFetcher;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \App\Command\UpdateFeedsCommand
 */
class UpdateFeedsCommandTest extends KernelTestCase
{
    public function testCommand()
    {
        $oldItem = new Item();
        $oldFeed = new Feed('https://localhost/atom.xml');

        $newItem = (new Item())->setPublicId('');
        $newFeed = new Feed('https://localhost/atom.xml');
        $newFeed->addItem($newItem);

        /** @var ItemRepository|MockObject $itemRepository */
        $itemRepository = $this->createMock(ItemRepository::class);
        $itemRepository->method('findAllExceptByIds')->willReturn([$oldItem]);

        /** @var FeedRepository|MockObject $feedRepository */
        $feedRepository = $this->createMock(FeedRepository::class);
        $feedRepository->method('findAllExceptByUrls')->willReturn([$oldFeed]);

        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('transactional')
            ->willReturnCallback(function (callable $callable) use ($entityManager) {
                $callable($entityManager);
            });
        $entityManager->expects($this->once())->method('merge')->with($newFeed);
        $entityManager->expects($this->atLeastOnce())->method('remove')->withConsecutive([$oldFeed], [$oldItem]);

        /** @var FeedFetcher|MockObject $feedFetcher */
        $feedFetcher = $this->createMock(FeedFetcher::class);
        $feedFetcher->method('getIterator')->willReturn(new \ArrayIterator([$newFeed]));

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->atLeastOnce())->method('validate')->willReturn(new ConstraintViolationList());

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new UpdateFeedsCommand(
            $entityManager,
            $validator,
            $feedFetcher,
            $feedRepository,
            $itemRepository
        ));

        $command = $application->find('app:update:feeds');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testInvalidFeedIsSkipped()
    {
        $newFeed = new Feed('https://localhost/atom.xml');

        /** @var ItemRepository|MockObject $itemRepository */
        $itemRepository = $this->createMock(ItemRepository::class);

        /** @var FeedRepository|MockObject $feedRepository */
        $feedRepository = $this->createMock(FeedRepository::class);
        $feedRepository->method('findAllExceptByUrls')->willReturn([]);

        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('transactional')
            ->willReturnCallback(function (callable $callable) use ($entityManager) {
                $callable($entityManager);
            });
        $entityManager->expects($this->never())->method('merge');
        $entityManager->expects($this->never())->method('remove');

        /** @var FeedFetcher|MockObject $feedFetcher */
        $feedFetcher = $this->createMock(FeedFetcher::class);
        $feedFetcher->method('getIterator')->willReturn(new \ArrayIterator([$newFeed]));

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        $validator
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([$this->createMock(ConstraintViolation::class)]));

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new UpdateFeedsCommand(
            $entityManager,
            $validator,
            $feedFetcher,
            $feedRepository,
            $itemRepository
        ));

        $command = $application->find('app:update:feeds');
        $commandTester = new CommandTester($command);
        $this->expectException(ValidationException::class);
        $commandTester->execute(['command' => $command->getName()]);
    }
}
