# Changelog

## Release 3.5.1 (2022-03-23)

* Add compatibility to new `symfony/yaml` releases to fix config export command when using `--hierarchical=y` option.
* Allow `symfony/yaml:^6.0`.

## Release 3.5.0 (2021-03-13)

* Use `\Magento\Framework\Console\CommandListInterface` instead of `\Magento\Framework\Console\CommandList` (#40) [@therouv](https://github.com/therouv)
* Add feature to recursively import configuration data (#41) [@Maksold](https://github.com/Maksold) [@therouv](https://github.com/therouv)

## Release 3.4.0 (2020-12-21)

* Update dependency version constraints [@therouv](https://github.com/therouv)


## Release 3.3.1 (2020-08-07)

* Remove trailing comma for PHP 7.2 compatibility [@therouv](https://github.com/therouv)


## Release 3.3.0 (2020-08-06)

* Add support for PHP 7.4 and Magento 2.4 ([@DavidLambauer](https://github.com/DavidLambauer)
* Add `no-cache` option to config import [@therouv](https://github.com/therouv)
* Improve docs and code style [@therouv](https://github.com/therouv)
