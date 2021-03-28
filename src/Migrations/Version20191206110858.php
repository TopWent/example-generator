<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191206110858 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE template_parameter (id INT AUTO_INCREMENT NOT NULL, template_id INT NOT NULL, alias VARCHAR(50) NOT NULL, INDEX IDX_E3C4E7A65DA0FB8 (template_id), UNIQUE INDEX alias_template_unique (alias, template_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template (id INT AUTO_INCREMENT NOT NULL, alias VARCHAR(50) NOT NULL, bank_id INT DEFAULT NULL, file_path VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX alias_bank_id_unique (alias, bank_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE template_parameter ADD CONSTRAINT FK_E3C4E7A65DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE template_parameter DROP FOREIGN KEY FK_E3C4E7A65DA0FB8');
        $this->addSql('DROP TABLE template_parameter');
        $this->addSql('DROP TABLE template');
    }
}
