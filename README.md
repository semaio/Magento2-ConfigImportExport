# ConfigImportExport

This extension provides CLI commands for Magento 2 to import/export system configuration data. 

## Why this extension?

While Magento 2 offers the `app:config:dump` / `app:config:import` commands, they're limited to one environment and a little bit hard to manage, especially in CI/CD setups. 

This extension supports managing configuration values for multiple environments (production, staging, etc.) out-of-the-box, defining and overwriting base configuration values in a specific environment. It also allows different file formats (e.g., YAML, JSON). In general, it makes that process a bit more streamlined and easier to manage, with the added benefit of generalization and cross-compatibility with other agencies/merchants using this.


## Installation

**Add the package to your composer.json**

```bash
composer require semaio/magento2-configimportexport
```


**Enable and install the module**

```bash
php bin/magento module:enable Semaio_ConfigImportExport
php bin/magento setup:upgrade
```


## Facts

* Supported Magento versions are 2.3 and 2.4.
* Supported PHP versions are 7.2, 7.3, 7.4, and 8.1.


## Functionality

This module is a work in progress and will be extended in the future with more functionality and support for other file formats.


### File formats

This module currently supports the following file formats:

* YAML (default)
* JSON

See [docs/file-formats.md](docs/file-formats.md) for more information and examples.


### Import config data

See [docs/config-import.md](docs/config-import.md) for more information.


### Export config data

See [docs/config-export.md](docs/config-export.md) for more information.


## Support

If you encounter any problems or bugs, please create an issue on [GitHub](https://github.com/semaio/Magento2-ConfigImportExport/issues).


## Contribution

Any contribution to the development of MageSetup is highly welcome. The best possibility to provide any code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).


## Licence

[Open Software License (OSL 3.0)](http://opensource.org/licenses/osl-3.0.php)


## Contributors

Thanks to all [contributors](https://github.com/semaio/Magento2-ConfigImportExport/graphs/contributors) who invested their valuable time to contribute to this module. Much appreciated!


## Inspiration

This module is inspired by the awesome n98-magerun plugin "HarrisStreet ImpEx" by @SchumacherFM for Magento 1 which you can find [here](https://github.com/Zookal/HarrisStreet-ImpEx).


## Copyright

(c) 2016-2022 semaio GmbH / Rouven Alexander Rieker
