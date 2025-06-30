<?php

namespace DiyPageBundle\Tests\Unit\Entity;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\BlockAttribute;
use PHPUnit\Framework\TestCase;

class BlockAttributeTest extends TestCase
{
    private BlockAttribute $blockAttribute;

    protected function setUp(): void
    {
        $this->blockAttribute = new BlockAttribute();
    }

    public function testName(): void
    {
        $name = 'test-name';
        $this->blockAttribute->setName($name);
        $this->assertSame($name, $this->blockAttribute->getName());
    }

    public function testValue(): void
    {
        $value = 'test-value';
        $this->blockAttribute->setValue($value);
        $this->assertSame($value, $this->blockAttribute->getValue());
    }

    public function testRemark(): void
    {
        $remark = 'test-remark';
        $this->blockAttribute->setRemark($remark);
        $this->assertSame($remark, $this->blockAttribute->getRemark());
    }

    public function testBlock(): void
    {
        $block = new Block();
        $this->blockAttribute->setBlock($block);
        $this->assertSame($block, $this->blockAttribute->getBlock());
    }

    public function testCreatedFromIp(): void
    {
        $ip = '127.0.0.1';
        $this->blockAttribute->setCreatedFromIp($ip);
        $this->assertSame($ip, $this->blockAttribute->getCreatedFromIp());
    }

    public function testUpdatedFromIp(): void
    {
        $ip = '192.168.1.1';
        $this->blockAttribute->setUpdatedFromIp($ip);
        $this->assertSame($ip, $this->blockAttribute->getUpdatedFromIp());
    }

    public function testRetrieveApiArray(): void
    {
        $this->blockAttribute->setName('test-name');
        $this->blockAttribute->setValue('test-value');

        $result = $this->blockAttribute->retrieveApiArray();

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertSame('test-name', $result['name']);
        $this->assertSame('test-value', $result['value']);
    }

    public function testRetrieveAdminArray(): void
    {
        $this->blockAttribute->setName('test-name');
        $this->blockAttribute->setValue('test-value');

        $result = $this->blockAttribute->retrieveAdminArray();

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertSame('test-name', $result['name']);
        $this->assertSame('test-value', $result['value']);
    }

    public function testToString(): void
    {
        $this->blockAttribute->setName('test-name');
        $this->blockAttribute->setValue('test-value');

        $this->assertSame('test-name: test-value', (string) $this->blockAttribute);
    }
}