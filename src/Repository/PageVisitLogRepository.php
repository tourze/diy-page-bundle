<?php

namespace DiyPageBundle\Repository;

use DiyPageBundle\Entity\PageVisitLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;

/**
 * @method PageVisitLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageVisitLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageVisitLog[]    findAll()
 * @method PageVisitLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageVisitLogRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageVisitLog::class);
    }
}
