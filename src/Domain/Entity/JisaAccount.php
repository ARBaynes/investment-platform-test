<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\AccountInterface;
use App\Infrastructure\Repository\IsaAccountRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IsaAccountRepository::class)]
class JisaAccount implements AccountInterface
{
    public const ACCOUNT_TYPE = 'JISA';
    public const ACCOUNT_LIMIT = 9000;
    public const AGE_LIMIT = 18;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = 2;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'float')]
    private float $balance = 0;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 255)]
    private string $accountHolder;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $accountHolderBirthday;

    public function __construct(string $accountHolder, DateTime $accountHolderBirthday)
    {
        $this->accountHolder = $accountHolder;
        $this->accountHolderBirthday = $accountHolderBirthday;
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

    /**
     * @return string
     */
    public function getAccountHolder(): string
    {
        return $this->accountHolder;
    }

    /**
     * @param string $accountHolder
     */
    public function setAccountHolder(string $accountHolder): void
    {
        $this->accountHolder = $accountHolder;
    }

    /**
     * @return DateTime
     */
    public function getAccountHolderBirthday(): DateTime
    {
        return $this->accountHolderBirthday;
    }

    /**
     * @param DateTime $accountHolderBirthday
     */
    public function setAccountHolderBirthday(DateTime $accountHolderBirthday): void
    {
        $this->accountHolderBirthday = $accountHolderBirthday;
    }

    public function getAccountType(): string
    {
        return self::ACCOUNT_TYPE;
    }

    public function getAccountLimit(): float
    {
        return self::ACCOUNT_LIMIT;
    }
}
