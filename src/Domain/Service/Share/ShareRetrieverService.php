<?php

namespace App\Domain\Service\Share;

use App\Domain\Entity\Interfaces\AccountInterface;
use App\Domain\Entity\JisaAccount;
use App\Domain\Entity\Share;
use App\Infrastructure\Repository\ShareRepository;

class ShareRetrieverService
{
    public function __construct(private readonly ShareRepository $shareRepository)
    {
    }

    public function retrieveOne(string $slug): ?Share
    {
        return $this->shareRepository->findOneBySlug($slug);
    }

    /** @return array<Share> */
    public function retrieveAll(): array
    {
        return $this->shareRepository->findAllShares();
    }

    public function retrieveAllByAccount(AccountInterface $account, string $slug): array
    {
        if ($account->getAccountType() === JisaAccount::ACCOUNT_TYPE) {
            return [];
        }

        return $this->shareRepository->findAllShares();
    }
}
