<?php

declare(strict_types=1);

namespace voku\diridea\processes;

use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;
use voku\diridea\DirValueObject;

class ArchiveDefault implements ArchiveInterface
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
            $filesystem->move($listContent->path(), $listContent->path() . '/archiv/' . basename($listContent->path()));
        } else {
            $filesystem->move($listContent->path(), dirname($listContent->path()) . '/archiv/' . basename($listContent->path()));
        }
    }
}