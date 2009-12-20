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
		 'Need' => Default_Model_Status::Need,
		 'Bad' => Default_Model_Status::Bad,
		 'Ok' => Default_Model_Status::Ok,
		 'Excellent' => Default_Model_Status::Excellent,
		 'Favorite' => Default_Model_Status::Favorite);
		 
}

?>