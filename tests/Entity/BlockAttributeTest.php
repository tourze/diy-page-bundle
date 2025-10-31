<?php

namespace DiyPageBundle\Tests\Entity;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\BlockAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(BlockAttribute::class)]
final class BlockAttributeTest extends AbstractEntityTestCase
{
    protected function createEntity(): BlockAttribute
    {
        return new BlockAttribute();
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', 'test-name'];
        yield 'value' => ['value', 'test-value'];
        yield 'remark' => ['remark', 'test-remark'];
        yield 'block' => ['block', new Block()];
        yield 'createdFromIp' => ['createdFromIp', '127.0.0.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.1'];
    }

    public function testRetrieveApiArray(): void
    {
        $blockAttribute = $this->createEntity();
        $blockAttribute->setName('test-name');
        $blockAttribute->setValue('test-value');

        $result = $blockAttribute->retrieveApiArray();

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertSame('test-name', $result['name']);
        $this->assertSame('test-value', $result['value']);
    }

    public function testRetrieveAdminArray(): void
    {
        $blockAttribute = $this->createEntity();
        $blockAttribute->setName('test-name');
        $blockAttribute->setValue('test-value');

        $result = $blockAttribute->retrieveAdminArray();

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertSame('test-name', $result['name']);
        $this->assertSame('test-value', $result['value']);
    }

    public function testToString(): void
    {
        $blockAttribute = $this->createEntity();
        $blockAttribute->setName('test-name');
        $blockAttribute->setValue('test-value');

        $this->assertSame('test-name: test-value', (string) $blockAttribute);
    }
}
