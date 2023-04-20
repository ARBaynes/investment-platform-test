<?php

namespace App\Domain\Entity;

use App\Entity\Brand;
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
    private string $company;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'int')]
    private int $amount = 1;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'float')]
    private float $price;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'float')]
    private float $value;

    #[ORM\ManyToOne(targetEntity: IsaAccount::class, inversedBy: 'shares')]
    #[ORM\JoinColumn(nullable: true)]
    private ?IsaAccount $owner;

    public function __construct(
        string $company,
        float $startingValue,
        ?float $price = 0
    ) {
        $this->company = $company;
        $this->value = $startingValue;
        $this->price = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
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
}
