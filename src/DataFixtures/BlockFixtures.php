<?php

namespace DiyPageBundle\DataFixtures;

use DiyPageBundle\Entity\Block;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class BlockFixtures extends Fixture
{
    public const BLOCK_SAMPLE = 'block-sample';

    public function load(ObjectManager $manager): void
    {
        $block = new Block();
        $block->setValid(true);
        $block->setCode('sample-block');
        $block->setTitle('示例广告位');
        $block->setSortNumber(1);
        $manager->persist($block);

        $this->addReference(self::BLOCK_SAMPLE, $block);

        $manager->flush();
    }
}
