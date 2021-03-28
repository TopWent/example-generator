<?php

declare(strict_types=1);

namespace App\Tests\DocumentGenerator;

use App\Entity\TemplateParameter;
use App\Service\DocumentGenerator\Replacer\Replacer;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BlockReplacerTest extends KernelTestCase
{
    /** @var Replacer $replacer */
    private $replacer;

    protected function setUp()
    {
        self::bootKernel();
        $this->replacer = self::$container->get('App\Service\DocumentGenerator\Replacer\Replacer');

        if (!file_exists($_ENV['TESTS_PATH'])) {
            mkdir($_ENV['TESTS_PATH']);
        }
    }

    public function testBlockStrategy()
    {
        $parameter = $this->constructParameter();

        foreach ($this->getCases() as $key => $case) {
            $tempFilePath = $_ENV['TESTS_PATH'] . '/test_' . $key . '.docx';
            $templateContent = file_get_contents($_ENV['TESTS_DOCUMENTS_GENERATOR_PATH'] . 'test_documents/test_template_with_block.docx');
            file_put_contents($tempFilePath, $templateContent);
            $templateProcessor = new TemplateProcessor($tempFilePath);

            $this->replacer->setStrategyByType('block')
                ->replacePlaceHolderInDocx($templateProcessor, $parameter, $case['value']);
            $templateProcessor->saveAs($tempFilePath);
            $content = file_get_contents($tempFilePath);

            $similarity = similar_text($content, file_get_contents(
                $_ENV['TESTS_DOCUMENTS_GENERATOR_PATH'] . $case['benchmark']
            ));

            $this->assertEquals($similarity, $case['similarity']);
        }
    }

    private function getCases()
    {
        return [
            'block_with_empty' => [
                'benchmark' => '/test_documents/benchmark_block_with_empty.docx',
                'similarity' => 20209,
                'value' => [],
            ],
            'block_cloning' => [
                'benchmark' => '/test_documents/benchmark_block_cloning.docx',
                'similarity' => 20355,
                'value' => [
                    [
                        'vd' => 'test',
                        'num' => 'test2',
                        'date' => 'test3',
                        'kem' => 'test4',
                        'vid' => 'test5',
                    ],
                    [
                        'vd' => 'test2',
                        'num' => 'test22',
                        'date' => 'test32',
                        'kem' => 'test42',
                        'vid' => 'test52',
                    ],
                ],
            ],
            'block_deleting' => [
                'benchmark' => '/test_documents/benchmark_block_deleting.docx',
                'similarity' => 19951,
                'value' => false,
            ]
        ];
    }

    private function constructParameter(): TemplateParameter
    {
        $parameter = new TemplateParameter();
        $parameter->setAlias('licenses')
            ->setType('block');

        $parameterVd = (new TemplateParameter())
            ->setType('simple')
            ->setAlias('vd');

        $parameterNum = (new TemplateParameter())
            ->setType('simple')
            ->setAlias('num');

        $parameterDate = (new TemplateParameter())
            ->setType('simple')
            ->setAlias('date');

        $parameterKem = (new TemplateParameter())
            ->setType('simple')
            ->setAlias('kem');

        $parameterVid = (new TemplateParameter())
            ->setType('simple')
            ->setAlias('vid');

        $parameter->addChild($parameterVd)
            ->addChild($parameterNum)
            ->addChild($parameterDate)
            ->addChild($parameterKem)
            ->addChild($parameterVid);

        return $parameter;
    }
}