#!/bin/sh

composer install --prefer-dist --no-dev

npm install

bower update

gulp

rm -f dist.zip
zip -r9 dist.zip dist vendor

git add dist.zip
git commit -m "release update --skip-ci"

