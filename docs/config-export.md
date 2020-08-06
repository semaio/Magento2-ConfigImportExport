# Export config data

Export all config values from your Magento installation to a yaml or json file.

The file(s) will be written to the **var** directory of your Magento installation
after successful export.


## Usage

```bash
$ php bin/magento config:data:export --help
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
