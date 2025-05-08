<?php

namespace DiyPageBundle\Repository;

use DiyPageBundle\Entity\BlockAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method BlockAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlockAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlockAttribute[]    findAll()
 * @method BlockAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockAttributeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockAttribute::class);
    }
}
