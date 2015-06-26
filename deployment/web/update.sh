#!/usr/bin/env bash

echo "Running Update Now\n"

MIGRATION=$1

if [ "$ANTIDOTE_ROLE" == "web" ]; then
  cd /var/www
  echo "Pulling Latest code from $GITREPO_BRANCH"
  git pull origin $GITREPO_BRANCH
  unzip dist.zip /var/www/
fi

if [ "$ANTIDOTE_ROLE" == "worker" ]; then
  cd /var/www
  echo "Pulling Latest code from $GITREPO_BRANCH"
  git pull origin $GITREPO_BRANCH
  unzip dist.zip /var/www/
  echo "Running Migrations"
  if [ -z "$MIGRATION" ];then
    php artisan $MIGRATION
  else
    php artisan migrate
  fi
fi
