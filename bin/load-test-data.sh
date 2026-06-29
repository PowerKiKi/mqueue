#!/usr/bin/env bash

set -xe

# On GitHub Actions we want to connect via tcp, but locally we prefer socket
HOST=$([ -n "$CI" ] && echo '127.0.0.1' || echo 'localhost')

sudo mariadb -h $HOST -e 'DROP DATABASE IF EXISTS mqueue; CREATE DATABASE mqueue;'
./bin/doctrine migrations:migrate --no-interaction
cat ./tests/data/fixture.sql | sudo mariadb mqueue
