# Raccoon WordPress starter [![Build Status](https://travis-ci.org/hiwelo/raccoon.svg?branch=develop)](https://travis-ci.org/hiwelo/raccoon)
**[Raccoon](https://github.com/hiwelo/raccoon/)** is a personal WordPress starter theme based on Composer, NPM, Babel (ES 2015), Gulp and Knacss


## Summary
  - [Requirements](#requirements)
  - [Installation](#installation)
  - [How to work with a raccoon](#how-to-work-with-a-raccoon)
    - [Before to work](#before-to-work)
    - [Vendor update](#vendor-update)
    - [Before to commit](#before-to-commit)
    - [Documentation](#documentation)
  - [How to code with a raccoon](#how-to-code-with-a-raccoon)
    - [OOP PHP Class](#oop-php-class)
  - [How a raccoon can help you with WordPress](#how-a-raccoon-can-help-you-with-wordpress)
    - [A configuration manifest](#a-configuration-manifest)


## Requirements
For its development, **[Raccoon](https://github.com/hiwelo/raccoon/)** requires:
  - PHP >= 5.6
  - Composer
  - Node.js with npm for package management


## Installation
It's pretty simple: you just have to clone the repository and run `composer install`
to start all required jobs.
```
git clone https://github.com/hiwelo/raccoon.git
composer install
```


## How to work with a raccoon

### Before to work
Before any modification, please run:
```
composer work
```
With this command you verify that your repository is up to date and it starts all
watch jobs.

### Vendor update
Regularly, don't forget to update all dependencies (composer & npm) with:
```
composer update
```

### Before to commit
After any modification and before you commit anything, I strongly advice to run:
```
composer test
```
And if there's no errors, you can commit your modifications.

If there's some errors and you absolutely want to commit, you've got to run this command
to avoid pre-commit verifications:
```
git commit --no-verify
```

### Documentation
If you want to parse all PHP files to generate the documentation, please run:
```
composer documentation
```
The generated documentation is available in the `./docs/api/` folder.


## How to code with a raccoon

### OOP PHP Class
**[Raccoon](https://github.com/hiwelo/raccoon/)** is an OOP-based WordPress template.
All **[Raccoon](https://github.com/hiwelo/raccoon/)**'s classes are placed within the namespace `Hwlo\Raccoon\` and you can find them in the `./lib` directory.

Any custom class that you can create should be placed in a specific namespace.
For example, you can use a namespace like `Hwlo\Raccoon\Custom\`.

When you create a new namespace, you have to add it in the `composer.json` file, in the `autoload` section.
I strongly advice to use a PSR-4 namespace.

For example, if you want to register a custom namespace like `Hwlo\Raccoon\Custom\`, you have to write custom classes in `./custom-lib/` and update `composer.json` like that:
```json
{
  "autoload": {
    "psr-4": {
      "Hwlo\\Raccoon\\": "./lib/",
      "Hwlo\\Raccoon\\Custom\\": "./custom-lib/"
    }
  }
}
```

For each new created class, you may need to regenerate the `./vendor/autoload.php` file. For this operation, please run:
```
composer autoload
```


## How a raccoon can help you with _WordPress_

### A configuration manifest
To avoid multiple initialization functions, **[Raccoon](https://github.com/hiwelo/raccoon/)** uses a _JSON_ configuration file: `manifest.json`.
In this file you can set all features proposed by _WordPress_ to its themes.

### WordPress theme namespace
With Raccoon, you can define a specific namespace for this theme.
This namespace will be mainly used by string translation methods like `__()` or `_e()` or `_x()` or `_n()`.

To define a specific namespace, you have to update `manifest.json` like that:
```json
{
  "namespace": "raccoon"
}
```
If empty or undefined, the default namespace will be `raccoon`.
