<?php

namespace DiyPageBundle\Tests\Repository;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Entity\VisitLog;
use DiyPageBundle\Repository\VisitLogRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(VisitLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class VisitLogRepositoryTest extends AbstractRepositoryTestCase
{
    private VisitLogRepository $repository;

    public function testSaveWithFlush(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('127.0.0.1');

        $this->repository->save($visitLog);

        $this->assertNotNull($visitLog->getId());

        // 清理数据
        $this->repository->remove($visitLog);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    private function createTestBlock(): Block
    {
        $block = new Block();
        $block->setTitle('测试广告位');
        $block->setCode('test-visitlog-block-' . uniqid());
        $block->setValid(true);
        self::getEntityManager()->persist($block);
        self::getEntityManager()->flush();

        return $block;
    }

    private function createTestElement(Block $block): Element
    {
        $element = new Element();
        $element->setTitle('测试元素');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/thumb.jpg');
        self::getEntityManager()->persist($element);
        self::getEntityManager()->flush();

        return $element;
    }

    public function testSaveWithoutFlush(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('127.0.0.1');

        $this->repository->save($visitLog, false);

        self::getEntityManager()->flush();
        $this->assertNotNull($visitLog->getId());

        // 清理数据
        $this->repository->remove($visitLog);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testFindByUser(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('127.0.0.1');

        $this->repository->save($visitLog);

        $foundVisitLogs = $this->repository->findBy(['user' => $user]);

        $this->assertCount(1, $foundVisitLogs);
        $this->assertSame($user, $foundVisitLogs[0]->getUser());

        // 清理数据
        $this->repository->remove($visitLog);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testFindByBlock(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('192.168.1.1');

        $this->repository->save($visitLog);

        $foundVisitLogs = $this->repository->findBy(['block' => $block]);

        $this->assertCount(1, $foundVisitLogs);
        $this->assertSame($block, $foundVisitLogs[0]->getBlock());
        $this->assertSame('192.168.1.1', $foundVisitLogs[0]->getCreatedFromIp());

        // 清理数据
        $this->repository->remove($visitLog);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testFindByElement(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('10.0.0.1');

        $this->repository->save($visitLog);

        $foundVisitLogs = $this->repository->findBy(['element' => $element]);

        $this->assertCount(1, $foundVisitLogs);
        $this->assertSame($element, $foundVisitLogs[0]->getElement());
        $this->assertSame('10.0.0.1', $foundVisitLogs[0]->getCreatedFromIp());

        // 清理数据
        $this->repository->remove($visitLog);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testFindByMultipleAssociations(): void
    {
        $block1 = $this->createTestBlock();
        $block2 = new Block();
        $block2->setTitle('测试广告位2');
        $block2->setCode('test-visitlog-block-2');
        $block2->setValid(true);
        self::getEntityManager()->persist($block2);
        self::getEntityManager()->flush();

        $element1 = $this->createTestElement($block1);
        $element2 = new Element();
        $element2->setTitle('测试元素2');
        $element2->setBlock($block2);
        $element2->setValid(true);
        $element2->setThumb1('/test/thumb2.jpg');
        self::getEntityManager()->persist($element2);
        self::getEntityManager()->flush();

        $user1 = $this->createNormalUser('visitlog-test1-' . uniqid() . '@example.com', 'password123');
        $user2 = $this->createNormalUser('visitlog-test2@example.com', 'password456');
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        // 创建多个访问日志记录
        $visitLog1 = new VisitLog();
        $visitLog1->setBlock($block1);
        $visitLog1->setElement($element1);
        $visitLog1->setUser($user1);
        $visitLog1->setCreatedFromIp('127.0.0.1');

        $visitLog2 = new VisitLog();
        $visitLog2->setBlock($block2);
        $visitLog2->setElement($element2);
        $visitLog2->setUser($user2);
        $visitLog2->setCreatedFromIp('127.0.0.2');

        $visitLog3 = new VisitLog();
        $visitLog3->setBlock($block1);
        $visitLog3->setElement($element1);
        $visitLog3->setUser($user2);
        $visitLog3->setCreatedFromIp('127.0.0.3');

        $this->repository->save($visitLog1);
        $this->repository->save($visitLog2);
        $this->repository->save($visitLog3);

        // 测试单一关联查询
        $userVisitLogs = $this->repository->findBy(['user' => $user1]);
        $this->assertCount(1, $userVisitLogs);

        $blockVisitLogs = $this->repository->findBy(['block' => $block1]);
        $this->assertCount(2, $blockVisitLogs);

        // 测试复合条件查询
        $specificVisitLogs = $this->repository->findBy([
            'block' => $block1,
            'user' => $user2,
        ]);
        $this->assertCount(1, $specificVisitLogs);
        $this->assertSame($block1, $specificVisitLogs[0]->getBlock());
        $this->assertSame($user2, $specificVisitLogs[0]->getUser());

        // 清理数据
        $this->repository->remove($visitLog1);
        $this->repository->remove($visitLog2);
        $this->repository->remove($visitLog3);
        self::getEntityManager()->remove($element1);
        self::getEntityManager()->remove($element2);
        self::getEntityManager()->remove($block1);
        self::getEntityManager()->remove($block2);
        self::getEntityManager()->remove($user1);
        self::getEntityManager()->remove($user2);
        self::getEntityManager()->flush();
    }

    public function testCount(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $initialCount = $this->repository->count([]);

        $visitLog1 = new VisitLog();
        $visitLog1->setBlock($block);
        $visitLog1->setElement($element);
        $visitLog1->setUser($user);
        $visitLog1->setCreatedFromIp('192.168.1.100');

        $visitLog2 = new VisitLog();
        $visitLog2->setBlock($block);
        $visitLog2->setElement($element);
        $visitLog2->setUser($user);
        $visitLog2->setCreatedFromIp('192.168.1.101');

        $this->repository->save($visitLog1);
        $this->repository->save($visitLog2);

        $totalCount = $this->repository->count([]);
        $this->assertSame($initialCount + 2, $totalCount);

        $userCount = $this->repository->count(['user' => $user]);
        $this->assertSame(2, $userCount);

        $blockCount = $this->repository->count(['block' => $block]);
        $this->assertSame(2, $blockCount);

        // 清理数据
        $this->repository->remove($visitLog1);
        $this->repository->remove($visitLog2);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testFindOneBy(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('203.0.113.1');

        $this->repository->save($visitLog);

        $foundVisitLog = $this->repository->findOneBy(['user' => $user]);
        $this->assertNotNull($foundVisitLog);
        $this->assertSame($user, $foundVisitLog->getUser());
        $this->assertSame($block, $foundVisitLog->getBlock());
        $this->assertSame($element, $foundVisitLog->getElement());
        $this->assertSame('203.0.113.1', $foundVisitLog->getCreatedFromIp());

        $nonExistentVisitLog = $this->repository->findOneBy(['id' => 999999]);
        $this->assertNull($nonExistentVisitLog);

        // 清理数据
        $this->repository->remove($visitLog);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testFind(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('198.51.100.1');

        $this->repository->save($visitLog);
        $id = $visitLog->getId();

        $foundVisitLog = $this->repository->find($id);
        $this->assertNotNull($foundVisitLog);
        $this->assertSame($id, $foundVisitLog->getId());
        $this->assertSame($user, $foundVisitLog->getUser());
        $this->assertSame('198.51.100.1', $foundVisitLog->getCreatedFromIp());

        $nonExistentVisitLog = $this->repository->find(999999);
        $this->assertNull($nonExistentVisitLog);

        // 清理数据
        $this->repository->remove($visitLog);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testFindAll(): void
    {
        $initialCount = count($this->repository->findAll());

        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog1 = new VisitLog();
        $visitLog1->setBlock($block);
        $visitLog1->setElement($element);
        $visitLog1->setUser($user);
        $visitLog1->setCreatedFromIp('172.16.0.1');

        $visitLog2 = new VisitLog();
        $visitLog2->setBlock($block);
        $visitLog2->setElement($element);
        $visitLog2->setUser($user);
        $visitLog2->setCreatedFromIp('172.16.0.2');

        $this->repository->save($visitLog1);
        $this->repository->save($visitLog2);

        $allVisitLogs = $this->repository->findAll();
        $this->assertCount($initialCount + 2, $allVisitLogs);

        // 清理数据
        $this->repository->remove($visitLog1);
        $this->repository->remove($visitLog2);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testFindWithSorting(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user1 = $this->createNormalUser('visitlog-test1-' . uniqid() . '@example.com', 'password123');
        $user2 = $this->createNormalUser('visitlog-sort@example.com', 'password789');
        self::getEntityManager()->persist($user2);
        self::getEntityManager()->flush();

        $visitLog1 = new VisitLog();
        $visitLog1->setBlock($block);
        $visitLog1->setElement($element);
        $visitLog1->setUser($user1);
        $visitLog1->setCreatedFromIp('10.0.1.1');

        $visitLog2 = new VisitLog();
        $visitLog2->setBlock($block);
        $visitLog2->setElement($element);
        $visitLog2->setUser($user2);
        $visitLog2->setCreatedFromIp('10.0.1.2');

        $this->repository->save($visitLog1);
        $this->repository->save($visitLog2);

        $sortedVisitLogs = $this->repository->findBy(['block' => $block], ['id' => 'ASC']);
        $this->assertCount(2, $sortedVisitLogs);
        $this->assertLessThan($sortedVisitLogs[1]->getId(), $sortedVisitLogs[0]->getId());

        $reverseSortedVisitLogs = $this->repository->findBy(['block' => $block], ['id' => 'DESC']);
        $this->assertCount(2, $reverseSortedVisitLogs);
        $this->assertGreaterThan($reverseSortedVisitLogs[1]->getId(), $reverseSortedVisitLogs[0]->getId());

        // 清理数据
        $this->repository->remove($visitLog1);
        $this->repository->remove($visitLog2);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user1);
        self::getEntityManager()->remove($user2);
        self::getEntityManager()->flush();
    }

    public function testFindWithLimitAndOffset(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLogs = [];
        for ($i = 1; $i <= 5; ++$i) {
            $visitLog = new VisitLog();
            $visitLog->setBlock($block);
            $visitLog->setElement($element);
            $visitLog->setUser($user);
            $visitLog->setCreatedFromIp("10.0.2.{$i}");
            $this->repository->save($visitLog, false);
            $visitLogs[] = $visitLog;
        }
        self::getEntityManager()->flush();

        // 测试限制数量
        $limitedVisitLogs = $this->repository->findBy(['user' => $user], ['id' => 'ASC'], 3);
        $this->assertCount(3, $limitedVisitLogs);

        // 测试偏移量
        $offsetVisitLogs = $this->repository->findBy(['user' => $user], ['id' => 'ASC'], 2, 2);
        $this->assertCount(2, $offsetVisitLogs);

        // 测试偏移量结果的正确性
        $allSortedVisitLogs = $this->repository->findBy(['user' => $user], ['id' => 'ASC']);
        $this->assertSame($allSortedVisitLogs[2]->getId(), $offsetVisitLogs[0]->getId());
        $this->assertSame($allSortedVisitLogs[3]->getId(), $offsetVisitLogs[1]->getId());

        // 清理数据
        foreach ($visitLogs as $visitLog) {
            $this->repository->remove($visitLog, false);
        }
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testEntityStringRepresentation(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('192.0.2.1');

        $this->repository->save($visitLog);

        $stringRepresentation = (string) $visitLog;
        $this->assertStringContainsString('访问记录', $stringRepresentation);
        $this->assertStringContainsString((string) $visitLog->getId(), $stringRepresentation);
        $blockTitle = $block->getTitle();
        $this->assertNotNull($blockTitle);
        $this->assertStringContainsString($blockTitle, $stringRepresentation);

        // 清理数据
        $this->repository->remove($visitLog);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testVisitLogWithTimestampTrait(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('203.0.113.100');

        $beforeSave = new \DateTime();
        $this->repository->save($visitLog);
        $afterSave = new \DateTime();

        $this->assertNotNull($visitLog->getCreateTime());
        $this->assertGreaterThanOrEqual($beforeSave->getTimestamp(), $visitLog->getCreateTime()->getTimestamp());
        $this->assertLessThanOrEqual($afterSave->getTimestamp(), $visitLog->getCreateTime()->getTimestamp());

        // 清理数据
        $this->repository->remove($visitLog);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testFindByIdShouldReturnEntityWithCorrectId(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('10.10.1.1');

        $this->repository->save($visitLog);
        $id = $visitLog->getId();

        $result = $this->repository->find($id);
        $this->assertNotNull($result);
        $this->assertInstanceOf(VisitLog::class, $result);
        $this->assertSame($id, $result->getId());

        // 清理数据
        $this->repository->remove($visitLog);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testCountByAssociationBlockShouldReturnCorrectNumber(): void
    {
        $block1 = $this->createTestBlock();
        $block2 = new Block();
        $block2->setTitle('测试广告位 Count Association');
        $block2->setCode('test-count-assoc-block-' . hrtime(true));
        $block2->setValid(true);
        self::getEntityManager()->persist($block2);
        self::getEntityManager()->flush();

        $element1 = $this->createTestElement($block1);
        $element2 = new Element();
        $element2->setTitle('测试元素 Count Association');
        $element2->setBlock($block2);
        $element2->setValid(true);
        $element2->setThumb1('/test/count-assoc.jpg');
        self::getEntityManager()->persist($element2);
        self::getEntityManager()->flush();

        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLogs = [];
        // 创建 4 个属于 block1 的访问日志
        for ($i = 1; $i <= 4; ++$i) {
            $visitLog = new VisitLog();
            $visitLog->setBlock($block1);
            $visitLog->setElement($element1);
            $visitLog->setUser($user);
            $visitLog->setCreatedFromIp("10.11.1.{$i}");
            $this->repository->save($visitLog, false);
            $visitLogs[] = $visitLog;
        }

        // 创建 2 个属于 block2 的访问日志
        for ($i = 1; $i <= 2; ++$i) {
            $visitLog = new VisitLog();
            $visitLog->setBlock($block2);
            $visitLog->setElement($element2);
            $visitLog->setUser($user);
            $visitLog->setCreatedFromIp("10.11.2.{$i}");
            $this->repository->save($visitLog, false);
            $visitLogs[] = $visitLog;
        }
        self::getEntityManager()->flush();

        $count = $this->repository->count(['block' => $block1]);
        $this->assertSame(4, $count);

        // 清理数据
        foreach ($visitLogs as $visitLog) {
            $this->repository->remove($visitLog, false);
        }
        self::getEntityManager()->remove($element1);
        self::getEntityManager()->remove($element2);
        self::getEntityManager()->remove($block1);
        self::getEntityManager()->remove($block2);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    public function testFindOneByAssociationBlockShouldReturnMatchingEntity(): void
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('10.12.1.1');

        $this->repository->save($visitLog);

        $result = $this->repository->findOneBy(['block' => $block]);
        $this->assertNotNull($result);
        $this->assertInstanceOf(VisitLog::class, $result);
        $this->assertSame($block, $result->getBlock());

        // 清理数据
        $this->repository->remove($visitLog);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->remove($user);
        self::getEntityManager()->flush();
    }

    protected function createNewEntity(): object
    {
        $block = $this->createTestBlock();
        $element = $this->createTestElement($block);
        $user = $this->createNormalUser('visitlog-test-' . uniqid() . '@example.com', 'password123');

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('127.0.0.1');

        return $visitLog;
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(VisitLogRepository::class);
    }

    /** @return ServiceEntityRepository<VisitLog> */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
