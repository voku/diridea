# TODO

Open tasks for the next development iteration.

## Core — missing default process implementations

- [ ] Implement `EncryptDefault` — encrypt files in-place using a configurable key/algorithm (e.g. `sodium_crypto_secretbox`)
- [ ] Implement `EncryptDebug` — dry-run counterpart: print which files would be encrypted without modifying them
- [ ] Implement `BackupDefault` — copy files to a configurable backup destination (local path or Flysystem adapter)
- [ ] Implement `BackupDebug` — dry-run counterpart: print which files would be backed up
- [ ] Implement `CacheDefault` — trigger cache warming/invalidation for files in marked directories (e.g. Redis, Varnish, CDN flush)
- [ ] Implement `CacheDebug` — dry-run counterpart: print which files would be cached/invalidated

## Features

- [ ] Add support for additional timing units: minutes (`m`) and weeks (`w`)
- [ ] Allow a configurable archive target path (currently hardcoded to `archiv/` subdirectory)
- [ ] Support a `_notify` flag to send a notification (e.g. email, webhook) when files expire or are archived
- [ ] Add an event/hook system so consumers can react to process outcomes without subclassing
- [ ] Support recursive directory naming (currently only top-level matches are treated as rule directories)
- [ ] Add a `listDirectories(): array` method to `Diridea` so callers can inspect discovered rule-directories without running processes

## CLI

- [ ] Create a standalone CLI entry point (`bin/diridea`) usable without a custom PHP script
- [ ] Support `--dry-run` flag on the CLI that automatically selects debug processes
- [ ] Support `--path` and `--web-path` arguments on the CLI
- [ ] Print a summary table (directories found, files processed, actions taken) when run in verbose/debug mode

## Testing

- [ ] Add unit tests for each existing process class (`LocationDefault`, `VisibilityDefault`, `ExpireDefault`, `ArchiveDefault`)
- [ ] Add unit tests for `DirValueObject` edge cases (invalid arguments, timing unit conversions)
- [ ] Add integration tests covering the full `run()` cycle against a temporary filesystem fixture
- [ ] Add tests for the fixture directories `article_images--backend_private_encrypt` and `article_images--backend_private_archive7h_encrypt`
- [ ] Add tests that verify debug processes output the expected strings

## Code quality

- [ ] Replace raw `echo` statements in debug processes with PSR-3 logger calls (inject logger into debug process constructors)
- [ ] Fix the regex character-class bug: `[d|h]` matches the literal pipe character — should be `[dh]`
- [ ] Add `@throws` PHPDoc annotations to process classes that can throw `RuntimeException`
- [ ] Enable `strict_types` enforcement in all process classes (already present but verify consistently)

## Documentation

- [ ] Add a `CHANGELOG.md` entry for any new features/fixes
- [ ] Add inline code examples for using a non-local Flysystem adapter (e.g. AWS S3, Google Cloud Storage)
- [ ] Document how to integrate Diridea into a Laravel/Symfony console command
