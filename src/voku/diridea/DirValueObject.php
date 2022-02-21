<?php

namespace voku\diridea;

class DirValueObject
{
    public const LOCATION_WEB = 'web';
    public const LOCATION_BACKEND = 'backend';

    /**
     * @var DirValueObject::LOCATION_*
     */
    private string $location;

    private string $path;

    private string $basePath;

    private string $prefix;

    public const TIMING_OPTION_EXPIRE = 'expire';
    public const TIMING_OPTION_ARCHIVE = 'archive';

    /**
     * @var null|DirValueObject::TIMING_OPTION_*
     */
    private ?string $timing_option;

    public const TIMING_UNIT_DAY = 'd';

    public const TIMING_UNIT_WEEK = 'm';

    /**
     * @var null|DirValueObject::TIMING_UNIT_*
     */
    private ?string $timing_unit;

    private ?int $timing_value;

    public const VISIBILITY_PUBLIC = 'public';

    public const VISIBILITY_PRIVATE = 'private';

    /**
     * @var DirValueObject::VISIBILITY_*
     */
    private string $visibility;

    private bool $encrypt;

    private bool $backup;

    private bool $cache;

    public function __construct(
        string $path,
        string $basePath,
        string $prefix,
        string $location,
        string $visibility,
        ?string $timing_option,
        ?int $timing_value,
        ?string $timing_unit,
        bool $encrypt,
        bool $backup,
        bool $cache
    )
    {
        $this->path = $path;
        $this->basePath = $basePath;
        $this->prefix = $prefix;

        if (!in_array($location, ['web', 'backend'])) {
            throw new \InvalidArgumentException('Invalid location:' . $location);
        }
        $this->location = $location;

        if (!in_array($visibility, ['public', 'private'])) {
            throw new \InvalidArgumentException('Invalid visibility: ' . $visibility);
        }
        $this->visibility = $visibility;

        if ($timing_option !== null && !in_array($timing_option, ['expire', 'archive'])) {
            throw new \InvalidArgumentException('Invalid timing option: ' . $timing_option);
        }
        $this->timing_option = $timing_option;

        if ($timing_option !== null && $timing_value === null) {
            throw new \InvalidArgumentException('Invalid timing value');
        }
        $this->timing_value = $timing_value;

        if ($timing_option !== null && $timing_unit === null) {
            throw new \InvalidArgumentException('Invalid timing unit');
        }
        if ($timing_unit !== null && !in_array($timing_unit, ['d', 'h'])) {
            throw new \InvalidArgumentException('Invalid timing unit: ' . $timing_unit);
        }
        $this->timing_unit = $timing_unit;

        $this->encrypt = $encrypt;

        $this->backup = $backup;

        $this->cache = $cache;
    }

    public function isEncryptDir(): bool
    {
        return $this->encrypt;
    }

    public function isBackupDir(): bool
    {
        return $this->backup;
    }

    public function isCacheDir(): bool
    {
        return $this->cache;
    }

    /**
     * @var DirValueObject::LOCATION_*
     */
    public function location(): string
    {
        return $this->location;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function prefix(): string
    {
        return $this->prefix;
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    /**
     * null|DirValueObject::TIMING_OPTION_*
     */
    public function timingOption(): ?string
    {
        return $this->timing_option;
    }

    public function timingValueInSeconds(): ?int
    {
        if ($this->timing_unit === self::TIMING_UNIT_DAY) {
            return $this->timing_value * 86400;
        }

        if ($this->timing_unit === self::TIMING_UNIT_WEEK) {
            return $this->timing_value * 604800;
        }

        throw new \RuntimeException('Invalid timing unit');
    }

    /**
     * @return 'public'|'private'
     */
    public function visibility(): string
    {
        return $this->visibility;
    }

}