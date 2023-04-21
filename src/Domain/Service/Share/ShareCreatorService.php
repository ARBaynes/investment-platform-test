<?php

namespace App\Domain\Service\Share;

use App\Domain\Entity\Interfaces\AccountInterface;
use App\Domain\Entity\Share;
use App\Infrastructure\Repository\ShareRepository;

class ShareCreatorService
{
    public function __construct(private readonly ShareRepository $shareRepository)
    {
    }

    public function createShare(string $slug, string $company, float $startingValue, ?float $startingPrice = 1.00): Share
    {
        $share = new Share($slug, $company, $startingValue, $startingPrice);
        $this->shareRepository->save($share);
        return $share;
    }
}
