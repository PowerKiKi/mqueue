#!/usr/bin/env bash

# Try to use specific PHP, or fallback to default version
PHP=`which php7.2` || PHP='php'
COMPOSER="$PHP `which composer` --no-interaction --no-plugins"

# Exit script on any error
set -e

echo "Installing git hooks..."
ln -fs ../../bin/pre-commit.sh .git/hooks/pre-commit

echo "Updating Node.js packages..."
yarn install

echo "Updating all PHP dependencies via composer..."
$COMPOSER install --classmap-authoritative --ignore-platform-reqs

echo "Updating database..."
$PHP ./bin/update_database.php

echo "Compiling CSS..."
./node_modules/.bin/sass --style=compressed --no-source-map application/sass/:public/css/

echo "Compiling JavaScript..."
./node_modules/.bin/esbuild --minify public/js/*.js --outdir=public/js/min
cat public/js/application/*.js  | ./node_modules/.bin/esbuild --minify > public/js/min/application.js
