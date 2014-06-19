Simple Auth module for Zend Framework 2
=======================

Introduction
------------
This repository includes a simple authentication module for Zend Framework 2 with support BCrypt 
and a sample application using this module.

Installation
------------

After cloning from repository you should be using composer to install dependencies:
    php composer.phar self-update
    php composer.phar install

If you don't want to use modules for debugging, before use composer you must delete these lines from "composer.json": 
         "zendframework / zend-developer-tools": "dev-master", 
         "bjyoungblood / BjyProfiler": "dev-master"

And delete lines from "application.config.php":
        'ZendDeveloperTools',
        'BjyProfiler',

Database configuration
------------

