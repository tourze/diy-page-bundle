<?php

declare(strict_types=1);

namespace DiyPageBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * GetBlockList Procedure 的参数对象
 *
 * 用于获取广告位列表
 */
readonly class GetBlockListParam implements RpcParamInterface
{
    public function __construct(
        #[MethodParam(description: '是否只获取有效的广告位')]
        public ?bool $validOnly = null,

        #[MethodParam(description: '类型ID筛选')]
        public ?string $typeId = null,

        #[MethodParam(description: '页码')]
        #[Assert\PositiveOrZero]
        public int $page = 1,

        #[MethodParam(description: '每页数量')]
        #[Assert\Positive]
        public int $limit = 20,
    ) {
    }
}
