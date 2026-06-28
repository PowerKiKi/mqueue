<?php

namespace Application\Repository;

use Application\Model\User;

/**
 * @extends AbstractRepository<User>
 */
class UserRepository extends AbstractRepository
{
    /**
     * Create and save a new user.
     *
     * @param array{nickname: string, email: string, password: string} $values
     */
    public function insertUser(array $values): User
    {
        $user = new User();
        $this->getEntityManager()->persist($user);

        $user->nickname = $values['nickname'];
        $user->email = $values['email'];
        $user->password = sha1($values['password']);

        $this->getEntityManager()->flush();

        return $user;
    }

    /**
     * Finds a user by its email and password (not hashed).
     */
    public function getByEmailAndPassword(string $email, string $password): ?User
    {
        return $this->findOneBy([
            'email' => $email,
            'password' => sha1($password),
        ]);
    }

    /**
     * Finds all users.
     *
     * @return User[]
     */
    public function getAll(): array
    {
        return $this->createQueryBuilder('user')
            ->addOrderBy('user.nickname')
            ->getQuery()->getResult();
    }
}
