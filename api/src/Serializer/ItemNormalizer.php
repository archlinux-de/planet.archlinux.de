<?php

namespace App\Serializer;

use App\Entity\Item;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ItemNormalizer implements NormalizerInterface
{
    private NormalizerInterface $normalizer;

    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')] NormalizerInterface $normalizer,
        private readonly HtmlSanitizerInterface $htmlSanitizer
    ) {
        assert($normalizer instanceof ObjectNormalizer);
        $this->normalizer = $normalizer;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Item;
    }

    /**
     * @param Item $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
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
        assert(is_string($data['description']));
        $data['description'] = $this->htmlSanitizer->sanitize($data['description']);

        return $data;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Item::class => true
        ];
    }
}
