<?php

namespace DiyPageBundle\Repository;

use DiyPageBundle\Entity\PageTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;

/**
 * @method PageTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageTag[]    findAll()
 * @method PageTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageTagRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageTag::class);
    }
}
