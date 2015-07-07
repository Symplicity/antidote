## Development Setup

This assumes you have already cloned the repository in a folder named antidote somewhere, and that you are using OS-X, though it should work on Windows and Linux as well. We provide docker-composer.yml setup to get you up and running with an environment that matches production.

### Installation

If you have not used docker on this machine before, install it for your OS from http://docs.docker.com/installation/ - and if your current version is below 1.6 please upgrade. boot2docker steps are only needed if you are on OS-X or Windows.

To make sure docker host is running and environment is correct in your terminal:
```bash
eval "$(boot2docker shellinit)"
```

### Configuration

Create configuration file and update it to match your environment: `cp .env.example .env`

It comes with default DB credentials provided by our docker containers, but you can use any other database supported by Eloquent ORM: http://laravel.com/docs/5.1/eloquent

See [Deployment doc](docs/Deployment.md) for additional details about configuration variables.

See http://lumen.laravel.com/docs/installation#basic-configuration for additional information about configuring a Lumen app.

### Setup

This script will run the initial setup, populate the database, open your local site in a browser, and put you into a web container shell: `./dev-setup.sh` (look inside for details)

### Updating

If you are in a container shell, run `./script/update.sh` (this assumes you already did a git pull or rebased if you are in a branch.) To run the same script without getting inside the shell:
```bash
docker exec -it antidote_web_1 /var/www/scripts/update.sh
```

### Troubleshooting

* See current running docker containers: `docker ps`
* See aggregated logs from all of them: `docker-compose logs`
* To login to shell on a container: `docker exec -it antidote_web_1 /bin/bash`
* Get the IP address of your docker host: `boot2docker ip`

### Starting all over

If things get messed up and you just wish to start from scratch:
```bash
cd ..
boot2docker destroy
mv antidote antidote_old
git clone git@github.com:Symplicity/antidote.git
cp antidote_old/.env antidote/
boot2docker init
boot2docker up
cd antidote
./dev-setup.sh
```
