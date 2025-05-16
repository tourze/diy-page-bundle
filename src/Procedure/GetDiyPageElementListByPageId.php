<?php

namespace DiyPageBundle\Procedure;

use DiyPageBundle\Entity\Block;
use DiyPageBundle\Entity\Element;
use DiyPageBundle\Repository\PageRepository;
use Doctrine\Common\Collections\Collection;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag('广告位模块')]
#[MethodDoc('获取单个page下所有数据')]
#[MethodExpose('GetDiyPageElementListByPageId')]
class GetDiyPageElementListByPageId extends BaseProcedure
{
    #[MethodParam('id')]
    public int $id;

    public function __construct(private readonly PageRepository $pageRepository)
    {
    }

    public function execute(): array
    {
        $page = $this->pageRepository->findOneBy([
            'id' => $this->id,
        ]);
        if (!$page) {
            throw new ApiException('记录不存在~');
        }

        $data = [
            'id' => $page->getId(),
            'title' => $page->getTitle(),
            'defaultThumb' => $page->getDefaultThumb(),
            'activeThumb' => $page->getActiveThumb(),
            'blocks' => [],
        ];
        /** @var Collection<int, Block> $blocks */
        $blocks = $page->getBlocks();
        if ($blocks->count() > 0) {
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
                    ];

                    $blockData['elements'][] = $elementData;
                }
                $data['blocks'][] = $blockData;
            }
        }

        return $data;
    }
}
