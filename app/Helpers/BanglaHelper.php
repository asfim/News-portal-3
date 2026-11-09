<?php

namespace App\Helpers;

use Carbon\Carbon;

class BanglaHelper
{
    protected static array $banglaNumbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    protected static array $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    protected static array $banglaMonths = [
        'January' => 'জানুয়ারি',
        'February' => 'ফেব্রুয়ারি',
        'March' => 'মার্চ',
        'April' => 'এপ্রিল',
        'May' => 'মে',
        'June' => 'জুন',
        'July' => 'জুলাই',
        'August' => 'আগস্ট',
        'September' => 'সেপ্টেম্বর',
        'October' => 'অক্টোবর',
        'November' => 'নভেম্বর',
        'December' => 'ডিসেম্বর',
    ];

    protected static array $banglaDays = [
        'Saturday' => 'শনিবার',
        'Sunday' => 'রবিবার',
        'Monday' => 'সোমবার',
        'Tuesday' => 'মঙ্গলবার',
        'Wednesday' => 'বুধবার',
        'Thursday' => 'বৃহস্পতিবার',
        'Friday' => 'শুক্রবার',
    ];

    public static function bnNumber($number): string
    {
        return str_replace(self::$englishNumbers, self::$banglaNumbers, (string)$number);
    }

    public static function bnDate($date = null): string
    {
        $carbon = $date ? Carbon::parse($date)->setTimezone('Asia/Dhaka') : Carbon::now('Asia/Dhaka');
        $dayName = self::$banglaDays[$carbon->format('l')] ?? '';
        $day = self::bnNumber($carbon->format('d'));
        $month = self::$banglaMonths[$carbon->format('F')] ?? '';
        $year = self::bnNumber($carbon->format('Y'));

        return "{$dayName}, {$day} {$month} {$year}";
    }

    public static function bnTimeAgo($date): string
    {
        if (!$date) return '';
        $carbon = Carbon::parse($date)->setTimezone('Asia/Dhaka');
        $diffMinutes = $carbon->diffInMinutes(Carbon::now('Asia/Dhaka'));

        if ($diffMinutes < 1) {
            return 'এইমাত্র';
        }
        if ($diffMinutes < 60) {
            return self::bnNumber($diffMinutes) . ' মিনিট আগে';
        }
        $diffHours = $carbon->diffInHours(Carbon::now('Asia/Dhaka'));
        if ($diffHours < 24) {
            return self::bnNumber($diffHours) . ' ঘণ্টা আগে';
        }
        $diffDays = $carbon->diffInDays(Carbon::now('Asia/Dhaka'));
        if ($diffDays < 7) {
            return self::bnNumber($diffDays) . ' দিন আগে';
        }

        return self::bnDate($carbon);
    }
}
