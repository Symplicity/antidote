#!/usr/bin/env bash
cd /var/www
echo "Running Update Now\n"
OLD_GITSHORT=`git rev-parse --short HEAD`

echo "Pulling Latest code from $GITREPO_BRANCH"
cp /var/www/.env /var/.env
git checkout master
git branch -D production
git pull origin
git checkout $GITREPO_BRANCH
rm -rf /var/www/{dist,vendor}
unzip -q dist.zip -d /var/www/
cp /var/.env /var/www/.env

ln -s /worker/ /var/www/dist/webhook
GITSHORT=`git rev-parse --short HEAD`

if [ "$ANTIDOTE_ROLE" = "worker" ]; then
  echo "Running Migrations"
  php artisan migrate --force
fi

/var/www/scripts/notify.sh SUCCESS "Deployed! Now running ${GITSHORT}, happened on ${HOSTNAME}"
