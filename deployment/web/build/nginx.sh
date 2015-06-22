#!/usr/bin/env bash

export DB_HOST=$RDS_HOSTNAME
export DB_PORT=$RDS_PORT
export DB_USERNAME=$RDS_USERNAME
export DB_PASSWORD=$RDS_PASSWORD
export DB_NAME=$RDS_DB_NAME

nginx
