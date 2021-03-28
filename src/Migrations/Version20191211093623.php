<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191211093623 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE template_parameter ADD parent_id INT DEFAULT NULL, ADD type VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE template_parameter ADD CONSTRAINT FK_E3C4E7A6727ACA70 FOREIGN KEY (parent_id) REFERENCES template_parameter (id)');
        $this->addSql('CREATE INDEX IDX_E3C4E7A6727ACA70 ON template_parameter (parent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE template_parameter DROP FOREIGN KEY FK_E3C4E7A6727ACA70');
        $this->addSql('DROP INDEX IDX_E3C4E7A6727ACA70 ON template_parameter');
        $this->addSql('ALTER TABLE template_parameter DROP parent_id, DROP type');
    }
}
