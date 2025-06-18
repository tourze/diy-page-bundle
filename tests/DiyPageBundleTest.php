<?php

namespace DiyPageBundle\Tests;

use DiyPageBundle\DiyPageBundle;
use PHPUnit\Framework\TestCase;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;

class DiyPageBundleTest extends TestCase
{
    public function testGetBundleDependencies(): void
    {
        $dependencies = DiyPageBundle::getBundleDependencies();
        
        $this->assertArrayHasKey(DoctrineIndexedBundle::class, $dependencies);
        $this->assertEquals(['all' => true], $dependencies[DoctrineIndexedBundle::class]);
    }
} 