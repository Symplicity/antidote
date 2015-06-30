## Development servers
This image has everything to do development work on Antidote by Symplicity.

#### Requirements
- latest Docker
- If not on linux then Boot2Docker

#### Building Instructions
If you would like to build a fresh check out of this image. Just do `make` This will generate a develop image. This process will take a bit of time to complete. If you make changed to the Docker file and wish to release them to your own docker repo. You will need to update the Makefile and set NS=<yourname> Name=<reponame> REPO=<reponame>, then just do `make release`. `make release` will do a build and then push the image to docker.

#### Running
You can run this image by updating the docker-compose.yml in this directory. You need to replace all the <> values. Then running `docker-compose up`

This will start up a DB server, Worker node and RabbitMQ Server. To connect to the container use docker exec commands.

If you want to add script to be run on a scheduled task. Then you will want to modify the jobs file as that gets copied to cron.d and will get run as they are defined. Any import data scripts, build scripts etc should be defined in this file. Each time you modify that script you will want to do a `make release` to make sure your running the latest version.
