Simple Auth module for Zend Framework 2
=======================

Introduction
------------
This repository includes a simple authentication module for Zend Framework 2 with support BCrypt 
and a sample application using this module.

Installation
------------

After cloning from repository you should be using composer to install dependencies:
```console
    php composer.phar self-update
    php composer.phar install
```

If you don't want to use modules for debugging, before use composer you must delete these lines from `"composer.json"`:
```json
    "zendframework / zend-developer-tools": "dev-master",
    "bjyoungblood / BjyProfiler": "dev-master"
```

And delete lines from `"application.config.php"`:
```php
    'ZendDeveloperTools',
    'BjyProfiler',
```

Configuration
------------
File `"\module\Auth\config\module.config.php"` include block:
```php
    'auth' => array(
        ...
    ),
```
This block may include parameters:
* **table_name** - name of the table with users,
* **identity_column** - column containing the user's login,
* **credential_column** - column contains the encrypted password,
* **crypt_method** - password encryption method ("md5" or "bcrypt")

Examples:
```php
    'auth' => array(
        'table_name' => 'users',
        'identity_column' => 'user',
        'credential_column' => 'pwd',
        'crypt_method' => 'md5'
    ),
```
```php
    'auth' => array(
        'crypt_method' => 'bcrypt'
    ),
```

Default values (if no parameter is specified):
```php
    'auth' => array(
        'table_name' => 'users',
        'identity_column' => 'login',
        'credential_column' => 'password',
        'crypt_method' => 'md5'
    ),
```
