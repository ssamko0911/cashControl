<?php

namespace App\Controller;

use App\DTO\AccountDTO;
use App\Entity\Account;
use App\Entity\Enum\AccountTypeEnum;
use App\Entity\Enum\TransactionType;
use App\Entity\Transaction;
use App\Repository\AccountRepository;
use App\Security\AccessGroup;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{

    public function __construct(
        private AccountRepository               $accountRepository,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route(path: '/test', name: 'test', methods: [Request::METHOD_GET])]
    public function save(): JsonResponse
    {
        $account = new Account();
        $account
            ->setAccountType(AccountTypeEnum::TYPE_DEBIT)
            ->setDescription('Test Account')
            ->addTransaction($this->getTransaction())
            ->setTotal(
                new Money(200,
                    new Currency('EUR')));

        $this->entityManager->persist($account);
        $this->entityManager->flush();

        $accountFromRepo = $this->accountRepository->find(4);

        return $this->json($accountFromRepo);
    }

    private function getTransaction(): Transaction
    {
        return (new Transaction())
            ->setAmount(new Money(20,
                new Currency('EUR')))
            ->setDescription('Test Transaction')
            ->setType(TransactionType::TYPE_EXPENSE);
    }

    #[Route(path: 'acc', name: 'acc', methods: ['POST'])]
    public function createAccount(
        #[MapRequestPayload(
            serializationContext: [
                'groups' => [AccessGroup::ACCOUNT_CREATE],
            ]
        )]
        AccountDTO $accountDTO
    ): JsonResponse
    {
        $account = new Account();
        $account
            ->setDescription($accountDTO->description)
            ->setTotal($accountDTO->total)
            ->setAccountType($accountDTO->accountType);

        $this->entityManager->persist($account);
        $this->entityManager->flush();

        /** @var Account|null $accountFromRepo */
        $accountFromRepo = $this->accountRepository->findOneBy(
            [
                'description' => $accountDTO->description,
            ]
        );

        $accountDTO->createdAt = $accountFromRepo->getCreatedAt();
        $accountDTO->updatedAt = $accountFromRepo->getUpdatedAt();
        $accountDTO->id = $accountFromRepo->getId();

        return $this->json($accountDTO, Response::HTTP_OK, [], ['groups' => AccessGroup::ACCOUNT_READ]);
    }
}

// GET api/accounts/{id}
// PATCH api/accounts/{id}

// GET api/accounts/{id}/transactions
// PATCH api/accounts/transactions/{id}
