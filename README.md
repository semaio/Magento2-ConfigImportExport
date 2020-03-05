# ConfigImportExport

This module provides new CLI commands for Magento 2 to import/export data in/from core_config_data.

This module is inspired by the awesome n98-magerun plugin "HarrisStreet ImpEx" by @SchumacherFM for Magento 1 which you can find [here](https://github.com/Zookal/HarrisStreet-ImpEx).

## Installation

**Add the Package to your composer.json** 

``` bash
composer require semaio/magento2-configimportexport
```


**Enable and install the Module**
``` bash
php bin/magento module:enable Semaio_ConfigImportExport
```

## Facts

* Version: 3.0.0
* Magento Support: >= 2.2
* PHP Versions: 7.0 + 7.1 + 7.2 + 7.3

## Functionality

This module is a work in progress and will be extended in the near future with more functionality and support for other file formats.

Currently are the following file formats supported:

* Yaml (default)
* Json

### Import

#### Usage

```bash
$ ./bin/magento config:data:import --help
 Usage:
  config:data:import [--base[="..."]] [-m|--format[="..."]] folder environment

 Arguments:
  folder                Import folder name
  environment           Environment name. SubEnvs separated by slash e.g.: development/osx/developer01

 Options:
  --base                Base folder name (default: "base")
  --format (-m)         Format: yaml, json (Default: yaml) (default: "yaml")
```

#### Folder Setup

To import the Magento configuration you'll need to setup a specific folder structure in the root directory of your Magento installation:

```
magento_root
├── app
├── bin
│   └── magento
├── config
│   └── store
│       ├── base
│       │   ├── allowsymlinks.yaml
│       │   └── general.yaml
│       ├── dev
│       │   ├── admin.yaml
│       │   └── therouv
│       │       └── web.yaml
│       ├── production
│       │   └── web.yaml
│       └── staging
│           └── web.yaml
├── lib
└── pub
```

To import my (@therouv) specific Magento configuration settings, I would run the following command in the "magento_root" directory:

`./bin/magento config:data:import config/store dev/therouv`

### Export

#### Usage

```bash
$ ./bin/magento config:data:export --help
Usage:
 config:data:export [-m|--format[="..."]] [-a|--hierarchical[="..."]] [-f|--filename[="..."]] [-i|--include[="..."]] [--includeScope[="..."]] [-x|--exclude[="..."]] [-s|--filePerNameSpace[="..."]]

Options:
 --format (-m)           Format: yaml, json (default: "yaml")
 --hierarchical (-a)     Create a hierarchical or a flat structure (not all export format supports that). Enable with: y (default: "n")
 --filename (-f)         File name into which should the export be written. Defaults into var directory.
 --include (-i)          Path prefix, multiple values can be comma separated; exports only those paths
 --includeScope          Scope name, multiple values can be comma separated; exports only those scopes
 --exclude (-x)          Path prefix, multiple values can be comma separated; exports everything except ...
 --filePerNameSpace (-s) Export each namespace into its own file. Enable with: y (default: "n")
```

#### Yaml File Format

```yaml
# Default scope
web/unsecure/base_url:
  default:
    0: 'http://example.com/my-base-url/'

# Store view scope -> "Example Store-ID 1"
web/unsecure/base_url:    
  stores:
    1: 'http://example.com/my-base-url/'  

# Store view scope -> "Example with store-view code"
web/unsecure/base_url:    
  stores:
    my_store_code: 'http://example.com/another-base-url/'  
```

#### Exported files

The files are written to the **var** directory of your Magento installation.


## Support

If you encounter any problems or bugs, please create an issue on [GitHub](https://github.com/semaio/Magento2-ConfigImportExport/issues).

## Contribution

Any contribution to the development of MageSetup is highly welcome. The best possibility to provide any code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

## Licence

[Open Software License (OSL 3.0)](http://opensource.org/licenses/osl-3.0.php)

## Contributors

Huge thanks to all [contributors](https://github.com/semaio/Magento2-ConfigImportExport/graphs/contributors) who contributed to this module.

## Copyright

(c) 2016 Rouven Alexander Rieker
