<?php

namespace DiyPageBundle\Procedure;

use DiyPageBundle\Entity\Element;
use DiyPageBundle\Repository\ElementRepository;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag(name: '广告位模块')]
#[MethodDoc(summary: '获取某个配置的具体信息')]
#[MethodExpose(method: 'GetOneDiyPageElement')]
class GetOneDiyPageElement extends BaseProcedure
{
    #[MethodParam(description: '需要查询的元素ID')]
    public int $elementId;

    public function __construct(
        private readonly ElementRepository $elementRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        // 查找元素
        $element = $this->elementRepository->find($this->elementId);

        if (!$element instanceof Element) {
            throw new ApiException('元素不存在', 404);
        }

        // 检查元素是否有效
        if (true !== $element->isValid()) {
            throw new ApiException('元素已禁用', 403);
        }

        // 检查时间范围
        $now = new \DateTimeImmutable();

        // 检查开始时间
        if (null !== $element->getBeginTime() && $element->getBeginTime() > $now) {
            throw new ApiException('元素尚未生效', 403);
        }

        // 检查结束时间
        if (null !== $element->getEndTime() && $element->getEndTime() < $now) {
            throw new ApiException('元素已过期', 403);
        }

        // 返回元素的API数组格式
        return $element->retrieveApiArray();
    }
}
