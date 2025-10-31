<?php

declare(strict_types=1);

namespace DiyPageBundle\Repository;

use DiyPageBundle\Entity\ElementAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<ElementAttribute>
 */
#[AsRepository(entityClass: ElementAttribute::class)]
class ElementAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ElementAttribute::class);
    }

    public function save(ElementAttribute $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ElementAttribute $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 根据元素ID获取所有属性
     *
     * @param string|null $elementId
     * @return array<ElementAttribute>
     */
    public function findByElementId(?string $elementId): array
    {
        if (null === $elementId) {
            return [];
        }

        $result = $this->createQueryBuilder('ea')
            ->andWhere('ea.element = :elementId')
            ->setParameter('elementId', $elementId)
            ->orderBy('ea.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        assert(is_array($result));

        /** @var array<ElementAttribute> $result */
        return $result;
    }

    /**
     * 根据元素ID和属性名获取属性
     */
    public function findByElementIdAndName(?string $elementId, string $name): ?ElementAttribute
    {
        if (null === $elementId) {
            return null;
        }

        $result = $this->createQueryBuilder('ea')
            ->andWhere('ea.element = :elementId')
            ->andWhere('ea.name = :name')
            ->setParameter('elementId', $elementId)
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        assert($result instanceof ElementAttribute || null === $result);

        return $result;
    }

    /**
     * 删除元素的所有属性
     */
    public function deleteByElementId(?string $elementId): int
    {
        if (null === $elementId) {
            return 0;
        }

        $result = $this->createQueryBuilder('ea')
            ->delete()
            ->andWhere('ea.element = :elementId')
            ->setParameter('elementId', $elementId)
            ->getQuery()
            ->execute()
        ;

        assert(is_int($result));

        return $result;
    }
}
