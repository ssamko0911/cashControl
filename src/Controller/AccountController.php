<?php

declare(strict_types=1);

namespace App\Controller;

use App\Builder\AccountEntityBuilder;
use App\DTO\AccountDTO;
use App\Manager\AccountManager;
use App\Security\AccessGroup;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    public function __construct(
        private readonly AccountManager $manager,
        private readonly AccountEntityBuilder $builder
    ) {
    }

    #[Route(path: 'acc', name: 'acc', methods: ['POST'])]
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
        $account = $this->manager->save($accountDTO);

        return $this->json($this->builder->buildDTO($account), Response::HTTP_OK, [], [
            'groups' => [AccessGroup::ACCOUNT_READ],
        ]);
    }
}
