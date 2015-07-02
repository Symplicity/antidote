#!/bin/bash

# Adapted from https://github.com/ericoc/zabbix-slack-alertscript
# Assumes SLACK_TOKEN is set in the environment

# Slack incoming web-hook URL and user name
url="https://hooks.slack.com/services/${SLACK_TOKEN}"
username='Antidote'

# Subject = $1 (FAILURE or SUCCESS)
# Message = $2

to="#entic"
subject="$1"

if [ "$subject" == 'SUCCESS' ]; then
	emoji=':smile:'
elif [ "$subject" == 'FAILURE' ]; then
	emoji=':frowning:'
else
	emoji=':ghost:'
fi

message="${subject}: $2"

# Build our JSON payload and send it as a POST request to the Slack incoming web-hook URL
payload="payload={\"channel\": \"${to}\", \"username\": \"${username}\", \"text\": \"${message}\", \"icon_emoji\": \"${emoji}\"}"
curl -m 5 --data-urlencode "${payload}" $url
echo
