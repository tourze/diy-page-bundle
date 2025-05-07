<?php

namespace DiyPageBundle\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodTag('广告位模块')]
#[MethodDoc('获取某个配置的具体信息')]
#[MethodExpose('GetOneDiyPageElement')]
class GetOneDiyPageElement extends BaseProcedure
{
    #[MethodParam('需要查询的元素ID')]
    public int $elementId;

    public function execute(): array
    {
        throw new ApiException('接口未实现');
    }
}
