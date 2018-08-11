#!/usr/bin/env bash

# Exit script on any error
set -e

# Install gulp.js
yarn global add gulp-cli

# Init database
cp application/configs/application.travis.ini application/configs/application.ini
mysql -e 'create database mqueue_travis;'
