<?php

declare(strict_types=1);

namespace App\Command;

use App\DTO\TemplateCreateApiModel;
use App\Entity\Bank;
use App\Entity\DocumentAlias;
use App\Entity\File;
use App\Service\TemplateCreator\DefaultCreator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use PhpOffice\PhpWord\Exception;

/**
 * Команда создания шаблонов документов.
 */
class SaveTemplateCommand extends Command
{
    /**
     * TODO шаблоны будут вынесены из файла. Записано как техдолг.
     */
    private const TEMPLATE_CF_APPLICATION_PARAMS = [
        'sumcf' => ['type' => 'simple'],
        'timecf' => ['type' => 'simple'],
        'typecf' => ['type' => 'simple'],
        'purposecf' => ['type' => 'simple'],
        'timecontr' => ['type' => 'simple'],
        'subjectcontr' => ['type' => 'simple'],
        'lotnum' => ['type' => 'simple'],
        'namezak' => ['type' => 'simple'],
        'innzak' => ['type' => 'simple'],
        'ogrnzak' => ['type' => 'simple']
    ];

    private const TEMPLATE_FINANCIAL_STATEMENT = [
        'currentPeriod' => ['type' => 'simple'],
        'previousPeriod' => ['type' => 'simple'],
        'beforeLastPeriod' => ['type' => 'simple'],

        '1110-0' => ['type' => 'simple'],
        '1120-0' => ['type' => 'simple'],
        '1130-0' => ['type' => 'simple'],
        '1140-0' => ['type' => 'simple'],
        '1150-0' => ['type' => 'simple'],
        '1160-0' => ['type' => 'simple'],
        '1170-0' => ['type' => 'simple'],
        '1180-0' => ['type' => 'simple'],
        '1190-0' => ['type' => 'simple'],
        '1100-0' => ['type' => 'simple'],

        '1210-0' => ['type' => 'simple'],
        '1220-0' => ['type' => 'simple'],
        '1230-0' => ['type' => 'simple'],
        '1231-0' => ['type' => 'simple'],
        '1240-0' => ['type' => 'simple'],
        '1250-0' => ['type' => 'simple'],
        '1260-0' => ['type' => 'simple'],
        '1200-0' => ['type' => 'simple'],
        '1600-0' => ['type' => 'simple'],

        '1310-0' => ['type' => 'simple'],
        '1320-0' => ['type' => 'simple'],
        '1340-0' => ['type' => 'simple'],
        '1350-0' => ['type' => 'simple'],
        '1360-0' => ['type' => 'simple'],
        '1370-0' => ['type' => 'simple'],
        '1300-0' => ['type' => 'simple'],

        '1410-0' => ['type' => 'simple'],
        '1420-0' => ['type' => 'simple'],
        '1430-0' => ['type' => 'simple'],
        '1450-0' => ['type' => 'simple'],
        '1400-0' => ['type' => 'simple'],

        '1510-0' => ['type' => 'simple'],
        '1520-0' => ['type' => 'simple'],
        '1521-0' => ['type' => 'simple'],
        '1530-0' => ['type' => 'simple'],
        '1540-0' => ['type' => 'simple'],
        '1550-0' => ['type' => 'simple'],
        '1500-0' => ['type' => 'simple'],
        '1700-0' => ['type' => 'simple'],

        '2110-0' => ['type' => 'simple'],
        '2120-0' => ['type' => 'simple'],
        '2100-0' => ['type' => 'simple'],
        '2210-0' => ['type' => 'simple'],
        '2220-0' => ['type' => 'simple'],
        '2200-0' => ['type' => 'simple'],
        '2310-0' => ['type' => 'simple'],
        '2320-0' => ['type' => 'simple'],
        '2330-0' => ['type' => 'simple'],
        '2340-0' => ['type' => 'simple'],
        '2350-0' => ['type' => 'simple'],
        '2300-0' => ['type' => 'simple'],
        '2410-0' => ['type' => 'simple'],
        '2430-0' => ['type' => 'simple'],
        '2450-0' => ['type' => 'simple'],
        '2460-0' => ['type' => 'simple'],
        '2400-0' => ['type' => 'simple'],

        '5640-0' => ['type' => 'simple'],

        '1110-1' => ['type' => 'simple'],
        '1120-1' => ['type' => 'simple'],
        '1130-1' => ['type' => 'simple'],
        '1140-1' => ['type' => 'simple'],
        '1150-1' => ['type' => 'simple'],
        '1160-1' => ['type' => 'simple'],
        '1170-1' => ['type' => 'simple'],
        '1180-1' => ['type' => 'simple'],
        '1190-1' => ['type' => 'simple'],
        '1100-1' => ['type' => 'simple'],

        '1210-1' => ['type' => 'simple'],
        '1220-1' => ['type' => 'simple'],
        '1230-1' => ['type' => 'simple'],
        '1231-1' => ['type' => 'simple'],
        '1240-1' => ['type' => 'simple'],
        '1250-1' => ['type' => 'simple'],
        '1260-1' => ['type' => 'simple'],
        '1200-1' => ['type' => 'simple'],
        '1600-1' => ['type' => 'simple'],

        '1310-1' => ['type' => 'simple'],
        '1320-1' => ['type' => 'simple'],
        '1340-1' => ['type' => 'simple'],
        '1350-1' => ['type' => 'simple'],
        '1360-1' => ['type' => 'simple'],
        '1370-1' => ['type' => 'simple'],
        '1300-1' => ['type' => 'simple'],

        '1410-1' => ['type' => 'simple'],
        '1420-1' => ['type' => 'simple'],
        '1430-1' => ['type' => 'simple'],
        '1450-1' => ['type' => 'simple'],
        '1400-1' => ['type' => 'simple'],

        '1510-1' => ['type' => 'simple'],
        '1520-1' => ['type' => 'simple'],
        '1521-1' => ['type' => 'simple'],
        '1530-1' => ['type' => 'simple'],
        '1540-1' => ['type' => 'simple'],
        '1550-1' => ['type' => 'simple'],
        '1500-1' => ['type' => 'simple'],
        '1700-1' => ['type' => 'simple'],

        '2110-1' => ['type' => 'simple'],
        '2120-1' => ['type' => 'simple'],
        '2100-1' => ['type' => 'simple'],
        '2210-1' => ['type' => 'simple'],
        '2220-1' => ['type' => 'simple'],
        '2200-1' => ['type' => 'simple'],
        '2310-1' => ['type' => 'simple'],
        '2320-1' => ['type' => 'simple'],
        '2330-1' => ['type' => 'simple'],
        '2340-1' => ['type' => 'simple'],
        '2350-1' => ['type' => 'simple'],
        '2300-1' => ['type' => 'simple'],
        '2410-1' => ['type' => 'simple'],
        '2430-1' => ['type' => 'simple'],
        '2450-1' => ['type' => 'simple'],
        '2460-1' => ['type' => 'simple'],
        '2400-1' => ['type' => 'simple'],

        '5640-1' => ['type' => 'simple'],

        '1110-2' => ['type' => 'simple'],
        '1120-2' => ['type' => 'simple'],
        '1130-2' => ['type' => 'simple'],
        '1140-2' => ['type' => 'simple'],
        '1150-2' => ['type' => 'simple'],
        '1160-2' => ['type' => 'simple'],
        '1170-2' => ['type' => 'simple'],
        '1180-2' => ['type' => 'simple'],
        '1190-2' => ['type' => 'simple'],
        '1100-2' => ['type' => 'simple'],

        '1210-2' => ['type' => 'simple'],
        '1220-2' => ['type' => 'simple'],
        '1230-2' => ['type' => 'simple'],
        '1231-2' => ['type' => 'simple'],
        '1240-2' => ['type' => 'simple'],
        '1250-2' => ['type' => 'simple'],
        '1260-2' => ['type' => 'simple'],
        '1200-2' => ['type' => 'simple'],
        '1600-2' => ['type' => 'simple'],

        '1310-2' => ['type' => 'simple'],
        '1320-2' => ['type' => 'simple'],
        '1340-2' => ['type' => 'simple'],
        '1350-2' => ['type' => 'simple'],
        '1360-2' => ['type' => 'simple'],
        '1370-2' => ['type' => 'simple'],
        '1300-2' => ['type' => 'simple'],

        '1410-2' => ['type' => 'simple'],
        '1420-2' => ['type' => 'simple'],
        '1430-2' => ['type' => 'simple'],
        '1450-2' => ['type' => 'simple'],
        '1400-2' => ['type' => 'simple'],

        '1510-2' => ['type' => 'simple'],
        '1520-2' => ['type' => 'simple'],
        '1521-2' => ['type' => 'simple'],
        '1530-2' => ['type' => 'simple'],
        '1540-2' => ['type' => 'simple'],
        '1550-2' => ['type' => 'simple'],
        '1500-2' => ['type' => 'simple'],
        '1700-2' => ['type' => 'simple'],

        '2110-2' => ['type' => 'simple'],
        '2120-2' => ['type' => 'simple'],
        '2100-2' => ['type' => 'simple'],
        '2210-2' => ['type' => 'simple'],
        '2220-2' => ['type' => 'simple'],
        '2200-2' => ['type' => 'simple'],
        '2310-2' => ['type' => 'simple'],
        '2320-2' => ['type' => 'simple'],
        '2330-2' => ['type' => 'simple'],
        '2340-2' => ['type' => 'simple'],
        '2350-2' => ['type' => 'simple'],
        '2300-2' => ['type' => 'simple'],
        '2410-2' => ['type' => 'simple'],
        '2430-2' => ['type' => 'simple'],
        '2450-2' => ['type' => 'simple'],
        '2460-2' => ['type' => 'simple'],
        '2400-2' => ['type' => 'simple'],

        '5640-2' => ['type' => 'simple'],
    ];

