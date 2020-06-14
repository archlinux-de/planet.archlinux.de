<?php

namespace App\ParamConverter;

use App\Request\PaginationRequest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaginationParamConverter implements ParamConverterInterface
{
    /** @var ValidatorInterface */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @return bool
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $paginationRequest = new PaginationRequest(
            $request->get('offset', 0),
            $request->get('limit', 100)
        );

        $errors = $this->validator->validate($paginationRequest);
        if ($errors->count() > 0) {
            throw new BadRequestHttpException('Invalid request');
        }

        $request->attributes->set(
            $configuration->getName(),
            $paginationRequest
        );

        return true;
    }

    /**
     * @param ParamConverter $configuration
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() == PaginationRequest::class;
    }
}
