<?php

namespace DiyPageBundle\DataFixtures;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class BasicBlockFixtures extends Fixture
{
    public const BLOCK_INDEX_BANNER = 'block-index-banner';

    public function load(ObjectManager $manager): void
    {
        // 创建block
        $block = new Block();
        $block->setValid(true);
        $block->setCode('index-banner');
        $block->setTitle('首页-Banner');
        $manager->persist($block);

        // 保存reference供其他fixture使用
        $this->addReference(self::BLOCK_INDEX_BANNER, $block);

        // 创建element
        $element = new Element();
        $element->setBlock($block);
        $element->setTitle('日常抽奖活动');
        $element->setValid(true);
        $element->setPath('/pages/index/index');
        $element->setThumb1('https://arvatorc.blob.core.chinacloudapi.cn/rcminipicture/pc15602380156089.jpg');
        $manager->persist($element);

        $manager->flush();
    }
}
