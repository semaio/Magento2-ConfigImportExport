# ConfigImportExport

This module provides new CLI commands for Magento 2 to import/export data in/from core_config_data.

This module is inspired by the awesome n98-magerun plugin "HarrisStreet ImpEx" by @SchumacherFM for Magento 1 which you can find [here](https://github.com/Zookal/HarrisStreet-ImpEx).

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

* Supported Magento versions are 2.3 and 2.4
* Supported PHP versions are 7.2, 7.3 and 7.4


## Functionality

This module is a work in progress and will be extended in the future with more functionality
and support for other file formats.


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


## Copyright

(c) 2016-2020 semaio GmbH / Rouven Alexander Rieker
