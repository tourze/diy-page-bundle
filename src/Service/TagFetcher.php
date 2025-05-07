<?php

namespace DiyPageBundle\Service;

use AntdCpBundle\Builder\Field\SelectDataInterrupt;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Tourze\EnumExtra\SelectDataFetcher;

/**
 * 读取标签数据
 */
class TagFetcher implements SelectDataFetcher
{
    public function __construct(
        #[TaggedIterator('diy-page.tag.provider')] private readonly iterable $providers,
    ) {
    }

    public function genSelectData(): array
    {
        $result = [];
        foreach ($this->providers as $provider) {
            /** @var SelectDataFetcher $provider */
            $subData = $provider->genSelectData();
            if ($provider instanceof SelectDataInterrupt && $provider->isInterrupt()) {
                return iterator_to_array($subData);
            }

            if (!is_array($subData)) {
                $subData = iterator_to_array($subData);
            }
            $result = array_merge($result, $subData);
        }

        return array_values($result);
    }
}
