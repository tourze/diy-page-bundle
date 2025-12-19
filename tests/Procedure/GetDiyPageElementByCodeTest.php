<?php

namespace DiyPageBundle\Tests\Procedure;

use DiyPageBundle\Param\GetDiyPageElementByCodeParam;
use DiyPageBundle\Procedure\GetDiyPageElementByCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;

/**
 * @internal
 */
#[CoversClass(GetDiyPageElementByCode::class)]
#[RunTestsInSeparateProcesses]
final class GetDiyPageElementByCodeTest extends AbstractProcedureTestCase
{
    private GetDiyPageElementByCode $procedure;

    protected function onSetUp(): void
    {
        $procedure = self::getContainer()->get(GetDiyPageElementByCode::class);
        $this->assertInstanceOf(GetDiyPageElementByCode::class, $procedure);
        $this->procedure = $procedure;
    }

    public function testExecuteThrowsExceptionWhenCodesEmpty(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('请输入codes');

        $param = new GetDiyPageElementByCodeParam(codes: []);
        $this->procedure->execute($param);
    }
}
