<?php

namespace App\Serializer;

use App\Entity\Feed;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class FeedNormalizer implements NormalizerInterface
{
    private NormalizerInterface $normalizer;

    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')] NormalizerInterface $normalizer,
    ) {
        assert($normalizer instanceof ObjectNormalizer);
        $this->normalizer = $normalizer;
    }


    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
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

    public function getSupportedTypes(?string $format): array
    {
        return [
            Feed::class => true
        ];
    }
}
