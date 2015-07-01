### Deployment
To deploy this project you will need docker, docker-machine, docker-compose and docker-swarm. You can also use a service like [tutum.co](http://tutum.co) for your Orchestration as a Service.
Your not required to use a OaaS at all. You can completely delpoy this locally on a single box or even with docker swarm using docker compose.

### Technologies Used
We used the following technologies that are all open source:

- [Docker](http://www.docker.com)
- [MariaDB](http://www.mariadb.com)
- [HaProxy](http://www.haproxy.org)
- [Nginx](http://www.nginx.org)
- [PHP](http://www.php.com)
- [Ubuntu](http://ubuntu.com)
- [GIT](http://git-scm.com)
- [rabbitmq](http://rabbitmq.com)
- [memcached](http://memcached.com)
- [arbeider](https://github.com/moos3/arbeider)
- [ElasticSearch](http://elasticsearch.com)
- [logstash](http://logstash.net)
- [kibana](https://www.elastic.co/products/kibana)
- [prometheus](http://prometheus.io/)

### Containers
We used the following containers to get Antidote by Symplicity running. We built a pair of custom containers to just make certain things easier to deploy. Those docker containers are:

- symplicity/webserver (Nginx + PHP-FPM)
- symplicity/worker (Runs Cron Jobs and Executes PHP code)

Community contributed containers that we took from docker hub and used with out modification:

- mariadb/mariadb (MariaDB -- Opensource MySQL)
- tutum/haproxy (Haproxy Load Balancer with autoconfiguration with out a lot of work)
- logstash:latest  (Log Indexing)
- gliderlabs/logspout:latest (Log forwarder)
- vfarcic/kibana:latest (Log Search WebUI)
- memcached:latest  (Caching layer)
- rabbitmq:latest  (Queue'ing layer)


### Configuration Management
All configuration is handled by Docker files and Docker Environmental Variables. We build custom webserver images and have include those files so that user can make any changes and build them use their own custom images.

#### Web Servers and Worker
Are going to except the following environmental variables to be set in order to start

```
environment:
  - ANTIDOTE_API_KEY=  # Set a 256 bit string, this is for the encryption of the api 
  - ANTIDOTE_DB_HOST=db   # point to db host
  - ANTIDOTE_DB_NAME=antidote  # Set to your Database Name
  - ANTIDOTE_DB_PASS=antidoteSecret  # Set to your database users password
  - ANTIDOTE_DB_PORT=3306  # Set to the port your going to use for mysql
  - ANTIDOTE_DB_USER=antidote  # Set to your database user
  - ANTIDOTE_ROLE= # Set this to either web or worker. Depending on the Image
  - FDA_TOKEN=  # Set this to your OPEN FDA API KEY
## Caching Settings
  - ANTIDOTE_CACHE_DRIVER= # Set this to memcached if you want to use memcache. Otherwise don't declare it.
  - MEMCACHED_HOST= # Set this to the Memcache server name
  - MEMCACHED_PORT=11211 # Set to default port, change this if your not running memcached on the standard port.
## Git Repo Settings
  - GITREPO_URL= # Set this your fork of Antidote by Symplicity or Use the Main Symplicity One
  - GITREPO_BRANCH= # Set this to the git branch with the production build. Must contain a dist.zip file with vendor and dist in it
## Rabbit MQ Settings for the Worker scripts
  - RABBITMQ_NAME= # Set this to the name which you want webhook for codeship to notify nodes 
  - RABBITMQ_NODE= # Set this to the name of the rabbitmq server.
  - RABBITMQ_PORT=5672 # default port for rabbitmq, change this if not using default.
  - WEBHOOK_API_KEY= # Set a 256 Bit key for the webhook url for [arbeider](https://github.com/moos3/arbeider)
  - WORKER_API_KEY= # Set a 256 bit key for the workers API key. Authenticates the rabbitmq messages.
## Mailgun Settings for sending emails
  - MAILGUN_DOMAIN=  # Set this to your Mailgun Domain
  - MAILGUN_PASSWORD=   # Set this to your Password for Mailgun
  - MAILGUN_SECRET=   # Set this to your Mailgun Secret Key
  - MAILGUN_USERNAME= # Set this to your Mailgun username
```

We recommend using mailgun by rackspace as your mail server. They can scale based on load with out issues, and removed a complex piece from the infrastructure. We can easily replace Mailgun with a SMTP service if needed.

#### Load Balancer

For the loadbalancers you will need to Get a SSL certificate per our recommendation. You can run it on http port 80 with out ssl, but we highly advise againist it. For that you will need to set the following environmental variables:

```
- 'SSL_CERT=<cert here>'

```
For the SSL Cert you will need to run `cat ssl-server.key ssl-server.cert >> cert.pem` then you will need to run `awk 1 ORS='\\n' cert.pem` take that output and paste it in SSL_CERT= . This will setup the loadbalancer for SSL.

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
These are standard HA Proxy settings. That you can read about in haproxy's documentation. Dont Change the BACKEND_PORT or the FRONTEND_PORT, when you set the SSL_CERT the haproxy configuration script will automatically make it so that http gets redirected to https.

#### Database
The database image is going to except the following environmental variables to be set:

```
environment:
  - MYSQL_DATABASE=antidote
  - MYSQL_PASSWORD=antidoteSecret
  - MYSQL_ROOT_PASSWORD=rootSecret
  - MYSQL_USER=antidote
  - TERM=dumb
```

### Local Deployment
So now that we have talked about everything you need get this working. Lets show you how to get this up locally for testing. So we will need to make the following docker-compose.yml setup locally. To use this locally you will need either boot2docker (Mac and Windows Users) or docker installed on your linux machine.
You will see in this version of the local docker-compose, we dont have a load balancer, We aren't doing SSL on the web server. We have the bare minimum containers that are needed for the application to work in a local environment. See "Production Deployment" for details on what is needed to run this in production.

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
- ANTIDOTE_API_KEY=  # Set a 256 bit string, this is for the encryption of the api 
  - ANTIDOTE_DB_HOST=db   # point to db host
  - ANTIDOTE_DB_NAME=antidote  # Set to your Database Name
  - ANTIDOTE_DB_PASS=antidoteSecret  # Set to your database users password
  - ANTIDOTE_DB_PORT=3306  # Set to the port your going to use for mysql
  - ANTIDOTE_DB_USER=antidote  # Set to your database user
  - ANTIDOTE_ROLE= # Set this to either web or worker. Depending on the Image
  - FDA_TOKEN=  # Set this to your OPEN FDA API KEY
## Caching Settings
  - ANTIDOTE_CACHE_DRIVER= # Set this to memcached if you want to use memcache. Otherwise don't declare it.
  - MEMCACHED_HOST= # Set this to the Memcache server name
  - MEMCACHED_PORT=11211 # Set to default port, change this if your not running memcached on the standard port.
## Git Repo Settings
  - GITREPO_URL= # Set this your fork of Antidote by Symplicity or Use the Main Symplicity One
  - GITREPO_BRANCH= # Set this to the git branch with the production build. Must contain a dist.zip file with vendor and dist in it
## Rabbit MQ Settings for the Worker scripts
  - RABBITMQ_NAME= # Set this to the name which you want webhook for codeship to notify nodes 
  - RABBITMQ_NODE= # Set this to the name of the rabbitmq server.
  - RABBITMQ_PORT=5672 # default port for rabbitmq, change this if not using default.
  - WEBHOOK_API_KEY= # Set a 256 Bit key for the webhook url for [arbeider](https://github.com/moos3/arbeider)
  - WORKER_API_KEY= # Set a 256 bit key for the workers API key. Authenticates the rabbitmq messages.
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
- ANTIDOTE_API_KEY=  # Set a 256 bit string, this is for the encryption of the api 
  - ANTIDOTE_DB_HOST=db   # point to db host
  - ANTIDOTE_DB_NAME=antidote  # Set to your Database Name
  - ANTIDOTE_DB_PASS=antidoteSecret  # Set to your database users password
  - ANTIDOTE_DB_PORT=3306  # Set to the port your going to use for mysql
  - ANTIDOTE_DB_USER=antidote  # Set to your database user
  - ANTIDOTE_ROLE= # Set this to either web or worker. Depending on the Image
  - FDA_TOKEN=  # Set this to your OPEN FDA API KEY
## Caching Settings
  - ANTIDOTE_CACHE_DRIVER= # Set this to memcached if you want to use memcache. Otherwise don't declare it.
  - MEMCACHED_HOST= # Set this to the Memcache server name
  - MEMCACHED_PORT=11211 # Set to default port, change this if your not running memcached on the standard port.
## Git Repo Settings
  - GITREPO_URL= # Set this your fork of Antidote by Symplicity or Use the Main Symplicity One
  - GITREPO_BRANCH= # Set this to the git branch with the production build. Must contain a dist.zip file with vendor and dist in it
## Rabbit MQ Settings for the Worker scripts
  - RABBITMQ_NAME= # Set this to the name which you want webhook for codeship to notify nodes 
  - RABBITMQ_NODE= # Set this to the name of the rabbitmq server.
  - RABBITMQ_PORT=5672 # default port for rabbitmq, change this if not using default.
  - WEBHOOK_API_KEY= # Set a 256 Bit key for the webhook url for [arbeider](https://github.com/moos3/arbeider)
  - WORKER_API_KEY= # Set a 256 bit key for the workers API key. Authenticates the rabbitmq messages.
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

So now that we wrote our docker-compose.yml file all your going to need to do is `docker-comose up` this will start up a mariadb, rabbitmq, web server and worker vm.

Once they all come up your should be able to connect to your docker ip or boot2docker ip.

#### Production
Now if you want to deploy this in aproduction environment then you will want to setup some nodes with docker-machine and docker-swarm. then use docker-compose to deploy using the docker swarm. Or you can use a Tutum as
your orchestration service and use the tutum.yml file to deploy this on Amazon AWS, Digital Ocean, Microsoft Azure and Softlayer by IBM. You will want at least 3 nodes and if you have spare hardware you can even bring your own nodes
to tutum. We have tried both AWS and Digital Ocean with tutum. You can have nodes in more than one provider to give your site HA on the IaaS level.

###### Recommendations for Production
I would put db container on a node with at least 4 gigabytes of ram and 2 cpus. I would also put a load balancer infront of the web nodes that terminates SSL. The web servers dont need much in terms of memory 512 Megabytes or 1 gigabyte should be more than enough. I would also stand up a memcached server for the site to use. 
