<?php

declare(strict_types=1);

namespace DiyPageBundle\Tests\Param;

use DiyPageBundle\Param\GetOneDiyPageElementParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * GetOneDiyPageElementParam 单元测试
 *
 * @internal
 */
#[CoversClass(GetOneDiyPageElementParam::class)]
final class GetOneDiyPageElementParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new GetOneDiyPageElementParam(elementId: 1);

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithRequiredParameter(): void
    {
        $param = new GetOneDiyPageElementParam(elementId: 123);

        $this->assertSame(123, $param->elementId);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(GetOneDiyPageElementParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(GetOneDiyPageElementParam::class);

        $properties = ['elementId'];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isPublic(), "{$propertyName} should be public");
            $this->assertTrue($property->isReadOnly(), "{$propertyName} should be readonly");
        }
    }

    public function testValidationPassesWithPositiveElementId(): void
    {
        $param = new GetOneDiyPageElementParam(elementId: 1);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($param);

        $this->assertCount(0, $violations);
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(GetOneDiyPageElementParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        foreach ($constructor->getParameters() as $parameter) {
            $attrs = $parameter->getAttributes(MethodParam::class);
            $this->assertNotEmpty($attrs, "Parameter {$parameter->getName()} should have MethodParam attribute");
        }
    }
}
