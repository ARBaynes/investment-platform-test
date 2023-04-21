<?php

namespace App\Domain\Entity;

use App\Infrastructure\Repository\ShareRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ShareRepository::class)]
class Share
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 255)]
    private string $slug;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 255)]
    private string $company;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'float')]
    private float $price;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'float')]
    private float $value;

    #[ORM\ManyToOne(targetEntity: IsaAccount::class, inversedBy: 'shares')]
    #[ORM\JoinColumn(nullable: true)]
    //I would like to have this be IsaAccount, but for time it will be a string of the accountHolder name
    private ?string $owner;

    public function __construct(
        string $slug,
        string $company,
        float $startingValue,
        ?float $price = 0
    ) {
        $this->slug = $slug;
        $this->company = $company;
        $this->value = $startingValue;
        $this->price = $price;
        $this->owner = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    public function getOwner(): ?string
    {
        return $this->owner ?? null;
    }

    public function setOwner(?string $owner): void
    {
        $this->owner = $owner;
    }

    public function isOwned(): bool
    {
        return $this->getOwner() !== null;
    }
}
