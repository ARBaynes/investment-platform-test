<?php

namespace App\Domain\Service\Account;

use App\Domain\Entity\Interfaces\AccountInterface;
use App\Domain\Entity\IsaAccount;
use App\Domain\Entity\JisaAccount;
use App\Domain\Service\AgeCheckerService;
use App\Infrastructure\Repository\IsaAccountRepository;
use App\Infrastructure\Repository\JisaAccountRepository;

class AccountBalanceUpdaterService
{
    public function __construct(
        private readonly IsaAccountRepository $isaAccountRepository,
        private readonly JisaAccountRepository $jisaAccountRepository,
        private readonly AgeCheckerService $ageCheckerService
    ) {
    }

    //In this instance, negative numbers indicate an overdrawn account
    // In the future I would want to implement a warning system for overdrawing your account
    public function withdraw(AccountInterface $account, float $amount): bool
    {
        if (($account->getAccountType() === JisaAccount::ACCOUNT_TYPE) &&
            !$this->ageCheckerService->canWithdrawJisa($account)) {
            return false;
        }

        $prevBalance = $account->getBalance();
        $updatedBalance = $prevBalance - $amount;

        $account->setBalance($updatedBalance);
        $this->saveChange($account);
        return true;
    }

    public function deposit(AccountInterface $account, float $amount): float|bool
    {
        $prevBalance = $account->getBalance();
        $updatedBalance = $prevBalance + $amount;

        if ($updatedBalance > $account->getAccountLimit()) {
            $account->setBalance($account->getAccountLimit());
            $this->saveChange($account);
            return $updatedBalance - $account->getAccountLimit();
        }

        $account->setBalance($updatedBalance);
        $this->saveChange($account);
        return true;
    }

    private function saveChange(AccountInterface $account): void
    {
        match ($account->getAccountType()) {
            IsaAccount::ACCOUNT_TYPE => $this->isaAccountRepository->save($account),
            JisaAccount::ACCOUNT_TYPE => $this->jisaAccountRepository->save($account)
        };
    }
}
