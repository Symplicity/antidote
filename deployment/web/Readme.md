## Development servers
This image has everything to do development work on Antidote by Symplicity.

#### Requirements
- latest Docker
- If not on linux then Boot2Docker

#### Building Instructions
If you would like to build a fresh check out of this image. Just do `make` This will generate a develop image. This process will take a bit of time to complete. If you make changed to the Docker file and wish to release them to your own docker repo. You will need to update the Makefile and set NS=<yourname> Name=<reponame> REPO=<reponame>, then just do `make release`. `make release` will do a build and then push the image to docker.

#### Running
You can run this image like so `ocker run -v /Users/moose/builds/18f/entic/:/var/www:rw -p 80:80 -p 2222:22 -i -t symplicity/develop /sbin/my_init --enable-insecure-key`

This will start Nginx, SSH and give you a private key for ssh'ing into the container. you will get a out put similar to this:
```
loki:dev moose$ docker run -v /Users/moose/builds/18f/entic/:/var/www:rw -p 80:80 -p 2222:22 -i -t symplicity/develop /sbin/my_init --enable-insecure-key
*** Installing insecure SSH key for user root
Creating directory /root/.ssh...
Editing /root/.ssh/authorized_keys...
Success: insecure key has been added to /root/.ssh/authorized_keys

+------------------------------------------------------------------------------+
| Insecure SSH key installed                                                   |
|                                                                              |
| DO NOT expose port 22 on the Internet unless you know what you are doing!    |
|                                                                              |
| Use the private key below to connect with user root                         |
+------------------------------------------------------------------------------+

-----BEGIN RSA PRIVATE KEY-----
....
-----END RSA PRIVATE KEY-----



*** Running /etc/my_init.d/00_regen_ssh_host_keys.sh...
*** Running /etc/rc.local...
*** Booting runit daemon...
*** Runit started as PID 15
[23-Jun-2015 16:18:52] NOTICE: fpm is running, pid 28
[23-Jun-2015 16:18:52] NOTICE: ready to handle connections
Jun 23 16:18:52 82614f10388f syslog-ng[23]: syslog-ng starting up; version='3.5.3'
```
