#!/usr/bin/env bash
# Post this on a Curl that will be always reachable.
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

if [ -z "$ANTIDOTE_SESSION_DRIVER" ]; then
  export ANTIDOTE_SESSION_DRIVER=array
fi

if [ -z "$ANTIDOTE_QUEUE_DRIVER" ]; then
  export ANTIDOTE_QUEUE_DRIVER=array
fi

if [ -z "$ANTIDOTE_CACHE_DRIVER" ]; then
  export ANTIDOTE_CACHE_DRIVER=array
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

if [ -z "$ANTIDOTE_API_KEY" ]; then
  echo "Missing \$ANTIDOTE_API_KEY"
  exit 1
fi

if [ -z "$SSL_CERTS_URL" ]; then
  echo "Missing \$SSL_CERTS_URL"
  echo "Proceeding with localhost SSL Cert"
else
  curl -o /tmp/certs.zip $SSL_CERTS_URL
  unzip /tmp/certs.zip -d /var/www/certs/
fi

rm -rf /var/www

if [ -z "$GITREPO_URL" ]; then
  echo "Missing \sGITREPO_URL"
  echo "You have to have some code to checkout!"
  exit 1
else
  touch /root/.ssh/known_hosts
  ssh-keyscan github.com >> /root/.ssh/known_hosts
  git clone $GITREPO_URL /var/www
  cd /var/www
  if [ -z "$GITREPO_BRANCH" ]; then
    echo "defaulting to master branch"
  else
    git checkout $GITREPO_BRANCH
  fi
  unzip -q dist.zip -d /var/www
  cp /var/www/deployment/web/build/env /var/www/.env
fi

sed -i "s/ANTIDOTE_API_KEY/$ANTIDOTE_API_KEY/" /var/www/.env
sed -i "s/ANTIDOTE_DB_PASS/$ANTIDOTE_DB_PASS/" /var/www/.env
sed -i "s/ANTIDOTE_DB_HOST/$ANTIDOTE_DB_HOST/" /var/www/.env
sed -i "s/ANTIDOTE_DB_NAME/$ANTIDOTE_DB_NAME/" /var/www/.env
sed -i "s/ANTIDOTE_DB_USER/$ANTIDOTE_DB_USER/" /var/www/.env
sed -i "s/ANTIDOTE_DB_PORT/$ANTIDOTE_DB_PORT/" /var/www/.env
sed -i "s/ANTIDOTE_CACHE_DRIVER/$ANTIDOTE_CACHE_DRIVER/" /var/www/.env
sed -i "s/ANTIDOTE_SESSION_DRIVER/$ANTIDOTE_SESSION_DRIVER/" /var/www/.env
sed -i "s/ANTIDOTE_QUEUE_DRIVER/$ANTIDOTE_QUEUE_DRIVER/" /var/www/.env
sed -i "s/MAILGUN_SECRET/$MAILGUN_SECRET/" /var/www/.env
sed -i "s/MAILGUN_PASSWORD/$MAILGUN_PASSWORD/" /var/www/.env
sed -i "s/MAILGUN_USERNAME/$MAILGUN_USERNAME/" /var/www/.env
sed -i "s/MAILGUN_DOMAIN/$MAILGUN_DOMAIN/" /var/www/.env
sed -i "s/FDA_TOKEN/$FDA_TOKEN/" /var/www/.env
sed -i "s/;error_log = syslog/error_log = syslog/" /etc/php5/cli/php.ini
sed -i "s/;error_log = syslog/error_log = syslog/" /etc/php5/fpm/php.ini


ln -s /worker /var/www/dist/webhook

if [ "$ANTIDOTE_ROLE" = "worker" ]; then
  cd /var/www
  if [ ! -f /var/www/.dbcomplete ];then
    echo 'Running PHP Artisan Migration'
    if [ -z "$TEST_SITE" ]; then
      php artisan migrate:refresh --seed
  #  elif [ ! -f /var/www/.importdrugs ];then
  #    php artisan import:drugs
  #    touch /var/www/.importdrugs
    else
      php artisan migrate --force
    fi
    echo 'Completed PHP Artisan Migration'
    touch /var/www/.dbcomplete
  else
    echo "DB Migrations already Completed"
  fi
fi
