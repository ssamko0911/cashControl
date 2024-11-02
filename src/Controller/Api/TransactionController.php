<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Builder\TransactionEntityBuilder;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Entity\Transaction;
use App\Manager\AutoMapper;
use App\Manager\TransactionListManager;
use App\Manager\TransactionManager;
use App\Security\AccessGroup;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes\Parameter;

#[Route(path: '')]
class TransactionController extends AbstractController
{

    public function __construct(
        private readonly TransactionManager           $manager,
        private readonly TransactionEntityBuilder $transactionEntityBuilder,
        private readonly TransactionListManager   $transactionListManager,
        private readonly AutoMapper $mapper
    )
    {
    }

    /**
     * @throws \ReflectionException
     * @throws RandomException
     */
    #[Post(
        summary: 'Creates transaction',
        tags: ['Transactions'],
        parameters: [
            new Parameter(
                name: 'categoryId',
                description: 'Category id',
                in: 'query',
                schema: new Schema(type: 'integer')
            ),
        ],
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
        Account        $account,
        Request $request
    ): JsonResponse
    {
        $categoryId = (int)$request->get('categoryId');
        $transaction = $this->manager->saveTransaction($transactionDTO, $categoryId, $account);

        return $this->json($this->mapper->mapToModel($transaction, AccessGroup::TRANSACTION_READ));
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
            new Parameter(
                name: 'order',
                description: 'Order to sort by, ascending (asc) or descending (desc)',
                in: 'query',
                required: false,
                schema: new Schema(
                    type: 'string',
                    default: 'ASC',
                    enum: [
                        'ASC',
                        'DESC',
                    ]
                )
            ),
            new Parameter(
                name: 'sort',
                description: 'Field to sort by',
                in: 'query',
                required: false,
                schema: new Schema(
                    type: 'string',
                    default: 'id',
                    enum: [
                        'id',
                        'description',
                        'amount',
                        'createdAt',
                        'type',
                    ]
                )
            ),
            new Parameter(
                name: 'search',
                description: 'Search by fields: description',
                in: 'query'
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
    public function paginate(Account $account, Request $request): JsonResponse
    {
        $dtos = $this->transactionListManager->getList($request, $account);

        return $this->json($dtos, HttpResponse::HTTP_OK, [], [
            'groups' => [AccessGroup::TRANSACTION_READ],
        ]);
    }
}
