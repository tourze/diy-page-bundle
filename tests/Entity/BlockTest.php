<?php

namespace DiyPageBundle\Tests\Entity;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class BlockTest extends TestCase
{
    private Block $block;

    protected function setUp(): void
    {
        $this->block = new Block();
    }

    public function testGetSetId(): void
    {
        $reflection = new \ReflectionClass(Block::class);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->block, 123);

        $this->assertSame(123, $this->block->getId());
    }

    public function testGetSetCreateTime(): void
    {
        $date = new \DateTimeImmutable('2023-01-01');
        $this->block->setCreateTime($date);
        $this->assertSame($date, $this->block->getCreateTime());
    }

    public function testGetSetUpdateTime(): void
    {
        $date = new \DateTimeImmutable('2023-01-02');
        $this->block->setUpdateTime($date);
        $this->assertSame($date, $this->block->getUpdateTime());
    }

    public function testGetSetCreatedBy(): void
    {
        $createdBy = 'user1';
        $result = $this->block->setCreatedBy($createdBy);
        
        $this->assertSame($this->block, $result);
        $this->assertSame($createdBy, $this->block->getCreatedBy());
    }

    public function testGetSetUpdatedBy(): void
    {
        $updatedBy = 'user2';
        $result = $this->block->setUpdatedBy($updatedBy);
        
        $this->assertSame($this->block, $result);
        $this->assertSame($updatedBy, $this->block->getUpdatedBy());
    }

    public function testGetSetCreatedFromIp(): void
    {
        $ip = '192.168.1.1';
        $result = $this->block->setCreatedFromIp($ip);
        
        $this->assertSame($this->block, $result);
        $this->assertSame($ip, $this->block->getCreatedFromIp());
    }

    public function testGetSetUpdatedFromIp(): void
    {
        $ip = '192.168.1.2';
        $result = $this->block->setUpdatedFromIp($ip);
        
        $this->assertSame($this->block, $result);
        $this->assertSame($ip, $this->block->getUpdatedFromIp());
    }

    public function testIsSetValid(): void
    {
        $this->assertFalse($this->block->isValid());
        
        $result = $this->block->setValid(true);
        
        $this->assertSame($this->block, $result);
        $this->assertTrue($this->block->isValid());
    }

    public function testGetSetTitle(): void
    {
        $title = '测试广告位';
        $result = $this->block->setTitle($title);
        
        $this->assertSame($this->block, $result);
        $this->assertSame($title, $this->block->getTitle());
    }

    public function testGetSetCode(): void
    {
        $code = 'test_ad_block';
        $result = $this->block->setCode($code);
        
        $this->assertSame($this->block, $result);
        $this->assertSame($code, $this->block->getCode());
    }

    public function testGetSetSortNumber(): void
    {
        $sortNumber = 10;
        $result = $this->block->setSortNumber($sortNumber);
        
        $this->assertSame($this->block, $result);
        $this->assertSame($sortNumber, $this->block->getSortNumber());
    }

    public function testGetElements(): void
    {
        $elements = $this->block->getElements();
        
        $this->assertInstanceOf(Collection::class, $elements);
        $this->assertCount(0, $elements);
    }

    public function testAddElement(): void
    {
        $element = new Element();
        $result = $this->block->addElement($element);
        
        $this->assertSame($this->block, $result);
        $this->assertCount(1, $this->block->getElements());
        $this->assertSame($this->block, $element->getBlock());
    }

    public function testRemoveElement(): void
    {
        $element = new Element();
        $this->block->addElement($element);
        $this->assertCount(1, $this->block->getElements());
        
        $result = $this->block->removeElement($element);
        
        $this->assertSame($this->block, $result);
        $this->assertCount(0, $this->block->getElements());
    }

    public function testGetSetShowExpression(): void
    {
        $expression = 'user.isAuthenticated()';
        $result = $this->block->setShowExpression($expression);
        
        $this->assertSame($this->block, $result);
        $this->assertSame($expression, $this->block->getShowExpression());
    }

    public function testGetSetTypeId(): void
    {
        $typeId = 'banner';
        $result = $this->block->setTypeId($typeId);
        
        $this->assertSame($this->block, $result);
        $this->assertSame($typeId, $this->block->getTypeId());
    }

    public function testGetSetBeginTime(): void
    {
        $date = new \DateTimeImmutable('2023-01-01');
        $this->block->setBeginTime($date);
        $this->assertSame($date, $this->block->getBeginTime());
    }

    public function testGetSetEndTime(): void
    {
        $date = new \DateTimeImmutable('2023-01-31');
        $this->block->setEndTime($date);
        $this->assertSame($date, $this->block->getEndTime());
    }

    public function testToString(): void
    {
        $this->assertEquals('', $this->block->__toString());
        
        $reflection = new \ReflectionClass(Block::class);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->block, 123);
        
        $this->block->setTitle('测试广告位');
        $this->block->setCode('test_code');
        
        $this->assertEquals('测试广告位(test_code)', $this->block->__toString());
    }

    public function testRetrieveAdminArray(): void
    {
        $this->block->setTitle('测试广告位');
        $this->block->setCode('test_code');
        
        $adminArray = $this->block->retrieveAdminArray();
        
        $this->assertArrayHasKey('title', $adminArray);
        $this->assertSame('测试广告位', $adminArray['title']);
    }
} 