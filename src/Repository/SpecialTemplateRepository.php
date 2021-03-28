<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SpecialTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SpecialTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpecialTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpecialTemplate[]    findAll()
 * @method SpecialTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecialTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpecialTemplate::class);
    }
}
