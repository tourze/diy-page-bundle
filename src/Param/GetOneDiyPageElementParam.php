<?php

declare(strict_types=1);

namespace DiyPageBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * GetOneDiyPageElement Procedure 的参数对象
 *
 * 用于获取某个配置的具体信息
 */
readonly class GetOneDiyPageElementParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '需要查询的元素ID')]
        #[Assert\NotBlank]
        #[Assert\Positive]
        public int $elementId,
    ) {
    }
}
