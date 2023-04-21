<?php

namespace App\Domain\Service\Account;

use App\Domain\Entity\Interfaces\AccountInterface;
use App\Domain\Entity\IsaAccount;
use App\Domain\Entity\Share;
use App\Infrastructure\Repository\ShareRepository;

class AccountBalanceCheckService
{
    public function __construct(private readonly ShareRepository $shareRepository)
    {
    }

    public function checkTotalBalance(AccountInterface $account): float
    {
        return $this->checkSharesValueBalance($account) + $this->checkSavingsBalance($account);
    }

    public function checkSharesValueBalance(AccountInterface $account): float
    {
        if ($account->getAccountType() !== IsaAccount::ACCOUNT_TYPE) {
            return 0;
        }

        $runningTotal = 0.0;
        $accountShares = $this->shareRepository->findAllByAccount($account->getAccountHolder());

        /** @var Share $share */
        foreach ($accountShares as $share) {
            $runningTotal += $share->getValue();
        }

        return $runningTotal;
    }

    public function checkSharesHeld(AccountInterface $account): array
    {
        if ($account->getAccountType() !== IsaAccount::ACCOUNT_TYPE) {
            return [];
        }

        return $this->shareRepository->findAllByAccount($account->getAccountHolder());
    }

    public function checkSavingsBalance(AccountInterface $account): float
    {
        return $account->getBalance();
    }
}
