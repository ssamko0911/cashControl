<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241010181113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'rename limit - fails as this one is reserved';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_budget CHANGE `limit` budget_limit JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_budget CHANGE budget_limit `limit` JSON NOT NULL');
    }
}
