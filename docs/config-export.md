# Export config data

Export all config values from your Magento installation to a yaml or json file.

The file(s) will be written to the **var** directory of your Magento installation
after successful export.


## Usage

```bash
$ php bin/magento config:data:export --help
Usage:
 config:data:export [-m|--format[="..."]] [-a|--hierarchical[="..."]] [-f|--filename[="..."]] [-i|--include[="..."]] [--includeScope[="..."] [--includeScopeId="..."]]  [-x|--exclude[="..."]] [-s|--filePerNameSpace[="..."]]

Options:
 --format (-m)           Format: yaml, json (default: "yaml")
 --hierarchical (-a)     Create a hierarchical or a flat structure (not all export format supports that). Enable with: y (default: "n")
 --filename (-f)         Specifies the export file name. Defaults to "config" (when not using "--filePerNameSpace").
 --filepath (-p)         Specifies the export path where the export file(s) will be written. Defaults to "var/export/config/Ymd_His/".
 --include (-i)          Path prefix, multiple values can be comma separated; exports only those paths
 --includeScope          Scope name, multiple values can be comma separated; exports only those scopes
 --includeScopeId        Scope ID(s), multiple values can be comma separated. Only applies if exactly one scope is given (websites, groups or stores). In that case values are exported only for the given scope ID(s).
 --exclude (-x)          Path prefix, multiple values can be comma separated; exports everything except ...
 --filePerNameSpace (-s) Export each namespace into its own file. Enable with: y (default: "n")
```
