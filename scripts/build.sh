#!/bin/sh

# If used on Codeship, uncomment the following line
# phpenv local 5.6

# Install dependencies through Composer
composer install --prefer-dist --no-interaction --optimize-autoloader
# We will use actual env variables, just make a blank file
touch .env

# Run unit tests, assumes CODECLIMATE_REPO_TOKEN
vendor/bin/phpunit --coverage-clover clover.xml
vendor/bin/test-reporter --coverage-report clover.xml

# Prepare db for API tests, assumes Codeship DB setup
export DB_CONNECTION="mysql"
export DB_USERNAME=$MYSQL_USER
export DB_PASSWORD=$MYSQL_PASSWORD
export DB_DATABASE="test"
./artisan migrate --seed

# Start local server and run API tests
php -S 127.0.0.1:8000 -t public/ > storage/logs/dredd-server.log 2>&1 &
# Lets not show the real commiter email in public test results on apiaryio
export CI_COMMITTER_EMAIL="dredd@test.com"

npm install -g dredd
dredd docs/api/*.apib http://127.0.0.1:8000/api --reporter=apiary -h "Authorization:JWT `php artisan --no-ansi make:token --user=test --expire=60`"

# Install node and bower modules
npm config set cache "${HOME}/cache/npm/"
npm install
npm install -g bower
bower install --config.interactive=false

# Build and karma-test the site
gulp
gulp test

# Lets test a partial import of real FDA data, may require OPENFDA_API_KEY
php artisan import:drugs --limit=5

# Run internal server for e2e tests
php -S 127.0.0.1:8000 -t public/ > storage/logs/e2e-server.log 2>&1 &
nohup ./node_modules/phantomjs/bin/phantomjs --webdriver=4444 &
gulp protractor
