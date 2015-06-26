#!/bin/sh

if [ "${APP_ROOT}" ]; then
  cd "${APP_ROOT}"
fi

composer install --prefer-dist --no-interaction

./artisan migrate

bower update --allow-root --config.interactive=false

gulp
