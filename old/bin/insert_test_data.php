<?php

use Application\Enum\Rating;
use Application\Model\Movie;
use Application\Model\User;

require_once __DIR__ . '/../public/index.php';

// Insert users
$user1 = new User();
_em()->persist($user1);
$user1->nickname = 'user1';
$user1->email = 'user1@mail.com';
$user1->password = sha1('user1');

$user2 = new User();
_em()->persist($user2);
$user2->nickname = 'user2';
$user2->email = 'user2@mail.com';
$user2->password = sha1('user2');

// Insert movies
$movie1 = new Movie();
_em()->persist($movie1);
$movie1->setId('0082198');

$movie2 = new Movie();
_em()->persist($movie2);
$movie2->setId('0095016');

$movie3 = new Movie();
_em()->persist($movie3);
$movie3->setId('0096446');

_em()->flush();

// Insert statuses
$movie1->setStatus($user1, Rating::Favorite);
$movie2->setStatus($user1, Rating::Excellent);
$movie3->setStatus($user1, Rating::Ok);
$movie1->setStatus($user2, Rating::Bad);
$movie2->setStatus($user2, Rating::Need);
