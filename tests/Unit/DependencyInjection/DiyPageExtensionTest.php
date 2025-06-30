<?php

namespace DiyPageBundle\Tests\Unit\DependencyInjection;

use DiyPageBundle\DependencyInjection\DiyPageExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DiyPageExtensionTest extends TestCase
{
    private DiyPageExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new DiyPageExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        $this->extension->load([], $this->container);

        $serviceIds = array_keys($this->container->getDefinitions());
        $hasServiceDefinitions = !empty($serviceIds);
        $this->assertTrue($hasServiceDefinitions, 'Extension should load service definitions');
    }

    public function testGetAlias(): void
    {
        $this->assertSame('diy_page', $this->extension->getAlias());
    }
}