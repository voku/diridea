<?php

declare(strict_types=1);

namespace voku\diridea\processes;

use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;
use voku\diridea\DirValueObject;

class ArchiveDebug implements ArchiveInterface
{
    public function isApplicable(Filesystem $filesystem, DirValueObject $options, StorageAttributes $listContent): bool
    {
        $timingValue = $options->timingValueInSeconds();

        return $timingValue !== null
               &&
               $filesystem->fileExists($listContent->path())
               &&
               ($filesystem->lastModified($listContent->path()) + $timingValue) <= time();
    }

    public function process(Filesystem $filesystem, DirValueObject $options, StorageAttributes $listContent): void
    {
        if ($listContent->isDir()) {
            echo 'move: ' . $listContent->path() . ' into ' . $listContent->path() . '/archiv/' . basename($listContent->path()) . "\n";
        } else {
            echo 'move: ' . $listContent->path() . ' into ' . dirname($listContent->path()) . '/archiv/' . basename($listContent->path()) . "\n";
        }
    }
}