<?php

declare(strict_types=1);

namespace voku\diridea\processes;

use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;
use voku\diridea\DirValueObject;

class ExpireDefault implements ExpireInterface
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
            if (\count($filesystem->listContents($listContent->path())->toArray()) === 0) {
                $filesystem->deleteDirectory($listContent->path());
            }
        } else {
            $filesystem->delete($listContent->path());
        }
    }
}