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

### Containers
We used the following containers to get Antidote by Symplicity running. We built a pair of custom containers to just make certain things easier to deploy. Those docker containers are:

- symplicity/webserver (Nginx + PHP-FPM)
- symplicity/worker (Runs Cron Jobs and Executes PHP code)

Community contributed containers that we took from docker hub and used with out modification:

- mariadb/mariadb (MariaDB -- Opensource MySQL)
- tutum/haproxy (Haproxy Load Balancer with autoconfiguration with out a lot of work)


### Configuration Management
All configuration is handled by Docker files and Docker Environmental Variables. We build custom webserver images and have include those files so that user can make any changes and build them use their own custom images.

#### Web Servers and Worker
Are going to except the following environmental variables to be set in order to start

```
environment:
  - ANTIDOTE_DB_HOST=db   # point to db host
  - ANTIDOTE_DB_NAME=antidote  # Set to your Database Name
  - ANTIDOTE_DB_PASS=antidoteSecret  # Set to your database users password
  - ANTIDOTE_DB_PORT=3306  # Set to the port your going to use for mysql
  - ANTIDOTE_DB_USER=antidote  # Set to your database user
  - FDA_TOKEN=  # Set this to your OPEN FDA API KEY
  - GITREPO_URL= # Set this your fork of Antidote by Symplicity or Use the Main Symplicity One
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

### Actual Deployment
So now that we have talked about everything you need get this working. Lets show you how to get this up locally for testing. So we will need to make the following docker-compose.yml setup locally. To use this locally you will need either boot2docker (Mac and Windows Users) or docker installed on your linux machine.
You will see in this version of the local docker-compose we have a image for loggly.com This just makes it easier to tack errors. In Production unless your willing to pay for a logging service you can replace this with a rsyslog server and logstash.
```
db:
  image: 'mariadb:latest'
  environment:
    - MYSQL_DATABASE=antidote
    - MYSQL_PASSWORD=antidoteSecret
    - MYSQL_ROOT_PASSWORD=rootSecret
    - MYSQL_USER=antidote
    - TERM=dumb
lb:
  image: 'tutum/haproxy:latest'
  environment:
    - BACKEND_PORT=80
    - BALANCE=roundrobin
    - FRONTEND_PORT=80
    - MAXCONN=4096
    - MODE=http
    - 'TIMEOUT=connect 5000,client 50000,server 500000'
    - 'OPTION=redispatch,httplog,dontlognull,forwardfor'
    - 'SSL_CERT=-'
  links:
    - loggly
    - web
  ports:
    - '80:80'
    - '443:443'
loggly:
  image: 'sendgridlabs/loggly-docker:latest'
  environment:
    - TAG=local-test
    - TOKEN=
web:
  image: 'symplicity/webserver:latest'
  environment:
    - ANTIDOTE_DB_HOST=db
    - ANTIDOTE_DB_NAME=antidote
    - ANTIDOTE_DB_PASS=antidoteSecret
    - ANTIDOTE_DB_PORT=3306
    - ANTIDOTE_DB_USER=antidote
    - FDA_TOKEN=
    - 'GITREPO_URL='
    - MAILGUN_DOMAIN=
    - MAILGUN_PASSWORD=
    - MAILGUN_SECRET=
    - MAILGUN_USERNAME=
  links:
    - db
    - loggly
worker:
  image: 'symplicity/worker:latest'
  environment:
    - ANTIDOTE_DB_HOST=db
    - ANTIDOTE_DB_NAME=antidote
    - ANTIDOTE_DB_PASS=antidoteSecret
    - ANTIDOTE_DB_PORT=3306
    - ANTIDOTE_DB_USER=antidote
    - FDA_TOKEN=
    - 'GITREPO_URL='
    - MAILGUN_DOMAIN=
    - MAILGUN_PASSWORD=
    - MAILGUN_SECRET=
    - MAILGUN_USERNAME=
    - TEST_SITE=yes
  links:
    - db
    - loggly
```

So now that we wrote our docker-compose.yml file all your going to need to do is `docker-comose up` this will start up a Haproxy server, mariadb, a log forwarder, web server and worker vm.
Once they all come up your should be able to connect to your docker ip or boot2docker ip on either on http or https. If you come into http and have that SSL_CERT set you will get redirected to https.

Now if you want to deploy this stay in a production environment then you will want to setup some nodes with docker-machine and docker-swarm. then use docker-compose to deploy using the docker swarm. Or you can use a Tutum as
your orchestration service and use the tutum.yml file to deploy this one Amazon AWS, Digital Ocean, Microsoft Azure and Softlayer by IBM. You will want at least 3 nodes and if you have spare hardware you can even bring your own nodes
to tutum. We have tried both AWS and Digital Ocean with tutum. You can have nodes in more than one provider to give your site HA on the IaaS level.
