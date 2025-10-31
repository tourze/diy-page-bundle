<?php

namespace DiyPageBundle\DataFixtures;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\BlockAttribute;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class BlockAttributeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $block = $this->getReference(BlockFixtures::BLOCK_SAMPLE, Block::class);
        assert($block instanceof Block);

        $attribute = new BlockAttribute();
        $attribute->setBlock($block);
        $attribute->setName('background_color');
        $attribute->setValue('#ffffff');
        $attribute->setRemark('背景颜色配置');
        $manager->persist($attribute);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BlockFixtures::class,
        ];
    }
}