    private const TEMPLATE_CF_QUESTIONNAIRE = [
        'zid' => ['type' => 'simple'],

        'nameorg' => ['type' => 'simple'],
        'nameorgfull' => ['type' => 'simple'],
        'nameorgshort' => ['type' => 'simple'],
        'orgform' => ['type' => 'simple'],
        'nameorginyazfull' => ['type' => 'simple'],
        'sokr_naimenovanie_inostr_yazik' => ['type' => 'simple'],

        'namereorg' => ['type' => 'simple'],
        'regdate' => ['type' => 'simple'],
        'registraciya_data_vidachi_svid_registracii' => ['type' => 'simple'],
        'placegosreg' => ['type' => 'simple'],

        'inn' => ['type' => 'simple'],
        'ogrn' => ['type' => 'simple'],
        'kpp' => ['type' => 'simple'],
        'okpo' => ['type' => 'simple'],
        'okved' => ['type' => 'simple'],
        'okato' => ['type' => 'simple'],

        'fm_sob_tip_sobstvennosti' => ['type' => 'simple'],
        'fm_sob_tip_organizacii' => ['type' => 'simple'],

        'celi_fin_hoz_deyatelnosti' => ['type' => 'simple'],

        'bankname' => ['type' => 'simple'],
        'bik' => ['type' => 'simple'],
        'bankaccount' => ['type' => 'simple'],
        'el_ras_cht_nalichie_kartoteki' => ['type' => 'simple'],

        'razm_ust_kap_oplacheniy_uk' => ['type' => 'simple'],
        'razm_ust_kap_objavlenniy_uk' => ['type' => 'simple'],

        'wageFund' => ['type' => 'simple'],

        'licenses' => [
            'type' => 'block',
            'children' => [
                'licensecode' => ['type' => 'simple'],
                'licensenum' => ['type' => 'simple'],
                'licensedate' => ['type' => 'simple'],
                'licensesource' => ['type' => 'simple'],
                'licensedetail' => ['type' => 'simple'],
            ],
        ],

        'yessite' => ['type' => 'simple'],
        'companysite' => ['type' => 'simple'],

        'ur_lica_uchastniki_dolya_1_perc' => [
            'type' => 'block',
            'children' => [
                'jurentityname' => ['type' => 'simple'],
                'jurentityinn' => ['type' => 'simple'],
                'jurentityogrn' => ['type' => 'simple'],
                'jurentitylocation' => ['type' => 'simple'],
                'jurentitysharecapital' => ['type' => 'simple'],
            ],
        ],

        'subject_msp_type' => ['type' => 'simple'],

        'jaindex' => ['type' => 'simple'],
        'jacountry' => ['type' => 'simple'],
        'jaregion' => ['type' => 'simple'],
        'jadistrict' => ['type' => 'simple'],
        'jacity' => ['type' => 'simple'],
        'jalocality' => ['type' => 'simple'],
        'jastreet' => ['type' => 'simple'],
        'jahouse' => ['type' => 'simple'],
        'jabuilding' => ['type' => 'simple'],
        'jaokato' => ['type' => 'simple'],
        'jaoffice' => ['type' => 'simple'],

        'block_flfiz' => [
            'type' => 'block',
            'children' => [
                'block_flfio_lastname' => ['type' => 'simple'],
                'block_flfio_firstname' => ['type' => 'simple'],
                'block_flfio_middlename' => ['type' => 'simple'],
                'block_flgender' => ['type' => 'simple'],
                'block_flbirthdate' => ['type' => 'simple'],
                'block_flbirthplace' => ['type' => 'simple'],
                'block_fltel' => ['type' => 'simple'],
                'block_flinn' => ['type' => 'simple'],

                'block_invalid' => ['type' => 'simple'],
                'block_relatedtoinvalid' => ['type' => 'simple'],
                'block_sharepercent' => ['type' => 'simple'],
                'block_flbeneficiary' => ['type' => 'simple'],
                'block_executive' => ['type' => 'simple'],
                'block_ofappointdate' => ['type' => 'simple'],
                'block_citizenship' => ['type' => 'simple'],

                'block_fldocseria' => ['type' => 'simple'],
                'block_fldocnum' => ['type' => 'simple'],
                'block_fldocdate' => ['type' => 'simple'],
                'block_fldocinstitut' => ['type' => 'simple'],
                'block_fldocinscode' => ['type' => 'simple'],

                'block_appointdocdate' => ['type' => 'simple'],
                'block_appointdocnum' => ['type' => 'simple'],
                'block_fltime' => ['type' => 'simple'],

                'block_flindex' => ['type' => 'simple'],
                'block_flcountry' => ['type' => 'simple'],
                'block_flregion' => ['type' => 'simple'],
                'block_fldistrict' => ['type' => 'simple'],
                'block_flcity' => ['type' => 'simple'],
                'block_fllocality' => ['type' => 'simple'],
                'block_flstreet' => ['type' => 'simple'],
                'block_flhouse' => ['type' => 'simple'],
                'block_flbuilding' => ['type' => 'simple'],
                'block_floffice' => ['type' => 'simple'],
                'block_flocato' => ['type' => 'simple'],
            ],
        ],

        'block_beneficiaries' => [
            'type' => 'block',
            'children' => [
                'block_flfio_lastname' => ['type' => 'simple'],
                'block_flfio_firstname' => ['type' => 'simple'],
                'block_flfio_middlename' => ['type' => 'simple'],
                'block_flbirthdate' => ['type' => 'simple'],
                'block_flbirthplace' => ['type' => 'simple'],
                'block_fltel' => ['type' => 'simple'],
                'block_flinn' => ['type' => 'simple'],

                'block_invalid' => ['type' => 'simple'],
                'block_relatedtoinvalid' => ['type' => 'simple'],
                'block_sharepercent' => ['type' => 'simple'],
                'block_flbeneficiary' => ['type' => 'simple'],
                'block_executive' => ['type' => 'simple'],
                'block_ofappointdate' => ['type' => 'simple'],
                'block_citizenship' => ['type' => 'simple'],

                'block_fldocseria' => ['type' => 'simple'],
                'block_fldocnum' => ['type' => 'simple'],
                'block_fldocdate' => ['type' => 'simple'],
                'block_fldocinstitut' => ['type' => 'simple'],
                'block_fldocinscode' => ['type' => 'simple'],

                'block_appointdocdate' => ['type' => 'simple'],
                'block_appointdocnum' => ['type' => 'simple'],
                'block_fltime' => ['type' => 'simple'],

                'block_flindex' => ['type' => 'simple'],
                'block_flcountry' => ['type' => 'simple'],
                'block_flregion' => ['type' => 'simple'],
                'block_fldistrict' => ['type' => 'simple'],
                'block_flcity' => ['type' => 'simple'],
                'block_fllocality' => ['type' => 'simple'],
                'block_flstreet' => ['type' => 'simple'],
                'block_flhouse' => ['type' => 'simple'],
                'block_flbuilding' => ['type' => 'simple'],
                'block_floffice' => ['type' => 'simple'],
                'block_flocato' => ['type' => 'simple'],
            ],
        ],

        'celi_ustanov_pred_haract_del_otnosheniy_s_bankom' => ['type' => 'simple'],

        'kontakty_kont_lico' => ['type' => 'simple'],
        'tel' => ['type' => 'simple'],
        'email' => ['type' => 'simple'],
        'fax' => ['type' => 'simple'],

        'controlorgan' => ['type' => 'simple'],
        'fioorgan' => ['type' => 'simple'],
        'executiveorgan' => ['type' => 'simple'],
        'executivefio' => ['type' => 'simple'],

        'filialy_predstavitelstav' => [
            'type' => 'block',
            'children' => [
                'branchlocation' => ['type' => 'simple'],
                'branchstate' => ['type' => 'simple'],
                'branchdate' => ['type' => 'simple'],
            ],
        ],

        'organizatciya_chast_holdinga' => ['type' => 'simple'],
        'uchast_drug_organ_sovm_deyat_s_drugimi' => ['type' => 'simple'],
        'lossdocs' => ['type' => 'simple'],
        'kreditnaya_istoriya' => ['type' => 'simple'],
        'deystvuyushie_kredit_obyazatelstva' => ['type' => 'simple'],
        'deyst_dogovora_org_yavl_zalog_poruch' => ['type' => 'simple'],

        'imeetsya_li_zadolzhnost_pers_org' => ['type' => 'simple'],
        'imeetsya_li_zadolzhnost_po_nalog_sbor' => ['type' => 'simple'],
        'imeetsya_li_zadolzhnost_gos_vnebyudzhet_fond' => ['type' => 'simple'],
        'overalldolg' => ['type' => 'simple'],
        'fin_ust_sum_kart_neopl_raschet_dok_bank_klient' => ['type' => 'simple'],
        'fin_ust_skritie_potery_25_perc' => ['type' => 'simple'],
        'fin_ust_neispol_2_ispol_narush_sroki' => ['type' => 'simple'],
        'fin_ust_plan_razvit_25_perc_chist_aktiv' => ['type' => 'simple'],
        'fin_ust_fakty_nom_1_buh_balans' => ['type' => 'simple'],
        'otcenka_fin_polozheniya' => ['type' => 'simple'],
        'sved_istoch_proish_den_sredstv' => ['type' => 'simple'],

        'sved_del_rep_kl_otzyvy_kontr' => ['type' => 'simple'],
        'sved_del_rep_kl_otzyvy_kredit' => ['type' => 'simple'],
        'sved_del_rep_kl_otric_fakty' => ['type' => 'simple'],

        'sv_sud_razb_proc_bankr_5_let' => ['type' => 'simple'],
        'sv_sud_razb_proisvod_sved_o_nesost' => ['type' => 'simple'],
        'sv_sud_razb_reshenie_sved_o_nesost' => ['type' => 'simple'],
        'sv_sud_razb_proved_likvid' => ['type' => 'simple'],
        'sv_sud_razb_fakty_neispol_obyaz' => ['type' => 'simple'],
        'sv_sud_razb_fakty_neispol_den_obyaz' => ['type' => 'simple'],
        'sv_sud_razb_sud_dela_otv' => ['type' => 'simple'],

        'tax_system' => ['type' => 'simple'],
        'buhuchet' => ['type' => 'simple'],

        'flfio' => ['type' => 'simple'],
        'fldocseria' => ['type' => 'simple'],
        'fldocnum' => ['type' => 'simple'],
        'fldocdate' => ['type' => 'simple'],
        'fldocinstitut' => ['type' => 'simple'],
        'fldocinscode' => ['type' => 'simple'],
        'fladdress2' => ['type' => 'simple'],
    ];

