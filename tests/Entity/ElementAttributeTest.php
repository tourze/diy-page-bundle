<?php

namespace DiyPageBundle\Tests\Entity;

use DiyPageBundle\Entity\Element;
use DiyPageBundle\Entity\ElementAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(ElementAttribute::class)]
final class ElementAttributeTest extends AbstractEntityTestCase
{
    protected function createEntity(): ElementAttribute
    {
        return new ElementAttribute();
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', 'test-name'];
        yield 'value' => ['value', 'test-value'];
        yield 'remark' => ['remark', 'test-remark'];
        yield 'element' => ['element', new Element()];
        yield 'createdFromIp' => ['createdFromIp', '127.0.0.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.1'];
    }

    public function testRetrieveApiArray(): void
    {
        $elementAttribute = $this->createEntity();
        $elementAttribute->setName('test-name');
        $elementAttribute->setValue('test-value');

        $result = $elementAttribute->retrieveApiArray();

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertSame('test-name', $result['name']);
        $this->assertSame('test-value', $result['value']);
    }

    public function testRetrieveAdminArray(): void
    {
        $elementAttribute = $this->createEntity();
        $elementAttribute->setName('test-name');
        $elementAttribute->setValue('test-value');

        $result = $elementAttribute->retrieveAdminArray();

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertSame('test-name', $result['name']);
        $this->assertSame('test-value', $result['value']);
    }

    public function testToString(): void
    {
        $elementAttribute = $this->createEntity();
        $elementAttribute->setName('test-name');
        $elementAttribute->setValue('test-value');

        $this->assertSame('test-name: test-value', (string) $elementAttribute);
    }
}
