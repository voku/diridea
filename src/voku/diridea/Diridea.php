<?php

declare(strict_types=1);

namespace voku\diridea;

use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use Psr\Log\LoggerInterface;
use voku\diridea\processes\ArchiveInterface;
use voku\diridea\processes\BackupInterface;
use voku\diridea\processes\CacheInterface;
use voku\diridea\processes\EncryptInterface;
use voku\diridea\processes\ExpireInterface;
use voku\diridea\processes\LocationInterface;
use voku\diridea\processes\VisibilityInterface;

final class Diridea
{
    private const REGEX = '#(?<prefix>.*?)--(?<location>web|backend)_(?<visibility>public|private)(?<timings>(?<timing_option>_expire|_archive)(?<timing_value>\d+)(?<timing_unit>[d|h]))?(?<encrypt>_encrypt)??(?<backup>_backup)?(?<cache>_cache?)?#';

    /**
     * @var array<string, DirValueObject>
     */
    private array $dirideaDirectories;

    private Filesystem $filesystem;

    private ?LoggerInterface $logger;

    /**
     * @var null|\Psr\Log\LogLevel::*
     */
    private ?int $loggerVerbosity;

    /**
     * @var null|array<int, VisibilityInterface>
     */
    private ?array $visibilityProcesses = null;

    /**
     * @var null|array<int, LocationInterface>
     */
    private ?array $locationProcesses = null;

    /**
     * @var null|array<int, EncryptInterface>
     */
    private ?array $encryptProcesses = null;

    /**
     * @var null|array<int, BackupInterface>
     */
    private ?array $backupProcesses = null;

    /**
     * @var null|array<int, CacheInterface>
     */
    private ?array $cacheProcesses = null;

    /**
     * @var null|array<int, ExpireInterface>
     */
    private ?array $expireProcesses = null;

    /**
     * @var null|array<int, ArchiveInterface>
     */
    private ?array $archiveProcesses = null;

    /**
     * @param class-string<FilesystemAdapter> $filesystemAdapterClass
     * @param null|\Psr\Log\LogLevel::*       $logVerbosity
     */
    public function __construct(
        string           $filesystemAdapterClass,
        string           $path,
        ?LoggerInterface $logger = null,
        ?string          $loggerVerbosity = null,
        ?array           $locationProcesses = null,
        ?array           $visibilityProcesses = null,
        ?array           $expireProcesses = null,
        ?array           $archiveProcesses = null,
        ?array           $encryptProcesses = null,
        ?array           $backupProcesses = null,
        ?array           $cacheProcesses = null
    )
    {
        $this->logger = $logger;
        $this->loggerVerbosity = $loggerVerbosity;

        $this->basePath = $path;

        $this->log('Path: ' . $this->basePath);

        if ($visibilityProcesses !== null) {
            foreach ($visibilityProcesses as $visibilityProcess) {
                if (!$visibilityProcess instanceof VisibilityInterface) {
                    throw new \InvalidArgumentException('Visibility processes must implement VisibilityInterface');
                }
            }
            $this->visibilityProcesses = $visibilityProcesses;
        }

        if ($locationProcesses !== null) {
            foreach ($locationProcesses as $locationProcess) {
                if (!$locationProcess instanceof LocationInterface) {
                    throw new \InvalidArgumentException('Location processes must implement LocationInterface');
                }
            }
            $this->locationProcesses = $locationProcesses;
        }

        if ($expireProcesses !== null) {
            foreach ($expireProcesses as $expireProcess) {
                if (!$expireProcess instanceof ExpireInterface) {
                    throw new \InvalidArgumentException('Expire processes must implement ExpireInterface');
                }
            }
            $this->expireProcesses = $expireProcesses;
        }

        if ($archiveProcesses !== null) {
            foreach ($archiveProcesses as $archiveProcess) {
                if (!$archiveProcess instanceof ArchiveInterface) {
                    throw new \InvalidArgumentException('Archive processes must implement ArchiveInterface');
                }
            }
            $this->archiveProcesses = $archiveProcesses;
        }

        if ($encryptProcesses !== null) {
            foreach ($encryptProcesses as $encryptProcess) {
                if (!$encryptProcess instanceof EncryptInterface) {
                    throw new \InvalidArgumentException('Encrypt processes must implement EncryptInterface');
                }
            }
            $this->encryptProcesses = $encryptProcesses;
        }

        if ($backupProcesses !== null) {
            foreach ($backupProcesses as $backupProcess) {
                if (!$backupProcess instanceof BackupInterface) {
                    throw new \InvalidArgumentException('Backup processes must implement BackupInterface');
                }
            }
            $this->backupProcesses = $backupProcesses;
        }

        if ($cacheProcesses !== null) {
            foreach ($cacheProcesses as $cacheProcess) {
                if (!$cacheProcess instanceof CacheInterface) {
                    throw new \InvalidArgumentException('Cache processes must implement CacheInterface');
                }
            }
            $this->cacheProcesses = $cacheProcesses;
        }

        $filesystemAdapter = new $filesystemAdapterClass(
            $path,
            PortableVisibilityConverter::fromArray(
                [
                    'file' => [
                        'public'  => 0640,
                        'private' => 0600,
                    ],
                    'dir'  => [
                        'public'  => 0740,
                        'private' => 0700,
                    ],
                ]
            )
        );

        $this->filesystem = new Filesystem($filesystemAdapter);
    }

