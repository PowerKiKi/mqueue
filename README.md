[![Build Status](https://secure.travis-ci.org/PowerKiKi/mqueue.png?branch=master)](http://travis-ci.org/PowerKiKi/mqueue)

# mQueue

## About

mQueue is a website to keep track of movies you want to watch, and rate the one you watched. It is integrated
with IMDb and allow you to rate movies on other webpages (www.imdb.com itself and any other page linking to
an IMDb movie). This is possible thanks to the use of user-script (greasemonkey for Firefox).

## Requirements

* PHP 5.5+ with cURL module
* Apache 2+
* MySQL 5.1+
* Zend Framework 1.12+
* [Compass](http://compass-style.org/)

## Installation

1. Create a database and a user in MySQL (eg: "mqueue")
2. Download latest version: ``git clone https://github.com/PowerKiKi/mqueue.git``
3. In application/configs/, copy application.sample.ini to application.ini and edit database configuration
4. Run ``npm install && gulp``
5. Open mQueue in your browser (something similar to http://mqueue/ or http://localhost/mqueue/public/)

## Upgrade

1. Download latest version: ``git pull``
2. Run ``npm install && gulp``
