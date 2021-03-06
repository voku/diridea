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
        $diridea = DirideaFactory::createDebug(
            __DIR__ . '/fixture/overview/',
            __DIR__ . '/fixture/web/'
        );
        $result = $diridea->run();

        $this->assertTrue($result);
    }

}
