#!/usr/bin/env bash

# Exit script on any error
set -e

# Install gulp.js
sudo apt-get -qq update
sudo apt-get install -qq software-properties-common # to get next command: add-apt-repository
sudo add-apt-repository --yes ppa:chris-lea/node.js
sudo apt-get -qq update
sudo apt-get install nodejs npm
sudo apt-get install nodejs-legacy || true
sudo npm install -g gulp

# Install Compass
gem install --no-rdoc --no-ri compass -v 0.13.alpha.2 --pre
gem install --no-rdoc --no-ri oily_png

# Install PHPUnit 3.4.15 (last supported version for ZF1)
pear config-set auto_discover 1
pear channel-discover pear.phpunit.de
pear install --force --alldeps phpunit/PHPUnit-3.4.15

# Install ZendFramework
ZF=ZendFramework-1.12.1
wget https://packages.zendframework.com/releases/$ZF/$ZF-minimal.zip
unzip -qq $ZF-minimal.zip
mv $ZF-minimal/library/Zend library/

# Init database
cp application/configs/application.travis.ini application/configs/application.ini
mysql -e 'create database mqueue_travis;'
