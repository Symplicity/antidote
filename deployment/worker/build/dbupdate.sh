#!/usr/bin/env bash

cd /var/www
if [[ ! -f /var/www/.dbcomplete ]];then
  logger 'Running PHP Artisan Migration'
  php artisan migrate
  Logger 'Completed PHP Artisan Migration'
  touch /var/www/.dbcomplete
else
  logger "DB Migrations already Completed"
fi
