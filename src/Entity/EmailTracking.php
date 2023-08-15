<?php

namespace App\Entity;

use App\Repository\EmailTrackingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailTrackingRepository::class)]
class EmailTracking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?String $trackedId = null;

   
    #[ORM\Column(type: 'boolean')]
    private $viewed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrackedId(): ?String
    {
        return $this->trackedId;
    }

    public function setTrackedId(String $trackedId): static
    {
        $this->trackedId = $trackedId;

        return $this;
    }

    public function isViewed(): ?bool
    {
        return $this->viewed;
    }

    public function setViewed(?bool $viewed): static
    {
        $this->viewed = $viewed;

        return $this;
    }
    /**
     * @ORM\Column(type="integer")
     */
    private $viewCount = 0;

    // ...

    public function getViewCount(): int
    {
        return $this->viewCount;
    }

    public function incrementViewCount(): void
    {
        $this->viewCount++;
    }
}
