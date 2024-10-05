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
}