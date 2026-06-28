<?php

namespace Application\Model;

use Application\Enum\Rating;
use Application\Repository\StatusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * A status (link between movie and user with a rating).
 */
#[ORM\Entity(StatusRepository::class)]
class Status extends AbstractModel
{
    #[ORM\Column(options: ['unsigned' => true, 'default' => Rating::Nothing])]
    public Rating $rating = Rating::Nothing;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $isLatest = false;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Movie::class)]
    public Movie $movie;

    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    public User $user;

    /**
     * Returns the unique ID for this status to be used in HTML.
     */
    public function getUniqueId(): string
    {
        return Movie::paddedId($this->movie->id) . '_' . (isset($this->user) ? $this->user->id : '');
    }

    public function getName(): string
    {
        return $this->rating->name();
    }
}
