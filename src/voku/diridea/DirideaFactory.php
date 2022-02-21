<?php

declare(strict_types=1);

namespace voku\diridea;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Psr\Log\LoggerInterface;
use voku\diridea\processes\ArchiveDefault;
use voku\diridea\processes\ExpireDefault;
use voku\diridea\processes\LocationDefault;
use voku\diridea\processes\VisibilityDefault;

final class DirideaFactory
{
    /**
     * @param string $path
     * @param null|class-string<FilesystemAdapter> $filesystemAdapterClass <p>Fallback to <code>LocalFilesystemAdapter</code> by <strong>NULL</strong>.</p>
     *
     * @return Diridea
     */
    public static function create(
        string $path,
        string $publicWebPath,
        ?string $filesystemAdapterClass = null,
        ?LoggerInterface $logger = null
    ): Diridea
    {
        return new Diridea(
            $filesystemAdapterClass ?? LocalFilesystemAdapter::class,
            $path,
            $logger,
            null,
            [
                new LocationDefault($publicWebPath)
            ],
            [
                new VisibilityDefault()
            ],
            [
                new ExpireDefault()
            ],
            [
                new ArchiveDefault()
            ],
            [],
            [],
            []
        );
    }
}
