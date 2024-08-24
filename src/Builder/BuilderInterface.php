<?php

declare(strict_types=1);

namespace App\Builder;

use App\DTO\DTOInterface;
use App\Entity\EntityInterface;

interface BuilderInterface
{
    public function buildDTO(EntityInterface $entity): DTOInterface;
}