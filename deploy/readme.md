EgyptFOSS Deployment Guide
=
This guide aims to deploy egyptfoss in a vagrant box based on "trusty-server-cloudimg-amd64-vagrant-disk1.box" vritual box image, with domain "egyptfoss.vag".

To run the vagrant you will need to get the required virtual box image and add it to the current folder then run 
```bash
vagrant up
vagrant ssh
```
Now you are on Vagrant terminal

# Install git, jre and needed php modules
```bash
sudo apt-get install git
sudo apt-get install php5-cli
sudo apt-get install php5-curl
sudo apt-get install default-jre
```

# clone project
```bash
cd /vagrant
git clone https://github.com/espace/egyptfoss.git
````

# Install composer globally to install required dependencies  
```bash
cd egyptfoss
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

# Prepare database
```bash
cd /vagrant/egyptfoss/database/DBDiff/
composer install #install required dependencies
cd /vagrant/egyptfoss/database/data
sudo sed -i 's/http:\/\/egyptfoss.com/http:\/\/egyptfoss.vag/g' *.sql  #replace domain in all batches files with your target domain egyptfoss.vag in this example
sudo sed -i 's/http:\/\/egyptfoss.com/http:\/\/egyptfoss.vag/g' fosspedia/*.sql  #replace domain in all batches files with your target domain egyptfoss.vag in this example
cd /vagrant/egyptfoss/database
bash fossdb.sh -s root
```
# Prepare API
```bash
cd /vagrant/egyptfoss/foss-api/api-v2
composer install
```
--------------------------------------------------------------- 
# Prepare Tetsting
```bash
cd /vagrant/egyptfoss/testing/
composer install
```
Install Ruby 
```bash
cd
git clone git://github.com/sstephenson/rbenv.git .rbenv
echo 'export PATH="$HOME/.rbenv/bin:$PATH"' >> ~/.bashrc
echo 'eval "$(rbenv init -)"' >> ~/.bashrc
exec $SHELL
git clone git://github.com/sstephenson/ruby-build.git ~/.rbenv/plugins/ruby-build
echo 'export PATH="$HOME/.rbenv/plugins/ruby-build/bin:$PATH"' >> ~/.bashrc
exec $SHELL
git clone https://github.com/sstephenson/rbenv-gem-rehash.git ~/.rbenv/plugins/rbenv-gem-rehash
rbenv install 2.2.3
rbenv global 2.2.3
ruby -v
```
Install Mailcatcher and run it
```bash
gem install mailcatcher
mailcathcer
```
Download selenium and run it
```bash
wget http://selenium-release.storage.googleapis.com/2.48/selenium-server-standalone-2.48.2.jar
java -jar /path/to/selenium-server-standalone-2.48.2.jar &
```
Install Phantomjs
```bash
sudo apt-get install build-essential chrpath libssl-dev libxft-dev
sudo apt-get install libfreetype6 libfreetype6-dev
sudo apt-get install libfontconfig1 libfontconfig1-dev
sudo apt-get install autoconf2.13 pkg-config build-essential qt5-qmake g++ python ruby perl sqlite flex bison gperf openssl fontconfig xorg xorg-dev xutils-dev xcb-proto libtool libsqlite0 libssl-dev libsqlite3-dev libfontconfig1-dev libicu-dev libfreetype6 libssl-dev libpng-dev libpng12-dev libjpeg-dev libx11-dev libxext-dev libxcb-xkb-dev x11proto-core-dev libxcb-render-util0 libqt5webkit5-dev
cd ~
export PHANTOM_JS="phantomjs-2.1.1-linux-x86_64"
wget https://bitbucket.org/ariya/phantomjs/downloads/$PHANTOM_JS.tar.bz2
sudo tar xvjf $PHANTOM_JS.tar.bz2
sudo mv $PHANTOM_JS /usr/local/share
sudo ln -sf /usr/local/share/$PHANTOM_JS/bin/phantomjs /usr/local/bin
```
# Prepare Virtual Hosts
```bash
cd /etc/nginx/sites-available/

sudo vi egyptfoss.vag # add the content of the attahced egyptfoss.vag 
sudo ln -s /etc/nginx/sites-available/egyptfoss.vag /etc/nginx/sites-enabled/egyptfoss.vag

sudo vi api.egyptfoss.vag
sudo ln -s /etc/nginx/sites-available/api.egyptfoss.vag /etc/nginx/sites-enabled/api.egyptfoss.vag

sudo service nginx restart
```

# Prepare FOSSPedia Configuration
```bash
sed -i 's/http:\/\/egyptfoss.com/http:\/\/egyptfoss.vag/g' /vagrant/egyptfoss/wiki/LocalSettingsEnglish.php

sed -i 's/http:\/\/egyptfoss.com/http:\/\/egyptfoss.vag/g' /vagrant/egyptfoss/wiki/LocalSettingsArabic.php
```
# Install Apache Stanbol
```bash
sudo apt-get install subversion
svn co --revision 1711228 http://svn.apache.org/repos/asf/stanbol/trunk <stanbol-directory>
export MAVEN_OPTS="-Xmx1024M -XX:MaxPermSize=256M"
cd <stanbol-directory>	
sudo apt-get install maven
mvn install -Dmaven.test.skip=true
java -Xmx1024M -XX:MaxPermSize=1024M -jar launchers/full/target/org.apache.stanbol.launchers.full-1.0.0-SNAPSHOT.jar -p 3000
```
# Install Apache Marmota 
```bash
apt-get install postgresql
apt-get install postgres-xc

sudo su postgres
createuser -d -P <user-name>
createdb <db-name> -O <user-name>


java -jar marmotta-installer.jar

marmota/startup.sh 
```
You will need to add this line to nginx configuration to fix serving cached and not complete asset files
```bash
# A VirtualBox bug forces vagrant to serve
# corrupt files via Apache or nginx
# The solution to that would be to turn off
# the SendFile option in apache or nginx

# If you use nginx as your main web server
# add this directive in your nginx.conf
sendfile off
```
