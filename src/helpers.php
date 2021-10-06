<?php

if (!function_exists('formatCpf')) {
    function formatCpf ($value)
    {
        $value = preg_replace("/[^\d]/", "", (string)$value);
        $value = substr($value, 0, 11);

        if (strlen($value) == 11) {
            return substr($value, 0, 3).'.'.substr($value, 3, 3).'.'.substr($value, 6, 3).'-'.substr($value, 9, 2);
        }

        return $value;
    }
}

if (!function_exists('formatCnpj')) {
    function formatCnpj ($value)
    {
        $value = preg_replace("/[^\d]/", "", (string)$value);
        $value = substr($value, 0, 14);

        if (strlen($value) == 14) {
            return substr($value, 0, 2).'.'.substr($value, 2, 3).'.'.substr($value, 5, 3).'/'.substr($value, 8, 4).'-'.substr($value, 12, 2);
        }

        return $value;
    }
}

if (!function_exists('formatPhone')) {
    function formatPhone($value)
    {
        $value = preg_replace("/[^\d]/", "", (string)$value);
        $value = substr($value, 0, 11);

        if (strlen($value) >= 10) {
            if (strlen($value) == 11) {
                return '('.substr($value, 0, 2).') '.substr($value, 2, 5).'-'.substr($value, 7, 4);
            } else {
                return '('.substr($value, 0, 2).') '.substr($value, 2, 4).'-'.substr($value, 6, 4);
            }
        }

        return $value;
    }
}

if (!function_exists('formatPostalCode')) {
    function formatPostalCode($value)
    {
        $value = preg_replace("/[^\d]/", "", (string)$value);
        $value = substr($value, 0, 8);

        if (strlen($value) == 8) {
            return substr($value, 0, 5).'-'.substr($value, 5, 3);
        }

        return $value;
    }
}

if (!function_exists('formatValueToFloat')) {
    function formatValueToFloat($value, int $precision = 2)
    {
        return number_format((float)preg_replace(['/[^0-9,]/', '/[,]/'], ['', '.'], $value), $precision, '.', ',');
    }
}

if (!function_exists('dateEnToBr')) {
    function dateEnToBr($date) {
        $date = explode('-', $date);
        $date = array_reverse($date);

        return implode('/', $date);
    }
}

if (!function_exists('dateBrToEn')) {
    function dateBrToEn($date) {
        $date = explode('/', $date);
        $date = array_reverse($date);

        return implode('-', $date);
    }
}

if (!function_exists('dateDiff')) {
    function dateDiff($dateOne, $dateTwo, $increment = 0, $type = 'M') {
        $diff = 0;
        switch ($type) {
            case 'D':
                $dateDiff = date_diff(date_create($dateOne), date_create($dateTwo));
                if ($dateDiff->invert == 0) {
                    $diff = $dateDiff->days;
                }
                break;
            case 'M':
                $years = (date('Y', strtotime($dateTwo)) - date('Y', strtotime($dateOne)));
                $months = (date('m', strtotime($dateTwo)) - date('m', strtotime($dateOne)));
                if ($months >= 0 || $years > 0) {
                    $diff = ($months + ($years * 12));
                }
                break;
            case 'Y':
                $years = (date('Y', strtotime($dateTwo)) - date('Y', strtotime($dateOne)));
                if ($years >= 0) {
                    $diff = $years;
                }
                break;
            default:
                return 0;
                break;
        }

        return ($diff += $increment);
    }
}
