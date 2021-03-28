<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201016084256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавлены параметры для cfGuaranteeLetterOfUnfulfilledIFNS, cfBkiConsent, cfPersonalDataProcessingConsent';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            SET @a = (SELECT id FROM template WHERE alias = "cfPersonalDataProcessingConsent" AND bank_id = 100);
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "flfio", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "fladdress2", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "fldocseria", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "fldocnum", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "fldocdate", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "fldocinstitut", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "fldocinscode", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "nameorg", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "createddate", null, "simple");
        ');
        $this->addSql('
            SET @a = (SELECT id FROM template WHERE alias = "cfGuaranteeLetterOfUnfulfilledIFNS" AND bank_id = 100);
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "nameorg", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "jaaddress", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "zid", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "createddate", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "inn", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "kpp", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "flfio", null, "simple");
        ');
        $this->addSql('
            SET @a = (SELECT id FROM template WHERE alias = "cfBkiConsent" AND bank_id = 100);
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "createddate", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "nameorg", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "ogrn", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "inn", null, "simple");
            INSERT INTO template_parameter (template_id, alias, parent_id, type) VALUES (@a, "jaaddress", null, "simple");
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM template_parameter WHERE (SELECT id FROM template WHERE alias = "cfPersonalDataProcessingConsent" AND bank_id = 100) = template_id');
        $this->addSql('DELETE FROM template_parameter WHERE (SELECT id FROM template WHERE alias = "cfGuaranteeLetterOfUnfulfilledIFNS" AND bank_id = 100) = template_id');
        $this->addSql('DELETE FROM template_parameter WHERE (SELECT id FROM template WHERE alias = "cfBkiConsent" AND bank_id = 100) = template_id');
    }
}
