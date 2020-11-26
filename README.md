# mQueue [![Build Status](https://github.com/PowerKiKi/mqueue/workflows/main/badge.svg)](https://github.com/PowerKiKi/mqueue/actions) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/PowerKiKi/mqueue/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/PowerKiKi/mqueue/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/PowerKiKi/mqueue/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/PowerKiKi/mqueue/?branch=master)

## About

mQueue is a website to keep track of movies you want to watch, and rate the one you watched. It is integrated
with IMDb and allow you to rate movies on other webpages (www.imdb.com itself and any other page linking to
an IMDb movie). This is possible thanks to the use of user-script (greasemonkey for Firefox).

## Requirements

* PHP with cURL module
* Apache
* MariaDB
* gulp

## Installation

1. Create a database and a user in MariaDB (eg: "mqueue")
2. Download latest version: ``git clone https://github.com/PowerKiKi/mqueue.git``
3. In application/configs/, copy application.sample.ini to application.ini and edit database configuration
4. Run ``yarn install && gulp``
5. Open mQueue in your browser (something similar to http://mqueue/ or http://localhost/mqueue/public/)

## Upgrade

1. Download latest version: ``git pull``
2. Run ``yarn install && gulp``
