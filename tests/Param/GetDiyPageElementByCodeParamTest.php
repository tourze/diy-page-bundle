<?php

declare(strict_types=1);

namespace DiyPageBundle\Tests\Param;

use DiyPageBundle\Param\GetDiyPageElementByCodeParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * GetDiyPageElementByCodeParam 单元测试
 *
 * @internal
 */
#[CoversClass(GetDiyPageElementByCodeParam::class)]
final class GetDiyPageElementByCodeParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new GetDiyPageElementByCodeParam();

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithDefaultParameters(): void
    {
        $param = new GetDiyPageElementByCodeParam();

        $this->assertSame([], $param->codes);
        $this->assertTrue($param->saveLog);
    }

    public function testConstructorWithAllParameters(): void
    {
        $param = new GetDiyPageElementByCodeParam(
            codes: ['code1', 'code2'],
            saveLog: false,
        );

        $this->assertSame(['code1', 'code2'], $param->codes);
        $this->assertFalse($param->saveLog);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(GetDiyPageElementByCodeParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(GetDiyPageElementByCodeParam::class);

        $properties = ['codes', 'saveLog'];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isPublic(), "{$propertyName} should be public");
            $this->assertTrue($property->isReadOnly(), "{$propertyName} should be readonly");
        }
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(GetDiyPageElementByCodeParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        foreach ($constructor->getParameters() as $parameter) {
            $attrs = $parameter->getAttributes(MethodParam::class);
            $this->assertNotEmpty($attrs, "Parameter {$parameter->getName()} should have MethodParam attribute");
        }
    }
}
