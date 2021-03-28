<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191226102438 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE template ADD html_template VARCHAR(255) DEFAULT NULL COMMENT \'Название файла с редактируемым шаблоном\', DROP html_template_path, CHANGE alias alias VARCHAR(50) NOT NULL COMMENT \'Название-идентификатор шаблона\', CHANGE bank_id bank_id INT DEFAULT NULL COMMENT \'ID банка\', CHANGE file_path file_path VARCHAR(255) NOT NULL COMMENT \'Путь к файлу шаблона\', CHANGE name name VARCHAR(255) NOT NULL COMMENT \'Название шаблона\', CHANGE editable editable TINYINT(1) DEFAULT \'0\' NOT NULL COMMENT \'Является ли шаблон редактируемым?\'');
        $this->addSql('ALTER TABLE special_template CHANGE body body LONGTEXT NOT NULL COMMENT \'Тело HTML, закодированное в base64\', CHANGE hash hash VARCHAR(255) NOT NULL COMMENT \'md5 hash, взятый от body\', CHANGE application_id application_id INT NOT NULL COMMENT \'ID заявки\'');
        $this->addSql('CREATE UNIQUE INDEX application_template_unique ON special_template (application_id, template_id)');
        $this->addSql('ALTER TABLE template_parameter CHANGE alias alias VARCHAR(50) NOT NULL COMMENT \'Название-идентификатор параметра\', CHANGE type type VARCHAR(50) NOT NULL COMMENT \'Тип параметра\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX application_template_unique ON special_template');
        $this->addSql('ALTER TABLE special_template CHANGE body body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE hash hash VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE application_id application_id INT NOT NULL');
        $this->addSql('ALTER TABLE template ADD html_template_path VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP html_template, CHANGE alias alias VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE bank_id bank_id INT DEFAULT NULL, CHANGE file_path file_path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE editable editable TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE template_parameter CHANGE alias alias VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE type type VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
