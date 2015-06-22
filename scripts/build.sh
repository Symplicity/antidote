#!/bin/sh

if [ "${APP_ROOT}" ]; then
  cd "${APP_ROOT}"
fi

composer install --prefer-source --no-interaction

# In CI we use actual env variables, just make a blank file
touch .env

# Install node modules
if [ ! -e node_modules ]; then
  npm config set cache "${HOME}/cache/npm/"
  npm install
  npm install -g bower
  npm install -g dredd
fi

bower install --allow-root

gulp

exit 0
