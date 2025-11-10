<?php

namespace App\Support;

class CalculateDV
{
    public static function fromNumber(string $number): int
    {
        $weights = [3,7,13,17,19,23,29,37,41,43,47,53,59,67,71];
        $number = preg_replace('/\D/', '', $number);
        $number = strrev($number);

        $sum = 0;
        for ($i = 0; $i < strlen($number); $i++) {
            $sum += intval($number[$i]) * $weights[$i];
        }

        $dv = $sum % 11;
        return ($dv > 1) ? 11 - $dv : $dv;
    }
}
