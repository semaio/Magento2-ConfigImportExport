# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Magento 2 module (`Semaio_ConfigImportExport`) that provides two CLI commands for managing `core_config_data` across environments: `config:data:import` and `config:data:export`. It is a standalone PHP library — there is no Magento install in this repo.

## Commands

**Run tests** (requires PHPUnit installed or available):
```bash
vendor/bin/phpunit -c phpunit.xml.dist
# Single test file
vendor/bin/phpunit Test/Unit/Model/Resolver/EnvironmentVariableResolverTest.php
```

**Check code style** (dry-run, mirrors CI):
```bash
php-cs-fixer fix --diff --dry-run
# Auto-fix
php-cs-fixer fix
```

CI runs tests via [ExtDN GitHub Actions](https://github.com/extdn/github-actions-m2) across Magento 2.3–2.6 / PHP 7.3–8.1. See `.github/workflows/extdn-unit-tests.yml`.

## Architecture

```
Command/          CLI layer — ImportCommand, ExportCommand, AbstractCommand
Model/
  Processor/      Core logic — ImportProcessor, ExportProcessor (interface + impl)
  File/
    Finder        Discovers files in base/ + environment/ directories
    Reader/       YamlReader, JsonReader — parse config files
    Writer/       YamlWriter, JsonWriter — write exported config
  Resolver/       Value transformers applied during import (in order):
                    EnvironmentVariableResolver  — %env(VAR_NAME)% syntax
                    ThemePathResolver            — theme path lookup
                    EncryptResolver              — encrypts values before saving
  Converter/      ScopeConverter — resolves store view codes to numeric IDs
  Validator/      ScopeValidator — validates scope type + ID combinations
```

### How import works

1. `ImportCommand` wires up a `Finder` and `Reader`, then delegates to `ImportProcessor`.
2. `Finder` scans: `<folder>/base/` first, then `<folder>/<environment>/` (env values win).
3. `ImportProcessor.collectConfigurationValues()` reads all files and merges them into a `[configPath][scope][scopeId] => value` buffer.
4. Resolvers run on each value in declaration order (see `etc/di.xml`).
5. Special sentinel values: `!!DELETE` deletes the row; `!!KEEP` skips it.

### Extending via DI

Readers, writers, and resolvers are all injected as named arrays in `etc/di.xml`. To add a new format or resolver, declare it there — no PHP changes to the command or processor are needed.

### Config file format

Both flat (`general/store_information/name:`) and hierarchical YAML/JSON formats are supported. Scope is `default`, `websites`, or `stores`; scope ID can be a numeric ID or a store view code. See `docs/file-formats.md` for full examples.

## Code conventions

- PSR-2 + additional php-cs-fixer rules (short array syntax, `single_quote`, `ordered_imports`, trailing commas in multiline). Run the fixer before committing.
- Every class that has logic has a corresponding interface; `etc/di.xml` maps interface → implementation.
- Unit tests live in `Test/Unit/`, mirroring the `Model/` structure. Tests use PHPUnit mocks (no Magento test framework required). Test class names end in `Test.php`.
