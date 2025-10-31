<?php

declare(strict_types=1);

namespace DiyPageBundle\Tests\Controller\Admin;

use DiyPageBundle\Controller\Admin\ElementCrudController;
use DiyPageBundle\Entity\Element;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(ElementCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ElementCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /** @return ElementCrudController */
    protected function getControllerService(): ElementCrudController
    {
        $controller = self::getService(ElementCrudController::class);
        self::assertInstanceOf(ElementCrudController::class, $controller);

        return $controller;
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '所属组件' => ['所属组件'];
        yield '标题' => ['标题'];
        yield '主图' => ['主图'];
        yield '排序' => ['排序'];
        yield '有效状态' => ['有效状态'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        yield 'block' => ['block'];
        yield 'title' => ['title'];
        yield 'subtitle' => ['subtitle'];
        yield 'description' => ['description'];
        yield 'thumb1' => ['thumb1'];
        yield 'thumb2' => ['thumb2'];
        yield 'path' => ['path'];
        yield 'appId' => ['appId'];
        yield 'sortNumber' => ['sortNumber'];
        yield 'valid' => ['valid'];
        yield 'loginJumpPage' => ['loginJumpPage'];
        yield 'beginTime' => ['beginTime'];
        yield 'endTime' => ['endTime'];
        yield 'showExpression' => ['showExpression'];
        yield 'tracking' => ['tracking'];
        yield 'showTagsString' => ['showTagsString'];
        yield 'subscribeTemplateIdsString' => ['subscribeTemplateIdsString'];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        return self::provideNewPageFields();
    }

    public function testGetEntityFqcn(): void
    {
        $controller = new ElementCrudController();
        $this->assertEquals(Element::class, $controller::getEntityFqcn());
    }
}
