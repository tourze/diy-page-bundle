<?php

namespace DiyPageBundle\DataFixtures;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class ElementFixtures extends Fixture implements DependentFixtureInterface
{
    public const ELEMENT_SAMPLE = 'element-sample';

    public function load(ObjectManager $manager): void
    {
        $block = $this->getReference(BlockFixtures::BLOCK_SAMPLE, Block::class);
        assert($block instanceof Block);

        $element = new Element();
        $element->setBlock($block);
        $element->setTitle('示例元素');
        $element->setSubtitle('副标题');
        $element->setDescription('这是一个示例元素的描述');
        $element->setThumb1('/uploads/sample-thumb.jpg');
        $element->setPath('/sample-path');
        $element->setValid(true);
        $element->setSortNumber(1);
        $element->setLoginJumpPage(false);
        $element->setSubscribeTemplateIds([]);
        $manager->persist($element);

        $this->addReference(self::ELEMENT_SAMPLE, $element);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BlockFixtures::class,
        ];
    }
}
