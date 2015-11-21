#!/usr/bin/env bash

# Exit script on any error
set -e

# Install gulp.js
nvm install 0.12
nvm use 0.12
npm install -g gulp-cli

# Install Compass
gem install --no-rdoc --no-ri compass -v 1.0.0.alpha.19 --pre
gem install --no-rdoc --no-ri oily_png

# Init database
cp application/configs/application.travis.ini application/configs/application.ini
mysql -e 'create database mqueue_travis;'
