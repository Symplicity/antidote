## Development servers
This image has everything to do development work on Antidote by Symplicity.

#### Requirements
- latest Docker
- If not on linux then Boot2Docker

#### Building Instructions
If you would like to build a fresh check out of this image. Just do `make` This will generate a develop image. This process will take a bit of time to complete. If you make changed to the Docker file and wish to release them to your own docker repo. You will need to update the Makefile and set NS=<yourname> Name=<reponame> REPO=<reponame>, then just do `make release`. `make release` will do a build and then push the image to docker.

#### Running
You can run this image by updating the docker-compose.yml in this directory. You need to replace all the <> values. Then running `docker-compose up`

This will start up a DB server, Web server and RabbitMQ Server. To connect to the container use docker exec commands.
