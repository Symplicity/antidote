#!/usr/bin/env bash
cd /var/www
echo "Running Update Now\n"
OLD_GITSHORT=`git rev-parse --short HEAD`

echo "Pulling Latest code from $GITREPO_BRANCH"
git pull origin $GITREPO_BRANCH
rm -rf /var/www/{dist,vendor}
unzip -q dist.zip -d /var/www/

ln -s /worker/ /var/www/dist/webhook
GITSHORT=`git rev-parse --short HEAD`

if [ "$ANTIDOTE_ROLE" = "worker" ]; then
  echo "Running Migrations"
  php artisan migrate --force
fi

/var/www/scripts/notify.sh SUCCESS "Deployed! Was Running ${OLD_GITSHORT} now running ${GITSHORT}, happened on ${HOSTNAME}"
