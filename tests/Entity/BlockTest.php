<?php

namespace DiyPageBundle\Tests\Entity;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Block::class)]
final class BlockTest extends AbstractEntityTestCase
{
    protected function createEntity(): Block
    {
        return new Block();
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function propertiesProvider(): iterable
    {
        yield 'valid' => ['valid', true];
        yield 'title' => ['title', '测试广告位'];
        yield 'code' => ['code', 'test_ad_block'];
        yield 'sortNumber' => ['sortNumber', 10];
        yield 'showExpression' => ['showExpression', 'user.isAuthenticated()'];
        yield 'typeId' => ['typeId', 'banner'];
        yield 'beginTime' => ['beginTime', new \DateTimeImmutable('2023-01-01')];
        yield 'endTime' => ['endTime', new \DateTimeImmutable('2023-01-31')];
        yield 'createTime' => ['createTime', new \DateTimeImmutable('2023-01-01')];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable('2023-01-02')];
        yield 'createdBy' => ['createdBy', 'user1'];
        yield 'updatedBy' => ['updatedBy', 'user2'];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
    }

    public function testGetElements(): void
    {
        $block = $this->createEntity();
        $elements = $block->getElements();

        $this->assertInstanceOf(Collection::class, $elements);
        $this->assertCount(0, $elements);
    }

    public function testAddElement(): void
    {
        $block = $this->createEntity();
        $element = new Element();
        $block->addElement($element);

        $this->assertCount(1, $block->getElements());
        $this->assertSame($block, $element->getBlock());
    }

    public function testRemoveElement(): void
    {
        $block = $this->createEntity();
        $element = new Element();
        $block->addElement($element);
        $this->assertCount(1, $block->getElements());

        $block->removeElement($element);

        $this->assertCount(0, $block->getElements());
    }

    public function testToString(): void
    {
        $block = $this->createEntity();
        $this->assertEquals('', $block->__toString());

        $reflection = new \ReflectionClass(Block::class);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($block, 123);

        $block->setTitle('测试广告位');
        $block->setCode('test_code');

        $this->assertEquals('测试广告位(test_code)', $block->__toString());
    }

    public function testRetrieveAdminArray(): void
    {
        $block = $this->createEntity();
        $block->setTitle('测试广告位');
        $block->setCode('test_code');

        $adminArray = $block->retrieveAdminArray();

        $this->assertArrayHasKey('title', $adminArray);
        $this->assertSame('测试广告位', $adminArray['title']);
    }
}
