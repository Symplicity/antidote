#!/bin/sh

# If used on Codeship, uncomment the following line
# phpenv local 5.6

set -e

# make sure we have a full checkout to be able to branch
git fetch --unshallow || true
git fetch origin "+refs/heads/*:refs/remotes/origin/*"

# update dependencies
unzip -q dist.zip
composer install --prefer-dist --no-dev --no-interaction --optimize-autoloader
npm config set cache "${HOME}/cache/npm/" && npm install
npm install -g bower && bower update --config.interactive=false

# build production files
gulp

# zip build files and push them to production branch
rm -f dist.zip && zip -r9 dist.zip dist vendor
git config --global user.email "your-machine-user@email.com" && git config --global user.name "your-machine-user"
git commit -m "release update --skip-ci" dist.zip
git branch -f production `git rev-parse HEAD` && git checkout production && git push -f --set-upstream origin production

# Update API documentation, assumes APIARY_API_KEY in the environment
gem install apiaryio
cat docs/api/index.apib docs/api/drugs.apib > index.apib
apiary publish --api-name=antidote --path=index.apib
