<?php

namespace DiyPageBundle\Tests\Repository;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Repository\BlockRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(BlockRepository::class)]
#[RunTestsInSeparateProcesses]
final class BlockRepositoryTest extends AbstractRepositoryTestCase
{
    private BlockRepository $repository;

    public function testSaveWithFlush(): void
    {
        $block = new Block();
        $block->setTitle('测试广告位');
        $block->setCode('test-block');
        $block->setValid(true);

        $this->repository->save($block);

        $this->assertNotNull($block->getId());

        // 清理数据
        $this->repository->remove($block);
    }

    public function testSaveWithoutFlush(): void
    {
        $block = new Block();
        $block->setTitle('测试广告位2');
        $block->setCode('test-block-2');
        $block->setValid(true);

        $this->repository->save($block, false);

        // 在不flush的情况下，ID可能为null
        self::getEntityManager()->flush();
        $this->assertNotNull($block->getId());

        // 清理数据
        $this->repository->remove($block);
    }

    public function testFindByCode(): void
    {
        $block = new Block();
        $block->setTitle('按代码查找测试');
        $block->setCode('find-by-code-test');
        $block->setValid(true);

        $this->repository->save($block);

        $foundBlock = $this->repository->findOneBy(['code' => 'find-by-code-test']);

        $this->assertInstanceOf(Block::class, $foundBlock);
        $this->assertSame('按代码查找测试', $foundBlock->getTitle());

        // 清理数据
        $this->repository->remove($block);
    }

    public function testFindOneByWithOrderByShouldReturnCorrectEntity(): void
    {
        // 先清理所有现有数据
        foreach ($this->repository->findAll() as $entity) {
            $this->repository->remove($entity);
        }

        $block1 = new Block();
        $block1->setTitle('排序查找1');
        $block1->setCode('order-findone-1');
        $block1->setValid(true);
        $block1->setSortNumber(20);

        $block2 = new Block();
        $block2->setTitle('排序查找2');
        $block2->setCode('order-findone-2');
        $block2->setValid(true);
        $block2->setSortNumber(10);

        $this->repository->save($block1);
        $this->repository->save($block2);

        // 按 sortNumber 升序查找第一个
        $ascResult = $this->repository->findOneBy(['valid' => true], ['sortNumber' => 'ASC']);
        $this->assertNotNull($ascResult);
        $this->assertSame('排序查找2', $ascResult->getTitle());
        $this->assertSame(10, $ascResult->getSortNumber());

        // 按 sortNumber 降序查找第一个
        $descResult = $this->repository->findOneBy(['valid' => true], ['sortNumber' => 'DESC']);
        $this->assertNotNull($descResult);
        $this->assertSame('排序查找1', $descResult->getTitle());
        $this->assertSame(20, $descResult->getSortNumber());

        // 清理数据
        $this->repository->remove($block1);
        $this->repository->remove($block2);
    }

    public function testFindByWithNullFieldsShouldWork(): void
    {
        // 先清理所有现有数据
        foreach ($this->repository->findAll() as $entity) {
            $this->repository->remove($entity);
        }

        $block1 = new Block();
        $block1->setTitle('空字段测试1');
        $block1->setCode('null-field-1');
        $block1->setValid(true);
        $block1->setSortNumber(null); // 设置为 null

        $block2 = new Block();
        $block2->setTitle('空字段测试2');
        $block2->setCode('null-field-2');
        $block2->setValid(true);
        $block2->setSortNumber(10);

        $this->repository->save($block1);
        $this->repository->save($block2);

        // 查找 sortNumber 为 null 的记录
        $nullSortBlocks = $this->repository->findBy(['sortNumber' => null]);
        $this->assertCount(1, $nullSortBlocks);
        $this->assertSame('空字段测试1', $nullSortBlocks[0]->getTitle());

        // 清理数据
        $this->repository->remove($block1);
        $this->repository->remove($block2);
    }

    public function testCountWithNullFieldsShouldWork(): void
    {
        // 先清理所有现有数据
        foreach ($this->repository->findAll() as $entity) {
            $this->repository->remove($entity);
        }

        $block1 = new Block();
        $block1->setTitle('空字段计数1');
        $block1->setCode('null-count-1');
        $block1->setValid(true);
        $block1->setTypeId(null);

        $block2 = new Block();
        $block2->setTitle('空字段计数2');
        $block2->setCode('null-count-2');
        $block2->setValid(true);
        $block2->setTypeId('test-type');

        $this->repository->save($block1);
        $this->repository->save($block2);

        // 计数 typeId 为 null 的记录
        $nullTypeCount = $this->repository->count(['typeId' => null]);
        $this->assertSame(1, $nullTypeCount);

        // 计数 typeId 不为 null 的记录
        $notNullTypeCount = $this->repository->count(['typeId' => 'test-type']);
        $this->assertSame(1, $notNullTypeCount);

        // 清理数据
        $this->repository->remove($block1);
        $this->repository->remove($block2);
    }

    private static int $entityCounter = 0;

    protected function createNewEntity(): object
    {
        $block = new Block();
        $block->setTitle('Create New Entity Block');
        $uniqueId = hrtime(true) . '-' . (++self::$entityCounter) . '-' . random_int(10000, 99999);
        $block->setCode('create-new-entity-' . $uniqueId);
        $block->setValid(true);

        return $block;
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(BlockRepository::class);
    }

    /** @return ServiceEntityRepository<Block> */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
