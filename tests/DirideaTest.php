<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use voku\diridea\DirideaFactory;

/**
 * @internal
 */
final class DirideaTest extends TestCase
{
    public function testSimpleFactory(): void
    {
        $diridea = DirideaFactory::create(__DIR__ . '/fixture/overview/');
        $result = $diridea->run();

        $this->assertTrue($result);
    }

}
