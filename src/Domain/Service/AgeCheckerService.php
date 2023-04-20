<?php

namespace App\Domain\Service;

use App\Domain\Entity\JisaAccount;
use DateTime;

class AgeCheckerService
{
    public function canWithdrawJisa(JisaAccount $account): bool
    {
        $today = new DateTime(date('Y-m-d'));
        $diff = $today->diff($account->getAccountHolderBirthday());
        return $diff->y >= JisaAccount::AGE_LIMIT;
    }
}
