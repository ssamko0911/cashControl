<?php

namespace App\Entity;

use App\DTO\DTOInterface;

interface EntityInterface
{
    public function getDTO(): DTOInterface;
}
