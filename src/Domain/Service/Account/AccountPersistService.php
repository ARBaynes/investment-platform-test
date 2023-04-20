<?php

namespace App\Domain\Service\Account;

use App\Domain\Entity\IsaAccount;
use App\Domain\Entity\JisaAccount;
use App\Infrastructure\Repository\IsaAccountRepository;
use App\Infrastructure\Repository\JisaAccountRepository;
use DateTime;

class AccountPersistService
{
    public function __construct(
        private readonly IsaAccountRepository $isaAccountRepository,
        private readonly JisaAccountRepository $jisaAccountRepository
    ) {
    }

    public function persistIsaAccount(string $accountHolder): ?IsaAccount
    {
        // Here I would check for preexisting accounts with these details
        $isaAccount = new IsaAccount($accountHolder);
        $this->isaAccountRepository->save($isaAccount);
        return $isaAccount;
    }

    public function persistJisaAccount(string $accountHolder, string $accountHolderBirthday): ?JisaAccount
    {
        // Here I would check for preexisting accounts with these details
        $accountHolderBirthday = DateTime::createFromFormat('Y-m-d', $accountHolderBirthday);
        if (!$accountHolderBirthday) {
            return null;
        }

        $jisaAccount = new JisaAccount($accountHolder, $accountHolderBirthday);
        $this->jisaAccountRepository->save($jisaAccount);
        return $jisaAccount;
    }
}
