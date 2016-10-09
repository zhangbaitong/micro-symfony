# micro-symfony
A php micro framework based on the symfony components.

## Dependency Management
Composer

## run server
php -S 127.0.0.1:8081 -t web/ web/front.php

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