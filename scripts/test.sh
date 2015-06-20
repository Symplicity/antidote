#!/bin/sh

gulp test

dredd

if [ "${CODECLIMATE_REPO_TOKEN}" ]; then
  vendor/bin/phpunit --coverage-clover clover.xml
  vendor/bin/test-reporter --coverage-report clover.xml
else
  vendor/bin/phpunit --coverage-text
fi

exit 0
