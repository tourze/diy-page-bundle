<?php

namespace DiyPageBundle\Tests\Procedure;

use DiyPageBundle\Param\GetOneDiyPageElementParam;
use DiyPageBundle\Procedure\GetOneDiyPageElement;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

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

        $param = new GetOneDiyPageElementParam(elementId: 999999); // 设置一个不存在的ID
        $this->procedure->execute($param);
    }
}
