# Install

### Install package
  * Add the following "repositories" key below the "scripts" section in composer.json file of your Laravel app
  * ```
    "repositories": [
        {
            "type": "path",
            "url": "packages/store",
            "symlink": true
        }
    ],
    ```
  * ```composer require packages/store```
  * ```php artisan vendor:publish --provider="Packages\Store\SeoServiceProvider" --tag="config"```
  
  * Remove package
  * ```composer remove packages/store```