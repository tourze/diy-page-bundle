<?php

namespace DiyPageBundle\Tests\Repository;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Repository\ElementRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ElementRepository::class)]
#[RunTestsInSeparateProcesses]
final class ElementRepositoryTest extends AbstractRepositoryTestCase
{
    private ElementRepository $repository;

    public function testSaveWithFlush(): void
    {
        $block = $this->createTestBlock();

        $element = new Element();
        $element->setTitle('测试元素');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/thumb1.jpg');

        $this->repository->save($element);

        $this->assertNotNull($element->getId());

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    private function createTestBlock(): Block
    {
        $block = new Block();
        $block->setTitle('测试广告位');
        $block->setCode('test-element-block-' . uniqid());
        $block->setValid(true);
        self::getEntityManager()->persist($block);
        self::getEntityManager()->flush();

        return $block;
    }

    public function testSaveWithoutFlush(): void
    {
        $block = $this->createTestBlock();

        $element = new Element();
        $element->setTitle('测试元素2');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/thumb2.jpg');

        $this->repository->save($element, false);

        self::getEntityManager()->flush();
        $this->assertNotNull($element->getId());

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testFindByBlock(): void
    {
        $block = $this->createTestBlock();

        $element = new Element();
        $element->setTitle('按块查找测试');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/thumb5.jpg');

        $this->repository->save($element);

        $foundElements = $this->repository->findBy(['block' => $block]);

        $this->assertCount(1, $foundElements);
        $this->assertSame('按块查找测试', $foundElements[0]->getTitle());

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testFindByTitle(): void
    {
        $block = $this->createTestBlock();

        $element = new Element();
        $element->setTitle('标题查找测试');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/thumb6.jpg');

        $this->repository->save($element);

        $foundElements = $this->repository->findBy(['title' => '标题查找测试']);

        $this->assertCount(1, $foundElements);
        $this->assertSame('标题查找测试', $foundElements[0]->getTitle());

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testFindByValid(): void
    {
        $block = $this->createTestBlock();

        $validElement = new Element();
        $validElement->setTitle('有效元素');
        $validElement->setBlock($block);
        $validElement->setValid(true);
        $validElement->setThumb1('/test/valid.jpg');

        $invalidElement = new Element();
        $invalidElement->setTitle('无效元素');
        $invalidElement->setBlock($block);
        $invalidElement->setValid(false);
        $invalidElement->setThumb1('/test/invalid.jpg');

        $this->repository->save($validElement);
        $this->repository->save($invalidElement);

        $validElements = $this->repository->findBy(['valid' => true]);
        $invalidElements = $this->repository->findBy(['valid' => false]);

        $this->assertGreaterThanOrEqual(1, count($validElements));
        $this->assertGreaterThanOrEqual(1, count($invalidElements));

        // 清理数据
        $this->repository->remove($validElement);
        $this->repository->remove($invalidElement);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testFindByNullableFields(): void
    {
        $block = $this->createTestBlock();

        // 测试包含可空字段的元素
        $elementWithNulls = new Element();
        $elementWithNulls->setTitle('包含空字段的元素');
        $elementWithNulls->setBlock($block);
        $elementWithNulls->setValid(true);
        $elementWithNulls->setThumb1('/test/with-nulls.jpg');
        // 故意不设置可空字段，保持为 null

        // 测试包含非空字段的元素
        $elementWithValues = new Element();
        $elementWithValues->setTitle('包含值的元素');
        $elementWithValues->setBlock($block);
        $elementWithValues->setValid(true);
        $elementWithValues->setThumb1('/test/with-values.jpg');
        $elementWithValues->setSubtitle('副标题');
        $elementWithValues->setDescription('描述内容');
        $elementWithValues->setThumb2('/test/thumb2.jpg');
        $elementWithValues->setPath('/test/path');
        $elementWithValues->setAppId('test-app-id');
        $elementWithValues->setSortNumber(10);
        $elementWithValues->setTracking('test-tracking');

        $this->repository->save($elementWithNulls);
        $this->repository->save($elementWithValues);

        // 测试查找空字段
        $elementsWithNullSubtitle = $this->repository->findBy(['subtitle' => null]);
        $elementsWithNullDescription = $this->repository->findBy(['description' => null]);
        $elementsWithNullThumb2 = $this->repository->findBy(['thumb2' => null]);
        $elementsWithNullPath = $this->repository->findBy(['path' => null]);
        $elementsWithNullAppId = $this->repository->findBy(['appId' => null]);
        $elementsWithNullSortNumber = $this->repository->findBy(['sortNumber' => null]);
        $elementsWithNullTracking = $this->repository->findBy(['tracking' => null]);

        $this->assertGreaterThanOrEqual(1, count($elementsWithNullSubtitle));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullDescription));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullThumb2));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullPath));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullAppId));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullSortNumber));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullTracking));

        // 测试查找非空字段 - 使用更具体的条件来避免与其他数据冲突
        $elementsWithSubtitle = $this->repository->findBy(['subtitle' => '副标题', 'title' => '包含值的元素']);
        $elementsWithDescription = $this->repository->findBy(['description' => '描述内容', 'title' => '包含值的元素']);
        $elementsWithThumb2 = $this->repository->findBy(['thumb2' => '/test/thumb2.jpg', 'title' => '包含值的元素']);
        $elementsWithPath = $this->repository->findBy(['path' => '/test/path', 'title' => '包含值的元素']);
        $elementsWithAppId = $this->repository->findBy(['appId' => 'test-app-id', 'title' => '包含值的元素']);
        $elementsWithSortNumber = $this->repository->findBy(['sortNumber' => 10, 'title' => '包含值的元素']);
        $elementsWithTracking = $this->repository->findBy(['tracking' => 'test-tracking', 'title' => '包含值的元素']);

        $this->assertCount(1, $elementsWithSubtitle);
        $this->assertCount(1, $elementsWithDescription);
        $this->assertCount(1, $elementsWithThumb2);
        $this->assertCount(1, $elementsWithPath);
        $this->assertCount(1, $elementsWithAppId);
        $this->assertCount(1, $elementsWithSortNumber);
        $this->assertCount(1, $elementsWithTracking);

        // 清理数据
        $this->repository->remove($elementWithNulls);
        $this->repository->remove($elementWithValues);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testFindByBooleanField(): void
    {
        $block = $this->createTestBlock();

        $elementWithJumpPage = new Element();
        $elementWithJumpPage->setTitle('跳转页面元素');
        $elementWithJumpPage->setBlock($block);
        $elementWithJumpPage->setValid(true);
        $elementWithJumpPage->setThumb1('/test/jump.jpg');
        $elementWithJumpPage->setLoginJumpPage(true);

        $elementWithoutJumpPage = new Element();
        $elementWithoutJumpPage->setTitle('不跳转页面元素');
        $elementWithoutJumpPage->setBlock($block);
        $elementWithoutJumpPage->setValid(true);
        $elementWithoutJumpPage->setThumb1('/test/no-jump.jpg');
        $elementWithoutJumpPage->setLoginJumpPage(false);

        $this->repository->save($elementWithJumpPage);
        $this->repository->save($elementWithoutJumpPage);

        $jumpElements = $this->repository->findBy(['loginJumpPage' => true]);
        $noJumpElements = $this->repository->findBy(['loginJumpPage' => false]);

        $this->assertGreaterThanOrEqual(1, count($jumpElements));
        $this->assertGreaterThanOrEqual(1, count($noJumpElements));

        // 清理数据
        $this->repository->remove($elementWithJumpPage);
        $this->repository->remove($elementWithoutJumpPage);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testFindOneBy(): void
    {
        $block = $this->createTestBlock();

        $element = new Element();
        $element->setTitle('唯一查找测试');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/unique.jpg');
        $element->setSortNumber(99);

        $this->repository->save($element);

        $foundElement = $this->repository->findOneBy(['sortNumber' => 99]);

        $this->assertNotNull($foundElement);
        $this->assertSame('唯一查找测试', $foundElement->getTitle());
        $this->assertSame(99, $foundElement->getSortNumber());

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testFindAll(): void
    {
        $block = $this->createTestBlock();

        $element1 = new Element();
        $element1->setTitle('全部查找测试1');
        $element1->setBlock($block);
        $element1->setValid(true);
        $element1->setThumb1('/test/all1.jpg');

        $element2 = new Element();
        $element2->setTitle('全部查找测试2');
        $element2->setBlock($block);
        $element2->setValid(true);
        $element2->setThumb1('/test/all2.jpg');

        $this->repository->save($element1);
        $this->repository->save($element2);

        $allElements = $this->repository->findAll();

        $this->assertGreaterThanOrEqual(2, count($allElements));

        // 清理数据
        $this->repository->remove($element1);
        $this->repository->remove($element2);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testFindWithOrdering(): void
    {
        $block = $this->createTestBlock();

        $element1 = new Element();
        $element1->setTitle('排序测试A');
        $element1->setBlock($block);
        $element1->setValid(true);
        $element1->setThumb1('/test/order1.jpg');
        $element1->setSortNumber(2);

        $element2 = new Element();
        $element2->setTitle('排序测试B');
        $element2->setBlock($block);
        $element2->setValid(true);
        $element2->setThumb1('/test/order2.jpg');
        $element2->setSortNumber(1);

        $this->repository->save($element1);
        $this->repository->save($element2);

        $orderedElements = $this->repository->findBy(['block' => $block], ['sortNumber' => 'ASC']);

        $this->assertGreaterThanOrEqual(2, count($orderedElements));
        // 找到我们创建的元素并验证顺序
        $testElements = array_filter($orderedElements, fn ($el) => str_contains($el->getTitle() ?? '', '排序测试'));
        $testElements = array_values($testElements);
        $this->assertCount(2, $testElements);
        $this->assertSame(1, $testElements[0]->getSortNumber());
        $this->assertSame(2, $testElements[1]->getSortNumber());

        // 清理数据
        $this->repository->remove($element1);
        $this->repository->remove($element2);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testFindWithLimitAndOffset(): void
    {
        $block = $this->createTestBlock();

        $elements = [];
        for ($i = 1; $i <= 5; ++$i) {
            $element = new Element();
            $element->setTitle("分页测试{$i}");
            $element->setBlock($block);
            $element->setValid(true);
            $element->setThumb1("/test/page{$i}.jpg");
            $element->setSortNumber($i);
            $this->repository->save($element);
            $elements[] = $element;
        }

        $pagedElements = $this->repository->findBy(['block' => $block], ['sortNumber' => 'ASC'], 2, 1);

        $this->assertGreaterThanOrEqual(2, count($pagedElements));

        // 清理数据
        foreach ($elements as $element) {
            $this->repository->remove($element);
        }
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testCount(): void
    {
        $block = $this->createTestBlock();

        $initialCount = $this->repository->count(['block' => $block]);

        $element = new Element();
        $element->setTitle('计数测试');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/count.jpg');

        $this->repository->save($element);

        $newCount = $this->repository->count(['block' => $block]);
        $this->assertSame($initialCount + 1, $newCount);

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testFindByDateTimeFields(): void
    {
        $block = $this->createTestBlock();

        $now = new \DateTimeImmutable();
        $element = new Element();
        $element->setTitle('时间字段测试');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/datetime.jpg');
        $element->setBeginTime($now);
        $element->setEndTime($now->modify('+1 hour'));

        $this->repository->save($element);

        $elementsWithBeginTime = $this->repository->findBy(['beginTime' => $now]);
        $this->assertGreaterThanOrEqual(1, count($elementsWithBeginTime));

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    // 基础测试用例 - 必需的标准方法测试

    public function testFindOneByWithOrderByShouldReturnCorrectEntity(): void
    {
        $block = $this->createTestBlock();

        $element1 = new Element();
        $element1->setTitle('排序查找测试1');
        $element1->setBlock($block);
        $element1->setValid(true);
        $element1->setThumb1('/test/order-find-1.jpg');
        $element1->setSortNumber(20);

        $element2 = new Element();
        $element2->setTitle('排序查找测试2');
        $element2->setBlock($block);
        $element2->setValid(true);
        $element2->setThumb1('/test/order-find-2.jpg');
        $element2->setSortNumber(10);

        $this->repository->save($element1);
        $this->repository->save($element2);

        // 按 sortNumber 升序查找第一个
        $ascResult = $this->repository->findOneBy(['block' => $block], ['sortNumber' => 'ASC']);
        $this->assertNotNull($ascResult);
        $this->assertSame('排序查找测试2', $ascResult->getTitle());
        $this->assertSame(10, $ascResult->getSortNumber());

        // 按 sortNumber 降序查找第一个
        $descResult = $this->repository->findOneBy(['block' => $block], ['sortNumber' => 'DESC']);
        $this->assertNotNull($descResult);
        $this->assertSame('排序查找测试1', $descResult->getTitle());
        $this->assertSame(20, $descResult->getSortNumber());

        // 清理数据
        $this->repository->remove($element1);
        $this->repository->remove($element2);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    // 关联查询测试
    public function testFindOneByBlockAsAssociationShouldReturnMatchingEntity(): void
    {
        $block = $this->createTestBlock();

        $element = new Element();
        $element->setTitle('关联查询测试');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/association.jpg');

        $this->repository->save($element);

        $foundElement = $this->repository->findOneBy(['block' => $block]);

        $this->assertNotNull($foundElement);
        $this->assertInstanceOf(Element::class, $foundElement);
        $this->assertSame('关联查询测试', $foundElement->getTitle());
        $foundElementBlock = $foundElement->getBlock();
        $this->assertNotNull($foundElementBlock);
        $this->assertSame($block->getId(), $foundElementBlock->getId());

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testCountByBlockAsAssociationShouldReturnCorrectNumber(): void
    {
        $block = $this->createTestBlock();

        $element1 = new Element();
        $element1->setTitle('关联计数测试1');
        $element1->setBlock($block);
        $element1->setValid(true);
        $element1->setThumb1('/test/assoc-count-1.jpg');

        $element2 = new Element();
        $element2->setTitle('关联计数测试2');
        $element2->setBlock($block);
        $element2->setValid(true);
        $element2->setThumb1('/test/assoc-count-2.jpg');

        $this->repository->save($element1);
        $this->repository->save($element2);

        $count = $this->repository->count(['block' => $block]);
        $this->assertSame(2, $count);

        // 清理数据
        $this->repository->remove($element1);
        $this->repository->remove($element2);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testFindByBlockAsAssociationShouldReturnAllMatchingEntities(): void
    {
        $block = $this->createTestBlock();

        $element1 = new Element();
        $element1->setTitle('关联批量查询1');
        $element1->setBlock($block);
        $element1->setValid(true);
        $element1->setThumb1('/test/assoc-findby-1.jpg');

        $element2 = new Element();
        $element2->setTitle('关联批量查询2');
        $element2->setBlock($block);
        $element2->setValid(true);
        $element2->setThumb1('/test/assoc-findby-2.jpg');

        $this->repository->save($element1);
        $this->repository->save($element2);

        $foundElements = $this->repository->findBy(['block' => $block]);

        $this->assertIsArray($foundElements);
        $this->assertCount(2, $foundElements);
        $this->assertContainsOnlyInstancesOf(Element::class, $foundElements);

        // 清理数据
        $this->repository->remove($element1);
        $this->repository->remove($element2);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    // IS NULL 查询测试
    public function testFindByWithNullFieldsShouldWork(): void
    {
        $block = $this->createTestBlock();

        $element = new Element();
        $element->setTitle('空字段查询测试');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/null-fields.jpg');
        // 故意不设置可空字段，保持为 null

        $this->repository->save($element);

        // 测试各个可空字段的 IS NULL 查询
        $elementsWithNullSubtitle = $this->repository->findBy(['subtitle' => null]);
        $elementsWithNullDescription = $this->repository->findBy(['description' => null]);
        $elementsWithNullThumb2 = $this->repository->findBy(['thumb2' => null]);
        $elementsWithNullPath = $this->repository->findBy(['path' => null]);
        $elementsWithNullAppId = $this->repository->findBy(['appId' => null]);
        $elementsWithNullSortNumber = $this->repository->findBy(['sortNumber' => null]);
        $elementsWithNullTracking = $this->repository->findBy(['tracking' => null]);
        $elementsWithNullShowExpression = $this->repository->findBy(['showExpression' => null]);
        $elementsWithNullBeginTime = $this->repository->findBy(['beginTime' => null]);
        $elementsWithNullEndTime = $this->repository->findBy(['endTime' => null]);

        $this->assertGreaterThanOrEqual(1, count($elementsWithNullSubtitle));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullDescription));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullThumb2));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullPath));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullAppId));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullSortNumber));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullTracking));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullShowExpression));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullBeginTime));
        $this->assertGreaterThanOrEqual(1, count($elementsWithNullEndTime));

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    public function testCountWithNullFieldsShouldWork(): void
    {
        $block = $this->createTestBlock();

        $element = new Element();
        $element->setTitle('空字段计数测试');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/null-count.jpg');
        // 故意不设置可空字段，保持为 null

        $this->repository->save($element);

        // 测试各个可空字段的 IS NULL 计数
        $countNullSubtitle = $this->repository->count(['subtitle' => null]);
        $countNullDescription = $this->repository->count(['description' => null]);
        $countNullThumb2 = $this->repository->count(['thumb2' => null]);
        $countNullPath = $this->repository->count(['path' => null]);
        $countNullAppId = $this->repository->count(['appId' => null]);
        $countNullSortNumber = $this->repository->count(['sortNumber' => null]);
        $countNullTracking = $this->repository->count(['tracking' => null]);
        $countNullShowExpression = $this->repository->count(['showExpression' => null]);
        $countNullBeginTime = $this->repository->count(['beginTime' => null]);
        $countNullEndTime = $this->repository->count(['endTime' => null]);

        $this->assertGreaterThanOrEqual(1, $countNullSubtitle);
        $this->assertGreaterThanOrEqual(1, $countNullDescription);
        $this->assertGreaterThanOrEqual(1, $countNullThumb2);
        $this->assertGreaterThanOrEqual(1, $countNullPath);
        $this->assertGreaterThanOrEqual(1, $countNullAppId);
        $this->assertGreaterThanOrEqual(1, $countNullSortNumber);
        $this->assertGreaterThanOrEqual(1, $countNullTracking);
        $this->assertGreaterThanOrEqual(1, $countNullShowExpression);
        $this->assertGreaterThanOrEqual(1, $countNullBeginTime);
        $this->assertGreaterThanOrEqual(1, $countNullEndTime);

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    // 具体的空字段测试方法

    protected function createNewEntity(): object
    {
        $block = $this->createTestBlock();

        $element = new Element();
        $element->setTitle('Create New Entity Element');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/create-new-entity.jpg');

        return $element;
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ElementRepository::class);
    }

    /** @return ServiceEntityRepository<Element> */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    // PHPStan 规则要求的特定方法名测试

    /**
     * 测试按关联字段 block 查找单个实体 - PHPStan 规则要求的方法名
     */
    public function testFindOneByAssociationBlockShouldReturnMatchingEntity(): void
    {
        $block = $this->createTestBlock();

        $element = new Element();
        $element->setTitle('关联块查找测试');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/association-block.jpg');

        $this->repository->save($element);

        $foundElement = $this->repository->findOneBy(['block' => $block]);

        $this->assertNotNull($foundElement);
        $this->assertInstanceOf(Element::class, $foundElement);
        $this->assertSame('关联块查找测试', $foundElement->getTitle());
        $foundElementBlock = $foundElement->getBlock();
        $this->assertNotNull($foundElementBlock);
        $this->assertSame($block->getId(), $foundElementBlock->getId());

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    /**
     * 测试按关联字段 block 查找多个实体 - PHPStan 规则要求的方法名
     */

    /**
     * 测试按关联字段 block 计数 - PHPStan 规则要求的方法名
     */
    public function testCountByAssociationBlockShouldReturnCorrectNumber(): void
    {
        $block = $this->createTestBlock();

        $element1 = new Element();
        $element1->setTitle('关联块计数测试1');
        $element1->setBlock($block);
        $element1->setValid(true);
        $element1->setThumb1('/test/assoc-block-count-1.jpg');

        $element2 = new Element();
        $element2->setTitle('关联块计数测试2');
        $element2->setBlock($block);
        $element2->setValid(true);
        $element2->setThumb1('/test/assoc-block-count-2.jpg');

        $this->repository->save($element1);
        $this->repository->save($element2);

        $count = $this->repository->count(['block' => $block]);
        $this->assertSame(2, $count);

        // 清理数据
        $this->repository->remove($element1);
        $this->repository->remove($element2);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    /**
     * 测试带排序的 findOneBy 查询 - PHPStan 规则要求的方法名
     */

    // findByBlockPaginated 方法测试

    /**
     * 测试 findByBlockPaginated 方法 - 正常情况下的分页查询
     */
    public function testFindByBlockPaginatedShouldReturnQueryBuilderForValidBlockCode(): void
    {
        $block = $this->createTestBlock();

        $element1 = new Element();
        $element1->setTitle('分页查询测试1');
        $element1->setSubtitle('副标题1');
        $element1->setBlock($block);
        $element1->setValid(true);
        $element1->setThumb1('/test/paginated-1.jpg');
        $element1->setSortNumber(10);

        $element2 = new Element();
        $element2->setTitle('分页查询测试2');
        $element2->setSubtitle('副标题2');
        $element2->setBlock($block);
        $element2->setValid(true);
        $element2->setThumb1('/test/paginated-2.jpg');
        $element2->setSortNumber(20);

        $this->repository->save($element1);
        $this->repository->save($element2);

        // 调用 findByBlockPaginated 方法
        $blockCode = $block->getCode();
        $this->assertNotNull($blockCode);
        $queryBuilder = $this->repository->findByBlockPaginated($blockCode);

        // 验证返回的是 QueryBuilder 对象
        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);

        // 执行查询并验证结果
        $result = $queryBuilder->getQuery()->getResult();
        $this->assertIsArray($result);
        $this->assertGreaterThanOrEqual(2, count($result));

        // 验证结果都是 Element 实例
        foreach ($result as $element) {
            $this->assertInstanceOf(Element::class, $element);
        }

        // 验证排序是否正确（按 sortNumber DESC，然后按 id DESC）
        $testElements = array_filter($result, fn ($el) => $el instanceof Element && str_contains($el->getTitle() ?? '', '分页查询测试'));
        /** @var Element[] $testElements */
        $testElements = array_values($testElements);
        $this->assertCount(2, $testElements);
        $this->assertSame('分页查询测试2', $testElements[0]->getTitle());
        $this->assertSame('分页查询测试1', $testElements[1]->getTitle());

        // 清理数据
        $this->repository->remove($element1);
        $this->repository->remove($element2);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    /**
     * 测试 findByBlockPaginated 方法 - 不同 blockCode 的过滤
     */
    public function testFindByBlockPaginatedWithDifferentBlockCodesShouldFilterCorrectly(): void
    {
        $block1 = $this->createTestBlock();
        $block1->setCode('test-block-code-1');
        self::getEntityManager()->flush();

        $block2 = $this->createTestBlock();
        $block2->setCode('test-block-code-2');
        self::getEntityManager()->flush();

        $element1 = new Element();
        $element1->setTitle('Block1元素');
        $element1->setBlock($block1);
        $element1->setValid(true);
        $element1->setThumb1('/test/block1-1.jpg');

        $element2 = new Element();
        $element2->setTitle('Block2元素');
        $element2->setBlock($block2);
        $element2->setValid(true);
        $element2->setThumb1('/test/block2-1.jpg');

        $this->repository->save($element1);
        $this->repository->save($element2);

        // 查询 block1 的元素
        $qb1 = $this->repository->findByBlockPaginated('test-block-code-1');
        $result1 = $qb1->getQuery()->getResult();
        $this->assertIsArray($result1);
        $block1Elements = array_filter($result1, fn ($el) => $el instanceof Element && str_contains($el->getTitle() ?? '', 'Block1元素'));
        $this->assertCount(1, $block1Elements);

        // 查询 block2 的元素
        $qb2 = $this->repository->findByBlockPaginated('test-block-code-2');
        $result2 = $qb2->getQuery()->getResult();
        $this->assertIsArray($result2);
        $block2Elements = array_filter($result2, fn ($el) => $el instanceof Element && str_contains($el->getTitle() ?? '', 'Block2元素'));
        $this->assertCount(1, $block2Elements);

        // 清理数据
        $this->repository->remove($element1);
        $this->repository->remove($element2);
        self::getEntityManager()->remove($block1);
        self::getEntityManager()->remove($block2);
        self::getEntityManager()->flush();
    }

    /**
     * 测试 findByBlockPaginated 方法 - 关键词搜索功能
     */
    public function testFindByBlockPaginatedWithKeywordSearchShouldFilterByTitleOrSubtitle(): void
    {
        $block = $this->createTestBlock();

        $element1 = new Element();
        $element1->setTitle('包含关键词的标题');
        $element1->setSubtitle('普通副标题');
        $element1->setBlock($block);
        $element1->setValid(true);
        $element1->setThumb1('/test/keyword-title.jpg');

        $element2 = new Element();
        $element2->setTitle('普通标题');
        $element2->setSubtitle('包含关键词的副标题');
        $element2->setBlock($block);
        $element2->setValid(true);
        $element2->setThumb1('/test/keyword-subtitle.jpg');

        $element3 = new Element();
        $element3->setTitle('不相关的标题');
        $element3->setSubtitle('也不相关的副标题');
        $element3->setBlock($block);
        $element3->setValid(true);
        $element3->setThumb1('/test/no-keyword.jpg');

        $this->repository->save($element1);
        $this->repository->save($element2);
        $this->repository->save($element3);

        // 搜索标题中的关键词
        $blockCode = $block->getCode();
        $this->assertNotNull($blockCode);
        $qbWithKeyword = $this->repository->findByBlockPaginated($blockCode, '关键词');
        $resultWithKeyword = $qbWithKeyword->getQuery()->getResult();
        $this->assertIsArray($resultWithKeyword);

        // 验证结果包含两个元素（一个在标题，一个在副标题）
        $keywordElements = array_filter($resultWithKeyword, fn ($el) => $el instanceof Element && (str_contains($el->getTitle() ?? '', '关键词') || str_contains($el->getSubtitle() ?? '', '关键词'))
        );
        $this->assertCount(2, $keywordElements);

        // 不带关键词的查询应该返回所有元素
        $blockCode = $block->getCode();
        $this->assertNotNull($blockCode);
        $qbWithoutKeyword = $this->repository->findByBlockPaginated($blockCode);
        $resultWithoutKeyword = $qbWithoutKeyword->getQuery()->getResult();
        $this->assertIsArray($resultWithoutKeyword);
        $allElements = array_filter($resultWithoutKeyword, fn ($el) => $el instanceof Element && (str_contains($el->getTitle() ?? '', '关键词')
            || str_contains($el->getSubtitle() ?? '', '关键词')
            || str_contains($el->getTitle() ?? '', '不相关'))
        );
        $this->assertCount(3, $allElements);

        // 清理数据
        $this->repository->remove($element1);
        $this->repository->remove($element2);
        $this->repository->remove($element3);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    /**
     * 测试 findByBlockPaginated 方法 - 空关键词和空格关键词的处理
     */
    public function testFindByBlockPaginatedWithEmptyKeywordShouldHandleCorrectly(): void
    {
        $block = $this->createTestBlock();

        $element = new Element();
        $element->setTitle('空关键词测试');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/empty-keyword.jpg');

        $this->repository->save($element);

        // 测试 null 关键词
        $blockCode = $block->getCode();
        $this->assertNotNull($blockCode);
        $qbNull = $this->repository->findByBlockPaginated($blockCode, null);
        $resultNull = $qbNull->getQuery()->getResult();
        $this->assertIsArray($resultNull);
        $this->assertGreaterThanOrEqual(1, count($resultNull));

        // 测试空字符串关键词
        $blockCode = $block->getCode();
        $this->assertNotNull($blockCode);
        $qbEmpty = $this->repository->findByBlockPaginated($blockCode, '');
        $resultEmpty = $qbEmpty->getQuery()->getResult();
        $this->assertIsArray($resultEmpty);
        $this->assertGreaterThanOrEqual(1, count($resultEmpty));

        // 测试只有空格的关键词
        $blockCode = $block->getCode();
        $this->assertNotNull($blockCode);
        $qbSpaces = $this->repository->findByBlockPaginated($blockCode, '   ');
        $resultSpaces = $qbSpaces->getQuery()->getResult();
        $this->assertIsArray($resultSpaces);
        $this->assertGreaterThanOrEqual(1, count($resultSpaces));

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    /**
     * 测试 findByBlockPaginated 方法 - 只查询有效的 Block 和 Element
     */
    public function testFindByBlockPaginatedShouldOnlyReturnValidBlocksAndElements(): void
    {
        $validBlock = $this->createTestBlock();
        $validBlock->setCode('valid-block-code');
        $validBlock->setValid(true);
        self::getEntityManager()->flush();

        $invalidBlock = $this->createTestBlock();
        $invalidBlock->setCode('invalid-block-code');
        $invalidBlock->setValid(false);
        self::getEntityManager()->flush();

        $validElement = new Element();
        $validElement->setTitle('有效元素');
        $validElement->setBlock($validBlock);
        $validElement->setValid(true);
        $validElement->setThumb1('/test/valid-element.jpg');

        $invalidElement = new Element();
        $invalidElement->setTitle('无效元素');
        $invalidElement->setBlock($validBlock);
        $invalidElement->setValid(false);
        $invalidElement->setThumb1('/test/invalid-element.jpg');

        $elementInInvalidBlock = new Element();
        $elementInInvalidBlock->setTitle('无效块中的元素');
        $elementInInvalidBlock->setBlock($invalidBlock);
        $elementInInvalidBlock->setValid(true);
        $elementInInvalidBlock->setThumb1('/test/element-in-invalid-block.jpg');

        $this->repository->save($validElement);
        $this->repository->save($invalidElement);
        $this->repository->save($elementInInvalidBlock);

        // 查询有效 block - 应该只返回有效的元素
        $qbValid = $this->repository->findByBlockPaginated('valid-block-code');
        $resultValid = $qbValid->getQuery()->getResult();
        $this->assertIsArray($resultValid);

        $foundElements = array_filter($resultValid, fn ($el) => $el instanceof Element && (str_contains($el->getTitle() ?? '', '有效元素')
            || str_contains($el->getTitle() ?? '', '无效元素'))
        );

        // 应该只找到有效元素，不包括无效元素
        $validOnlyElements = array_filter($foundElements, fn ($el) => '有效元素' === $el->getTitle());
        $this->assertCount(1, $validOnlyElements);

        // 查询无效 block - 应该返回空结果
        $qbInvalid = $this->repository->findByBlockPaginated('invalid-block-code');
        $resultInvalid = $qbInvalid->getQuery()->getResult();
        $this->assertIsArray($resultInvalid);

        $invalidBlockElements = array_filter($resultInvalid, fn ($el) => $el instanceof Element && str_contains($el->getTitle() ?? '', '无效块中的元素')
        );
        $this->assertCount(0, $invalidBlockElements);

        // 清理数据
        $this->repository->remove($validElement);
        $this->repository->remove($invalidElement);
        $this->repository->remove($elementInInvalidBlock);
        self::getEntityManager()->remove($validBlock);
        self::getEntityManager()->remove($invalidBlock);
        self::getEntityManager()->flush();
    }

    /**
     * 测试 findByBlockPaginated 方法 - 返回结果的格式验证
     */
    public function testFindByBlockPaginatedResultFormatShouldBeCorrect(): void
    {
        $block = $this->createTestBlock();

        $element = new Element();
        $element->setTitle('格式验证测试');
        $element->setBlock($block);
        $element->setValid(true);
        $element->setThumb1('/test/format-test.jpg');
        $element->setSortNumber(5);

        $this->repository->save($element);

        $blockCode = $block->getCode();
        $this->assertNotNull($blockCode);
        $queryBuilder = $this->repository->findByBlockPaginated($blockCode);

        // 验证返回的是 QueryBuilder 实例
        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);

        // 验证 QueryBuilder 的 DQL 包含预期的连接和条件
        $dql = $queryBuilder->getDQL();
        $this->assertStringContainsString('INNER JOIN', $dql);
        $this->assertStringContainsString('e.block', $dql);
        $this->assertStringContainsString('b.code', $dql);
        $this->assertStringContainsString('b.valid', $dql);
        $this->assertStringContainsString('e.valid', $dql);

        // 验证排序
        $this->assertStringContainsString('e.sortNumber DESC', $dql);
        $this->assertStringContainsString('e.id DESC', $dql);

        // 执行查询并验证结果格式
        $result = $queryBuilder->getQuery()->getResult();
        $this->assertIsArray($result);

        if (count($result) > 0) {
            $firstElement = $result[0];
            $this->assertInstanceOf(Element::class, $firstElement);
            $this->assertNotNull($firstElement->getId());
            $this->assertInstanceOf(Block::class, $firstElement->getBlock());
            $this->assertTrue($firstElement->isValid());
            $this->assertTrue($firstElement->getBlock()->isValid());
        }

        // 清理数据
        $this->repository->remove($element);
        self::getEntityManager()->remove($block);
        self::getEntityManager()->flush();
    }

    /**
     * 测试 findByBlockPaginated 方法 - 不存在的 blockCode
     */
    public function testFindByBlockPaginatedWithNonExistentBlockCodeShouldReturnEmptyResult(): void
    {
        $nonExistentCode = 'non-existent-block-code-' . uniqid();

        $queryBuilder = $this->repository->findByBlockPaginated($nonExistentCode);
        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);

        // 执行查询应该返回空结果
        $result = $queryBuilder->getQuery()->getResult();
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }
}
