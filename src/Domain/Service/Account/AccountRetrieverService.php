<?php

namespace App\Domain\Service\Account;

use App\Domain\Entity\Interfaces\AccountInterface;
use App\Domain\Entity\IsaAccount;
use App\Domain\Entity\JisaAccount;
use App\Infrastructure\Repository\IsaAccountRepository;
use App\Infrastructure\Repository\JisaAccountRepository;

// With more time, I would likely use a chain of responsibility style pattern to loop through multiple retriever services
// to see if they match the requested account type
class AccountRetrieverService
{
    public function __construct(
        private readonly IsaAccountRepository $isaAccountRepository,
        private readonly JisaAccountRepository $jisaAccountRepository
    ) {
    }

    public function retrieve(string $type, string $accountHolder): ?AccountInterface
    {
        return match ($type) {
            IsaAccount::ACCOUNT_TYPE => $this->getIsa($accountHolder),
            JisaAccount::ACCOUNT_TYPE => $this->getJisa($accountHolder),
            default => null,
        };
    }

    private function getIsa(string $accountHolder): ?IsaAccount
    {
        try {
            return $this->isaAccountRepository->findOneByAccountHolder($accountHolder);
        } catch (\Throwable) {
            return null;
        }
    }

    private function getJisa(string $accountHolder): ?JisaAccount
    {
        try {
            return $this->jisaAccountRepository->findOneBy(['accountHolder' => $accountHolder]);
        } catch (\Throwable) {
            return null;
        }
    }

}
