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
  --allow-empty-directories (-e)   Do not throw error if import directories are empty.
  --lock-config (-c)               Additionally lock imported values in app/etc/config.php (read-only in Admin).
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

### Theme code substitution

If you do not want to store hard-coded theme IDs in your files, but rather the theme code, you can use placeholders for theme codes in the configuration files. This is done with the notation `%theme(path)%` (make sure to put quotes around it).

For example, this might be the content of your config file:

```
design/theme/theme_id:
  default:
    0: '%theme(frontend/Vendor/theme)%'
```

Always use theme path that is defined as component name in the `registration.php` file of your theme (including the area (e.g. `frontend`) in front), e.g. `frontend/Vendor/theme` and never `Vendor/theme`.

### Environment Variables substitution

If you do not want to store your secrets in version control, you can use placeholders for environment variables in the configuration files. This is done with the notation `%env(ENV_VAR_NAME)%` (make sure to put quotes around it).

For example, this might be the content of your config file:

```
vendorx/general/api_key:
  default:
    0: '%env(VENDORX_API_KEY)%'
```

You can then set the environment variable `VENDORX_API_KEY` in your CI/CD configuration to the secret API key. 

### Encryption Value Substitution

For importing encrypted configuration data, such as passwords and API keys, into fields utilizing Magento's `\Magento\Config\Model\Config\Backend\Encrypted` backend model, use `%encrypt(value)%` (make sure to put quotes around it) placeholder within your configuration files.

For example, this could be the content of your configuration file:

```
payment/provider/secret_key:
  default:
    0: '%encrypt(mySecretKey)%'
```

:exclamation: It is generally not recommended to store sensitive data in your GIT repository but instead keep it securely in the environment's database. Please use this option with caution and at your own risk.

### Delete Config

Sometimes, it might be helpful to be able to delete certain config values and get back to the default behavior. To do so, your config value has to be a magic-ish string. 

```yaml
vendorx/general/api_key:
  default:
    0: "!!DELETE"
```

### Keep Config

To ensure that a specific configuration value will not be changed by the config importer, please use the following string as configuration value:

```yaml
vendorx/general/api_key:
  default:
    0: "!!KEEP"
```

This is helpful when you've got the same settings across different environments but want to keep one environment ( `X` env) unchanged without showing the exact value in the config file. It's a common scenario, especially when dealing with sensitive data. You really should only keep that kind of info in the environment’s database, not in your GIT repo.

### Lock Config

By default, imported values are written to the database (`core_config_data`) and remain editable in the Admin panel. If you want to additionally lock the values so they become **read-only in Admin**, use the `--lock-config` option:

```bash
php bin/magento config:data:import config/store production --lock-config
```

This writes the imported values to both the database **and** `app/etc/config.php`. Values stored in `config.php` take precedence over database values and appear greyed out (non-editable) in Admin > Stores > Configuration.

:exclamation: When using `--lock-config` together with `!!DELETE`, the value is removed from the database but **not** from `config.php`. If the value was previously locked, you need to remove it from `app/etc/config.php` manually.

:exclamation: The `--lock-config` option writes to the filesystem. Make sure `app/etc/config.php` is writable by the PHP process.

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
