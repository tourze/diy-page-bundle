<?php

namespace DiyPageBundle\Tests\Unit\Event;

use DiyPageBundle\Entity\Element;
use DiyPageBundle\Event\ElementDataFormatEvent;
use PHPUnit\Framework\TestCase;

class ElementDataFormatEventTest extends TestCase
{
    private ElementDataFormatEvent $event;

    protected function setUp(): void
    {
        $this->event = new ElementDataFormatEvent();
    }

    public function testElement(): void
    {
        $element = new Element();
        $this->event->setElement($element);
        $this->assertSame($element, $this->event->getElement());
    }

    public function testResult(): void
    {
        $result = ['key' => 'value', 'data' => ['foo' => 'bar']];
        $this->event->setResult($result);
        $this->assertSame($result, $this->event->getResult());
    }

    public function testResultInitiallyEmptyArray(): void
    {
        $this->assertSame([], $this->event->getResult());
    }

    public function testResultCanBeSetToNull(): void
    {
        $this->event->setResult(null);
        $this->assertNull($this->event->getResult());
    }
}