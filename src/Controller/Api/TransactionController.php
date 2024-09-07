<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Builder\TransactionEntityBuilder;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Manager\AccountManager;
use App\Manager\TransactionListManager;
use App\Security\AccessGroup;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes\Parameter;

#[Route(path: '/api')]
class TransactionController extends AbstractController
{

    public function __construct(
        private readonly AccountManager $manager,
        private readonly TransactionEntityBuilder $transactionEntityBuilder
    ) {
    }

    #[Post(
        summary: 'Creates transaction',
        tags: ['Transactions'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_CREATED,
                description: 'Resource created',
                content: [
                    new Model(
                        type: TransactionDTO::class,
                        groups: [AccessGroup::TRANSACTION_READ],
                    ),
                ]
            ),
            new Response(
                response: HttpResponse::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
        ]
    )]
    #[Route(path: '/accounts/{id}/transactions', name: 'app_account_create_transaction', methods: ['POST'])]
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
    ): JsonResponse {
        $transaction = $this->manager->saveTransaction($transactionDTO, $account);

        return $this->json($this->transactionEntityBuilder->buildDTO($transaction), HttpResponse::HTTP_CREATED, [], [
            'groups' => [AccessGroup::TRANSACTION_READ],
        ]);
    }

    #[Get(
        summary: 'Returns transaction by ID',
        tags: ['Transactions'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Successful response',
                content: [
                    new Model(
                        type: TransactionDTO::class,
                        groups: [AccessGroup::TRANSACTION_READ],
                    ),
                ]
            ),
            new Response(
                response: HttpResponse::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
        ]
    )]
    #[Route(path: '/transactions/{id}', name: 'app_account_get_transaction_by_id', methods: ['GET'])]
    public function getTransactionById(Transaction $transaction): JsonResponse
    {
        return $this->json($this->transactionEntityBuilder->buildDTO($transaction), HttpResponse::HTTP_OK, [], [
            'groups' => [AccessGroup::TRANSACTION_READ],
        ]);
    }


    #[Get(
        summary: 'Returns transactions by Account ID',
        tags: ['Transactions'],
        parameters: [
            new Parameter(
                name: 'page',
                description: 'The collection page number',
                in: 'query',
                schema: new Schema(type: 'integer', default: 1)
            ),
        ],
        responses: [
            new Response(
                response: HttpResponse::HTTP_OK,
                description: 'Successful response',
                content: [
                    new Model(
                        type: TransactionDTO::class,
                        groups: [AccessGroup::TRANSACTION_READ],
                    ),
                ]
            ),
            new Response(
                response: HttpResponse::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
        ]
    )]
    #[Route(path: '/accounts/{id}/transactions', name: 'app_account_get_transactions_by_account_id', methods: ['GET'])]
    public function paginate(Account $account, Request $request, TransactionListManager $listManager): JsonResponse
    {


        return $this->json($listManager->getList($request, $account), HttpResponse::HTTP_OK, [], [
            'groups' => [AccessGroup::TRANSACTION_READ],
        ]);
    }
}
