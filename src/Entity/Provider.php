<?php

namespace App\Entity;

use App\Repository\ProviderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $job_id = null;

    #[ORM\Column]
    private ?int $difficulty = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column]
    private ?int $provider_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJobId(): ?int
    {
        return $this->job_id;
    }

    public function setJobId(int $job_id): static
    {
        $this->job_id = $job_id;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getProviderId(): ?int
    {
        return $this->provider_id;
    }

    public function setProviderId(int $provider_id): static
    {
        $this->provider_id = $provider_id;

        return $this;
    }
}
