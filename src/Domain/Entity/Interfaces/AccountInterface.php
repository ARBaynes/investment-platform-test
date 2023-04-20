<?php

namespace App\Domain\Entity\Interfaces;

interface AccountInterface
{
    public function getAccountHolder(): string;
    public function getBalance(): float;
    public function setBalance(float $balance): void;
    public function getAccountType(): string;
    public function getAccountLimit(): float;
}
