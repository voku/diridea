<?php


namespace voku\diridea\processes;

use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;
use voku\diridea\DirValueObject;

interface DirideaInterface
{
    public function isApplicable(Filesystem $filesystem, DirValueObject $options, StorageAttributes $listContent): bool;

    public function process(Filesystem $filesystem, DirValueObject $options, StorageAttributes $listContent): void;

}