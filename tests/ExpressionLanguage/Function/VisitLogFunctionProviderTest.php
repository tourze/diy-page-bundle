<?php

namespace DiyPageBundle\Tests\ExpressionLanguage\Function;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\ExpressionLanguage\Function\VisitLogFunctionProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(VisitLogFunctionProvider::class)]
#[RunTestsInSeparateProcesses]
final class VisitLogFunctionProviderTest extends AbstractIntegrationTestCase
{
    private VisitLogFunctionProvider $provider;

    public function testGetFunctions(): void
    {
        $functions = $this->provider->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertInstanceOf(ExpressionFunction::class, $functions[0]);
        $this->assertSame('getDiyPageElementTodayVisitCount', $functions[0]->getName());
    }

    public function testGetDiyPageElementTodayVisitCountWithNullUser(): void
    {
        $block = new Block();
        $block->setTitle('Test Block');
        $block->setCode('test-block');
        $block->setValid(true);
        $block->setSortNumber(1);
        $block->setTypeId('1');

        $element = new Element();
        $element->setTitle('Test Element');
        $element->setValid(true);
        $element->setSortNumber(1);
        $element->setThumb1('https://example.com/test.jpg');
        $element->setBlock($block);

        $result = $this->provider->getDiyPageElementTodayVisitCount([], null, $element);

        $this->assertSame(0, $result);
    }

    public function testGetDiyPageElementTodayVisitCountWithNullElement(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $result = $this->provider->getDiyPageElementTodayVisitCount([], $user, null);

        $this->assertSame(0, $result);
    }

    public function testGetDiyPageElementTodayVisitCountWithValidParameters(): void
    {
        $user = $this->createNormalUser('test@example.com', 'password123');

        $block = new Block();
        $block->setTitle('Test Block');
        $block->setCode('test-block');
        $block->setValid(true);
        $block->setSortNumber(1);
        $block->setTypeId('1');

        $element = new Element();
        $element->setTitle('Test Element');
        $element->setValid(true);
        $element->setSortNumber(1);
        $element->setThumb1('https://example.com/test.jpg');
        $element->setBlock($block);

        $entityManager = self::getEntityManager();
        $entityManager->persist($block);
        $entityManager->persist($element);
        $entityManager->flush();

        $result = $this->provider->getDiyPageElementTodayVisitCount([], $user, $element);

        $this->assertSame(0, $result);
    }

    protected function onSetUp(): void
    {
        $this->provider = self::getService(VisitLogFunctionProvider::class);
    }
}
