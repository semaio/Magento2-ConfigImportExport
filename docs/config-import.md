# Import config data

Import configuration values in Magento in an automated way instead of manually clicking through the store configuration.


## Usage

```bash
$ php bin/magento config:data:import --help
 Usage:
  config:data:import [--base[="..."]] [-m|--format[="..."]] folder environment

 Arguments:
  folder                Import folder name
  environment           Environment name. SubEnvs separated by slash e.g.: development/osx/developer01

 Options:
  --base                Base folder name (default: "base")
  --format (-m)         Format: yaml, json (Default: yaml) (default: "yaml")
  --no-cache            Do not clear cache after config data import.
```

:exclamation: Only use the `no-cache` option if you clear the cache afterwards, e.g. in a deployment process. Otherwise the changes will have no effect.


## Folder Setup

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

To import my ([@therouv](https://github.com/therouv)) specific Magento configuration settings, 
I would run the following command in the "magento_root" directory:

```bash 
php bin/magento config:data:import config/store dev/therouv
```
