<?php

namespace App\Domain\Service\Account;

use App\Domain\Entity\Interfaces\AccountInterface;
use App\Domain\Entity\IsaAccount;

class AccountBalanceCheckService
{
    public function checkTotalBalance(AccountInterface $account): float
    {
        return $this->checkSharesValueBalance($account) + $this->checkSavingsBalance($account);
    }

    public function checkSharesValueBalance(AccountInterface $account): float
    {
        if ($account->getAccountType() !== IsaAccount::ACCOUNT_TYPE) {
            return 0;
        }
        return 2.22;
    }

    public function checkSavingsBalance(AccountInterface $account): float
    {
        return $account->getBalance();
    }
}
