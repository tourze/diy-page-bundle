<?php

declare(strict_types=1);

namespace DiyPageBundle\Tests\Procedure;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Procedure\GetBlockList;
use DiyPageBundle\Repository\BlockRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetBlockList::class)]
#[RunTestsInSeparateProcesses]
final class GetBlockListTest extends AbstractProcedureTestCase
{
    private BlockRepository&MockObject $blockRepository;

    private GetBlockList $procedure;

    protected function onSetUp(): void
    {
        $container = self::getContainer();

        $this->blockRepository = $this->createMock(BlockRepository::class);
        $container->set(BlockRepository::class, $this->blockRepository);

        $procedure = $container->get(GetBlockList::class);
        $this->assertInstanceOf(GetBlockList::class, $procedure);
        $this->procedure = $procedure;
    }

    public function testExecuteWithDefaults(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $countQueryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        $countQuery = $this->createMock(Query::class);

        $block1 = $this->createBlock(1, 'Block 1', 'block1', true);
        $block2 = $this->createBlock(2, 'Block 2', 'block2', false);

        $this->blockRepository->expects($this->exactly(2))
            ->method('createQueryBuilder')
            ->with('b')
            ->willReturnOnConsecutiveCalls($queryBuilder, $countQueryBuilder)
        ;

        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with('b.sortNumber', 'ASC')
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('addOrderBy')
            ->with('b.id', 'DESC')
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('setFirstResult')
            ->with(0)
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with(20)
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query)
        ;

        $query->expects($this->once())
            ->method('getResult')
            ->willReturn([$block1, $block2])
        ;

        $countQueryBuilder->expects($this->once())
            ->method('select')
            ->with('COUNT(b.id)')
            ->willReturnSelf()
        ;

        $countQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($countQuery)
        ;

        $countQuery->expects($this->once())
            ->method('getSingleScalarResult')
            ->willReturn('2')
        ;

        $result = $this->procedure->execute();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayHasKey('limit', $result);
        $this->assertArrayHasKey('pages', $result);

        $this->assertCount(2, $result['items']);
        $this->assertEquals(2, $result['total']);
        $this->assertEquals(1, $result['page']);
        $this->assertEquals(20, $result['limit']);
        $this->assertEquals(1, $result['pages']);
    }

    public function testExecuteWithValidOnlyFilter(): void
    {
        $this->procedure->validOnly = true;

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $countQueryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        $countQuery = $this->createMock(Query::class);

        $block1 = $this->createBlock(1, 'Valid Block', 'valid-block', true);

        $this->blockRepository->expects($this->exactly(2))
            ->method('createQueryBuilder')
            ->with('b')
            ->willReturnOnConsecutiveCalls($queryBuilder, $countQueryBuilder)
        ;

        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('b.valid = :valid')
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('valid', true)
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with('b.sortNumber', 'ASC')
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('addOrderBy')
            ->with('b.id', 'DESC')
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('setFirstResult')
            ->with(0)
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with(20)
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query)
        ;

        $query->expects($this->once())
            ->method('getResult')
            ->willReturn([$block1])
        ;

        $countQueryBuilder->expects($this->once())
            ->method('andWhere')
            ->with('b.valid = :valid')
            ->willReturnSelf()
        ;

        $countQueryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('valid', true)
            ->willReturnSelf()
        ;

        $countQueryBuilder->expects($this->once())
            ->method('select')
            ->with('COUNT(b.id)')
            ->willReturnSelf()
        ;

        $countQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($countQuery)
        ;

        $countQuery->expects($this->once())
            ->method('getSingleScalarResult')
            ->willReturn('1')
        ;

        $result = $this->procedure->execute();

        $this->assertEquals(1, $result['total']);
        $this->assertCount(1, $result['items']);
    }

    public function testExecuteWithPagination(): void
    {
        $this->procedure->page = 2;
        $this->procedure->limit = 10;

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $countQueryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        $countQuery = $this->createMock(Query::class);

        $this->blockRepository->expects($this->exactly(2))
            ->method('createQueryBuilder')
            ->with('b')
            ->willReturnOnConsecutiveCalls($queryBuilder, $countQueryBuilder)
        ;

        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with('b.sortNumber', 'ASC')
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('addOrderBy')
            ->with('b.id', 'DESC')
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('setFirstResult')
            ->with(10)
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->with(10)
            ->willReturnSelf()
        ;

        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query)
        ;

        $query->expects($this->once())
            ->method('getResult')
            ->willReturn([])
        ;

        $countQueryBuilder->expects($this->once())
            ->method('select')
            ->with('COUNT(b.id)')
            ->willReturnSelf()
        ;

        $countQueryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($countQuery)
        ;

        $countQuery->expects($this->once())
            ->method('getSingleScalarResult')
            ->willReturn('25')
        ;

        $result = $this->procedure->execute();

        $this->assertEquals(25, $result['total']);
        $this->assertEquals(2, $result['page']);
        $this->assertEquals(10, $result['limit']);
        $this->assertEquals(3, $result['pages']);
    }

    public function testProcedureAttributes(): void
    {
        $reflection = new \ReflectionClass(GetBlockList::class);

        $attributes = $reflection->getAttributes();
        $this->assertNotEmpty($attributes);

        $methodExpose = null;
        $methodDoc = null;
        $methodTag = null;

        foreach ($attributes as $attribute) {
            switch ($attribute->getName()) {
                case 'Tourze\JsonRPC\Core\Attribute\MethodExpose':
                    $methodExpose = $attribute;
                    break;
                case 'Tourze\JsonRPC\Core\Attribute\MethodDoc':
                    $methodDoc = $attribute;
                    break;
                case 'Tourze\JsonRPC\Core\Attribute\MethodTag':
                    $methodTag = $attribute;
                    break;
            }
        }

        $this->assertNotNull($methodExpose);
        $this->assertNotNull($methodDoc);
        $this->assertNotNull($methodTag);

        $exposeArgs = $methodExpose->getArguments();
        $this->assertEquals('GetBlockList', $exposeArgs['method']);

        $docArgs = $methodDoc->getArguments();
        $this->assertEquals('获取广告位列表', $docArgs['summary']);

        $tagArgs = $methodTag->getArguments();
        $this->assertEquals('广告位模块', $tagArgs['name']);
    }

    private function createBlock(int $id, string $title, string $code, bool $valid): Block
    {
        $block = $this->createMock(Block::class);
        $block->method('retrieveAdminArray')->willReturn([
            'id' => $id,
            'title' => $title,
            'code' => $code,
            'valid' => $valid,
        ]);

        return $block;
    }
}
