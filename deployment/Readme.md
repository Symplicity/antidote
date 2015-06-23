# To build images

### Requirements
To build these images its really simple. You are either going to need a linux box with docker installed or Mac OS X with Boot2Docker. (We haven't tried to build on a windows host).

#### Makefile Configuration
You need to edit and change `NS` value. This is need to map to your docker hub username. Update `REPO` to match the repo name you choosen for this build.

#### Steps Build
 1. cd in servers directory ie. web
 2. make build
 3. make push this will push it your docker repo

#### Testing
If you want check something you can shell into the image by doing `make shell` this will drop you into the shell of the latest build of the image.


### AWS infra
You will see in this folder theres a `Dockerrun.aws.json` file. This is needed for running this environment in a AWS Elastic Beanstalk environment. We will document the complete process on getting Antidote by Symplicity running in AWS in another document.

### Kubernetes
Coming soon.

### Docker Machine/Swarm/Compose
In the root of the repo just do docker compose up -d and it will spool up and link up, a dev server, mysql and job server for you. 
