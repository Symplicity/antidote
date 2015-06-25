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

exit 0
