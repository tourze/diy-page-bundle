<?php

namespace DiyPageBundle\Tests\Unit\Event;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Event\BlockDataFormatEvent;
use PHPUnit\Framework\TestCase;

class BlockDataFormatEventTest extends TestCase
{
    private BlockDataFormatEvent $event;

    protected function setUp(): void
    {
        $this->event = new BlockDataFormatEvent();
    }

    public function testBlock(): void
    {
        $block = new Block();
        $this->event->setBlock($block);
        $this->assertSame($block, $this->event->getBlock());
    }

    public function testResult(): void
    {
        $result = ['key' => 'value', 'foo' => 'bar'];
        $this->event->setResult($result);
        $this->assertSame($result, $this->event->getResult());
    }

    public function testResultInitiallyNull(): void
    {
        $this->assertNull($this->event->getResult());
    }
}