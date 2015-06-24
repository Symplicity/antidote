#!/bin/sh

echo "Resetting the database, updating composer and bower dependencies, rebuilding static assets"

if [ "${APP_ROOT}" ]; then
  cd "${APP_ROOT}"
fi

composer install --prefer-source --no-interaction

./artisan migrate:reset
./artisan migrate
./artisan db:seed

bower update --allow-root --config.interactive=false

gulp
