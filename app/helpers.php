<?php

use App\Helpers\BanglaHelper;

if (!function_exists('bn_num')) {
    function bn_num($number)
    {
        return BanglaHelper::bnNumber($number);
    }
}

if (!function_exists('bn_date')) {
    function bn_date($date = null)
    {
        return BanglaHelper::bnDate($date);
    }
}

if (!function_exists('bn_time_ago')) {
    function bn_time_ago($date)
    {
        return BanglaHelper::bnTimeAgo($date);
    }
}
