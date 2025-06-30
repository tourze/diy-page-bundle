<?php

namespace DiyPageBundle\Tests\Unit\Procedure;

use Carbon\CarbonImmutable;
use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Procedure\GetDiyPageElementByCode;
use DiyPageBundle\Repository\BlockRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;
use Tourze\EcolBundle\Service\Engine;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;

class GetDiyPageElementByCodeTest extends TestCase
{
    private GetDiyPageElementByCode $procedure;
    private BlockRepository|MockObject $blockRepository;
    private NormalizerInterface|MockObject $normalizer;
    private Engine|MockObject $engine;
    private EventDispatcherInterface|MockObject $eventDispatcher;
    private AsyncInsertService|MockObject $doctrineService;
    private LoggerInterface|MockObject $logger;
    private Security|MockObject $security;

    protected function setUp(): void
    {
        $this->blockRepository = $this->createMock(BlockRepository::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->engine = $this->createMock(Engine::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->doctrineService = $this->createMock(AsyncInsertService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->security = $this->createMock(Security::class);

        $this->procedure = new GetDiyPageElementByCode(
            $this->blockRepository,
            $this->normalizer,
            $this->engine,
            $this->eventDispatcher,
            $this->doctrineService,
            $this->logger,
            $this->security
        );
    }

    public function testExecuteThrowsExceptionWhenCodesEmpty(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('请输入codes');

        $this->procedure->codes = [];
        $this->procedure->execute();
    }

    public function testGetCacheKeyReturnsEmptyStringWhenUserExists(): void
    {
        $request = $this->createMock(JsonRpcRequest::class);
        $user = $this->createMock(UserInterface::class);
        $this->security->method('getUser')->willReturn($user);

        $result = $this->procedure->getCacheKey($request);
        $this->assertSame('', $result);
    }

    public function testGetCacheDuration(): void
    {
        $request = $this->createMock(JsonRpcRequest::class);
        $this->assertSame(60, $this->procedure->getCacheDuration($request));
    }

    public function testGetCacheTags(): void
    {
        $request = $this->createMock(JsonRpcRequest::class);
        $tags = iterator_to_array($this->procedure->getCacheTags($request));
        $this->assertSame([null], $tags);
    }

    public function testSaveLogProperty(): void
    {
        $this->assertTrue($this->procedure->saveLog);
        $this->procedure->saveLog = false;
        $this->assertFalse($this->procedure->saveLog);
    }

    public function testCodesProperty(): void
    {
        $codes = ['test1', 'test2'];
        $this->procedure->codes = $codes;
        $this->assertSame($codes, $this->procedure->codes);
    }
}