    const TEMPLATES = [
        DocumentAlias::CF_FINANCIAL_STATEMENT => [
            'alias' => DocumentAlias::CF_FINANCIAL_STATEMENT,
            'bank_id' => Bank::NEXUS_ID,
            'name' => 'Электронный вариант Финансовой отчетности',
            'params' => self::TEMPLATE_FINANCIAL_STATEMENT,
            'editable' => false,
        ],
        DocumentAlias::CF_APPLICATION_PARAMS => [
            'alias' => DocumentAlias::CF_APPLICATION_PARAMS,
            'bank_id' => Bank::NEXUS_ID,
            'name' => 'Электронный вариант параметров Заявки',
            'params' => self::TEMPLATE_CF_APPLICATION_PARAMS,
            'editable' => false,
        ],
        DocumentAlias::CF_QUESTIONARE => [
            'alias' => DocumentAlias::CF_QUESTIONARE,
            'bank_id' => Bank::NEXUS_ID,
            'name' => 'Анкета клиента ЮЛ',
            'params' => self::TEMPLATE_CF_QUESTIONNAIRE,
            'editable' => false,
        ],
    ];

    protected static $defaultName = 'save-template';

    /**
     * @var ContainerInterface
     */
    private $containerBuilder;

    /**
     * @var DefaultCreator
     */
    private $defaultCreator;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $templatesForGeneration;

