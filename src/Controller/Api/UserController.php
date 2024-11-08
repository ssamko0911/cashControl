<?php

namespace App\Controller\Api;

use App\DTO\UserDTO;
use App\Manager\AutoMapper;
use App\Manager\UserManager;
use App\Security\AccessGroup;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserManager $manager,
        private readonly AutoMapper  $mapper
    )
    {
    }

    /**
     * @throws \ReflectionException
     * @throws RandomException
     */
    #[Route('/users', name: 'api_user', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            serializationContext: [
                'groups' => [AccessGroup::USER_SIGN],
            ],
            validationGroups: [
                AccessGroup::USER_SIGN,
            ]
        )]
        UserDTO $userDTO
    ): JsonResponse
    {
        $user = $this->manager->saveUser($userDTO);

        return $this->json([
            $this->mapper->mapToModel($user, AccessGroup::USER_SIGN_RESPONSE)
        ]);
    }
}
