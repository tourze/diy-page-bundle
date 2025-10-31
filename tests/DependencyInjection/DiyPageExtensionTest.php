<?php

namespace DiyPageBundle\Tests\DependencyInjection;

use DiyPageBundle\DependencyInjection\DiyPageExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(DiyPageExtension::class)]
final class DiyPageExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
}
