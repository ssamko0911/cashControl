<?php

declare(strict_types=1);

namespace App\Tests\Unit\Builder;

use App\Builder\AccountEntityBuilder;
use App\DTO\AccountDTO;
use App\DTO\CurrencyDTO;
use App\DTO\MoneyDTO;
use App\Entity\Account;
use App\Entity\Enum\AccountTypeEnum;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class AccountEntityBuilderTest extends TestCase
{
    private AccountEntityBuilder $builder;

    public function setUp(): void
    {
        $this->builder = new AccountEntityBuilder();
    }

    public function testCreateAccountFromAccountDTO(): void
    {
        $expected = $this->getAccount();
        $actual = $this->builder->buildFromDTO($this->getAccountDTO());
        self::assertEqualsCanonicalizing($expected, $actual, 'Failed');
    }

    private function getAccount(): Account
    {
        return (new Account())->setName('TestAcc')
            ->setDescription('TestAccDesc')
            ->setTotal(
                new Money(
                    '200',
                    new Currency('USD')
                )
            )
            ->setAccountType(AccountTypeEnum::TYPE_DEBIT);
    }

    private function getAccountDTO(): AccountDTO
    {
        $dto = new AccountDTO();
        $dto->name = 'TestAcc';
        $dto->accountType = AccountTypeEnum::TYPE_DEBIT;
        $dto->description = 'TestAccDesc';

        $currencyDto = new CurrencyDTO();
        $currencyDto->code = 'USD';

        $total = new MoneyDTO();
        $total->amount = '200';
        $total->currency = $currencyDto;

        $dto->total = $total;
        $dto->id = 999;

        return $dto;
    }


    //TODO: destroy builder;
    protected function tearDown(): void
    {
        parent::tearDown();
    }


}