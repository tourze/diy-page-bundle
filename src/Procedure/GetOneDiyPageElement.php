<?php

namespace DiyPageBundle\Procedure;

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

    public function execute(): array
    {
        throw new ApiException('接口未实现');
    }
}
