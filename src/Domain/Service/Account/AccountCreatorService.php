<?php

namespace App\Domain\Service\Account;

use App\Domain\Entity\IsaAccount;
use App\Domain\Entity\JisaAccount;
use App\Infrastructure\Repository\IsaAccountRepository;
use App\Infrastructure\Repository\JisaAccountRepository;
use DateTime;

class AccountCreatorService
{
    public function __construct(
        private readonly AccountRetrieverService $accountRetrieverService,
        private readonly IsaAccountRepository $isaAccountRepository,
        private readonly JisaAccountRepository $jisaAccountRepository
    ) {
    }

    public function createIsaAccount(string $accountHolder): ?IsaAccount
    {
        if ($this->accountRetrieverService->retrieve(IsaAccount::ACCOUNT_TYPE, $accountHolder)) {
            return null;
        }

        $isaAccount = new IsaAccount($accountHolder);
        $this->isaAccountRepository->save($isaAccount);
        return $isaAccount;
    }

    public function createJisaAccount(string $accountHolder, string $accountHolderBirthday): ?JisaAccount
    {
        if ($this->accountRetrieverService->retrieve(JisaAccount::ACCOUNT_TYPE, $accountHolder)) {
            return null;
        }

        $accountHolderBirthday = DateTime::createFromFormat('Y-m-d', $accountHolderBirthday);

        if (!$accountHolderBirthday) {
            return null;
        }

        $jisaAccount = new JisaAccount($accountHolder, $accountHolderBirthday);
        $this->jisaAccountRepository->save($jisaAccount);
        return $jisaAccount;
    }
}