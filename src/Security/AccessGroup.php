<?php

declare(strict_types=1);

namespace App\Security;

class AccessGroup
{
    // Account
    public const string ACCOUNT_READ = 'account:read';
    public const string ACCOUNT_EDIT = 'account:edit';
    public const string ACCOUNT_CREATE = 'account:create';

    // Transaction
    public const string TRANSACTION_READ = 'transaction:read';
    public const string TRANSACTION_EDIT = 'transaction:edit';
    public const string TRANSACTION_CREATE = 'transaction:create';

    // Category
    public const string CATEGORY_READ = 'category:read';
    public const string CATEGORY_EDIT = 'category:edit';
    public const string CATEGORY_CREATE = 'category:create';

    // CategoryBudget
    public const string CATEGORY_BUDGET_READ = 'category:budget:read';
    public const string CATEGORY_BUDGET_EDIT = 'category:budget:edit';
    public const string CATEGORY_BUDGET_CREATE = 'category:budget:create';

    //User
    public const string USER_SIGN = 'user:sign';
    public const string USER_SIGN_RESPONSE = 'user:sign:response';
    public const string USER_EDIT = 'user:edit';
    public const string USER_READ = 'user:read';
}
