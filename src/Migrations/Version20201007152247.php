<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201007152247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавление поля для хранения шаблона в формате base64';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE template ADD file_base64 LONGTEXT DEFAULT NULL COMMENT \'Файл шаблона в формате base64\', CHANGE file_path file_path VARCHAR(255) DEFAULT NULL COMMENT \'Путь к файлу шаблона\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE template DROP file_base64, CHANGE file_path file_path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'Путь к файлу шаблона\'');
    }
}
