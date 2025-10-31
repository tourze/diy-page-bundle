<?php

declare(strict_types=1);

namespace DiyPageBundle\Tests;

use DiyPageBundle\DiyPageBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(DiyPageBundle::class)]
#[RunTestsInSeparateProcesses]
final class DiyPageBundleTest extends AbstractBundleTestCase
{
}
