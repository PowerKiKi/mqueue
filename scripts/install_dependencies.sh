#!/usr/bin/env bash

# Exit script on any error
set -e

# Install gulp.js
sudo apt-get -qq update
sudo apt-get install -qq software-properties-common # to get next command: add-apt-repository
sudo add-apt-repository --yes ppa:chris-lea/node.js
sudo apt-get -qq update
sudo apt-get -qq install nodejs
sudo apt-get -qq install nodejs-legacy || true
sudo npm install -g gulp --cache /tmp/.npm

# Install Compass
gem install --no-rdoc --no-ri compass -v 1.0.0.alpha.19 --pre
gem install --no-rdoc --no-ri oily_png

# Init database
cp application/configs/application.travis.ini application/configs/application.ini
mysql -e 'create database mqueue_travis;'
