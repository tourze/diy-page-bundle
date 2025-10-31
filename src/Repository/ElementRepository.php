<?php

namespace DiyPageBundle\Repository;

use DiyPageBundle\Entity\Element;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Element>
 */
#[AsRepository(entityClass: Element::class)]
class ElementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Element::class);
    }

    public function save(Element $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Element $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 根据 Block 分页查询有效的 Elements
     */
    public function findByBlockPaginated(string $blockCode, ?string $keyword = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e')
            ->innerJoin('e.block', 'b')
            ->where('b.code = :blockCode')
            ->andWhere('b.valid = :blockValid')
            ->andWhere('e.valid = :elementValid')
            ->setParameter('blockCode', $blockCode)
            ->setParameter('blockValid', true)
            ->setParameter('elementValid', true)
        ;

        // 添加关键词搜索条件
        if (null !== $keyword && '' !== trim($keyword)) {
            $qb->andWhere('(e.title LIKE :keyword OR e.subtitle LIKE :keyword)')
                ->setParameter('keyword', '%' . trim($keyword) . '%')
            ;
        }

        return $qb->orderBy('e.sortNumber', 'DESC')
            ->addOrderBy('e.id', 'DESC')
        ;
    }
}
