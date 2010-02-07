<?php
require_once('../public/index.php');

try
{
	// Insert users
	$mapper = new Default_Model_UserMapper();

	$user1 = $mapper->getDbTable()->createRow();
	$user1->nickname = 'user1';
	$user1->email = 'user1@mail.com';
	$user1->password = sha1('user1');
	$user1->save();

	$user2 = $mapper->getDbTable()->createRow();
	$user2->nickname = 'user2';
	$user2->email = 'user2@mail.com';
	$user2->password = sha1('user2');
	$user2->save();


	// Insert movies
	$mapper = new Default_Model_MovieMapper();

	$movie1 = $mapper->getDbTable()->createRow();
	$movie1->setId('0082198');
	$movie1->save();

	$movie2 = $mapper->getDbTable()->createRow();
	$movie2->setId('0095016');
	$movie2->save();


	$movie3 = $mapper->getDbTable()->createRow();
	$movie3->setId('0096446');
	$movie3->save();


	// Insert statuses
	$mapper = new Default_Model_StatusMapper();

	$status = $mapper->find($user1->id, $movie1->id);
	$status->rating = 5;
	$status->save();

	$status = $mapper->find($user1->id, $movie2->id);
	$status->rating = 4;
	$status->save();

	$status = $mapper->find($user1->id, $movie3->id);
	$status->rating = 3;
	$status->save();

	$status = $mapper->find($user2->id, $movie1->id);
	$status->rating = 2;
	$status->save();

	$status = $mapper->find($user2->id, $movie2->id);
	$status->rating = 1;
	$status->save();
}
catch (Exception $e)
{
	echo $e->getMessage();
}