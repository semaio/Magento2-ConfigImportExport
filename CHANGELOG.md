# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

* ...

## [3.9.0] - 2022-09-29

### Changed

* Added some validation checks for environment variables by [@peterjaap](https://github.com/peterjaap)

## [3.8.0] - 2022-09-29

### Added

* Added support and documentation for using environment variables by [@peterjaap](https://github.com/peterjaap)

### Changed

* Added documentation for previously undocumented base feature by [@peterjaap](https://github.com/peterjaap)

## [3.7.0] - 2022-08-17

### Changed

* PHP 8.1 compatibility changes when using YAML export (handling of null values) [@therouv](https://github.com/therouv)

## [3.6.0] - 2022-05-31

### Changed

* Allow PHP 8.1 for Magento 2.4.4 compatibility [@therouv](https://github.com/therouv)

## [3.5.1] - 2022-03-23

### Changed

* Add compatibility to new `symfony/yaml` releases to fix config export command when using `--hierarchical=y` option [@therouv](https://github.com/therouv)
* Allow `symfony/yaml:^6.0` [@therouv](https://github.com/therouv)

## [3.5.0] - 2021-03-13

### Added

* Add feature to recursively import configuration data (#41) [@Maksold](https://github.com/Maksold) [@therouv](https://github.com/therouv)

### Changed

* Use `\Magento\Framework\Console\CommandListInterface` instead of `\Magento\Framework\Console\CommandList` (#40) [@therouv](https://github.com/therouv)

## [3.4.0] - 2020-12-21

### Changed

* Update dependency version constraints [@therouv](https://github.com/therouv)

## [3.3.1] - 2020-08-07

### Changed

* Remove trailing comma for PHP 7.2 compatibility [@therouv](https://github.com/therouv)

## [3.3.0] - 2020-08-06

### Added

* Add `no-cache` option to config import [@therouv](https://github.com/therouv)

### Changed

* Add support for PHP 7.4 and Magento 2.4 ([@DavidLambauer](https://github.com/DavidLambauer)
* Improve docs and code style [@therouv](https://github.com/therouv)
