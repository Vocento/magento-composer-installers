# A Vocento Magento [Composer](http://getcomposer.org) Library Installer

This is for PHP package authors to require in their `composer.json`. It will
install their package to the correct location based on the specified package
type.

The goal of `installers` is to be a simple package type to install path map.
Users can also customize the install path per package and package authors can
modify the package name upon installing.

`installers` isn't intended on replacing all custom installers. If your
package requires special installation handling then by all means, create a
custom installer to handle it.

**Current Supported Package Types**:

| Framework    | Types
| ---------    | -----
| Magento      | `vocento-magento-core`
| Magento      | `vocento-magento-community`
| Magento      | `vocento-magento-statics`

## Example `composer.json` File

This is an example for a Magento Core plugin. The only important parts to set in your
composer.json file are `"type": "vocento-magento-core"` which describes what your
package is and `"require": { "vocento/magento-composer-installers": "~1.0" }` which tells composer
to load the custom installers.

```json
{
    "name": "vocento/magento-core",
    "type": "vocento-magento-core",
    "require": {
        "vocento/magento-composer-installers": "~1.0"
    }
}
```

This would install your package to the root path when a user runs `php composer.phar install`.

## Extra Configurations

This package has the possibility of setup some extra configurations related to ignoring files on the install proccesses of the packages. There is a general configuration to exclude files from all packages that will be installed, and there are three configurations to exclude files for each package that can be installed. 

The names for the general exclude files configuration and for the three supported packages types configurations are:

- `exclude-magento-files`
- `exclude-magento-core-files`
- `exclude-magento-community-files`
- `exclude-magento-statics-files`

You can add custom exclude-file configurations to your own custom installers.

You can use these extra configurations adding a config node with these names and the files to be excluded on the
composer.json file. Example:

```json
{
    "name": "vocento/magento-core",
    "type": "vocento-magento-core",
    "require": {
        "vocento/magento-composer-installers": "~1.0"
    },
    "config": {
        "exclude-magento-files": [
          "excluded all packages file1",
          "excluded all packages file2"
        ],
        "exclude-magento-core-files": [
          "excluded magento core file1",
          "excluded magento core file2"
        ]
    }
}
```
