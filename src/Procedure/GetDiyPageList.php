<?php

namespace DiyPageBundle\Procedure;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Entity\Page;
use DiyPageBundle\Entity\Point;
use DiyPageBundle\Repository\PageRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\SecurityBundle\Security;
use Tourze\DoctrineHelper\CacheHelper;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;

#[MethodTag('广告位模块')]
#[MethodDoc('获取page列表')]
#[MethodExpose('GetDiyPagePageList')]
class GetDiyPageList extends CacheableProcedure
{
    use PaginatorTrait;

    #[MethodParam('是否推荐')]
    public ?bool $isRecommend = null;

    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $query = $this->pageRepository->createQueryBuilder('p')
            ->where('p.valid=:valid')
            ->setParameter('valid', true);

        if (null !== $this->isRecommend) {
            $query->andWhere('p.recommend =:recommend')
                ->setParameter('recommend', $this->isRecommend);
        }

        $query->addOrderBy('p.sortNumber', Criteria::DESC);
        $query->addOrderBy('p.createTime', Criteria::DESC);

        try {
            return $this->fetchList($query, $this->formatItem(...));
        } catch (\Throwable $exception) {
            throw new ApiException($exception->getMessage(), previous: $exception);
        }
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        $key = static::buildParamCacheKey($request->getParams());
        if ($this->security->getUser()) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return $key;
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60 * 5;
    }

    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Page::class);
        yield CacheHelper::getClassTags(Block::class);
        yield CacheHelper::getClassTags(Element::class);
        yield CacheHelper::getClassTags(Point::class);
    }

    private function formatItem(Page $page): array
    {
        $data = [
            'id' => $page->getId(),
            'title' => $page->getTitle(),
            'defaultThumb' => $page->getDefaultThumb(),
            'activeThumb' => $page->getActiveThumb(),
            'blocks' => [],
        ];
        /** @var Block[] $blocks */
        $blocks = $page->getBlocks();
        foreach ($blocks as $block) {
            $blockData = [
                'id' => $block->getId(),
                'title' => $block->getTitle(),
                'code' => $block->getCode(),
                'typeId' => $block->getTypeId(),
                'elements' => [],
            ];
            $elements = $block->getElements();
            /** @var Element $element */
            foreach ($elements as $element) {
                $elementData = [
                    'id' => $element->getId(),
                    'title' => $element->getTitle(),
                    'thumb1' => $element->getThumb1(),
                    'thumb2' => $element->getThumb2(),
                    'path' => $element->getPath(),
                    'appId' => $element->getAppId(),
                    'tracking' => $element->getTracking(),
                    'description' => $element->getDescription(),
                    'points' => [],
                ];
                $points = $element->getPoints();
                /** @var Point $point */
                foreach ($points as $point) {
                    $elementData['points'][] = [
                        'id' => $point->getId(),
                        'title' => $point->getTitle(),
                        'thumb' => $point->getThumb(),
                        'xAxis' => $point->getXAxis(),
                        'yAxis' => $point->getYAxis(),
                        'appId' => $point->getAppId(),
                        'path' => $point->getPath(),
                    ];
                }
                $blockData['elements'][] = $elementData;
            }
            $data['blocks'][] = $blockData;
        }

        return $data;
    }
}
