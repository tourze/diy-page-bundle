<?php

namespace DiyPageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '广告位模块')]
class DiyPageBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class => ['all' => true],
            \Tourze\DoctrineIpBundle\DoctrineIpBundle::class => ['all' => true],
            \Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle::class => ['all' => true],
            \Tourze\DoctrineTrackBundle\DoctrineTrackBundle::class => ['all' => true],
            \Tourze\DoctrineUserBundle\DoctrineUserBundle::class => ['all' => true],
            \Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle::class => ['all' => true],
            \Tourze\EcolBundle\EcolBundle::class => ['all' => true],
        ];
    }
}
