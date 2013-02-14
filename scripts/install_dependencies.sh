#!/usr/bin/env bash

# Install Compass
gem install --no-rdoc --no-ri sass compass oily_png

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
