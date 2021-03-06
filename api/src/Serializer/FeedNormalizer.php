<?php

namespace App\Serializer;

use App\Entity\Feed;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class FeedNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /** @var ObjectNormalizer */
    private $normalizer;

    /**
     * @param ObjectNormalizer $normalizer
     */
    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof Feed;
    }

    /**
     * @param Feed $object
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
                        'title',
                        'description',
                        'link'
                    ]
                ]
            )
        );

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
