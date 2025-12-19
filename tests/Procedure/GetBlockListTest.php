<?php

declare(strict_types=1);

namespace DiyPageBundle\Tests\Procedure;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Param\GetBlockListParam;
use DiyPageBundle\Procedure\GetBlockList;
use DiyPageBundle\Repository\BlockRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetBlockList::class)]
#[RunTestsInSeparateProcesses]
final class GetBlockListTest extends AbstractProcedureTestCase
{
    private BlockRepository $repository;
    private GetBlockList $procedure;

    protected function onSetUp(): void
    {
        // 清理数据库
        self::cleanDatabase();

        $this->repository = self::getService(BlockRepository::class);
        $this->procedure = self::getService(GetBlockList::class);

        // 创建测试数据
        $this->createTestData();
    }

    private function createTestData(): void
    {
        $manager = self::getEntityManager();

        $uniqid = uniqid();

        // 创建示例广告位
        $sampleBlock = new Block();
        $sampleBlock->setValid(true);
        $sampleBlock->setCode('sample-block-' . $uniqid);
        $sampleBlock->setTitle('示例广告位');
        $sampleBlock->setSortNumber(1);
        $manager->persist($sampleBlock);

        // 创建无效的广告位
        $invalidBlock = new Block();
        $invalidBlock->setValid(false);
        $invalidBlock->setCode('invalid-block-' . $uniqid);
        $invalidBlock->setTitle('无效广告位');
        $invalidBlock->setSortNumber(2);
        $manager->persist($invalidBlock);

        // 创建第二个有效的广告位
        $validBlock2 = new Block();
        $validBlock2->setValid(true);
        $validBlock2->setCode('valid-block-2-' . $uniqid);
        $validBlock2->setTitle('有效广告位2');
        $validBlock2->setSortNumber(3);
        $manager->persist($validBlock2);

        $manager->flush();
    }

    public function testExecuteWithDefaults(): void
    {
        $param = new GetBlockListParam();
        $result = $this->procedure->execute($param);

        $this->assertInstanceOf(ArrayResult::class, $result);

        // ArrayResult 使用公共属性 data
        $this->assertIsArray($result->data);
        $resultArray = $result->data;
        $this->assertArrayHasKey('items', $resultArray);
        $this->assertArrayHasKey('total', $resultArray);
        $this->assertArrayHasKey('page', $resultArray);
        $this->assertArrayHasKey('limit', $resultArray);
        $this->assertArrayHasKey('pages', $resultArray);

        // 应该返回所有的广告位（包括有效的和无效的）
        $this->assertGreaterThanOrEqual(3, $resultArray['total']);
        $this->assertEquals(1, $resultArray['page']);
        $this->assertEquals(20, $resultArray['limit']);
        $this->assertGreaterThanOrEqual(1, $resultArray['pages']);

        // 验证返回的数据结构
        $items = $resultArray['items'];
        $this->assertIsArray($items);
        if (count($items) > 0) {
            $firstItem = $items[0];
            $this->assertArrayHasKey('id', $firstItem);
            $this->assertArrayHasKey('title', $firstItem);
            $this->assertArrayHasKey('code', $firstItem);
            $this->assertArrayHasKey('valid', $firstItem);
        }
    }

    public function testExecuteWithValidOnlyFilter(): void
    {
        $param = new GetBlockListParam(validOnly: true);
        $result = $this->procedure->execute($param);

        $this->assertInstanceOf(ArrayResult::class, $result);

        // ArrayResult 使用公共属性 data
        $this->assertIsArray($result->data);
        $resultArray = $result->data;
        $this->assertArrayHasKey('items', $resultArray);
        $this->assertArrayHasKey('total', $resultArray);

        // 应该只返回有效的广告位
        $this->assertGreaterThanOrEqual(2, $resultArray['total']);
        $this->assertCount($resultArray['total'], $resultArray['items']);

        // 验证所有返回的广告位都是有效的
        foreach ($resultArray['items'] as $item) {
            $this->assertArrayHasKey('valid', $item);
            $this->assertTrue($item['valid']);
        }
    }

    public function testExecuteWithPagination(): void
    {
        $param = new GetBlockListParam(page: 2, limit: 1);
        $result = $this->procedure->execute($param);

        $this->assertInstanceOf(ArrayResult::class, $result);

        // ArrayResult 使用公共属性 data
        $this->assertIsArray($result->data);
        $resultArray = $result->data;
        $this->assertArrayHasKey('items', $resultArray);
        $this->assertArrayHasKey('total', $resultArray);
        $this->assertArrayHasKey('page', $resultArray);
        $this->assertArrayHasKey('limit', $resultArray);
        $this->assertArrayHasKey('pages', $resultArray);

        $this->assertEquals(2, $resultArray['page']);
        $this->assertEquals(1, $resultArray['limit']);
        $this->assertGreaterThanOrEqual(3, $resultArray['total']); // 至少有3条数据
        $this->assertGreaterThanOrEqual(3, $resultArray['pages']); // 至少有3页

        // 验证分页逻辑
        $expectedPages = (int) ceil($resultArray['total'] / $resultArray['limit']);
        $this->assertEquals($expectedPages, $resultArray['pages']);
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
}
