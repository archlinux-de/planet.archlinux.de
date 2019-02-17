<?php

namespace App\Tests\Command\Exception;

use App\Command\Exception\ValidationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationExceptionTest extends TestCase
{
    public function testMessageIsRendered()
    {
        /** @var ConstraintViolationInterface|MockObject $violationA */
        $violationA = $this->createMock(ConstraintViolationInterface::class);
        $violationA
            ->expects($this->once())
            ->method('getInvalidValue')
            ->willReturn('foo');
        $violationA
            ->expects($this->once())
            ->method('getMessage')
            ->willReturn('bar');

        /** @var ConstraintViolationInterface|MockObject $violationB */
        $violationB = $this->createMock(ConstraintViolationInterface::class);
        $violationB
            ->expects($this->once())
            ->method('getInvalidValue')
            ->willReturn(['nope']);
        $violationB
            ->expects($this->once())
            ->method('getMessage')
            ->willReturn('baz');

        $violationList = new ConstraintViolationList([$violationA, $violationB]);

        $requestException = new ValidationException($violationList);

        $this->assertContains('foo', $requestException->getMessage());
        $this->assertContains('bar', $requestException->getMessage());
        $this->assertContains('nope', $requestException->getMessage());
        $this->assertContains('baz', $requestException->getMessage());
    }
}
