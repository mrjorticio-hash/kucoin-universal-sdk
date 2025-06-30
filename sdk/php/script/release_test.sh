#!/bin/bash
set -e

echo "USE_LOCAL = ${USE_LOCAL:-<unset>}"

echo '{
  "name": "test/test",
  "description": "Test project",
  "type": "project",
  "require": {},
  "minimum-stability": "alpha",
  "prefer-stable": true
}' > composer.json


if [[ "${USE_LOCAL,,}" == "true" ]]; then
    echo "Installing local package(s)…"
    ls /tmp/*.tar 1>/dev/null 2>&1
    mkdir -p ./local-packages
    cp /tmp/*.tar ./local-packages/
    composer config repositories.local artifact ./local-packages
    composer require kucoin/kucoin-universal-sdk
else
    echo "📦 Installing kucoin-universal-sdk from Packagist…"
    composer require kucoin/kucoin-universal-sdk
fi

echo "Echo depends..."
composer depends php

echo "Running service scripts..."
cp /src/tests/regression/RunServiceTest.php /app
composer require phpunit/phpunit
composer require ramsey/uuid
vendor/bin/phpunit --colors --bootstrap vendor/autoload.php RunServiceTest.php

echo "Running example scripts..."
cd /app/example
php ExampleAPI.php
php ExampleGetStarted.php
php ExampleSign.php
php ExampleWs.php