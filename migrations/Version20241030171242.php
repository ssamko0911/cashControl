<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241030171242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'rename property overBudget';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_budget CHANGE is_over_budget over_budget TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_budget CHANGE over_budget is_over_budget TINYINT(1) NOT NULL');
    }
}
