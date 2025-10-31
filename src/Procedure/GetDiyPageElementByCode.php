<?php

namespace DiyPageBundle\Procedure;

use Carbon\CarbonImmutable;
use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Entity\VisitLog;
use DiyPageBundle\Event\BlockDataFormatEvent;
use DiyPageBundle\Event\ElementDataFormatEvent;
use DiyPageBundle\Repository\BlockRepository;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;
use Tourze\EcolBundle\Service\Engine;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;
use Yiisoft\Arrays\ArraySorter;

#[MethodTag(name: '广告位模块')]
#[MethodDoc(summary: '传入指定的code，然后加载元素配置')]
#[MethodExpose(method: 'GetDiyPageElementByCode')]
#[WithMonologChannel(channel: 'procedure')]
class GetDiyPageElementByCode extends CacheableProcedure
{
    /**
     * @var array<string>
     */
    #[MethodParam(description: '多个code的集合')]
    public array $codes = [];

    #[MethodParam(description: '是否保存日志')]
    public bool $saveLog = true;

    public function __construct(
        private readonly BlockRepository $blockRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly Engine $engine,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly DoctrineService $doctrineService,
        private readonly LoggerInterface $logger,
        private readonly Security $security,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        if (0 === count($this->codes)) {
            throw new ApiException('请输入codes');
        }

        $blocks = $this->blockRepository->findBy([
            'code' => $this->codes,
            'valid' => true,
        ], ['sortNumber' => 'DESC', 'id' => 'ASC']);

        $values = [
            'user' => $this->security->getUser(),
            'env' => $_ENV,
        ];

        $result = [];
        foreach ($blocks as $block) {
            $blockArray = $this->processBlock($block, $values);
            if (null !== $blockArray) {
                $result[$block->getCode()] = $blockArray;
            }
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>|null
     */
    private function processBlock(Block $block, array $values): ?array
    {
        if (!$this->isBlockTimeValid($block)) {
            return null;
        }

        $values['block'] = $block;

        if (!$this->evaluateBlockExpression($block, $values)) {
            return null;
        }

        $blockArray = $this->formatBlockData($block);
        if (null === $blockArray) {
            return null;
        }

        $validElements = $this->processElements($block, $values);
        $blockArray['validElements'] = $validElements;

        return $blockArray;
    }

    private function isBlockTimeValid(Block $block): bool
    {
        if (null === $block->getBeginTime() || null === $block->getEndTime()) {
            return true;
        }

        $now = CarbonImmutable::now();

        return !$now->lessThan($block->getBeginTime()) && !$now->greaterThan($block->getEndTime());
    }

    /**
     * @param array<string, mixed> $values
     */
    private function evaluateBlockExpression(Block $block, array $values): bool
    {
        if (null === $block->getShowExpression() || '' === $block->getShowExpression()) {
            return true;
        }

        try {
            $checkRes = $this->engine->evaluate($block->getShowExpression(), $values);
        } catch (\Throwable $exception) {
            $this->logger->error('广告位规则判断时发生异常', [
                'exception' => $exception,
                'block' => $block,
                'expression' => $block->getShowExpression(),
                'values' => $values,
            ]);

            return false;
        }

        if (false === $checkRes) {
            $this->logger->debug('广告位资格判断不通过', [
                'block' => $block,
            ]);

            return false;
        }

        return true;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function formatBlockData(Block $block): ?array
    {
        $event = new BlockDataFormatEvent();
        $event->setBlock($block);
        $blkArray = $this->normalizer->normalize($block, 'array', ['groups' => 'restful_read']);
        if (is_array($blkArray)) {
            /** @var array<string, mixed> $blkArray */
            $event->setResult($blkArray);
        }
        $this->eventDispatcher->dispatch($event);

        return $event->getResult();
    }

    /**
     * @param array<string, mixed> $values
     * @return array<mixed>
     */
    private function processElements(Block $block, array $values): array
    {
        $validElements = [];
        foreach ($this->getValidElements($block) as $validElement) {
            $elementArray = $this->processElement($validElement, $block, $values);
            if (null !== $elementArray) {
                $validElements[] = $elementArray;
            }
        }

        return $validElements;
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>|null
     */
    private function processElement(Element $element, Block $block, array $values): ?array
    {
        if (!$this->isElementTimeValid($element)) {
            return null;
        }

        $values['element'] = $element;

        if (!$this->evaluateElementExpression($element, $values)) {
            return null;
        }

        $elementArray = $this->formatElementData($element);
        if (null === $elementArray) {
            return null;
        }

        $this->saveVisitLog($block, $element);

        return $elementArray;
    }

    private function isElementTimeValid(Element $element): bool
    {
        if (null === $element->getBeginTime() || null === $element->getEndTime()) {
            return true;
        }

        $now = CarbonImmutable::now();

        return !$now->lessThan($element->getBeginTime()) && !$now->greaterThan($element->getEndTime());
    }

    /**
     * @param array<string, mixed> $values
     */
    private function evaluateElementExpression(Element $element, array $values): bool
    {
        if (null === $element->getShowExpression() || '' === $element->getShowExpression()) {
            return true;
        }

        try {
            $checkRes = $this->engine->evaluate($element->getShowExpression(), $values);
        } catch (\Throwable $exception) {
            $this->logger->error('广告元素规则判断时发生异常', [
                'exception' => $exception,
                'element' => $element,
                'expression' => $element->getShowExpression(),
                'values' => $values,
            ]);

            return false;
        }

        if (false === $checkRes) {
            $this->logger->debug('广告元素资格判断不通过', [
                'element' => $element,
            ]);

            return false;
        }

        return true;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function formatElementData(Element $element): ?array
    {
        $event = new ElementDataFormatEvent();
        $event->setElement($element);
        $eleArray = $this->normalizer->normalize($element, 'array', ['groups' => 'restful_read']);
        if (is_array($eleArray)) {
            /** @var array<string, mixed> $eleArray */
            $event->setResult($eleArray);
        }
        $this->eventDispatcher->dispatch($event);

        $result = $event->getResult();
        if (null === $result) {
            return null;
        }
        $result['elementId'] = $element->getId();

        return $result;
    }

    private function saveVisitLog(Block $block, Element $element): void
    {
        if (!$this->saveLog || null === $this->security->getUser()) {
            return;
        }

        $visitLog = new VisitLog();
        $visitLog->setUser($this->security->getUser());
        $visitLog->setBlock($block);
        $visitLog->setElement($element);
        $this->doctrineService->asyncInsert($visitLog);
    }

    /**
     * @return array<Element>
     */
    private function getValidElements(Block $block): array
    {
        $allElements = $block->getElements();

        /** @var array<Element> $validElements */
        $validElements = [];

        foreach ($allElements as $element) {
            if ($element instanceof Element && true === $element->isValid()) {
                $validElements[] = $element;
            }
        }

        ArraySorter::multisort($validElements, [
            fn (Element $element) => $element->getSortNumber(),
            fn (Element $element) => $element->getId(),
        ], [
            SORT_DESC,
            SORT_DESC,
        ]);

        /** @var array<Element> $validElements */
        return $validElements;
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        if (null !== $this->security->getUser()) {
            return '';
        }

        $params = $request->getParams();
        if (null === $params) {
            return '';
        }

        return parent::buildParamCacheKey($params);
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60;
    }

    /**
     * @return iterable<string>
     */
    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        return [];
    }
}
