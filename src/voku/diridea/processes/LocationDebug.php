<?php

namespace voku\diridea\processes;

use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;
use voku\diridea\DirValueObject;

class LocationDebug implements LocationInterface
{
    private string $publicWebPath;

    public function __construct(string $publicWebPath)
    {
        $this->publicWebPath = $publicWebPath;
    }

    public function isApplicable(Filesystem $filesystem, DirValueObject $options, StorageAttributes $listContent): bool
    {
        static $CACHE_DIR = [];

        if (isset($CACHE_DIR[$options->path()])) {
            return false;
        }

        $CACHE_DIR[$options->path()] = 'done';

        return true;
    }

    public function process(Filesystem $filesystem, DirValueObject $options, StorageAttributes $listContent): void
    {
        $link = rtrim($this->publicWebPath, '/') . '/' . basename($options->path());
        $target = $options->basePath() . $options->path();

        if (!is_link($link)) {
            echo 'symlink: ' . $target . ' to ' . $link . "\n";
        }
    }
}