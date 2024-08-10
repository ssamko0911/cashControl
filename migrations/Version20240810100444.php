<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240810100444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'set account_id not null';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE transaction CHANGE account_id account_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE transaction CHANGE account_id account_id INT DEFAULT NULL');
    }
}
