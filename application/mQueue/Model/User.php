<?php

namespace mQueue\Model;

use mQueue\Model\User as DefaultModelUser;
use Zend_Session_Namespace;

class User extends AbstractModel
{
    /**
     * The current user logged in.
     * -1 before initialization
     * null if no user logged in
     * \mQueue\Model\User if logged in
     *
     * @var User
     */
    private static $currentUser = -1;

    /**
     * Returns the user currently logged in or null
     *
     * @return null|User
     */
    public static function getCurrent()
    {
        if (is_int(self::$currentUser)) {
            $session = new Zend_Session_Namespace();
            if (isset($session->idUser)) {
                self::$currentUser = UserMapper::find($session->idUser);
            } else {
                self::$currentUser = null;
            }
        }

        return self::$currentUser;
    }

    /**
     * Set the user currently logged in, or log him out
     *
     * @param User $user
     */
    public static function setCurrent(DefaultModelUser $user = null): void
    {
        $session = new Zend_Session_Namespace();
        $session->idUser = $user ? $user->id : null;
        self::$currentUser = $user;
    }

    /**
     * Returns movie ratings statistics
     *
     * @return array of count of movies per ratings
     */
    public function getStatistics()
    {
        return StatusMapper::getStatistics($this);
    }

    /**
     * Override parent to auto-logout when deleting logged in user
     */
    public function delete()
    {
        if (DefaultModelUser::getCurrent() == $this) {
            DefaultModelUser::setCurrent(null);
        }

        return parent::delete();
    }
}
