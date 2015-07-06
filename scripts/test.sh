#!/bin/sh

if [ "${APP_ROOT}" ]; then
  cd "${APP_ROOT}"
fi

gulp test

dredd docs/api/*.apib http://127.0.0.1/api --reporter=dots -h "Authorization:JWT `php artisan --no-ansi make:token --user=test --expire=300`"

if [ "${APP_ROOT}" ]; then
  vendor/bin/phpunit
else
  vendor/bin/phpunit --coverage-text
fi

./node_modules/phantomjs/bin/phantomjs --webdriver=4444 2>&1 > storage/logs/phantom.log &
gulp protractor

exit 0
