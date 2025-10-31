<?php

namespace DiyPageBundle\Tests\Service;

use DiyPageBundle\Service\AdminMenu;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $linkGenerator->method('getCurdListPage')->willReturn('/admin/list');

        self::getContainer()->set(LinkGeneratorInterface::class, $linkGenerator);
    }

    public function testMenuCreation(): void
    {
        $adminMenu = self::getService(AdminMenu::class);

        // 使用真实的 MenuFactory 而不是 mock
        $factory = new MenuFactory();
        $rootItem = new MenuItem('root', $factory);

        $adminMenu($rootItem);

        $this->assertGreaterThan(0, count($rootItem->getChildren()), 'Root item should have at least one child');

        $decorationCenter = $rootItem->getChild('装修中心');
        $this->assertNotNull($decorationCenter, '装修中心 menu should exist');
        $this->assertGreaterThan(0, count($decorationCenter->getChildren()), 'Decoration center menu should have children');

        // 验证所有子菜单项都存在
        $this->assertNotNull($decorationCenter->getChild('广告位管理'));
        $this->assertNotNull($decorationCenter->getChild('图片元素管理'));
        $this->assertNotNull($decorationCenter->getChild('元素属性管理'));
        $this->assertNotNull($decorationCenter->getChild('组件属性管理'));
        $this->assertNotNull($decorationCenter->getChild('访问日志'));
    }

    public function testMenuIcons(): void
    {
        $adminMenu = self::getService(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = new MenuItem('root', $factory);

        $adminMenu($rootItem);

        $decorationCenter = $rootItem->getChild('装修中心');
        $this->assertNotNull($decorationCenter);

        // 验证图标设置
        $blockMenu = $decorationCenter->getChild('广告位管理');
        $this->assertNotNull($blockMenu);
        $this->assertEquals('fas fa-th-large', $blockMenu->getAttribute('icon'));

        $elementMenu = $decorationCenter->getChild('图片元素管理');
        $this->assertNotNull($elementMenu);
        $this->assertEquals('fas fa-images', $elementMenu->getAttribute('icon'));

        $elementAttributeMenu = $decorationCenter->getChild('元素属性管理');
        $this->assertNotNull($elementAttributeMenu);
        $this->assertEquals('fas fa-tags', $elementAttributeMenu->getAttribute('icon'));

        $blockAttributeMenu = $decorationCenter->getChild('组件属性管理');
        $this->assertNotNull($blockAttributeMenu);
        $this->assertEquals('fas fa-cogs', $blockAttributeMenu->getAttribute('icon'));

        $visitLogMenu = $decorationCenter->getChild('访问日志');
        $this->assertNotNull($visitLogMenu);
        $this->assertEquals('fas fa-chart-line', $visitLogMenu->getAttribute('icon'));
    }
}
