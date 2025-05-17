<?php

namespace DiyPageBundle\Tests\Entity;

use DateTime;
use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Entity\VisitLog;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class VisitLogTest extends TestCase
{
    private VisitLog $visitLog;

    protected function setUp(): void
    {
        $this->visitLog = new VisitLog();
    }

    public function testGetSetId(): void
    {
        $reflection = new \ReflectionClass(VisitLog::class);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->visitLog, 123);

        $this->assertSame(123, $this->visitLog->getId());
    }

    public function testGetSetCreateTime(): void
    {
        $date = new DateTime('2023-01-01');
        $result = $this->visitLog->setCreateTime($date);
        
        $this->assertSame($this->visitLog, $result);
        $this->assertSame($date, $this->visitLog->getCreateTime());
    }

    public function testGetSetBlock(): void
    {
        $block = $this->createMock(Block::class);
        $result = $this->visitLog->setBlock($block);
        
        $this->assertSame($this->visitLog, $result);
        $this->assertSame($block, $this->visitLog->getBlock());
    }

    public function testGetSetElement(): void
    {
        $element = $this->createMock(Element::class);
        $result = $this->visitLog->setElement($element);
        
        $this->assertSame($this->visitLog, $result);
        $this->assertSame($element, $this->visitLog->getElement());
    }

    public function testGetSetUser(): void
    {
        $user = $this->createMock(UserInterface::class);
        $result = $this->visitLog->setUser($user);
        
        $this->assertSame($this->visitLog, $result);
        $this->assertSame($user, $this->visitLog->getUser());
    }

    public function testGetSetCreatedFromIp(): void
    {
        $ip = '192.168.1.1';
        $this->visitLog->setCreatedFromIp($ip);
        $this->assertSame($ip, $this->visitLog->getCreatedFromIp());
    }
} 