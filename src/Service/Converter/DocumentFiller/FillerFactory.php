<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller;

use App\Service\Converter\DocumentFiller\Handler\HandlerFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Создает объекты, которые заполняют макеты, обрабатывая html шаблоны.
 *
 * Class FillerFactory
 * @package App\Service\Converter\DocumentFiller
 */
class FillerFactory
{
    /**
     * @var ContainerInterface
     */
    private $handlerFactory;

    public function __construct(HandlerFactory $handlerFactory)
    {
        $this->handlerFactory = $handlerFactory;
    }

    /**
     * @param string $converterType
     *
     * @return PhpWordFillerInterface
     *
     * @throws \Exception
     */
    public function createPhpWordFiller(string $converterType): PhpWordFillerInterface
    {
        switch ($converterType) {
            case 'bg_layout':
                return new BgLayoutFiller($this->handlerFactory);
            case 'bg_layout_peresvet':
                return new BgLayoutPeresvetFiller($this->handlerFactory);
            case 'agreement_layout':
                return new AgreementLayoutFiller($this->handlerFactory);
            default:
                throw new \Exception('Неизвестный тип шаблона: '.$converterType);
        }
    }
}
