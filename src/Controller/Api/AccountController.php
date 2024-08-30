<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Builder\AccountEntityBuilder;
use App\Builder\TransactionEntityBuilder;
use App\DTO\AccountDTO;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Manager\AccountManager;
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
        private readonly TransactionEntityBuilder $transactionEntityBuilder
    ) {
    }

    #[Post(
        summary: 'Create account',
        tags: ['Accounts'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_CREATED,
                description: 'Resource created',
                content: [
                    new Model(
                        type: AccountDTO::class,
                        groups: [AccessGroup::ACCOUNT_READ],
                    )
                ]
            ),
            new Response(
                response: HttpResponse::HTTP_BAD_REQUEST,
                description: 'Bad request',
            )
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

    #[Post(
        summary: 'Create transaction',
        tags: ['Transactions'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_CREATED,
                description: 'Resource created',
                content: [
                    new Model(
                        type: TransactionDTO::class,
                        groups: [AccessGroup::TRANSACTION_READ],
                    )
                ]
            ),
            new Response(
                response: HttpResponse::HTTP_BAD_REQUEST,
                description: 'Bad request',
            )
        ]
    )]
    #[Route(path: '/{id}/transactions', name: 'app_account_create_transaction', methods: ['POST'])]
    public function createTransactionByAccountId(
        #[MapRequestPayload(
            serializationContext: [
                'groups' => [AccessGroup::TRANSACTION_CREATE],
            ],
            validationGroups: [
                AccessGroup::TRANSACTION_CREATE,
            ]
        )]
        TransactionDTO $transactionDTO,
        Account $account
    ): JsonResponse
    {
        //dd($transactionDTO);
        $transaction = $this->manager->saveTransaction($transactionDTO, $account);
        return $this->json($this->transactionEntityBuilder->buildDTO($transaction), HttpResponse::HTTP_CREATED, [], [
            'groups' => [AccessGroup::TRANSACTION_READ],
        ]);
    }
}
