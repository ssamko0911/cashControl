<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241026120925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'update relation Category-CategoryBudget';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_budget DROP INDEX UNIQ_7C1A3D0012469DE2, ADD INDEX IDX_7C1A3D0012469DE2 (category_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category_budget DROP INDEX IDX_7C1A3D0012469DE2, ADD UNIQUE INDEX UNIQ_7C1A3D0012469DE2 (category_id)');
    }
}
