<?php

namespace App\Tests\Command;

use App\Command\UpdateFeedsCommand;
use App\Entity\Feed;
use App\Entity\Item;
use App\Repository\FeedRepository;
use App\Service\FeedFetcher;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
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
    public function testCommand(): void
    {
        $oldItem = new Item('https://localhost/1');
        $oldFeed = new Feed('https://localhost/atom.xml');

        $newItem = new Item('https://localhost/2');
        $newFeed = new Feed('https://localhost/atom.xml');
        $newFeed->addItem($newItem);

        /** @var FeedRepository|MockObject $feedRepository */
        $feedRepository = $this->createMock(FeedRepository::class);
        $feedRepository->method('findAllExceptByUrls')->willReturn([$oldFeed]);

        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('transactional')
            ->willReturnCallback(
                function (callable $callable) use ($entityManager) {
                    $callable($entityManager);
                }
            );
        $entityManager->expects($this->once())->method('persist')->with($newFeed);
        $entityManager->expects($this->atLeastOnce())->method('remove')->withConsecutive([$oldFeed], [$oldItem]);

        /** @var FeedFetcher|MockObject $feedFetcher */
        $feedFetcher = $this->createMock(FeedFetcher::class);
        $feedFetcher->method('getIterator')->willReturn(new \ArrayIterator([$newFeed]));

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->atLeastOnce())->method('validate')->willReturn(new ConstraintViolationList());

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->never())
            ->method('error');

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(
            new UpdateFeedsCommand(
                $entityManager,
                $validator,
                $feedFetcher,
                $feedRepository,
                $logger
            )
        );

        $command = $application->find('app:update:feeds');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testInvalidFeedIsSkipped(): void
    {
        $newFeed = new Feed('https://localhost/atom.xml');

        /** @var FeedRepository|MockObject $feedRepository */
        $feedRepository = $this->createMock(FeedRepository::class);
        $feedRepository->method('findAllExceptByUrls')->willReturn([]);

        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('transactional')
            ->willReturnCallback(
                function (callable $callable) use ($entityManager) {
                    $callable($entityManager);
                }
            );
        $entityManager->expects($this->never())->method('persist');
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

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error');

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(
            new UpdateFeedsCommand(
                $entityManager,
                $validator,
                $feedFetcher,
                $feedRepository,
                $logger
            )
        );

        $command = $application->find('app:update:feeds');
        $commandTester = new CommandTester($command);
        $this->assertNotEquals(0, $commandTester->execute(['command' => $command->getName()]));
    }

    public function testUpdateFeed(): void
    {
        $item = new Item('https://localhost/1');
        $feed = new Feed('https://localhost/atom.xml');
        $feed->addItem($item);

        /** @var FeedRepository|MockObject $feedRepository */
        $feedRepository = $this->createMock(FeedRepository::class);
        $feedRepository->method('find')->willReturn($feed);

        /** @var EntityManagerInterface|MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->atLeastOnce())
            ->method('transactional')
            ->willReturnCallback(
                function (callable $callable) use ($entityManager) {
                    $callable($entityManager);
                }
            );
        $entityManager->expects($this->once())->method('persist')->with($feed);
        $entityManager->expects($this->atLeastOnce())->method('remove')->withConsecutive([$feed]);

        /** @var FeedFetcher|MockObject $feedFetcher */
        $feedFetcher = $this->createMock(FeedFetcher::class);
        $feedFetcher->method('getIterator')->willReturn(new \ArrayIterator([$feed]));

        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->atLeastOnce())->method('validate')->willReturn(new ConstraintViolationList());

        /** @var LoggerInterface|MockObject $logger */
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->never())
            ->method('error');

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add(
            new UpdateFeedsCommand(
                $entityManager,
                $validator,
                $feedFetcher,
                $feedRepository,
                $logger
            )
        );

        $command = $application->find('app:update:feeds');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}
