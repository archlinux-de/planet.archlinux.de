<?php

namespace App\Serializer;

use App\Entity\Feed;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class FeedNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(private ObjectNormalizer $normalizer)
    {
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Feed;
    }

    /**
     * @param Feed $object
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        /** @var array $data */
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

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
