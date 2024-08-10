<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240810104915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE transaction ADD type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE transaction DROP type');
    }
}
