# Raccoon WordPress starter [![Build Status](https://travis-ci.org/hiwelo/raccoon.svg?branch=develop)](https://travis-ci.org/hiwelo/raccoon)
Raccoon is a personal WordPress starter theme based on Composer, NPM, Babel (ES 2015), Gulp and Knacss

## Requirements
For its development, this project require:
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
composer vendor-update
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
