<?php

namespace DiyPageBundle\DataFixtures;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Repository\BlockRepository;
use DiyPageBundle\Repository\ElementRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BasicBlockFixture extends Fixture
{
    public function __construct(
        private readonly BlockRepository $blockRepository,
        private readonly ElementRepository $elementRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 保存block
        $block = $this->blockRepository->findOneBy(['code' => 'index-banner']);
        if ($block === null) {
            $block = new Block();
            $block->setValid(true);
            $block->setCode('index-banner');
            $block->setTitle('首页-Banner');
        }

        $manager->persist($block);

        // 保存element
        $elements = $this->elementRepository->findBy(['block' => $block]);
        if (empty($elements)) {
            $element = new Element();
            $element->setBlock($block);
            $element->setTitle('日常抽奖活动');
            $element->setValid(true);
            $element->setPath('/pages/index/index');
            $element->setThumb1('https://arvatorc.blob.core.chinacloudapi.cn/rcminipicture/pc15602380156089.jpg');
            $manager->persist($element);
        }

        $manager->flush();
    }
}
