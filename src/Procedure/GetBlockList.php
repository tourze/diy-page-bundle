<?php

namespace DiyPageBundle\Procedure;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Repository\BlockRepository;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag(name: '广告位模块')]
#[MethodDoc(summary: '获取广告位列表')]
#[MethodExpose(method: 'GetBlockList')]
class GetBlockList extends BaseProcedure
{
    #[MethodParam(description: '是否只获取有效的广告位')]
    public ?bool $validOnly = null;

    #[MethodParam(description: '类型ID筛选')]
    public ?string $typeId = null;

    #[MethodParam(description: '页码')]
    public int $page = 1;

    #[MethodParam(description: '每页数量')]
    public int $limit = 20;

    public function __construct(
        private readonly BlockRepository $blockRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $queryBuilder = $this->blockRepository->createQueryBuilder('b');

        if (null !== $this->validOnly && $this->validOnly) {
            $queryBuilder->andWhere('b.valid = :valid')
                ->setParameter('valid', true)
            ;
        }

        if (null !== $this->typeId) {
            $queryBuilder->andWhere('b.typeId = :typeId')
                ->setParameter('typeId', $this->typeId)
            ;
        }

        $queryBuilder->orderBy('b.sortNumber', 'ASC')
            ->addOrderBy('b.id', 'DESC')
        ;

        $offset = ($this->page - 1) * $this->limit;
        $queryBuilder->setFirstResult($offset)
            ->setMaxResults($this->limit)
        ;

        // Create a separate query builder for counting
        $countQueryBuilder = $this->blockRepository->createQueryBuilder('b');

        if (null !== $this->validOnly && $this->validOnly) {
            $countQueryBuilder->andWhere('b.valid = :valid')
                ->setParameter('valid', true)
            ;
        }

        if (null !== $this->typeId) {
            $countQueryBuilder->andWhere('b.typeId = :typeId')
                ->setParameter('typeId', $this->typeId)
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

        return [
            'items' => $items,
            'total' => $total,
            'page' => $this->page,
            'limit' => $this->limit,
            'pages' => (int) ceil($total / $this->limit),
        ];
    }
}
