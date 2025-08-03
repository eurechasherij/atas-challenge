<?php

namespace App\Helpers;

use Carbon\Carbon;

class XeroHelper
{
    public static function parseXeroDate(?string $dateStr): ?Carbon
    {
        if (!$dateStr) return null;

        if (preg_match('/\/Date\((\d+)(?:[+-]\d+)?\)\//', $dateStr, $matches)) {
            return Carbon::createFromTimestampMs($matches[1]);
        }

        return null;
    }
}
