#!/bin/sh

# If used on Codeship, uncomment the following line
# phpenv local 5.6

# Install dependencies through Composer
composer install --prefer-dist --no-interaction --optimize-autoloader

# We will use actual env variables, just make a blank file
touch .env

# Install node and bower modules
npm config set cache "${HOME}/cache/npm/"
npm install
npm install -g bower
bower install --config.interactive=false

# Build the site
gulp
