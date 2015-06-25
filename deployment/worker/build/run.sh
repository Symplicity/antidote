#!/usr/bin/env bash

if [ -z "$ANTIDOTE_DB_USER" ]; then
  echo "Missing \$ANTIDOTE_DB_USER"
  exit 1
fi

if [ -z "$ANTIDOTE_DB_PORT" ]; then
  echo "Missing \$ANTIDOTE_DB_PORT"
  exit 1
fi

if [ -z "$ANTIDOTE_DB_NAME" ]; then
  echo "Missing \$ANTIDOTE_DB_NAME"
  exit 1
fi

if [ -z "$ANTIDOTE_DB_PASS" ]; then
  echo "Missing \$ANTIDOTE_DB_PSS"
  exit 1
fi

if [ -z "$ANTIDOTE_DB_HOST" ]; then
  echo "Missing \$ANTIDOTE_DB_HOST"
  exit 1
fi

if [ -z "$FDA_TOKEN" ]; then
  echo "Missing \$FDA_TOKEN"
  exit 1
fi

if [ -z "$MAILGUN_DOMAIN" ]; then
  echo "Missing \$MAILGUN_DOMAIN"
  exit 1
fi

if [ -z "$MAILGUN_USERNAME" ]; then
  echo "Missing \$MAILGUN_USERNAME"
  exit 1
fi

if [ -z "$MAILGUN_PASSWORD" ]; then
  echo "Missing \$MAILGUN_PASSWORD"
  exit 1
fi

if [ -z "$MAILGUN_SECRET" ]; then
  echo "Missing \$MAILGUN_SECRET"
  exit 1
fi

if [ -z "$SSL_CERTS_URL" ]; then
  echo "Missing \$SSL_CERTS_URL"
  echo "Proceeding with localhost SSL Cert"
else
  curl -O /tmp/certs.zip $SSL_CERTS_URL
  unzip /tmp/certs.zip -d /var/www/certs/
fi

if [ -z "$GITREPO_URL" ]; then
  echo "Missing \sGITREPO_URL"
  echo "You have to have some code to checkout!"
  exit 1
else
  touch /root/.ssh/known_hosts
  ssh-keyscan github.com >> /root/.ssh/known_hosts
  git clone $GITREPO_URL /tmp/REPO
  cd /tmp/REPO
  unzip dist.zip -d /var/www
fi

sed -i "s/ANTIDOTE_DB_PASS/$ANTIDOTE_DB_PASS/" /var/www/.env
sed -i "s/ANTIDOTE_DB_HOST/$ANTIDOTE_DB_HOST/" /var/www/.env
sed -i "s/ANTIDOTE_DB_NAME/$ANTIDOTE_DB_NAME/" /var/www/.env
sed -i "s/ANTIDOTE_DB_USER/$ANTIDOTE_DB_USER/" /var/www/.env
sed -i "s/ANTIDOTE_DB_PORT/$ANTIDOTE_DB_PORT/" /var/www/.env
sed -i "s/MAILGUN_SECRET/$MAILGUN_SECRET/" /var/www/.env
sed -i "s/MAILGUN_PASSWORD/$MAILGUN_PASSWORD/" /var/www/.env
sed -i "s/MAILGUN_USERNAME/$MAILGUN_USERNAME/" /var/www/.env
sed -i "s/MAILGUN_DOMAIN/$MAILGUN_DOMAIN/" /var/www/.env
sed -i "s/FDA_TOKEN/$FDA_TOKEN/" /var/www/.env

cd /var/www
if [[ ! -f /var/www/.dbcomplete ]];then
  logger 'Running PHP Artisan Migration'
  php artisan migrate
  Logger 'Completed PHP Artisan Migration'
  touch /var/www/.dbcomplete
else
  logger "DB Migrations already Completed"
fi


/sbin/my_init
