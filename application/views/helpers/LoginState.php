<?php

class Default_View_Helper_LoginState extends Zend_View_Helper_Abstract
{
	public function loginState()
	{
		$session = new Zend_Session_Namespace();
		if (isset($session->idUser))
		{
			$mapper = new Default_Model_UserMapper();
			$user = $mapper->find($session->idUser);
			$result = 'logged as ' . $user->nickname;
		}
		else
		{
			$result = 'not logged in';
		}
		
		return $result;
	}

}
?>