#!/usr/bin/env bash

echo "Running Update Now\n"

cd /var/www
echo "Pulling Latest code from $GITREPO_BRANCH"
git pull origin $GITREPO_BRANCH
unzip -q dist.zip -d /var/www/

if [ "$ANTIDOTE_ROLE" = "worker" ]; then
  echo "Running Migrations"
  php artisan migrate --force
fi
/var/www/scripts/notify.sh SUCCESS "Deploy happened."
