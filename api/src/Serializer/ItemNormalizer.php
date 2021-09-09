<?php

namespace App\Serializer;

use App\Entity\Item;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ItemNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(private ObjectNormalizer $normalizer, private \HTMLPurifier $planetPurifier)
    {
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Item;
    }

    /**
     * @param Item $object
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
                        'link',
                        'title',
                        'description',
                        'lastModified',
                        'author'
                    ]
                ]
            )
        );
        $data['description'] = $this->planetPurifier->purify($data['description']);

        return $data;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
