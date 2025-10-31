<?php

namespace DiyPageBundle\Tests\Procedure;

use DiyPageBundle\Procedure\GetOneDiyPageElement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetOneDiyPageElement::class)]
#[RunTestsInSeparateProcesses]
final class GetOneDiyPageElementTest extends AbstractProcedureTestCase
{
    private GetOneDiyPageElement $procedure;

    protected function onSetUp(): void
    {
        $procedure = self::getContainer()->get(GetOneDiyPageElement::class);
        $this->assertInstanceOf(GetOneDiyPageElement::class, $procedure);
        $this->procedure = $procedure;
    }

    public function testExecuteThrowsApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('元素不存在');

        $this->procedure->elementId = 999999; // 设置一个不存在的ID
        $this->procedure->execute();
    }

    public function testElementIdProperty(): void
    {
        $elementId = 123;
        $this->procedure->elementId = $elementId;
        $this->assertSame($elementId, $this->procedure->elementId);
    }
}
