<?php

require_once __DIR__ . '/../public/index.php';

$db = Zend_Registry::get('db');
$db->beginTransaction();

try {
    // Insert users
    $user1 = \mQueue\Model\UserMapper::getDbTable()->createRow();
    $user1->nickname = 'user1';
    $user1->email = 'user1@mail.com';
    $user1->password = sha1('user1');
    $user1->save();

    $user2 = \mQueue\Model\UserMapper::getDbTable()->createRow();
    $user2->nickname = 'user2';
    $user2->email = 'user2@mail.com';
    $user2->password = sha1('user2');
    $user2->save();

    // Insert movies
    $movie1 = \mQueue\Model\MovieMapper::getDbTable()->createRow();
    $movie1->setId('0082198');
    $movie1->save();

    $movie2 = \mQueue\Model\MovieMapper::getDbTable()->createRow();
    $movie2->setId('0095016');
    $movie2->save();

    $movie3 = \mQueue\Model\MovieMapper::getDbTable()->createRow();
    $movie3->setId('0096446');
    $movie3->save();

    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo 'FATAL: test data insertion cancelled' . PHP_EOL;

    throw $e;
}

// Insert statuses
$movie1->setStatus($user1, \mQueue\Model\Status::Favorite);
$movie2->setStatus($user1, \mQueue\Model\Status::Excellent);
$movie3->setStatus($user1, \mQueue\Model\Status::Ok);
$movie1->setStatus($user2, \mQueue\Model\Status::Bad);
$movie2->setStatus($user2, \mQueue\Model\Status::Need);
