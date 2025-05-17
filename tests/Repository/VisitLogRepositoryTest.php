<?php

namespace DiyPageBundle\Tests\Repository;

use DiyPageBundle\Repository\VisitLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class VisitLogRepositoryTest extends TestCase
{
    private VisitLogRepository $repository;
    private ManagerRegistry $registry;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->registry->method('getManagerForClass')
            ->willReturn($this->entityManager);
        
        $this->repository = new VisitLogRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(VisitLogRepository::class, $this->repository);
    }
} 