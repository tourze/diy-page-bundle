<?php

declare(strict_types=1);

namespace DiyPageBundle\Tests\Controller\Admin;

use DiyPageBundle\Controller\Admin\ElementAttributeCrudController;
use DiyPageBundle\Entity\ElementAttribute;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ElementAttributeCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ElementAttributeCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): ElementAttributeCrudController
    {
        $controller = self::getService(ElementAttributeCrudController::class);
        self::assertInstanceOf(ElementAttributeCrudController::class, $controller);

        return $controller;
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '所属元素' => ['所属元素'];
        yield '属性名' => ['属性名'];
        yield '属性值' => ['属性值'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'element' => ['element'];
        yield 'name' => ['name'];
        yield 'value' => ['value'];
        yield 'remark' => ['remark'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        return self::provideNewPageFields();
    }

    public function testGetEntityFqcn(): void
    {
        $controller = new ElementAttributeCrudController();
        $this->assertEquals(ElementAttribute::class, $controller::getEntityFqcn());
    }

    public function testRequiredFieldValidation(): void
    {
        $client = $this->createAuthenticatedClient();

        // 提交空表单数据，验证必填字段错误
        $client->request('POST', '/admin/diy-page/element-attribute/new', [
            'ElementAttribute' => [
                'name' => '',
                'value' => '',
                'remark' => '',
            ],
        ]);

        // 应该返回422状态码并显示验证错误
        $this->assertResponseStatusCodeSame(422);
        $content = $client->getResponse()->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('should not be blank', $content);
    }
}
