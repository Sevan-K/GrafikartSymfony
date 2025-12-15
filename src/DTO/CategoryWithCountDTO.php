<?php

namespace App\DTO;

class CategoryWithCountDTO
{

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly int $count
    ) {}
}
