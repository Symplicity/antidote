#!/usr/bin/env bash

if [ -z "$SETUP_SCRIPT_URL" ]; then
  echo "No setup url enabled"
else
  echo "Fetching Setup Script"
  curl -o /setup.sh $SETUP_SCRIPT_URL
  chmod +x /setup.sh
  sh /setup.sh
fi

 /sbin/my_init
