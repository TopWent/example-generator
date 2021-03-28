<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201030141233 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Меняет bank_id в шаблонах КФ на 96497';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE template SET bank_id = 96497 WHERE alias IN ("cfApplicationParams", "cfFinancialStatement", "cfQuestionnaire", "cfPersonalDataProcessingConsent", "cfBkiConsent", "cfGuaranteeLetterOfUnfulfilledIFNS");');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('UPDATE template SET bank_id = 100 WHERE alias IN ("cfApplicationParams", "cfFinancialStatement", "cfQuestionnaire", "cfPersonalDataProcessingConsent", "cfBkiConsent", "cfGuaranteeLetterOfUnfulfilledIFNS");');
    }
}
