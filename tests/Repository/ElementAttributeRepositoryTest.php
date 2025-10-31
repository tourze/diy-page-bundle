<?php

namespace DiyPageBundle\Tests\Repository;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Entity\ElementAttribute;
use DiyPageBundle\Repository\ElementAttributeRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(ElementAttributeRepository::class)]
#[RunTestsInSeparateProcesses]
final class ElementAttributeRepositoryTest extends AbstractRepositoryTestCase
{
    private ElementAttributeRepository $repository;

    public function testSaveWithFlush(): void
    {
        $element = $this->createTestElement('测试元素');

        $attribute = new ElementAttribute();
        $attribute->setElement($element);
        $attribute->setName('test-attribute');
        $attribute->setValue('test-value');

        $this->repository->save($attribute);

        $this->assertNotNull($attribute->getId());

        // 清理数据
        $this->repository->remove($attribute);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->flush();
    }

    public function testSaveWithoutFlush(): void
    {
        $element = $this->createTestElement('测试元素2');

        $attribute = new ElementAttribute();
        $attribute->setElement($element);
        $attribute->setName('test-attribute-2');
        $attribute->setValue('test-value-2');

        $this->repository->save($attribute, false);

        // 在不flush的情况下，ID可能为null
        self::getEntityManager()->flush();
        $this->assertNotNull($attribute->getId());

        // 清理数据
        $this->repository->remove($attribute);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->flush();
    }

    public function testFindByElementId(): void
    {
        $element = $this->createTestElement('查找测试元素');

        $attribute1 = new ElementAttribute();
        $attribute1->setElement($element);
        $attribute1->setName('attr1');
        $attribute1->setValue('value1');

        $attribute2 = new ElementAttribute();
        $attribute2->setElement($element);
        $attribute2->setName('attr2');
        $attribute2->setValue('value2');

        $this->repository->save($attribute1);
        $this->repository->save($attribute2);

        $attributes = $this->repository->findByElementId($element->getId());

        $this->assertCount(2, $attributes);
        $this->assertSame('attr1', $attributes[0]->getName());
        $this->assertSame('attr2', $attributes[1]->getName());

        // 清理数据
        $this->repository->remove($attribute1);
        $this->repository->remove($attribute2);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->flush();
    }

    public function testFindByElementIdAndName(): void
    {
        $element = $this->createTestElement('按名称查找测试');

        $attribute = new ElementAttribute();
        $attribute->setElement($element);
        $attribute->setName('specific-attr');
        $attribute->setValue('specific-value');

        $this->repository->save($attribute);

        $foundAttribute = $this->repository->findByElementIdAndName($element->getId(), 'specific-attr');

        $this->assertInstanceOf(ElementAttribute::class, $foundAttribute);
        $this->assertSame('specific-value', $foundAttribute->getValue());

        // 测试查找不存在的属性
        $notFoundAttribute = $this->repository->findByElementIdAndName($element->getId(), 'non-existent');
        $this->assertNull($notFoundAttribute);

        // 清理数据
        $this->repository->remove($attribute);
        self::getEntityManager()->remove($element);
        self::getEntityManager()->flush();
    }

    public function testDeleteByElementId(): void
    {
        $element = $this->createTestElement('删除测试元素');

        $attribute1 = new ElementAttribute();
        $attribute1->setElement($element);
        $attribute1->setName('delete-attr1');
        $attribute1->setValue('delete-value1');

        $attribute2 = new ElementAttribute();
        $attribute2->setElement($element);
        $attribute2->setName('delete-attr2');
        $attribute2->setValue('delete-value2');

        $this->repository->save($attribute1);
        $this->repository->save($attribute2);

        $deletedCount = $this->repository->deleteByElementId($element->getId());

        $this->assertSame(2, $deletedCount);

        $attributes = $this->repository->findByElementId($element->getId());
        $this->assertEmpty($attributes);

        // 清理数据
        self::getEntityManager()->remove($element);
        self::getEntityManager()->flush();
    }

    private static int $entityCounter = 0;

    protected function createNewEntity(): object
    {
        $element = $this->createTestElement('Create New Entity Element');

        $attribute = new ElementAttribute();
        $attribute->setElement($element);
        $uniqueId = hrtime(true) . '-' . (++self::$entityCounter) . '-' . random_int(10000, 99999);
        $attribute->setName('create-new-entity-attr-' . $uniqueId);
        $attribute->setValue('create-new-entity-value');

        return $attribute;
    }

    protected function onSetUp(): void
    {
        $this->repository = self::getService(ElementAttributeRepository::class);
    }

    /** @return ServiceEntityRepository<ElementAttribute> */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    private function createTestElement(string $title): Element
    {
        $block = new Block();
        $block->setTitle('测试广告位');
        $block->setCode('test-block-' . uniqid());
        $block->setValid(true);
        self::getEntityManager()->persist($block);

        $element = new Element();
        $element->setBlock($block);
        $element->setTitle($title);
        $element->setValid(true);
        $element->setThumb1('/test/image.jpg');
        self::getEntityManager()->persist($element);
        self::getEntityManager()->flush();

        return $element;
    }
}
