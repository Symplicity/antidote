### Monitoring Methodology

The monitoring methodology for this project is based on the collection of metrics from all layers of the serving infrastructure, with the use of modern open-source technologies which allow us to quickly analyze and correlate these metrics for data driven decision making.  The three general categories of monitoring are listed below:

 - Application performance monitoring from end-user POV
  - custom scripts which test app sections and API endpoints (link)
 - Physical host system monitoring
  - node_exporter (link)
 - Container/Component-level monitoring
  - cadvisor (link)

### Logging

 - In a containerized environment where many components are empemeral and without login shells, central logging is very important
 - We've tackled this by sending logs to local syslog daemons on containers, which forward the logs to a centralized LogStash instance, which then indexes the log data in ElasticSearch, with Kibana as a front-end for log searching and realtime monitoring

### Visualization and Analysis

 - PromDash gives us realtime visualization of stats gathered by Prometheus, with customizable dashboards and ability to drill down and zoom in on specific events
 - Kibana gives us realtime log searching and visualization of metrics gleaned from logs by LogStash, such as the number of 500 errors per minute, or the average response time over the last 5 minutes
 - Prometheus also gives us the ability to do complex queries on the metrics stored in its time series database, such as percentile calculations (median, 95th, 98th, etc), as well as other realtime aggregate calculations to pinpoint exactly how metrics correlate

### Alerting

 - Prometheus supports alerting rules which allow us to define alert conditions like a host being down or an endpoint having higher than acceptable latency
 - Notifications are then sent using AlertManager or other custom scripts, to a number of supported mediums including SMS/email/Slack
 - The alerting methodology used is based on modern web app alerting philosophy discussed here https://docs.google.com/document/d/199PqyG3UsyXlwieHaqbGiWVa8eMWi8zzAn0YfcApr8Q where the aim is to focus on having as few alerts as possible, and alerting on end-user performance problems as opposed to trying to catch every possible cause of pain
 - Prometheus alert definitions, sample of our config
 - AlertManager setup, sample config
