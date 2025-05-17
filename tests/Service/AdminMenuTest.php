<?php

namespace DiyPageBundle\Tests\Service;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Service\AdminMenu;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

class AdminMenuTest extends TestCase
{
    private AdminMenu $adminMenu;
    private LinkGeneratorInterface $linkGenerator;

    protected function setUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->adminMenu = new AdminMenu($this->linkGenerator);
    }

    public function testInvoke_withNoExistingChild(): void
    {
        // 创建对象
        $childItem = $this->createMock(ItemInterface::class);
        $childItem->expects($this->once())
            ->method('setUri')
            ->willReturnSelf();
        
        $decorationCenterItem = $this->createMock(ItemInterface::class);
        $decorationCenterItem->expects($this->once())
            ->method('addChild')
            ->with('广告位')
            ->willReturn($childItem);
            
        $item = $this->createMock(ItemInterface::class);
        $item->method('getChild')
            ->with('装修中心')
            ->willReturnOnConsecutiveCalls(null, $decorationCenterItem);
        $item->expects($this->once())
            ->method('addChild')
            ->with('装修中心')
            ->willReturn($decorationCenterItem);
            
        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->with(Block::class)
            ->willReturn('/admin/block/list');
            
        // 执行测试
        ($this->adminMenu)($item);
    }
    
    public function testInvoke_withExistingChild(): void
    {
        // 创建对象
        $childItem = $this->createMock(ItemInterface::class);
        $childItem->expects($this->once())
            ->method('setUri')
            ->willReturnSelf();
        
        $decorationCenterItem = $this->createMock(ItemInterface::class);
        $decorationCenterItem->expects($this->once())
            ->method('addChild')
            ->with('广告位')
            ->willReturn($childItem);
            
        $item = $this->createMock(ItemInterface::class);
        $item->method('getChild')
            ->with('装修中心')
            ->willReturn($decorationCenterItem);
        $item->expects($this->never())
            ->method('addChild');
            
        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->with(Block::class)
            ->willReturn('/admin/block/list');
            
        // 执行测试
        ($this->adminMenu)($item);
    }
} 