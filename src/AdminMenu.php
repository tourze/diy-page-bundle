<?php

namespace DiyPageBundle;

use DiyPageBundle\Entity\Block;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

class AdminMenu implements MenuProviderInterface
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (!$item->getChild('装修中心')) {
            $item->addChild('装修中心');
        }
        $item->getChild('装修中心')->addChild('广告位')->setUri($this->linkGenerator->getCurdListPage(Block::class));
    }
}
