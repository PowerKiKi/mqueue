<?php

declare(strict_types=1);

namespace ApplicationTest\Traits;

use Application\Model\User;
use Doctrine\ORM\EntityManager;

/**
 * Allow to run test within a database transaction, so database will be unchanged after test.
 */
trait TestWithTransactionAndUser
{
    private function getEntityManager(): EntityManager
    {
        return _em();
    }

    private function toIds(iterable $models): array
    {
        $models = iterator_to_array($models);
        foreach ($models as &$model) {
            $model = $model->id;
        }

        return $models;
    }

    /**
     * Start transaction.
     */
    protected function setUp(): void
    {
        $this->getEntityManager()->clear();
        $this->getEntityManager()->beginTransaction();

        User::setCurrent(null);
    }

    /**
     * Cancel transaction, to undo all changes made.
     */
    protected function tearDown(): void
    {
        User::setCurrent(null);

        $this->getEntityManager()->rollback();
        $this->getEntityManager()->clear();
        $this->getEntityManager()->getConnection()->close();

        parent::tearDown();
    }

    /**
     * @return ($email is 'anonymous' ? null : User)
     */
    protected function setCurrentUser(string $email): ?User
    {
        $user = null;
        if ($email !== 'anonymous') {
            $email = $email . '@example.com';
            $userRepository = $this->getEntityManager()->getRepository(User::class);
            $user = $userRepository->findOneByEmail($email);
            self::assertNotNull($user, 'given email must exist in test DB: ' . $email);
        }

        User::setCurrent($user);

        return $user;
    }
}
