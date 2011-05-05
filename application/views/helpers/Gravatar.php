<?php

class Default_View_Helper_Gravatar extends Zend_View_Helper_Abstract
{
	/**
	 * Return an image for a user on gravatar
	 * @param Default_Model_User $user
	 * @param boolean $small
	 */
	public function gravatar(Default_Model_User $user, $small = true)
	{
		$url = 'http://www.gravatar.com/avatar/' . md5(trim(strtolower($user->email))) . '.jpg?';
		$url .= 'default=identicon';
		
		if ($small)
			$url .= ('&amp;size=16');
		else 
			$url .= ('&amp;size=96');
			
		$result = '<span class="gravatar user_' . $user->id . ' ' . ($small ? 'small' : 'big') . '"></span>';
				
		return $result;
	}

}
?>