<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Builder\AccountEntityBuilder;
use App\DTO\AccountDTO;
use App\Entity\Account;
use App\Manager\AccountManager;
use App\Manager\AutoMapper;
use App\Security\AccessGroup;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/accounts')]
final class AccountController extends AbstractController
{
    public function __construct(
        private readonly AccountManager $manager,
        private readonly AccountEntityBuilder $accountEntityBuilder,
        private readonly AutoMapper $mapper
    ) {
    }

    #[Post(
        summary: 'Creates account',
        tags: ['Accounts'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_CREATED,
                description: 'Resource created',
                content: [
                    new Model(
                        type: AccountDTO::class,
                        groups: [AccessGroup::ACCOUNT_READ],
                    ),
                ]
            ),
            new Response(
                response: HttpResponse::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
        ]
    )]
    #[Route(path: '', name: 'app_account_create', methods: ['POST'])]
    public function createAccount(
        #[MapRequestPayload(
            serializationContext: [
                'groups' => [AccessGroup::ACCOUNT_CREATE],
            ],
            validationGroups: [
                AccessGroup::ACCOUNT_CREATE,
            ]
        )]
        AccountDTO $accountDTO
    ): JsonResponse {
        $account = $this->manager->saveAccount($accountDTO);

        return $this->json($this->accountEntityBuilder->buildDTO($account), HttpResponse::HTTP_CREATED, [], [
            'groups' => [AccessGroup::ACCOUNT_READ],
        ]);
    }

    #[Route(path: '/{id}', name: 'app_account_get', methods: ['GET'])]
    public function getAccountById(Account $account): JsonResponse
    {
        return $this->json($this->mapper->mapToModel($account, AccessGroup::ACCOUNT_READ));
    }
}
