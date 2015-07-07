### Deployment
To deploy this project you will need Docker, docker-machine, docker-compose and docker-swarm. You can also use a service like [tutum.co](http://tutum.co) for orchestration, but that is not required. Antidote can be deployed locally on a single box, or with docker-swarm using docker-compose.

### Technologies Used
We used the following open source technologies:

- [Docker](http://www.docker.com)
- [MariaDB](http://www.mariadb.com)
- [HAProxy](http://www.haproxy.org)
- [NGINX](http://www.nginx.org)
- [PHP](http://www.php.com)
- [Ubuntu](http://ubuntu.com)
- [Git](http://git-scm.com)
- [RabbitMQ](http://rabbitmq.com)
- [Memcached](http://memcached.org)
- [arbeider](https://github.com/moos3/arbeider)
- [ElasticSearch](http://elasticsearch.com)
- [Logstash](http://logstash.net)
- [Kibana](https://www.elastic.co/products/kibana)
- [Prometheus](http://prometheus.io/)

### Containers
We built a pair of custom containers to make certain things easier to deploy:

- symplicity/webserver (NGINX + PHP-FPM)
- symplicity/worker (Runs Cron Jobs and Executes PHP code)

We also used these community-contributed containers from Docker Hub without modification:

- mariadb/mariadb (MariaDB -- Opensource MySQL)
- tutum/haproxy (Haproxy Load Balancer with autoconfiguration without a lot of work)
- logstash:latest  (Log Indexing)
- gliderlabs/logspout:latest (Log forwarder)
- vfarcic/kibana:latest (Log Search WebUI)
- memcached:latest  (Caching layer)
- rabbitmq:latest  (Queuing layer)


### Configuration Management
All configuration is handled by Docker files and Docker environmental variables. Dockerfiles for our custom web server images are included in the repository so that users can make additional changes as needed.

#### Web Servers and Worker
Web servers and worker are going to expect the following environmental variables to be set in order to start:

```
environment:
  - ANTIDOTE_API_KEY=  # Set a 256 bit string, this is for the encryption of the API 
  - ANTIDOTE_DB_HOST=db   # point to db host
  - ANTIDOTE_DB_NAME=antidote  # Set to your Database Name
  - ANTIDOTE_DB_PASS=antidoteSecret  # Set to your database user's password
  - ANTIDOTE_DB_PORT=3306  # Set to the port you're going to use for MySQL
  - ANTIDOTE_DB_USER=antidote  # Set to your database user
  - ANTIDOTE_ROLE= # Set this to either web or worker, depending on the Image
  - FDA_TOKEN=  # Set this to your openFDA API key
## Caching Settings
  - ANTIDOTE_CACHE_DRIVER= # Set this to Memcached if you want to use Memcache. Otherwise don't declare it.
  - MEMCACHED_HOST= # Set this to the Memcache server name
  - MEMCACHED_PORT=11211 # Set to default port, change this if you're not running Memcached on the standard port.
## Mailgun Settings for sending emails
  - MAILGUN_DOMAIN=  # Set this to your Mailgun Domain
  - MAILGUN_PASSWORD=   # Set this to your Password for Mailgun
  - MAILGUN_SECRET=   # Set this to your Mailgun Secret Key
  - MAILGUN_USERNAME= # Set this to your Mailgun username
```

Above variables are enough for running the site. For an automated deployment triggered by a web hook call from a CI server, these variables are needed as well:

```
## Git Repo Settings
  - GITREPO_URL= # Set this your fork of Antidote by Symplicity or use the Main Symplicity one
  - GITREPO_BRANCH= # Set this to the Git branch with the production build. Must contain a dist.zip file with vendor and dist folders in it
## Rabbit MQ Settings for the Worker scripts
  - RABBITMQ_NAME= # Set this to the name which you want webhook for Codeship to notify nodes 
  - RABBITMQ_NODE= # Set this to the name of the RabbitMQ server.
  - RABBITMQ_PORT=5672 # default port for RabbitMQ, change this if not using default.
  - WEBHOOK_API_KEY= # Set a 256 bit key for the webhook URL for [arbeider](https://github.com/moos3/arbeider)
  - WORKER_API_KEY= # Set a 256 bit key for the workers API key. Authenticates the RabbitMQ messages.
```

We recommend using Mailgun by Rackspace as your mail server. It can scale based on actual load, and removes a complex piece from the infrastructure. Mailgun can easily be replaced with a SMTP service if needed.

#### Load Balancer

For the load balancers you will need to obtain a SSL certificate. You can run it on http port 80 without SSL, but we advise against that. You will need to set the following environmental variables:

```
- 'SSL_CERT=<cert here>'

```
To create a certificate, run `cat ssl-server.key ssl-server.cert >> cert.pem` followed by `awk 1 ORS='\\n' cert.pem`

Take that output and paste it in SSL_CERT= . This will setup the load balancer for SSL.

Other Environmental Variables that should be set are:

```
environment:
  - BACKEND_PORT=80
  - BALANCE=roundrobin
  - FRONTEND_PORT=80
  - MAXCONN=4096
  - MODE=http
  - 'SSL_BIND_CIPHERS=ECDH+AESGCM:DH+AESGCM:ECDH+AES256:DH+AES256:ECDH+AES128:DH+AES:ECDH+3DES:DH+3DES:RSA+AESGCM:RSA+AES:RSA+3DES:!aNULL:!MD5:!DSS'
  - 'SSL_BIND_OPTIONS=no-sslv3 no-tls-tickets'
  - 'TIMEOUT=connect 5000,client 50000,server 500000'
  - 'OPTION=redispatch,httplog,dontlognull,forwardfor'
```
These are standard HAProxy settings. See more in HAProxy's documentation. Don't change the BACKEND_PORT or the FRONTEND_PORT, when you set the SSL_CERT the HAProxy configuration script will automatically make it so that http gets redirected to https.

#### Database
The database image is going to accept the following environmental variables to be set:

```
environment:
  - MYSQL_DATABASE=antidote
  - MYSQL_PASSWORD=antidoteSecret
  - MYSQL_ROOT_PASSWORD=rootSecret
  - MYSQL_USER=antidote
  - TERM=dumb
```

### Local Deployment
After you've got everything above working, you will next configure things locally for testing.  You will need to make the following docker-compose.yml setup locally. To use this locally you will need either boot2docker (Mac and Windows Users) or Docker installed on your Linux machine.
You will see in this version of the local docker-compose, a load balancer and SSL were not utilized on the web server. The bare minimum containers that are needed for the application to work in a local environment are utilized. See "Production Deployment" for details on what is needed to run this in production.

```
db:
  image: 'mariadb:latest'
  environment:
    - MYSQL_DATABASE=antidote
    - MYSQL_PASSWORD=antidoteSecret
    - MYSQL_ROOT_PASSWORD=rootSecret
    - MYSQL_USER=antidote
    - TERM=dumb
web:
  image: 'symplicity/webserver:latest'
  ports:
    - '80:80'
  environment:
    - ANTIDOTE_API_KEY=  # Set a 256 bit string, this is for the encryption of the API 
    - ANTIDOTE_DB_HOST=db   # point to db host
    - ANTIDOTE_DB_NAME=antidote  # Set to your Database Name
    - ANTIDOTE_DB_PASS=antidoteSecret  # Set to your database users password
    - ANTIDOTE_DB_PORT=3306  # Set to the port your going to use for MySQL
    - ANTIDOTE_DB_USER=antidote  # Set to your database user
    - ANTIDOTE_ROLE= # Set this to either web or worker, depending on the Image
    - FDA_TOKEN=  # Set this to your openFDA API key
## Caching Settings
    - ANTIDOTE_CACHE_DRIVER= # Set this to memcached if you want to use memcache. Otherwise don't declare it.
    - MEMCACHED_HOST= # Set this to the Memcache server name
    - MEMCACHED_PORT=11211 # Set to default port, change this if your not running memcached on the standard port.
## Git Repo Settings
    - GITREPO_URL= # Set this your fork of Antidote by Symplicity or use the Main Symplicity one
    - GITREPO_BRANCH= # Set this to the Git branch with the production build. Must contain a dist.zip file with vendor and dist in it
## Rabbit MQ Settings for the Worker scripts
    - RABBITMQ_NAME= # Set this to the name which you want webhook for Codeship to notify nodes 
    - RABBITMQ_NODE= # Set this to the name of the RabbitMQ server.
    - RABBITMQ_PORT=5672 # default port for RabbitMQ, change this if not using default.
    - WEBHOOK_API_KEY= # Set a 256 Bit key for the webhook URL for [arbeider](https://github.com/moos3/arbeider)
    - WORKER_API_KEY= # Set a 256 bit key for the workers API key. Authenticates the RabbitMQ messages.
## Mailgun Settings for sending emails
    - MAILGUN_DOMAIN=  # Set this to your Mailgun Domain
    - MAILGUN_PASSWORD=   # Set this to your Password for Mailgun
    - MAILGUN_SECRET=   # Set this to your Mailgun Secret Key
    - MAILGUN_USERNAME= # Set this to your Mailgun username

  links:
    - db
    - rabbitmq
worker:
  image: 'symplicity/worker:latest'
  environment:
- ANTIDOTE_API_KEY=  # Set a 256 bit string, this is for the encryption of the API 
  - ANTIDOTE_DB_HOST=db   # point to db host
  - ANTIDOTE_DB_NAME=antidote  # Set to your Database Name
  - ANTIDOTE_DB_PASS=antidoteSecret  # Set to your database users password
  - ANTIDOTE_DB_PORT=3306  # Set to the port your going to use for MySQL
  - ANTIDOTE_DB_USER=antidote  # Set to your database user
  - ANTIDOTE_ROLE= # Set this to either web or worker, depending on the Image
  - FDA_TOKEN=  # Set this to your openFDA API key
## Caching Settings
  - ANTIDOTE_CACHE_DRIVER= # Set this to Memcached if you want to use memcache. Otherwise don't declare it.
  - MEMCACHED_HOST= # Set this to the Memcache server name
  - MEMCACHED_PORT=11211 # Set to default port, change this if your not running Memcached on the standard port.
## Git Repo Settings
  - GITREPO_URL= # Set this your fork of Antidote by Symplicity or use the Main Symplicity one
  - GITREPO_BRANCH= # Set this to the Git branch with the production build. Must contain a dist.zip file with vendor and dist in it
## Rabbit MQ Settings for the Worker scripts
  - RABBITMQ_NAME= # Set this to the name which you want webhook for Codeship to notify nodes 
  - RABBITMQ_NODE= # Set this to the name of the RabbitMQ server.
  - RABBITMQ_PORT=5672 # default port for RabbitMQ, change this if not using default.
  - WEBHOOK_API_KEY= # Set a 256 Bit key for the webhook URL for [arbeider](https://github.com/moos3/arbeider)
  - WORKER_API_KEY= # Set a 256 bit key for the workers API key. Authenticates the RabbitMQ messages.
## Mailgun Settings for sending emails
  - MAILGUN_DOMAIN=  # Set this to your Mailgun Domain
  - MAILGUN_PASSWORD=   # Set this to your Password for Mailgun
  - MAILGUN_SECRET=   # Set this to your Mailgun Secret Key
  - MAILGUN_USERNAME= # Set this to your Mailgun username
    - TEST_SITE=yes  # This var enabled random data seeding.
  links:
    - db
    - rabbitmq
```

After the docker-compose.yml is written, you will need to `docker-comose up`. This will start up a MariaDB, RabbitMQ, web server and worker VM.

Once they all come up you should be able to connect to your Docker IP or boot2docker IP.

#### Production
If you want to deploy this in a production environment it is appropriate to setup some nodes with docker-machine and docker-swarm. You would then use docker-compose to deploy using the docker-swarm. Alternatively, you can use Tutum as your orchestration service and use the tutum.yml file to deploy this on Amazon AWS, Digital Ocean, Microsoft Azure and Softlayer by IBM. You should use at least 3 nodes and if you have spare hardware you can also bring your own nodes to Tutum. We have tried both AWS and Digital Ocean with Tutum. You can have nodes in more than one provider to give your site HA on the IaaS level. If you need to change crons on the worker node, you just need to edit `deployments/jobs/antidote-cron` this file is reloaded on every update.

###### Recommendations for Production
It is recommended to put the db container on a node with at least 4 GB of RAM and 2 CPUs. Putting a load balancer in front of the web nodes that terminates SSL is also recommended. The web servers don't require a large amount of memory so 512 GB or 1 GB should be enough. A final recommendation is to stand up a Memcached server for the site to use. 