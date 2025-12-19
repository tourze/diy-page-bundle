<?php

namespace DiyPageBundle\Tests\Entity;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Entity\VisitLog;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use Tourze\UserIDBundle\Model\SystemUser;

/**
 * @internal
 */
#[CoversClass(VisitLog::class)]
final class VisitLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): VisitLog
    {
        return new VisitLog();
    }

    /** @return iterable<string, array{string, mixed}> */
    public static function propertiesProvider(): iterable
    {
        yield 'createTime' => ['createTime', new \DateTimeImmutable('2023-01-01')];
        yield 'block' => ['block', new Block()];
        yield 'element' => ['element', new Element()];
        yield 'user' => ['user', new SystemUser()];
        yield 'createdFromIp' => ['createdFromIp', '192.168.1.1'];
    }
}
