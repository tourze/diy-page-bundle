<?php

namespace DiyPageBundle\Tests\Unit\Procedure;

use DiyPageBundle\Procedure\GetOneDiyPageElement;
use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Exception\ApiException;

class GetOneDiyPageElementTest extends TestCase
{
    private GetOneDiyPageElement $procedure;

    protected function setUp(): void
    {
        $this->procedure = new GetOneDiyPageElement();
    }

    public function testExecuteThrowsApiException(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('接口未实现');

        $this->procedure->execute();
    }

    public function testElementIdProperty(): void
    {
        $elementId = 123;
        $this->procedure->elementId = $elementId;
        $this->assertSame($elementId, $this->procedure->elementId);
    }
}