<?php

namespace App\Domain\Service;

class TimeCheckerService
{
    private const OPENING_HOURS = '08:00';
    private const CLOSING_HOURS = '16:30';
    private const FORMAT = 'H:i';

    public function canSharesBePurchased(): bool
    {
        $time = \DateTime::createFromFormat(self::FORMAT, date(self::FORMAT));
        $opening = \DateTime::createFromFormat(self::FORMAT, self::OPENING_HOURS);
        $closing = \DateTime::createFromFormat(self::FORMAT, self::CLOSING_HOURS);
        return !($time < $opening || $time > $closing);
    }
}
