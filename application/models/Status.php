<?php

class Default_Model_Status extends Default_Model_AbstractModel
{
	const Nothing = 0;
	const Need = 1;
	const Bad = 2;
	const Ok = 3;
	const Excellent = 4;
	const Favorite = 5;
	
	public static $ratings = array(
		 Default_Model_Status::Need => 'Need',
		 Default_Model_Status::Bad => 'Bad',
		 Default_Model_Status::Ok => 'Ok',
		 Default_Model_Status::Excellent => 'Excellent',
		 Default_Model_Status::Favorite => 'Favorite');
}

?>