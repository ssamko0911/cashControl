<?php

namespace App\Controller\Api;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Manager\AutoMapper;
use App\Manager\UserManager;
use App\Security\AccessGroup;
use OpenApi\Attributes\Post;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes\Patch;

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
    #[Post(
        summary: 'Create user',
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: new Model(type: UserDTO::class, groups: [AccessGroup::USER_SIGN])
            )
        ),
        tags: ['User'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Successful response',
                content: [new Model(type: UserDTO::class, groups: [AccessGroup::USER_SIGN_RESPONSE])]
            ),
        ]
    )]
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

    /**
     * @throws \ReflectionException
     * @throws RandomException
     */
    #[Patch(
        summary: 'Update user',
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: new Model(type: UserDTO::class, groups: [AccessGroup::USER_EDIT])
            )
        ),
        tags: ['User'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Successful response',
                content: [new Model(type: UserDTO::class, groups: [AccessGroup::USER_READ])]
            ),
        ]
    )]
    #[Route('/users/{id}', name: 'api_user_patch', methods: ['PATCH'])]
    public function update(User $user, #[MapRequestPayload(
        serializationContext: [
            'groups' => [AccessGroup::USER_EDIT],
        ],
        validationGroups: [
            AccessGroup::USER_EDIT,
        ]
    )]
    UserDTO $userDTO): JsonResponse
    {
        $this->manager->update($user, $userDTO);

        return $this->json($this->mapper->mapToModel($user, AccessGroup::USER_READ));
    }
}
