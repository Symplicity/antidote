curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
composer install
vagrant box add laravel/homestead
composer global require laravel/homestead
echo 'export PATH=~/.composer/vendor/bin:$PATH' >>~/.bash_profile
source ~/.bash_profile
homestead init
cp Homestead.yaml ~/.homestead/
homestead up