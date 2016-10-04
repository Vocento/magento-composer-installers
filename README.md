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

