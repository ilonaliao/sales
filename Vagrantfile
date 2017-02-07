Vagrant.configure("2") do |config|
    config.vm.box = "ubuntu/xenial64"
    config.vm.network "forwarded_port", guest: 80, host: 8080
    config.vm.synced_folder "supersales", "/var/www/html/supersales"
    config.vm.provider "virtualbox" do |vb|
      vb.memory = "1536"
    end

  config.vm.provision "shell", inline: <<-SHELL

  # Install 

  sudo apt-get update
  sudo apt-get -y install apache2 php7.0 libapache2-mod-php7.0 sysv-rc-conf php-xml php-mbstring php7.0-curl php-apcu

  # Setup Apache Settings Here
  # 1. Replace Apache2.conf with our setup
  # 2. Use supersales.conf to setup

  sudo rm /etc/apache2/apache2.conf
  sudo cp /var/www/html/supersales/apache2.conf /etc/apache2/
  sudo cp /var/www/html/supersales/supersales.conf /etc/apache2/sites-available
  sudo a2ensite supersales.conf
  sudo a2dissite 000-default.conf
  sudo a2enmod rewrite

  cd /var/www/html
  curl -Ss https://getcomposer.org/installer | php
  sudo mv composer.phar /usr/bin/composer
  cd /var/www/html/supersales
  composer install --no-plugins --no-scripts --prefer-source --no-interaction

  sudo service apache2 restart

  SHELL

  

end