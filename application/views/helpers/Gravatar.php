<?php

class Default_View_Helper_Gravatar extends Zend_View_Helper_Abstract
{
	public function gravatar(Default_Model_User $user, $small = true)
	{
		$url = 'http://www.gravatar.com/avatar/' . md5(trim(strtolower($user->email))) . '.jpg?';
		$url .= 'default=identicon';
		
		if ($small)
			$url .= ('&amp;size=16');
		else 
			$url .= ('&amp;size=96');
			
		$result = '<img alt="' . $this->view->escape($user->nickname) . '" class="gravatar ' . ($small ? 'small' : 'big') . '" src="' . $url . '" />';
				
		return $result;
	}

}
?>