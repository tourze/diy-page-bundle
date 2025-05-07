<?php

namespace DiyPageBundle\Repository;

use DiyPageBundle\Entity\VisitLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;

/**
 * @method VisitLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method VisitLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method VisitLog[]    findAll()
 * @method VisitLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitLogRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VisitLog::class);
    }
}
