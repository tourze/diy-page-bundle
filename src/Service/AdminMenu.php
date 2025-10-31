<?php

namespace DiyPageBundle\Service;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\BlockAttribute;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Entity\ElementAttribute;
use DiyPageBundle\Entity\VisitLog;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * DIY页面装修管理菜单服务
 */
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('装修中心')) {
            $item->addChild('装修中心');
        }

        $decorationCenter = $item->getChild('装修中心');
        if (null === $decorationCenter) {
            return;
        }

        // 广告位管理菜单
        $decorationCenter->addChild('广告位管理')
            ->setUri($this->linkGenerator->getCurdListPage(Block::class))
            ->setAttribute('icon', 'fas fa-th-large')
        ;

        // 图片元素管理菜单
        $decorationCenter->addChild('图片元素管理')
            ->setUri($this->linkGenerator->getCurdListPage(Element::class))
            ->setAttribute('icon', 'fas fa-images')
        ;

        // 元素属性管理菜单
        $decorationCenter->addChild('元素属性管理')
            ->setUri($this->linkGenerator->getCurdListPage(ElementAttribute::class))
            ->setAttribute('icon', 'fas fa-tags')
        ;

        // 组件属性管理菜单
        $decorationCenter->addChild('组件属性管理')
            ->setUri($this->linkGenerator->getCurdListPage(BlockAttribute::class))
            ->setAttribute('icon', 'fas fa-cogs')
        ;

        // 访问日志菜单
        $decorationCenter->addChild('访问日志')
            ->setUri($this->linkGenerator->getCurdListPage(VisitLog::class))
            ->setAttribute('icon', 'fas fa-chart-line')
        ;
    }
}
