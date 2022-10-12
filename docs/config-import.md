# Import config data

Import configuration values in Magento in an automated way instead of manually clicking through the store configuration.


## Usage

```bash
$ php bin/magento config:data:import --help
 Usage:
  config:data:import [--base[="..."]] [-m|--format[="..."]] folder environment

 Arguments:
  folder                           Import folder name
  environment                      Environment name. SubEnvs separated by slash e.g.: development/osx/developer01

 Options:
  --base (-b)                      Base folder name (default: "base")
  --format (-m)                    Format: yaml, json (Default: yaml)
  --no-cache                       Do not clear cache after config data import.
  --recursive (-r)                 Recursively go over subdirectories and import configs.
  --prompt-missing-env-vars (-p)   Prompt in interactive mode when environment variables are found but not configured (Default: true)
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

To import the Magento configuration settings for ([@therouv](https://github.com/therouv)), you would run the following command in the "magento_root" directory:

```bash
php bin/magento config:data:import config/store dev/therouv
```

The files in the `base` folder will always be imported (if they exist), regardless of which environment parameter has been passed. If the base and environment configurations have the same configuration field set, then the environment value for that configuration will overwrite the base configuration.

### Environment Variables substitution

If you do not want to store your secrets in version control, you can use placeholders for environment variables in the configuration files. This is done with the notation `%env(ENV_VAR_NAME)%` (make sure to put quotes around it).

For example, this might be the content of your config file:

```
vendorx/general/api_key:
  default:
    0: '%env(VENDORX_API_KEY)%'
```

You can then set the environment variable `VENDORX_API_KEY` in your CI/CD configuration to the secret API key. 

### Recursive folder setup

If you choose to store your configuration files in subdirectories, e.g. per vendor, the recommended folder setup should look like this:

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
│       │   └── vendor
│       │       └── package1.yaml
│       │       └── package2.yaml
│       ├── dev
│       │   ├── admin.yaml
│       │   ├── web.yaml
│       │   └── vendor
│       │       └── package1.yaml
│       ├── production
│       │   └── web.yaml
│       └── staging
│           └── web.yaml
├── lib
└── pub
```

You would run the following command in the "magento_root" directory to import the configuration settings:

```bash
php bin/magento config:data:import config/store dev --recursive
```

Or with shortcut:

```bash
php bin/magento config:data:import config/store dev -r
```
