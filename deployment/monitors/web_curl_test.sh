#!/usr/bin/env bash

METRICS=`curl -o /dev/null -s -w %{time_namelookup}:%{time_connect}:%{time_pretransfer}:%{time_starttransfer}:%{time_total} $1`
#PUSH_GATEWAY_URL='http://pushgateway.monstack.noc-symp.svc.tutum.io:9091/metrics/jobs/curl_perf/instances'

IFS=':' read -ra ARR <<< "$METRICS"

TIME_NAMELOOKUP=${ARR[0]}
TIME_CONNECT=${ARR[1]}
TIME_PRETRANSFER=${ARR[2]}
TIME_STARTTRANSFER=${ARR[3]}
TIME_TOTAL=${ARR[4]}

CLEANURL=`echo $1 | sed -e 's/\//_/g'`

cat <<EOF | curl --data-binary @- $PUSH_GATEWAY_URL/$CLEANURL
# TYPE curl_time_namelookup gauge
curl_time_namelookup $TIME_NAMELOOKUP
# TYPE curl_time_connect gauge
curl_time_connect $TIME_CONNECT
# TYPE curl_time_pretransfer gauge
curl_time_pretransfer $TIME_PRETRANSFER
# TYPE curl_time_starttransfer gauge
curl_time_starttransfer $TIME_STARTTRANSFER
# TYPE curl_time_total gauge
curl_time_total $TIME_TOTAL
EOF

