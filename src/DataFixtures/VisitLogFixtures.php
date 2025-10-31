<?php

namespace DiyPageBundle\DataFixtures;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Entity\VisitLog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use Tourze\UserServiceContracts\UserManagerInterface;

#[When(env: 'test')]
#[When(env: 'dev')]
class VisitLogFixtures extends Fixture implements DependentFixtureInterface
{
    public const VISIT_LOG_SAMPLE = 'visit-log-sample';

    public function __construct(
        private readonly UserManagerInterface $userManager,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $block = $this->getReference(BlockFixtures::BLOCK_SAMPLE, Block::class);
        assert($block instanceof Block);

        $element = $this->getReference(ElementFixtures::ELEMENT_SAMPLE, Element::class);
        assert($element instanceof Element);

        // 创建测试用户
        $user = $this->userManager->createUser('admin-test@localhost', 'Admin Test');
        $manager->persist($user);

        $visitLog = new VisitLog();
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $visitLog->setUser($user);
        $visitLog->setCreatedFromIp('192.168.1.100');
        $manager->persist($visitLog);

        $this->addReference(self::VISIT_LOG_SAMPLE, $visitLog);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BlockFixtures::class,
            ElementFixtures::class,
        ];
    }
}
