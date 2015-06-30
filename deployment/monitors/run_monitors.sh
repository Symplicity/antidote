#!/usr/bin/env bash

if [ -f /tmp/run_monitors.lock ]; then
	echo 'already running'
	exit 1
else
	touch /tmp/run_monitors.lock
fi

CURL_TEST_BIN='/var/www/deployment/monitors/web_curl_test.sh'

CHECK_THESE=('https://antidote.symplicity-opensource.com/api/drugs'
'https://antidote.symplicity-opensource.com/api/drugs/1'
'https://antidote.symplicity-opensource.com/api/drugs/2/reviews')

for i in ${CHECK_THESE[*]}; do
	$CURL_TEST_BIN $i
done

rm -f /tmp/run_monitors.lock
