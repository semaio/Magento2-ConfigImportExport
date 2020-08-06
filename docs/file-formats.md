# File Formats

This module currently supports the following file formats:

* YAML (default)
* JSON


## YAML

### Flattened format

Set a configuration value for the default scope:

```yaml
general/store_information/name:
  default:
    0: 'Example'
```

Set a configuration value for website scope:

```yaml
# Store view scope -> "Example Store-ID 1"
general/store_information/name:
  websites:
    1: 'Example Website'
```
 
Set a configuration value for store scope: 
 
```yaml
# Store view scope -> "Example Store-ID 1"
general/store_information/name:
  stores:
    1: 'Example Store'
```

Set a configuration value for store scope but with store view code instead of store view ID:

```yaml
# Store view scope -> "Example with store-view code"
general/store_information/name:
  stores:
    my_store_code: 'Example Store'
```

Set a configuration value for different scopes in one go: 

```yaml
# Set values for default scope, website scope and store view scope in one go
general/store_information/name:
  default:
    0: 'Example'
  websites:
    1: 'Example Website'
  stores:
    1: 'Example Store DE'
    2: 'Example Store EN'
```


### Hierarchical format

Instead of using the flattened approach above, you can also use the hierarchical format to set a configuration value:

```yaml
general:
  store_information:
    name:
      default:
        0: 'Example'
      websites:
        1: 'Example Website'
      stores:
        1: 'Example Store DE'
        2: 'Example Store EN'
```


## JSON

### Flattened format

Set a configuration value for the default scope:

```json
{
  "general/store_information/name": {
    "default": {
      "0": "Example"
    }
  }
}
```

Set a configuration value for website scope:

```json
{
  "general/store_information/name": {
    "websites": {
      "1": "Example Website"
    }
  }
}
```
 
Set a configuration value for store scope: 
 
```json
{
  "general/store_information/name": {
    "stores": {
      "1": "Example Store"
    }
  }
}
```

Set a configuration value for store scope but with store view code instead of store view ID:

```json
{
  "general/store_information/name": {
    "stores": {
      "my_store_code": "Example Store"
    }
  }
}
```

Set a configuration value for different scopes in one go: 

```json
{
  "general/store_information/name": {
    "default": {
      "0": "Example"
    },
    "websites": {
      "1": "Example Website"
    },
    "stores": {
      "1": "Example Store DE",
      "2": "Example Store EN"
    }
  }
}
```


### Hierarchical format

Instead of using the flattened approach above, you can also use the hierarchical format to set a configuration value:

```json
{
  "general": {
    "store_information": {
      "name": {
        "default": {
          "0": "Example"
        },
        "websites": {
          "1": "Example Website"
        },
        "stores": {
          "1": "Example Store DE",
          "2": "Example Store EN"
        }
      }
    }
  }
}
```
