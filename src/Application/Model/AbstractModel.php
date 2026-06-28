<?php

namespace Application\Model;

use Cake\Chronos\Chronos;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractModel
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    public ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    public Chronos $dateUpdate;

    public function __construct()
    {
        $this->dateUpdate = Chronos::now();
    }

    /**
     * Automatically called by Doctrine when the object is created/updated.
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function timestamp(): void
    {
        $this->dateUpdate = Chronos::now();
    }
}
