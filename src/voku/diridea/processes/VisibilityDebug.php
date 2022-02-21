<?php

namespace voku\diridea\processes;

use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;
use voku\diridea\DirValueObject;

class VisibilityDebug implements VisibilityInterface
{

    public function isApplicable(Filesystem $filesystem, DirValueObject $options, StorageAttributes $listContent): bool
    {
        return $listContent->visibility() !== $options->visibility();
    }

    public function process(Filesystem $filesystem, DirValueObject $options, StorageAttributes $listContent): void
    {
        echo 'visibility: ' . $listContent->path() . ' to ' . $options->visibility() . "\n";
    }
}