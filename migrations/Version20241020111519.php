<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241020111519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C127EBA5CB');
        $this->addSql('DROP INDEX UNIQ_64C19C127EBA5CB ON category');
        $this->addSql('ALTER TABLE category DROP monthly_budget_id');
        $this->addSql('ALTER TABLE category_budget ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category_budget ADD CONSTRAINT FK_7C1A3D0012469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C1A3D0012469DE2 ON category_budget (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD monthly_budget_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C127EBA5CB FOREIGN KEY (monthly_budget_id) REFERENCES category_budget (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C127EBA5CB ON category (monthly_budget_id)');
        $this->addSql('ALTER TABLE category_budget DROP FOREIGN KEY FK_7C1A3D0012469DE2');
        $this->addSql('DROP INDEX UNIQ_7C1A3D0012469DE2 ON category_budget');
        $this->addSql('ALTER TABLE category_budget DROP category_id');
    }
}