    public function __construct(
        ContainerInterface $containerBuilder,
        Filesystem $filesystem,
        DefaultCreator $defaultCreator,
        string $templatesForGeneration
    ) {
        $this->containerBuilder = $containerBuilder;
        $this->filesystem = $filesystem;
        $this->defaultCreator = $defaultCreator;
        $this->templatesForGeneration = $templatesForGeneration;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Save template in var/ directory.')
            ->addOption(
                'all',
                null,
                InputOption::VALUE_OPTIONAL,
                'Load all templates',
                false
            )
            ->addOption(
                'alias',
                null,
                InputOption::VALUE_OPTIONAL,
                'Template alias',
                false
            )
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $all = $input->getOption('all');
        $alias = $input->getOption('alias');

        if (false === $all && false === $alias) {
            throw new RuntimeException('Введите all для генерации всех шаблонов или алиас нужного шаблона.');
        }

        if (false !== $all) {
            $createdTemplatesAliases = [];

            foreach (self::TEMPLATES as $template) {
                $createdTemplatesAliases[] = $this->createTemplate($template);
            }

            $output->writeln('Шаблоны загружены: ' . implode(',', $createdTemplatesAliases));
        } elseif (false !== $alias) {
            DocumentAlias::isValid($alias);

            $this->createTemplate(self::TEMPLATES[$alias]);

            $output->writeln('Шаблон загружен: ' . $alias);
        }
    }

