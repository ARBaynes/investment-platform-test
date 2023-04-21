<?php

namespace App\Domain\Service\Share;

use App\Domain\Entity\Interfaces\AccountInterface;
use App\Domain\Entity\IsaAccount;
use App\Domain\Entity\JisaAccount;
use App\Infrastructure\Repository\IsaAccountRepository;
use App\Infrastructure\Repository\ShareRepository;

class SharePurchaseService
{
    public function __construct(
        private readonly ShareRepository $shareRepository,
        private readonly IsaAccountRepository $accountRepository
    ) {
    }

    public function purchase(AccountInterface $account, string $slug): bool
    {
        if ($account->getAccountType() === JisaAccount::ACCOUNT_TYPE) {
            return false;
        }

        $share = $this->shareRepository->findOneBySlug($slug);
        if (!$share) {
            return false;
        }

        /** @var IsaAccount $account */
        $account->addShare($share->getSlug());
        $this->accountRepository->save($account);

        $share->setOwner($account->getAccountHolder());
        $this->shareRepository->save($share);
        return true;
    }

}
