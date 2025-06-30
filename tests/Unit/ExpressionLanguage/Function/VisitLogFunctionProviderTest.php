<?php

namespace DiyPageBundle\Tests\Unit\ExpressionLanguage\Function;

use DiyPageBundle\Entity\Element;
use DiyPageBundle\ExpressionLanguage\Function\VisitLogFunctionProvider;
use DiyPageBundle\Repository\VisitLogRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class VisitLogFunctionProviderTest extends TestCase
{
    private VisitLogFunctionProvider $provider;
    private LoggerInterface|MockObject $logger;
    private VisitLogRepository|MockObject $visitLogRepository;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->visitLogRepository = $this->createMock(VisitLogRepository::class);
        $this->provider = new VisitLogFunctionProvider($this->logger, $this->visitLogRepository);
    }

    public function testGetFunctions(): void
    {
        $functions = $this->provider->getFunctions();
        $this->assertCount(1, $functions);
        $this->assertSame('getDiyPageElementTodayVisitCount', $functions[0]->getName());
    }

    public function testGetDiyPageElementTodayVisitCountWithNullUser(): void
    {
        $result = $this->provider->getDiyPageElementTodayVisitCount([], null, new Element());
        $this->assertSame(0, $result);
    }

    public function testGetDiyPageElementTodayVisitCountWithUser(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('test@example.com');
        
        $element = $this->createMock(Element::class);
        $element->method('getId')->willReturn('123');

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $query = $this->createMock(Query::class);
        
        $this->visitLogRepository
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->with('a')
            ->willReturn($queryBuilder);

        $queryBuilder->method('select')->willReturn($queryBuilder);
        $queryBuilder->method('where')->willReturn($queryBuilder);
        $queryBuilder->method('setParameter')->willReturn($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($query);
        
        $query->method('getSingleScalarResult')->willReturn('5');

        $this->logger
            ->expects($this->once())
            ->method('info')
            ->with($this->stringContains('消费者[test@example.com]已经看了广告[123] 5 次'));

        $result = $this->provider->getDiyPageElementTodayVisitCount([], $user, $element);
        $this->assertSame(5, $result);
    }
}