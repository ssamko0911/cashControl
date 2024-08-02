<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Enum\AccountTypeEnum;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{

    public function __construct(
        private AccountRepository      $accountRepository,
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
            ->setTotal(
                new Money(200,
                    new Currency('EUR')));

        $this->entityManager->persist($account);
        $this->entityManager->flush();

        $accountFromRepo = $this->accountRepository->findOneBy([
            'accountType' => AccountTypeEnum::TYPE_DEBIT->value
        ]);

        return $this->json($accountFromRepo);
    }
}