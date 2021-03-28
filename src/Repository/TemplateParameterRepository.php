<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TemplateParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TemplateParameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateParameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateParameter[]    findAll()
 * @method TemplateParameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateParameter::class);
    }

    public function deleteAllParametersOfTemplate(int $templateId)
    {
        $this->createQueryBuilder('tp')
            ->delete()
            ->where('tp.template = :template')
            ->setParameter('template', $templateId)
            ->getQuery()
            ->execute();
    }
}
