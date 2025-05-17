<?php

namespace DiyPageBundle\Tests\Repository;

use DiyPageBundle\Repository\BlockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class BlockRepositoryTest extends TestCase
{
    private BlockRepository $repository;
    private ManagerRegistry $registry;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getManagerForClass')
            ->willReturn($this->entityManager);
        
        $this->repository = new BlockRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(BlockRepository::class, $this->repository);
    }
} 