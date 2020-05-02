<?php

namespace App\Serializer;

use App\Entity\Item;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ItemNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /** @var ObjectNormalizer */
    private $normalizer;

    /** @var \HTMLPurifier */
    private $planetPurifier;

    /**
     * @param ObjectNormalizer $normalizer
     * @param \HTMLPurifier $planetPurifier
     */
    public function __construct(ObjectNormalizer $normalizer, \HTMLPurifier $planetPurifier)
    {
        $this->normalizer = $normalizer;
        $this->planetPurifier = $planetPurifier;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Item;
    }

    /**
     * @param Item $object
     * @param string $format
     * @param array<mixed> $context
     * @return array<mixed>
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        /** @var array<mixed> $data */
        $data = $this->normalizer->normalize(
            $object,
            $format,
            array_merge(
                $context,
                [
                    AbstractNormalizer::ATTRIBUTES => [
                        'link',
                        'title',
                        'description',
                        'lastModified',
                        'author'
                    ]
                ]
            )
        );

        if (isset($data['description'])) {
            $data['description'] = $this->planetPurifier->purify($data['description']);
        }

        return $data;
    }

    /**
     * @return bool
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
