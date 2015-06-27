FROM phusion/baseimage:0.9.16
MAINTAINER Richard Genthner <moose@symplicity.com>

LABEL version="1.2.15"
LABEL description="This is the Antidote Worker, with PHP 5.6"
LABEL "com.symplicity.vendor"="Symplicity Corp"

# Ensure UTF-8
RUN locale-gen en_US.UTF-8
ENV LANG  en_US.UTF-8
ENV LC_ALL  en_US.UTF-8

ENV HOME /root
RUN /etc/my_init.d/00_regen_ssh_host_keys.sh
ADD build/run.sh /run.sh
RUN chmod +x /run.sh
CMD ["/run.sh"]

#php install
RUN apt-get update
RUN DEBIAN_FRONTEND="noninteractive" apt-get install -y vim curl wget build-essential python-software-properties git rsyslog zip
RUN add-apt-repository -y ppa:ondrej/php5-5.6
RUN add-apt-repository -y ppa:nginx/stable
RUN apt-get update
RUN DEBIAN_FRONTEND="noninteractive" apt-get install -y --force-yes php5-cli php5-mysql php5-sqlite php5-curl php5-gd php5-mcrypt php5-intl php5-imap php5-tidy
RUN sed -i "s/;date.timezone = .*/date.timezone = UTC/" /etc/php5/cli/php.ini
# end php install
RUN rm -rf /etc/service/syslog-ng

# setup directories
RUN mkdir -p /var/www

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=bin --filename=composer
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ADD jobs /etc/cron.d/antidote
### Get code
ADD update.sh /update.sh
RUN touch /root/.ssh/known_hosts && ssh-keyscan github.com >> /root/.ssh/known_hosts
ADD hithub /root/.ssh/id_rsa
RUN chmod 0600 /root/.ssh/id_rsa
RUN git clone https://github.com/moos3/arbeider.git /worker
RUN cd /worker && composer install
RUN mkdir /etc/service/worker
RUN cp /worker/run /etc/service/worker/
