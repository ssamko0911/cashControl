<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241026113959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'make budgetLimit nullable';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_budget CHANGE budget_limit budget_limit JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_budget CHANGE budget_limit budget_limit JSON NOT NULL');
    }
}
