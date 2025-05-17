<?php

namespace DiyPageBundle\Tests\Repository;

use DiyPageBundle\Repository\ElementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class ElementRepositoryTest extends TestCase
{
    private ElementRepository $repository;
    private ManagerRegistry $registry;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getManagerForClass')
            ->willReturn($this->entityManager);
        
        $this->repository = new ElementRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(ElementRepository::class, $this->repository);
    }
} 