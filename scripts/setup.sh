#!/bin/sh

if [ "${APP_ROOT}" ]; then
  cd "${APP_ROOT}"
fi

composer install --prefer-source --no-interaction

./artisan migrate
./artisan db:seed

# Install node modules
if [ ! -e node_modules ]; then
  npm config set cache "${HOME}/cache/npm/"
  npm install
  npm install -g bower
fi

bower install --allow-root

gulp
