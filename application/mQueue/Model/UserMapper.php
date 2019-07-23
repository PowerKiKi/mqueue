<?php

namespace mQueue\Model;

use mQueue\Model\UserMapper as DefaultModelUserMapper;

abstract class UserMapper extends AbstractMapper
{
    /**
     * Create and save a new user
     *
     * @param array $values
     *
     * @return User
     */
    public static function insertUser(array $values)
    {
        $user = DefaultModelUserMapper::getDbTable()->createRow();
        $user->nickname = $values['nickname'];
        $user->email = $values['email'];
        $user->password = sha1($values['password']);
        $user->save();

        return $user;
    }

    /**
     * Finds a user by its email and password (not hashed)
     *
     * @param string $email
     * @param string $password
     *
     * @return null|User
     */
    public static function findEmailPassword($email, $password)
    {
        $select = self::getDbTable()->select()
            ->where('email = ?', $email)
            ->where('password = SHA1(?)', $password);

        $record = self::getDbTable()->fetchRow($select);

        return $record;
    }

    /**
     * Finds a user by its ID
     *
     * @param int $id
     *
     * @return null|User
     */
    public static function find($id)
    {
        $result = self::getDbTable()->find([$id]);

        return $result->current();
    }

    /**
     * Finds all users
     *
     * @return User[]
     */
    public static function fetchAll()
    {
        $resultSet = self::getDbTable()->fetchAll(null, 'LOWER(nickname)');

        return $resultSet;
    }
}
