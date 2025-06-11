<?php

namespace DiyPageBundle\Procedure;

use Carbon\Carbon;
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

#[MethodTag('广告位模块')]
#[MethodDoc('传入指定的code，然后加载元素配置')]
#[MethodExpose('GetDiyPageElementByCode')]
#[WithMonologChannel('procedure')]
class GetDiyPageElementByCode extends CacheableProcedure
{
    #[MethodParam('多个code的集合')]
    public array $codes = [];

    #[MethodParam('是否保存日志')]
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

    public function execute(): array
    {
        if (empty($this->codes)) {
            throw new ApiException('请输入codes');
        }

        $blocks = $this->blockRepository->findBy([
            'code' => $this->codes,
            'valid' => true,
        ], ['sortNumber' => 'DESC', 'id' => 'ASC']);
        $result = [];

        $values = [
            'user' => $this->security->getUser(),
            'env' => $_ENV,
        ];

        foreach ($blocks as $block) {
            if ($block->getBeginTime() && $block->getEndTime()) {// 历史数据是没有配置的
                if (Carbon::now()->lessThan($block->getBeginTime())) {
                    continue;
                }
                if (Carbon::now()->greaterThan($block->getEndTime())) {
                    continue;
                }
            }

            $values['block'] = $block;

            // 如果有配置规则的话，我们判断下是否满足规则
            if (!empty($block->getShowExpression())) {
                try {
                    $checkRes = $this->engine->evaluate($block->getShowExpression(), $values);
                } catch (\Throwable $exception) {
                    $this->logger->error('广告位规则判断时发生异常', [
                        'exception' => $exception,
                        'block' => $block,
                        'expression' => $block->getShowExpression(),
                        'values' => $values,
                    ]);
                    continue;
                }

                if (!$checkRes) {
                    $this->logger->debug('广告位资格判断不通过', [
                        'block' => $block,
                    ]);
                    continue;
                }
            }

            // 格式化数据
            $event = new BlockDataFormatEvent();
            $event->setBlock($block);
            $blkArray = $this->normalizer->normalize($block, 'array', ['groups' => 'restful_read']);
            $event->setResult($blkArray);
            $this->eventDispatcher->dispatch($event);
            $blkArray = $event->getResult();
            if (null === $blkArray) {
                continue;
            }

            $validElements = [];
            foreach ($this->getValidElements($block) as $validElement) {
                if ($validElement->getBeginTime() && $validElement->getEndTime()) {// 历史数据是没有配置的
                    if (Carbon::now()->lessThan($validElement->getBeginTime())) {
                        continue;
                    }
                    if (Carbon::now()->greaterThan($validElement->getEndTime())) {
                        continue;
                    }
                }
                $values['element'] = $validElement;

                // 如果有配置规则的话，我们判断下是否满足规则
                if (!empty($validElement->getShowExpression())) {
                    try {
                        $checkRes = $this->engine->evaluate($validElement->getShowExpression(), $values);
                    } catch (\Throwable $exception) {
                        $this->logger->error('广告元素规则判断时发生异常', [
                            'exception' => $exception,
                            'element' => $validElement,
                            'expression' => $validElement->getShowExpression(),
                            'values' => $values,
                        ]);
                        continue;
                    }

                    if (!$checkRes) {
                        $this->logger->debug('广告元素资格判断不通过', [
                            'element' => $validElement,
                        ]);
                        continue;
                    }
                }

                $event = new ElementDataFormatEvent();
                $event->setElement($validElement);
                $eleArray = $this->normalizer->normalize($validElement, 'array', ['groups' => 'restful_read']);
                $event->setResult($eleArray);
                $this->eventDispatcher->dispatch($event);
                $eleArray = $event->getResult();
                if (null === $eleArray) {
                    continue;
                }

                $validElements[] = $eleArray;

                // 访问记录存到数据库
                if ($this->saveLog && $this->security->getUser()) {
                    $visitLog = new VisitLog();
                    $visitLog->setUser($this->security->getUser());
                    $visitLog->setBlock($block);
                    $visitLog->setElement($validElement);
                    $this->doctrineService->asyncInsert($visitLog);
                }
            }

            $blkArray['validElements'] = $validElements;

            $result[$block->getCode()] = $blkArray;
        }

        return $result;
    }

    /**
     * @return array|Element[]
     */
    private function getValidElements(Block $block): array
    {
        $elements = $block->getElements()
            ->filter(fn (Element $element) => (bool) $element->isValid())
            ->toArray();
        ArraySorter::multisort($elements, [
            fn (Element $element) => $element->getSortNumber(),
            fn (Element $element) => $element->getId(),
        ], [
            SORT_DESC,
            SORT_DESC,
        ]);

        return $elements;
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        if ($this->security->getUser()) {
            return '';
        }

        return parent::buildParamCacheKey($request->getParams());
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60;
    }

    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield null;
    }
}
