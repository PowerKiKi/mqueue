#!/usr/bin/env bash

set -xe

sudo mariadb mqueue -e 'DROP DATABASE IF EXISTS mqueue; CREATE DATABASE mqueue;'
./bin/doctrine migrations:migrate --no-interaction
cat ./tests/data/fixture.sql | sudo mariadb mqueue
