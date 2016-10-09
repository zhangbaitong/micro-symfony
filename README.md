# micro-symfony
A php micro framework based on the symfony components.

## Dependency Management

```
wget http://getcomposer.org/composer.phar
php composer.phar install
composer require symfony/http-foundation
composer update
```

## run server
php -S 127.0.0.1:8081 -t web/ web/front.php

access for [page](http://localhost:8081/is_leap_year/2014)
## update composer autoload
composer dump-autoload

## install and useage unit test
local install:

composer require --dev "phpunit/phpunit=5.5.*"

global install:

composer global require "phpunit/phpunit=5.5.*"

run phpunit:

./vendor/bin/phpunit

## unit test coverage
output html:

./vendor/bin/phpunit --coverage-html=cov/

output console:

./vendor/bin/phpunit --coverage-text

## install xdebug for coverage

```
brew update -v
brew search xdebug
brew install homebrew/php/php70-xdebug
```