<?php

namespace App\Helpers;

class NumberToWords
{
    private static $ones = [
        '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
        'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
        'seventeen', 'eighteen', 'nineteen'
    ];

    private static $tens = [
        '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
    ];

    private static $thousands = [
        '', 'thousand', 'million', 'billion', 'trillion'
    ];

    public static function convert($number)
    {
        if ($number == 0) {
            return 'zero';
        }

        $number = number_format(floatval($number), 2, '.', '');
        list($integer, $fraction) = explode('.', $number);

        $words = self::convertInteger($integer);

        if (intval($fraction) > 0) {
            $words .= ' and ' . self::convertInteger($fraction) . ' cents';
        }

        return ucwords($words);
    }

    private static function convertInteger($number)
    {
        $number = intval($number);
        
        if ($number == 0) {
            return '';
        } elseif ($number < 20) {
            return self::$ones[$number];
        } elseif ($number < 100) {
            return trim(self::$tens[intval($number / 10)] . ' ' . self::$ones[$number % 10]);
        } elseif ($number < 1000) {
            return trim(self::$ones[intval($number / 100)] . ' hundred ' . self::convertInteger($number % 100));
        } else {
            $result = '';
            $thousands_index = 0;
            
            while ($number > 0) {
                $chunk = $number % 1000;
                if ($chunk > 0) {
                    $chunk_words = self::convertInteger($chunk);
                    if ($thousands_index > 0) {
                        $chunk_words .= ' ' . self::$thousands[$thousands_index];
                    }
                    $result = $chunk_words . ' ' . $result;
                }
                $number = intval($number / 1000);
                $thousands_index++;
            }
            
            return trim($result);
        }
    }
} 