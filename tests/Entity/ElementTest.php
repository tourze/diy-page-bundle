<?php

namespace DiyPageBundle\Tests\Entity;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Element::class)]
final class ElementTest extends AbstractEntityTestCase
{
    protected function createEntity(): Element
    {
        return new Element();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'valid' => ['valid', true];
        yield 'title' => ['title', '测试元素'];
        yield 'subtitle' => ['subtitle', '测试子标题'];
        yield 'description' => ['description', '这是一段描述文本'];
        yield 'thumb1' => ['thumb1', '/path/to/image1.jpg'];
        yield 'thumb2' => ['thumb2', '/path/to/image2.jpg'];
        yield 'path' => ['path', 'https://example.com/page'];
        yield 'appId' => ['appId', 'app123'];
        yield 'sortNumber' => ['sortNumber', 10];
        yield 'showTags' => ['showTags', ['tag1', 'tag2']];
        yield 'tracking' => ['tracking', 'tracking-code-123'];
        yield 'showExpression' => ['showExpression', 'user.hasRole("ROLE_ADMIN")'];
        yield 'beginTime' => ['beginTime', new \DateTimeImmutable('2023-01-01')];
        yield 'endTime' => ['endTime', new \DateTimeImmutable('2023-01-31')];
        yield 'loginJumpPage' => ['loginJumpPage', true];
        yield 'subscribeTemplateIds' => ['subscribeTemplateIds', ['template1', 'template2']];
        yield 'createTime' => ['createTime', new \DateTimeImmutable('2023-01-01')];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable('2023-01-02')];
        yield 'createdBy' => ['createdBy', 'user1'];
        yield 'updatedBy' => ['updatedBy', 'user2'];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '192.168.1.2'];
        yield 'block' => ['block', new Block()];
    }

    public function testToString(): void
    {
        $element = $this->createEntity();
        $element->setTitle('测试元素');

        $reflection = new \ReflectionClass(Element::class);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($element, '123456789');

        $this->assertEquals('测试元素', $element->__toString());
    }

    public function testRetrieveApiArray(): void
    {
        $element = $this->createEntity();
        $element->setTitle('测试元素');
        $element->setPath('https://example.com');
        $element->setThumb1('/path/to/image.jpg');

        $apiArray = $element->retrieveApiArray();

        $this->assertArrayHasKey('title', $apiArray);
        $this->assertArrayHasKey('path', $apiArray);
        $this->assertArrayHasKey('thumb1', $apiArray);
        $this->assertSame('测试元素', $apiArray['title']);
        $this->assertSame('https://example.com', $apiArray['path']);
        $this->assertSame('/path/to/image.jpg', $apiArray['thumb1']);
    }

    public function testRetrieveAdminArray(): void
    {
        $element = $this->createEntity();
        $element->setTitle('测试元素');

        $adminArray = $element->retrieveAdminArray();

        $this->assertArrayHasKey('title', $adminArray);
        $this->assertSame('测试元素', $adminArray['title']);
    }
}
