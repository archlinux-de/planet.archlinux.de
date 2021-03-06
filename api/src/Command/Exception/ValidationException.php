<?php

namespace App\Command\Exception;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \RuntimeException
{
    /**
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $constraintViolationList
     */
    public function __construct(ConstraintViolationListInterface $constraintViolationList)
    {
        parent::__construct(
            implode(
                "\n",
                iterator_to_array(
                    (function () use ($constraintViolationList) {
                        /** @var ConstraintViolationInterface $constraintViolation */
                        foreach ($constraintViolationList as $constraintViolation) {
                            yield sprintf(
                                'Validation of value %s in "%s" failed. %s',
                                json_encode($constraintViolation->getInvalidValue()),
                                $constraintViolation->getPropertyPath(),
                                (string)$constraintViolation->getMessage()
                            );
                        }
                    })()
                )
            )
        );
    }
}
