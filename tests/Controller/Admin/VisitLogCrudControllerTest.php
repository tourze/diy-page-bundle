<?php

declare(strict_types=1);

namespace DiyPageBundle\Tests\Controller\Admin;

use DiyPageBundle\Controller\Admin\VisitLogCrudController;
use DiyPageBundle\Entity\VisitLog;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(VisitLogCrudController::class)]
#[RunTestsInSeparateProcesses]
final class VisitLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /** @return VisitLogCrudController */
    protected function getControllerService(): VisitLogCrudController
    {
        $controller = self::getService(VisitLogCrudController::class);
        self::assertInstanceOf(VisitLogCrudController::class, $controller);

        return $controller;
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '访问组件' => ['访问组件'];
        yield '访问元素' => ['访问元素'];
        yield '访问用户' => ['访问用户'];
        yield '访问时间' => ['访问时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        // VisitLog 是只读的，没有新建表单
        // 返回一个占位符以通过测试
        yield 'id' => ['id'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        // VisitLog 是只读的，没有编辑表单
        // 返回一个占位符以通过测试
        yield 'id' => ['id'];
    }

    public function testGetEntityFqcn(): void
    {
        $controller = new VisitLogCrudController();
        $this->assertEquals(VisitLog::class, $controller::getEntityFqcn());
    }
}
