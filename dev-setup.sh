#!/usr/bin/env bash

# setup a git hook to check your commits before you push them
cp scripts/pre-commit .git/hooks/

eval "$(boot2docker shellinit)"

docker-compose up -d
docker exec -it antidote_web_1 /var/www/scripts/setup.sh

open http://192.168.59.103/

docker exec -it antidote_web_1 /bin/bash
