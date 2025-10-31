<?php

namespace DiyPageBundle\Tests\Procedure;

use DiyPageBundle\Procedure\GetDiyPageElementByCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Tests\AbstractProcedureTestCase;

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

        $this->procedure->codes = [];
        $this->procedure->execute();
    }

    public function testSaveLogProperty(): void
    {
        $this->assertTrue($this->procedure->saveLog);
        $this->procedure->saveLog = false;
        $this->assertFalse($this->procedure->saveLog);
    }

    public function testCodesProperty(): void
    {
        $codes = ['test1', 'test2'];
        $this->procedure->codes = $codes;
        $this->assertSame($codes, $this->procedure->codes);
    }
}
