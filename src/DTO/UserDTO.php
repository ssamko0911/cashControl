<?php

namespace App\DTO;

use App\Entity\EntityInterface;
use App\Entity\User;
use App\Security\AccessGroup;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

class UserDTO implements DTOInterface
{
    #[Groups([AccessGroup::USER_READ])]
    public ?int $id;

    #[Groups([AccessGroup::USER_READ, AccessGroup::USER_EDIT, AccessGroup::USER_SIGN])]
    public string $firstName;

    #[Groups([AccessGroup::USER_READ, AccessGroup::USER_EDIT, AccessGroup::USER_SIGN])]
    public string $lastName;

    #[Groups([AccessGroup::USER_READ, AccessGroup::USER_EDIT, AccessGroup::USER_SIGN])]
    public string $email;

    #[Groups([AccessGroup::USER_EDIT, AccessGroup::USER_SIGN])]
    public string $password;

    #[Groups([AccessGroup::USER_READ, AccessGroup::USER_EDIT, AccessGroup::USER_SIGN])]
    public ?array $roles;

    #[Ignore] public function getEntityObject(): EntityInterface
    {
        return new User();
    }
}
