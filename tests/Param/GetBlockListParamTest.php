<?php

declare(strict_types=1);

namespace DiyPageBundle\Tests\Param;

use DiyPageBundle\Param\GetBlockListParam;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * GetBlockListParam 单元测试
 *
 * @internal
 */
#[CoversClass(GetBlockListParam::class)]
final class GetBlockListParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new GetBlockListParam();

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithDefaultParameters(): void
    {
        $param = new GetBlockListParam();

        $this->assertNull($param->validOnly);
        $this->assertNull($param->typeId);
        $this->assertSame(1, $param->page);
        $this->assertSame(20, $param->limit);
    }

    public function testConstructorWithAllParameters(): void
    {
        $param = new GetBlockListParam(
            validOnly: true,
            typeId: 'type-123',
            page: 2,
            limit: 50,
        );

        $this->assertTrue($param->validOnly);
        $this->assertSame('type-123', $param->typeId);
        $this->assertSame(2, $param->page);
        $this->assertSame(50, $param->limit);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(GetBlockListParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(GetBlockListParam::class);

        $properties = ['validOnly', 'typeId', 'page', 'limit'];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isPublic(), "{$propertyName} should be public");
            $this->assertTrue($property->isReadOnly(), "{$propertyName} should be readonly");
        }
    }

    public function testValidationFailsWhenPageIsNegative(): void
    {
        $param = new GetBlockListParam(page: -1);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($param);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testValidationFailsWhenLimitIsZero(): void
    {
        $param = new GetBlockListParam(limit: 0);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($param);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testValidationPassesWithValidParameters(): void
    {
        $param = new GetBlockListParam(page: 1, limit: 20);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator()
        ;

        $violations = $validator->validate($param);

        $this->assertCount(0, $violations);
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(GetBlockListParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        foreach ($constructor->getParameters() as $parameter) {
            $attrs = $parameter->getAttributes(MethodParam::class);
            $this->assertNotEmpty($attrs, "Parameter {$parameter->getName()} should have MethodParam attribute");
        }
    }
}
