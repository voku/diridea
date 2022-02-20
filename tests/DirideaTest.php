<?php

declare(strict_types=1);

use League\Flysystem\Local\LocalFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use voku\diridea\Diridea;

/**
 * @internal
 */
final class DirideaTest extends TestCase
{
    public function testSimpleRun() {
        $diridea = new Diridea(LocalFilesystemAdapter::class, __DIR__ . '/fixture/overview/');
        $result = $diridea->run();

        $this->assertTrue($result);
    }

}
