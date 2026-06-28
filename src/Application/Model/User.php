<?php

namespace Application\Model;

use Application\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(UserRepository::class)]
class User extends AbstractModel
{
    /**
     * The current user logged in.
     */
    private static ?User $currentUser = null;

    /**
     * Set currently logged-in user
     * WARNING: this method should only be called from \Application\Authentication\AuthenticationListener.
     */
    public static function setCurrent(?self $user): void
    {
        self::$currentUser = $user;
    }

    /**
     * Returns the user currently logged in or null.
     */
    public static function getCurrent(): ?self
    {
        return self::$currentUser;
    }

    #[ORM\Column(type: 'string', unique: true)]
    public string $nickname;

    #[ORM\Column(type: 'string', unique: true)]
    public string $email;

    #[ORM\Column(type: 'string')]
    public string $password;

    /**
     * Returns movie ratings statistics.
     *
     * @return array of count of movies per ratings
     */
    public function getStatistics(): array
    {
        return _em()->getRepository(Status::class)->getStatistics($this);
    }
}
