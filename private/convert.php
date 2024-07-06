<?php
function numberToWords($number) {
    $words = [
        0 => 'zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 
        14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 
        18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty', 
        30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 
        70 => 'Seventy', 80 => 'Eighty', 90 => 'ninety'
    ];
    if ($number < 100) {
        return twoDigitNumberToWords($number, $words);
    }
    $levels = ['', ' thousand'];
    $numLevels = count($levels);
    $number = number_format($number, 0, '.', ',');
    $parts = explode(',', $number);
    $parts = array_reverse($parts);

    $wordsArray = [];
    foreach ($parts as $index => $part) {
        if ((int)$part > 0) {
            $wordsArray[] = twoDigitNumberToWords($part, $words) . $levels[$index];
        }
    }
    return implode(' ', array_reverse($wordsArray));
}

function twoDigitNumberToWords($number, $words) {
    if ($number <= 20) {
        return $words[$number];
    } elseif ($number < 100) {
        $tens = intval($number / 10) * 10;
        $units = $number % 10;
        if ($units == 0) {
            return $words[$tens];
        } else {
            return $words[$tens] . '-' . $words[$units];
        }
    } else {
        $hundreds = intval($number / 100);
        $remainder = $number % 100;
        $remainderWords = $remainder > 0 ? ' and ' . twoDigitNumberToWords($remainder, $words) : '';
        return $words[$hundreds] . ' hundred' . $remainderWords;
    }
}
?>