    private function log(string $message): void
    {
        if ($this->logger !== null) {
            $this->logger->log($this->loggerVerbosity, $message);
        }
    }

    /**
     * @return string[]
     */
    private function dirideaDirectoriesPaths(): array
    {
        $dirPaths = [];
        foreach ($this->dirideaDirectories as $dir) {
            $dirPaths[] = $dir->path();
        }

        return $dirPaths;
    }

    /**
     * @param DirectoryAttributes[]|FileAttributes[] $listContents
     */
    private function processContentHelper(array $listContents, DirValueObject $dir): bool
    {
        // init
        $result = true;
        $listContentsSubs = [];

        foreach ($listContents as $listContent) {

            if (
                $listContent->isDir()
                &&
                in_array($listContent->path(), $this->dirideaDirectoriesPaths(), true)
            ) {
                continue;
            }

            $tmpResult = $this->processContent($dir, $listContent);
            if ($tmpResult === false) {
                $result = false;
            }

            if ($listContent->isDir()) {
                $listContentsSubs[] = $this->filesystem->listContents($listContent->path())->toArray();
            }
        }

        foreach ($listContentsSubs as $listContentsSub) {
            $tmpResult = $this->processContentHelper($listContentsSub, $dir);
            if ($tmpResult === false) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @param DirectoryAttributes|FileAttributes $listContent
     */
    private function processContent(DirValueObject $options, StorageAttributes $listContent): bool
    {
        foreach ($this->visibilityProcesses ?? [] as $visibilityProcess) {
            if ($visibilityProcess->isApplicable(clone $this->filesystem, $options, $listContent)) {
                $visibilityProcess->process(clone $this->filesystem, $options, $listContent);
            }
        }

        foreach ($this->locationProcesses ?? [] as $locationProcess) {
            if ($locationProcess->isApplicable(clone $this->filesystem, $options, $listContent)) {
                $locationProcess->process(clone $this->filesystem, $options, $listContent);
            }
        }

        if ($options->isEncryptDir()) {
            foreach ($this->encryptProcesses ?? [] as $encryptProcess) {
                if ($encryptProcess->isApplicable(clone $this->filesystem, $options, $listContent)) {
                    $encryptProcess->process(clone $this->filesystem, $options, $listContent);
                }
            }
        }

        if ($options->isBackupDir()) {
            foreach ($this->backupProcesses ?? [] as $backupProcess) {
                if ($backupProcess->isApplicable(clone $this->filesystem, $options, $listContent)) {
                    $backupProcess->process(clone $this->filesystem, $options, $listContent);
                }
            }
        }

        if ($options->isCacheDir()) {
            foreach ($this->cacheProcesses ?? [] as $cacheProcess) {
                if ($cacheProcess->isApplicable(clone $this->filesystem, $options, $listContent)) {
                    $cacheProcess->process(clone $this->filesystem, $options, $listContent);
                }
            }
        }

        if ($options->timingOption() === DirValueObject::TIMING_OPTION_EXPIRE) {
            foreach ($this->expireProcesses ?? [] as $expireProcess) {
                if ($expireProcess->isApplicable(clone $this->filesystem, $options, $listContent)) {
                    $expireProcess->process(clone $this->filesystem, $options, $listContent);
                }
            }
        }

        if ($options->timingOption() === DirValueObject::TIMING_OPTION_ARCHIVE) {
            foreach ($this->archiveProcesses ?? [] as $archiveProcess) {
                if ($archiveProcess->isApplicable(clone $this->filesystem, $options, $listContent)) {
                    $archiveProcess->process(clone $this->filesystem, $options, $listContent);
                }
            }
        }

        return true;
    }

    public function run(): bool
    {
        $this->dirideaDirectories = $this->readDirideaDirectories();

        return $this->processDirectories();
    }

    /**
     * @return bool
     */
    private function processDirectories(): bool
    {
        // init
        $result = true;

        foreach ($this->dirideaDirectories as $dir) {
            $listContents = $this->filesystem->listContents($dir->path())->toArray();
            $tmpResult = $this->processContentHelper($listContents, $dir);
            if ($tmpResult === false) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @return array<string, DirValueObject>
     */
    private function readDirideaDirectories(): array
    {
        // init
        $dirs = [];

        $listContents = $this->filesystem->listContents('.', true);
        foreach ($listContents as $listContent) {
            if ($listContent->isDir()) {
                $directory = $listContent->path();

                $matches = [];
                $count = preg_match(self::REGEX, $directory, $matches);

                // DEBUG
                //var_dump($matches);

                if ($count > 0) {
                    $dirs[$listContent->path()] = new DirValueObject(
                        $listContent->path(),
                        $this->basePath,
                        $matches['prefix'],
                        $matches['location'],
                        $matches['visibility'],
                        isset($matches['timing_option']) ? \ltrim($matches['timing_option'], '_') : null,
                        isset($matches['timing_value']) ? (int) $matches['timing_value'] : null,
                        $matches['timing_unit'] ?? null,
                        (isset($matches['encrypt']) ? \ltrim($matches['encrypt'], '_') : null) === 'encrypt',
                        (isset($matches['backup']) ? \ltrim($matches['backup'], '_') : null) === 'backup',
                        (isset($matches['cache']) ? \ltrim($matches['cache'], '_') : null) === 'cache'
                    );
                }
            }
        }

        return $dirs;
    }

}
