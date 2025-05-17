<?php

namespace DiyPageBundle\Tests\Entity;

use DateTime;
use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase
{
    private Element $element;

    protected function setUp(): void
    {
        $this->element = new Element();
    }

    public function testGetSetId(): void
    {
        $reflection = new \ReflectionClass(Element::class);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->element, '123456789');

        $this->assertSame('123456789', $this->element->getId());
    }

    public function testGetSetCreateTime(): void
    {
        $date = new DateTime('2023-01-01');
        $this->element->setCreateTime($date);
        $this->assertSame($date, $this->element->getCreateTime());
    }

    public function testGetSetUpdateTime(): void
    {
        $date = new DateTime('2023-01-02');
        $this->element->setUpdateTime($date);
        $this->assertSame($date, $this->element->getUpdateTime());
    }

    public function testGetSetCreatedBy(): void
    {
        $createdBy = 'user1';
        $result = $this->element->setCreatedBy($createdBy);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($createdBy, $this->element->getCreatedBy());
    }

    public function testGetSetUpdatedBy(): void
    {
        $updatedBy = 'user2';
        $result = $this->element->setUpdatedBy($updatedBy);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($updatedBy, $this->element->getUpdatedBy());
    }

    public function testGetSetCreatedFromIp(): void
    {
        $ip = '192.168.1.1';
        $result = $this->element->setCreatedFromIp($ip);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($ip, $this->element->getCreatedFromIp());
    }

    public function testGetSetUpdatedFromIp(): void
    {
        $ip = '192.168.1.2';
        $result = $this->element->setUpdatedFromIp($ip);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($ip, $this->element->getUpdatedFromIp());
    }

    public function testIsSetValid(): void
    {
        $this->assertFalse($this->element->isValid());
        
        $result = $this->element->setValid(true);
        
        $this->assertSame($this->element, $result);
        $this->assertTrue($this->element->isValid());
    }

    public function testGetSetBlock(): void
    {
        $block = new Block();
        $result = $this->element->setBlock($block);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($block, $this->element->getBlock());
    }

    public function testGetSetTitle(): void
    {
        $title = '测试元素';
        $result = $this->element->setTitle($title);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($title, $this->element->getTitle());
    }

    public function testGetSetSubtitle(): void
    {
        $subtitle = '测试子标题';
        $this->element->setSubtitle($subtitle);
        $this->assertSame($subtitle, $this->element->getSubtitle());
    }

    public function testGetSetDescription(): void
    {
        $description = '这是一段描述文本';
        $result = $this->element->setDescription($description);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($description, $this->element->getDescription());
    }

    public function testGetSetThumb1(): void
    {
        $thumb = '/path/to/image1.jpg';
        $result = $this->element->setThumb1($thumb);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($thumb, $this->element->getThumb1());
    }

    public function testGetSetThumb2(): void
    {
        $thumb = '/path/to/image2.jpg';
        $result = $this->element->setThumb2($thumb);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($thumb, $this->element->getThumb2());
    }

    public function testGetSetPath(): void
    {
        $path = 'https://example.com/page';
        $result = $this->element->setPath($path);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($path, $this->element->getPath());
    }

    public function testGetSetAppId(): void
    {
        $appId = 'app123';
        $result = $this->element->setAppId($appId);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($appId, $this->element->getAppId());
    }

    public function testGetSetSortNumber(): void
    {
        $sortNumber = 10;
        $result = $this->element->setSortNumber($sortNumber);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($sortNumber, $this->element->getSortNumber());
    }

    public function testGetSetShowTags(): void
    {
        $tags = ['tag1', 'tag2'];
        $result = $this->element->setShowTags($tags);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($tags, $this->element->getShowTags());
    }

    public function testGetSetTracking(): void
    {
        $tracking = 'tracking-code-123';
        $result = $this->element->setTracking($tracking);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($tracking, $this->element->getTracking());
    }

    public function testGetSetShowExpression(): void
    {
        $expression = 'user.hasRole("ROLE_ADMIN")';
        $result = $this->element->setShowExpression($expression);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($expression, $this->element->getShowExpression());
    }

    public function testGetSetBeginTime(): void
    {
        $date = new DateTime('2023-01-01');
        $this->element->setBeginTime($date);
        $this->assertSame($date, $this->element->getBeginTime());
    }

    public function testGetSetEndTime(): void
    {
        $date = new DateTime('2023-01-31');
        $this->element->setEndTime($date);
        $this->assertSame($date, $this->element->getEndTime());
    }

    public function testIsSetLoginJumpPage(): void
    {
        $loginJumpPage = true;
        $result = $this->element->setLoginJumpPage($loginJumpPage);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($loginJumpPage, $this->element->isLoginJumpPage());
    }

    public function testGetSetSubscribeTemplateIds(): void
    {
        $ids = ['template1', 'template2'];
        $result = $this->element->setSubscribeTemplateIds($ids);
        
        $this->assertSame($this->element, $result);
        $this->assertSame($ids, $this->element->getSubscribeTemplateIds());
    }

    public function testToString(): void
    {
        $this->element->setTitle('测试元素');
        
        $reflection = new \ReflectionClass(Element::class);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->element, '123456789');
        
        $this->assertEquals('测试元素', $this->element->__toString());
    }

    public function testRetrieveApiArray(): void
    {
        $this->element->setTitle('测试元素');
        $this->element->setPath('https://example.com');
        $this->element->setThumb1('/path/to/image.jpg');
        
        $apiArray = $this->element->retrieveApiArray();
        
        $this->assertIsArray($apiArray);
        $this->assertArrayHasKey('title', $apiArray);
        $this->assertArrayHasKey('path', $apiArray);
        $this->assertArrayHasKey('thumb1', $apiArray);
        $this->assertSame('测试元素', $apiArray['title']);
        $this->assertSame('https://example.com', $apiArray['path']);
        $this->assertSame('/path/to/image.jpg', $apiArray['thumb1']);
    }

    public function testRetrieveAdminArray(): void
    {
        $this->element->setTitle('测试元素');
        
        $adminArray = $this->element->retrieveAdminArray();
        
        $this->assertIsArray($adminArray);
        $this->assertArrayHasKey('title', $adminArray);
        $this->assertSame('测试元素', $adminArray['title']);
    }
} 