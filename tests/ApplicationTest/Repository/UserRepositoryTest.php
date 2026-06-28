<?php

namespace ApplicationTest\Repository;

use Application\Model\User;
use Application\Repository\UserRepository;
use ApplicationTest\Traits\TestWithTransactionAndUser;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    use TestWithTransactionAndUser {
        setUp as traitSetupWithTransaction;
    }

    private UserRepository $repository;

    protected function setUp(): void
    {
        $this->traitSetupWithTransaction();
        $this->repository = _em()->getRepository(User::class);
    }

    public function testInsertUser(): void
    {
        $user = $this->repository->insertUser([
            'nickname' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'mypassword',
        ]);

        self::assertSame('newuser', $user->nickname);
        self::assertSame('newuser@example.com', $user->email);
        self::assertSame('91dfd9ddb4198affc5c194cd8ce6d338fde470e2', $user->password);
        self::assertNotNull($user->id);
    }

    public function testGetByEmailAndPassword(): void
    {
        $user = $this->repository->getByEmailAndPassword('user1@example.com', 'user1');
        self::assertSame('user1', $user?->nickname);

        $user = $this->repository->getByEmailAndPassword('user1@example.com', 'invalid password');
        self::assertNull($user);

        $user = $this->repository->getByEmailAndPassword('invalid email@example.com', 'user1');
        self::assertNull($user);

        $user = $this->repository->getByEmailAndPassword('user2@example.com', 'user1');
        self::assertNull($user);

        $user = $this->repository->getByEmailAndPassword('user2@example.com', 'user2');
        self::assertSame('user2', $user?->nickname);
    }

    public function testGetAll(): void
    {
        self::assertCount(3, $this->repository->getAll());
    }
}
