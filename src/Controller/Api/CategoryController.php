<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Builder\CategoryEntityBuilder;
use App\DTO\CategoryDTO;
use App\DTO\TransactionDTO;
use App\Entity\Account;
use App\Manager\CategoryManager;
use App\Security\AccessGroup;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryManager $manager,
        private readonly CategoryEntityBuilder $builder
    )
    {
    }

    #[Post(
        summary: 'Creates category',
        tags: ['Category'],
        responses: [
            new Response(
                response: HttpResponse::HTTP_CREATED,
                description: 'Resource created',
                content: [
                    new Model(
                        type: CategoryDTO::class,
                        groups: [AccessGroup::CATEGORY_READ],
                    ),
                ]
            ),
            new Response(
                response: HttpResponse::HTTP_BAD_REQUEST,
                description: 'Bad request',
            ),
        ]
    )]
    #[Route(path: '/categories', name: 'app_category_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(
            serializationContext: [
                'groups' => [AccessGroup::CATEGORY_CREATE],
            ],
            validationGroups: [
                AccessGroup::CATEGORY_CREATE,
            ]
        )]
        CategoryDTO $categoryDTO,
    ): JsonResponse {
        $category = $this->manager->create($categoryDTO);

        return $this->json($this->builder->buildDTO($category), HttpResponse::HTTP_CREATED, [], [
            'groups' => [AccessGroup::TRANSACTION_READ],
        ]);
    }
}
