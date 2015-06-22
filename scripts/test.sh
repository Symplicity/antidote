#!/bin/sh

gulp test

# uncomment when ready to run the site and dredd in CI
# dredd

if [ "${CODECLIMATE_REPO_TOKEN}" ]; then
  vendor/bin/phpunit --coverage-clover clover.xml
  vendor/bin/test-reporter --coverage-report clover.xml
else
  vendor/bin/phpunit --coverage-text
fi

exit 0
