<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Converter\HtmlToDocxConverter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertHtmlToDocx extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'convert:html-to-docx';

    /**
     * @var HtmlToDocxConverter
     */
    private $converter;

    public function __construct(HtmlToDocxConverter $converter)
    {
        $this->converter = $converter;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Converts .html file to .docx and saves result in var/ directory.')
            ->addArgument('htmlPath', InputArgument::REQUIRED, 'The path to .html file')
            ->addArgument('converterType', InputArgument::REQUIRED, 'Type of converter.');
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
        $htmlPath = $input->getArgument('htmlPath');
        $converterType = $input->getArgument('converterType');

        $this->converter
            ->init(file_get_contents($htmlPath), $converterType)
            ->convertToDirWithName($_ENV['CONVERTER_SAVE_PATH'], 'converted.docx');

        return 0;
    }
}
