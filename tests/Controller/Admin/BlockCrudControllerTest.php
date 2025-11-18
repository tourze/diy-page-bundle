<?php

declare(strict_types=1);

namespace DiyPageBundle\Tests\Controller\Admin;

use DiyPageBundle\Controller\Admin\BlockCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(BlockCrudController::class)]
#[RunTestsInSeparateProcesses]
final class BlockCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): BlockCrudController
    {
        $controller = self::getService(BlockCrudController::class);
        self::assertInstanceOf(BlockCrudController::class, $controller);

        return $controller;
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '标题' => ['标题'];
        yield '唯一标志' => ['唯一标志'];
        yield '排序' => ['排序'];
        yield '有效状态' => ['有效状态'];
        yield '包含元素' => ['包含元素'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'title' => ['title'];
        yield 'code' => ['code'];
        yield 'typeId' => ['typeId'];
        yield 'sortNumber' => ['sortNumber'];
        yield 'valid' => ['valid'];
        yield 'beginTime' => ['beginTime'];
        yield 'endTime' => ['endTime'];
        yield 'showExpression' => ['showExpression'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        return self::provideNewPageFields();
    }

    public function testRequiredFieldValidation(): void
    {
        $client = $this->createAuthenticatedClient();

        // 提交空表单数据，验证必填字段错误
        $client->request('POST', '/admin/diy-page/block/new', [
            'Block' => [
                'title' => '',
                'code' => '',
                'typeId' => '',
                'sortNumber' => '0',
                'valid' => false,
            ],
        ]);

        // 应该返回422状态码并显示验证错误
        $this->assertResponseStatusCodeSame(422);
        $content = $client->getResponse()->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('should not be blank', $content);
    }
}
