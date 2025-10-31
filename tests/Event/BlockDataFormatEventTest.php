<?php

namespace DiyPageBundle\Tests\Event;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Event\BlockDataFormatEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;

/**
 * @internal
 */
#[CoversClass(BlockDataFormatEvent::class)]
final class BlockDataFormatEventTest extends AbstractEventTestCase
{
    private BlockDataFormatEvent $event;

    protected function setUp(): void
    {
        parent::setUp();
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
