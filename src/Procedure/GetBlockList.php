<?php

namespace DiyPageBundle\Procedure;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Param\GetBlockListParam;
use DiyPageBundle\Repository\BlockRepository;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag(name: '广告位模块')]
#[MethodDoc(summary: '获取广告位列表')]
#[MethodExpose(method: 'GetBlockList')]
final class GetBlockList extends BaseProcedure
{
    public function __construct(
        private readonly BlockRepository $blockRepository,
    ) {
    }

    /**
     * @phpstan-param GetBlockListParam $param
     */
    public function execute(GetBlockListParam|RpcParamInterface $param): ArrayResult
    {
        $queryBuilder = $this->blockRepository->createQueryBuilder('b');

        if (null !== $param->validOnly && $param->validOnly) {
            $queryBuilder->andWhere('b.valid = :valid')
                ->setParameter('valid', true)
            ;
        }

        if (null !== $param->typeId) {
            $queryBuilder->andWhere('b.typeId = :typeId')
                ->setParameter('typeId', $param->typeId)
            ;
        }

        $queryBuilder->orderBy('b.sortNumber', 'ASC')
            ->addOrderBy('b.id', 'DESC')
        ;

        $offset = ($param->page - 1) * $param->limit;
        $queryBuilder->setFirstResult($offset)
            ->setMaxResults($param->limit)
        ;

        // Create a separate query builder for counting
        $countQueryBuilder = $this->blockRepository->createQueryBuilder('b');

        if (null !== $param->validOnly && $param->validOnly) {
            $countQueryBuilder->andWhere('b.valid = :valid')
                ->setParameter('valid', true)
            ;
        }

        if (null !== $param->typeId) {
            $countQueryBuilder->andWhere('b.typeId = :typeId')
                ->setParameter('typeId', $param->typeId)
            ;
        }

        $countQueryBuilder->select('COUNT(b.id)');
        $total = (int) $countQueryBuilder->getQuery()->getSingleScalarResult();

        $blocks = $queryBuilder->getQuery()->getResult();

        if (!is_array($blocks)) {
            $blocks = [];
        }

        $items = [];
        foreach ($blocks as $block) {
            if (!$block instanceof Block) {
                continue;
            }
            $items[] = $block->retrieveAdminArray();
        }

        return new ArrayResult([
            'items' => $items,
            'total' => $total,
            'page' => $param->page,
            'limit' => $param->limit,
            'pages' => (int) ceil($total / $param->limit),
        ]);
    }
}
