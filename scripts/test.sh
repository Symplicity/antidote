#!/bin/sh

if [ "${APP_ROOT}" ]; then
  cd "${APP_ROOT}"
fi

gulp test

dredd

if [ "${CODECLIMATE_REPO_TOKEN}" ]; then
  vendor/bin/phpunit --coverage-clover clover.xml
  vendor/bin/test-reporter --coverage-report clover.xml
elif [ "${APP_ROOT}" ]; then
  vendor/bin/phpunit
else
  vendor/bin/phpunit --coverage-text
fi

./node_modules/phantomjs/bin/phantomjs --webdriver=4444 2>&1 > storage/logs/phantom.log &
gulp phantom

exit 0
