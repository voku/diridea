
# :file_folder: Diridea

[![Build Status](https://github.com/voku/diridea/actions/workflows/ci.yml/badge.svg)](https://github.com/voku/diridea/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/github/voku/diridea/badge.svg?branch=main)](https://coveralls.io/github/voku/diridea?branch=main)
[![codecov.io](https://codecov.io/github/voku/diridea/coverage.svg?branch=main)](https://codecov.io/github/voku/diridea?branch=main)
[![Codacy Badge](https://www.codacy.com/project/badge/Grade/unknown)](https://www.codacy.com/app/voku/diridea)
[![Latest Stable Version](https://poser.pugx.org/voku/diridea/v/stable)](https://packagist.org/packages/voku/diridea)
[![License](https://poser.pugx.org/voku/diridea/license)](https://packagist.org/packages/voku/diridea)

**Diridea** is a PHP directory management system that lets you encode rules directly into your directory names. By following a simple naming convention, Diridea automatically manages file visibility, web accessibility, expiration, archiving, encryption, backup, and caching — all driven by the directory structure itself.

## How It Works

Diridea scans a root directory for folders whose names match a specific pattern. When a match is found, the library reads the encoded rules from the folder name and applies the corresponding operations (visibility changes, symlink creation, file deletion, archiving, etc.) to the files inside.

This means your directory structure **is** your configuration — no separate config files needed.

## Naming Convention

```
{prefix}--{location}_{visibility}[_{timing_option}{timing_value}{timing_unit}][_encrypt][_backup][_cache]
```

| Segment | Values | Description |
|---|---|---|
| `prefix` | any string | Human-readable identifier for the directory |
| `location` | `web` \| `backend` | Whether the directory is web-accessible (symlinked) or backend-only |
| `visibility` | `public` \| `private` | File permission level |
| `timing_option` | `expire` \| `archive` | What to do when the timing threshold is reached |
| `timing_value` | integer | The number of time units |
| `timing_unit` | `d` (days) \| `h` (hours) | Unit for the timing value |
| `_encrypt` | optional flag | Mark files for encryption |
| `_backup` | optional flag | Mark files for backup |
| `_cache` | optional flag | Mark files for caching |

### Regex

```
#(?<prefix>.*?)--(?<location>web|backend)_(?<visibility>public|private)(?<timings>(?<timing_option>_expire|_archive)(?<timing_value>\d+)(?<timing_unit>[d|h]))?(?<encrypt>_encrypt)??(?<backup>_backup)?(?<cache>_cache?)?#
```

→ [Test it on regex101](https://regex101.com/r/wM17gy/1)

## Examples

| Directory name | Meaning |
|---|---|
| `download--web_public_expire1d` | Web-accessible, public, files deleted after 1 day |
| `article_images--backend_private` | Backend-only, private permissions |
| `article_images--backend_private_archive7h_encrypt` | Backend-only, private, archived after 7 hours, encrypted |
| `uploads--web_public_expire30d` | Web-accessible, public, files deleted after 30 days |

## Installation

```bash
composer require voku/diridea
```

## Usage

### Production mode

```php
use voku\diridea\DirideaFactory;

$diridea = DirideaFactory::create(
    __DIR__ . '/storage/',   // root directory to scan
    __DIR__ . '/public/web/' // public web path for symlinks
);
$result = $diridea->run();
```

### Debug / dry-run mode

```php
use voku\diridea\DirideaFactory;

$diridea = DirideaFactory::createDebug(
    __DIR__ . '/storage/',
    __DIR__ . '/public/web/'
);
$result = $diridea->run(); // prints planned actions, makes no changes
```

### Custom processes

You can inject your own process implementations for any of the supported operations:

```php
use voku\diridea\Diridea;
use League\Flysystem\Local\LocalFilesystemAdapter;

$diridea = new Diridea(
    LocalFilesystemAdapter::class,
    __DIR__ . '/storage/',
    $logger,
    \Psr\Log\LogLevel::DEBUG,
    $locationProcesses,
    $visibilityProcesses,
    $expireProcesses,
    $archiveProcesses,
    $encryptProcesses,
    $backupProcesses,
    $cacheProcesses
);
```

Each process type implements a corresponding interface (`LocationInterface`, `VisibilityInterface`, `ExpireInterface`, `ArchiveInterface`, `EncryptInterface`, `BackupInterface`, `CacheInterface`) with two methods:

- `isApplicable(Filesystem, DirValueObject, StorageAttributes): bool` — decides whether the process should run for a given file/directory
- `process(Filesystem, DirValueObject, StorageAttributes): void` — performs the actual operation

## Supported Process Types

| Interface | Purpose |
|---|---|
| `VisibilityInterface` | Set file/directory permissions |
| `LocationInterface` | Create symlinks for web-accessible directories |
| `ExpireInterface` | Delete files that have exceeded their TTL |
| `ArchiveInterface` | Move files to an archive subdirectory after their TTL |
| `EncryptInterface` | Encrypt files in marked directories |
| `BackupInterface` | Back up files in marked directories |
| `CacheInterface` | Cache files in marked directories |

## Running Tests

1. Install dependencies:

```bash
composer install
```

2. Run the test suite:

```bash
./vendor/bin/phpunit
```

3. Run static analysis:

```bash
./vendor/bin/phpstan analyse
```

## Support

For support and donations please visit [GitHub](https://github.com/voku/diridea/) | [Issues](https://github.com/voku/diridea/issues) | [PayPal](https://paypal.me/moelleken) | [Patreon](https://www.patreon.com/voku).

For status updates and release announcements please visit [Releases](https://github.com/voku/diridea/releases) | [Twitter](https://twitter.com/suckup_de) | [Patreon](https://www.patreon.com/voku/posts).

For professional support please contact [me](https://about.me/voku).

## Thanks

- Thanks to [GitHub](https://github.com) (Microsoft) for hosting the code and providing CI/CD infrastructure via GitHub Actions!
- Thanks to [IntelliJ](https://www.jetbrains.com) as they make the best IDEs for PHP and they gave me an open source license for PhpStorm!
- Thanks to [PHPStan](https://github.com/phpstan/phpstan) for the excellent static analysis tool!
- Thanks to [League Flysystem](https://flysystem.thephpleague.com/) for the powerful filesystem abstraction layer!
