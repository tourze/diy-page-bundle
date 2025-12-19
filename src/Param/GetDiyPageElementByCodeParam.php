<?php

declare(strict_types=1);

namespace DiyPageBundle\Param;

use Symfony\Component\Validator\Constraints as Assert;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;

/**
 * GetDiyPageElementByCode Procedure 的参数对象
 *
 * 用于传入指定的 code,然后加载元素配置
 */
readonly class GetDiyPageElementByCodeParam implements RpcParamInterface
{
    public function __construct(
        /**
         * @var array<string>
         */
        #[MethodParam(description: '多个code的集合')]
        #[Assert\NotBlank]
        #[Assert\Type(type: 'array')]
        public array $codes = [],

        #[MethodParam(description: '是否保存日志')]
        public bool $saveLog = true,
    ) {
    }
}
