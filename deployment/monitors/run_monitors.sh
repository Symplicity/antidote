#!/usr/bin/env bash

lockfile -r 0 /tmp/run_monitors.lock || exit 1

CURL_TEST_BIN='/var/www/deployment/monitors/web_curl_test.sh'

CHECK_THESE=('https://antidote.symplicity-opensource.com/api/drugs'
'https://antidote.symplicity-opensource.com/api/drugs/1'
'https://antidote.symplicity-opensource.com/api/drugs/2/reviews')

for i in ${CHECK_THESE[*]}; do
	$CURL_TEST_BIN $i
done

rm -f /tmp/run_monitors.lock
