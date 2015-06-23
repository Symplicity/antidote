#!/bin/sh

if [ "${APP_ROOT}" ]; then
  cd "${APP_ROOT}"
fi

# Install node modules
if [ ! -e node_modules ]; then
  npm config set cache "${HOME}/cache/npm/"
  npm install
fi

bower update --allow-root

gulp
