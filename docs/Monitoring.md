### Monitoring Methodology

The monitoring methodology for this project is based on the collection of metrics from all layers of the serving infrastructure, with the use of modern open source technologies which allow us to quickly analyze and correlate these metrics for data driven decision making.

#### Core Technologies Used

[Prometheus](https://github.com/prometheus/prometheus) - An open source service monitoring system and time series database which provides central metric gathering, statistical analytics, data visualization, health dashboards, and alerting, with out of box support for various open source exporters that run on hosts and containers and provide metrics gathered by central server.  Deployed in docker containers and scalable.

ELK Stack - [ElasticSearch](https://github.com/elastic/elasticsearch), [LogStash](https://github.com/elastic/logstash), [Kibana](https://github.com/elastic/kibana)
 (open source, self-hostable with as-a-service available as well) - Logs of all types are shipped using open protocols to ELK which provides detailed analytics, searching, and correlations.  Deployed in docker containers and scalable.

#### Monitor Types

 - Application performance monitoring from end-user POV
  - custom scripts which test app sections and API endpoints, and push metrics to prometheus
   - [web_curl_test.sh](https://github.com/Symplicity/antidote/blob/master/deployment/monitors/web_curl_test.sh)
   - [run_monitors.sh](https://github.com/Symplicity/antidote/blob/master/deployment/monitors/run_monitors.sh)
 - Physical host system monitoring
  - [node_exporter](https://github.com/prometheus/node_exporter) running on each physical host, reporting system-level metrics (memory/cpu/networking/etc) through a prometheus-supported /metrics endpoint
 - Container/Component-level monitoring
  - [cadvisor](https://github.com/google/cadvisor) running as a docker container on each physical host, reporting container-level metrics through a prometheus-supported /metrics endpoint
  - [haproxy_exporter](https://github.com/prometheus/haproxy_exporter/) running as a docker container to expose haproxy load balancer stats in prometheus-supported /metrics format


### Logging

 - In a containerized environment where most components are ephemeral, dynamic, and typically without login shells, central logging is very important
 - We've tackled this by sending logs to local syslog daemons on containers, which forward the logs to a centralized LogStash instance, which then parses and transforms the log data, before it indexes it in ElasticSearch, with Kibana as a front-end for log searching and realtime monitoring of log data

### Visualization and Analysis

 - [PromDash](https://github.com/prometheus/promdash) gives us realtime visualization of stats gathered by Prometheus, with customizable dashboards and ability to drill down and zoom in on specific events. 
Examples from our environment:
PromDash Backend Node Load
![](https://github.com/Symplicity/antidote/blob/master/docs/attachments/promdash_backend_node_load.png)
PromDash API Response Time
![](https://github.com/Symplicity/antidote/blob/master/docs/attachments/promdash_api_response_time.png)
 - Kibana gives us realtime log searching and visualization of metrics gleaned from logs by LogStash, such as the number of 500 errors per minute, or the average response time over the last 5 minutes. Examples from our environment:
Kibana Error Host Search:
![](https://github.com/Symplicity/antidote/blob/master/docs/attachments/kibana_error_host_search.png)
Kibana Host HAProxy Stats:
![](https://github.com/Symplicity/antidote/blob/master/docs/attachments/kibana_host_haproxy_stats.png)
 - Prometheus also gives us the ability to do complex queries on the metrics stored in its time series database, such as percentile calculations (median, 95th, 98th, etc), as well as other realtime aggregate calculations to pinpoint exactly how metrics correlate
 - Realtime data stored in prometheus backend can be used to drive external client-facing metrics pages for more transparency

### Alerting

 - Prometheus supports alerting rules which allow us to define alert conditions like a host being down or an endpoint having higher than acceptable latency
 - Notifications are then sent using AlertManager or other custom scripts, to a number of supported mediums including SMS/email/Slack
 - The alerting methodology used is based on modern web app alerting philosophy discussed [here](https://docs.google.com/document/d/199PqyG3UsyXlwieHaqbGiWVa8eMWi8zzAn0YfcApr8Q) where the aim is to focus on having as few alerts as possible, and alerting on end-user performance problems as opposed to trying to catch every possible cause

### Deployment
 
 - Prometheus and supporting components all run in docker containers in any docker-supported environment and can be orchestrated from our stack file [here](https://github.com/Symplicity/antidote/blob/master/deployment/prometheus/stackfile.yml) and configuration files [here](https://github.com/Symplicity/antidote/tree/master/deployment/prometheus)
 - ELK Stack is integrated into main stackfile [here](https://github.com/Symplicity/antidote/blob/master/deployment/tutum-stack.yml) with LogStash configuration [here](https://github.com/Symplicity/antidote/blob/master/deployment/logstash/antidote.conf)