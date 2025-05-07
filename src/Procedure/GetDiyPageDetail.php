<?php

namespace DiyPageBundle\Procedure;

use DiyPageBundle\Repository\PageRepository;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag('广告位模块')]
#[MethodDoc('获取某个装修页')]
#[MethodExpose('GetDiyPageDetail')]
class GetDiyPageDetail extends BaseProcedure
{
    #[MethodParam('页面ID')]
    public string $pageId;

    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly GetDiyPageElementByCode $byCode,
    ) {
    }

    public function execute(): array
    {
        $page = $this->pageRepository->findOneBy([
            'id' => $this->pageId,
            'valid' => true,
        ]);
        if (!$page) {
            throw new ApiException('找不到装修页面');
        }

        $result = $this->normalizer->normalize($page, 'array', ['groups' => 'restful_read']);
        $result['blocks'] = [];

        foreach ($page->getValidBlocks() as $block) {
            $this->byCode->codes = [$block->getCode()];
            $tmp = $this->byCode->execute();
            if (!isset($tmp[$block->getCode()])) {
                continue;
            }
            $result['blocks'][] = $tmp[$block->getCode()];
        }

        return $result;
    }
}