    /**
     * Метод создает шаблон файла и записывает параметры шаблона в БД.
     *
     * @param array $template
     * @return string
     * @throws Exception\CopyFileException
     * @throws Exception\CreateTemporaryFileException
     */
    private function createTemplate(array $template): string
    {
        $templateDto = $this->createTemplateDto($template);

        $templateDto->setFile($this->getFileContentInBase64($templateDto->getAlias()));

        $this->defaultCreator->init($templateDto)->create();

        return $templateDto->getAlias();
    }

    /**
     * Метод создает dto шаблона.
     *
     * @param array $template
     * @return TemplateCreateApiModel
     */
    private function createTemplateDto(array $template): TemplateCreateApiModel
    {
        $dto = new TemplateCreateApiModel();
        $dto->setAlias($template['alias']);
        $dto->setBankId($template['bank_id']);
        $dto->setName($template['name']);
        $dto->setParams($template['params']);
        $dto->setEditable($template['editable']);

        return $dto;
    }

    /**
     * Метод получает контент файла в base64.
     *
     * @param string $alias
     * @return string
     */
    private function getFileContentInBase64(string $alias): string
    {
        $templateForGeneration = $this->templatesForGeneration . '/' . $alias . '.docx';

        $file = new File($templateForGeneration);

        return $file->getInBase64();
    }
}
