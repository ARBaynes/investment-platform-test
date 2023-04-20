<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\AccountInterface;
use App\Infrastructure\Repository\IsaAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IsaAccountRepository::class)]
class IsaAccount implements AccountInterface
{
    public const ACCOUNT_TYPE = 'ISA';
    public const ACCOUNT_LIMIT = 20000;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 255)]
    private string $accountHolder;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'float')]
    private float $balance = 0;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Share::class, orphanRemoval: false)]
    private Collection $shares;

    public function __construct(string $accountHolder)
    {
        $this->setAccountHolder($accountHolder);
        $this->shares = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    public function getShares(): Collection
    {
        return $this->shares;
    }

    public function addShare(Share $shares): void
    {
        $this->shares->add($shares);
    }

    public function getAccountHolder(): string
    {
        return $this->accountHolder;
    }

    public function setAccountHolder(string $accountHolder): void
    {
        $this->accountHolder = $accountHolder;
    }
}
