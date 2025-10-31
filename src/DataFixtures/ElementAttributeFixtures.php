<?php

namespace DiyPageBundle\DataFixtures;

use DiyPageBundle\Entity\Element;
use DiyPageBundle\Entity\ElementAttribute;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class ElementAttributeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $element = $this->getReference(ElementFixtures::ELEMENT_SAMPLE, Element::class);
        assert($element instanceof Element);

        $attribute = new ElementAttribute();
        $attribute->setElement($element);
        $attribute->setName('custom_field');
        $attribute->setValue('custom_value');
        $attribute->setRemark('自定义属性配置');
        $manager->persist($attribute);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ElementFixtures::class,
        ];
    }
}
