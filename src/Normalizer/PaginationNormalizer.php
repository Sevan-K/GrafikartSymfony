<?php

namespace App\Normalizer;

use \RuntimeException;
use App\Entity\Recipe;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaginationNormalizer implements NormalizerInterface
{

    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer
    ) {}

    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (!($data instanceof PaginationInterface)) {
            return new RuntimeException();
        }

        return [
            'total' => $data->getTotalItemCount(),
            'page' => $data->getCurrentPageNumber(),
            'lastPage' => ceil($data->getTotalItemCount() / $data->getItemNumberPerPage()),
            'items' => array_map(
                fn(Recipe $recipe) => $this->normalizer->normalize($recipe, $format, $context),
                $data->getItems()
            ),
        ];
    }
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        // return $data instanceof PaginationInterface;
        return $data instanceof PaginationInterface && $format === 'json';
    }


    public function getSupportedTypes(?string $format): array
    {
        return [PaginationInterface::class => true];
    }
}
