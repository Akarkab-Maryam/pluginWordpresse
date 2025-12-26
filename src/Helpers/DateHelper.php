<?php

namespace MyPlugin\Helpers;

class DateHelper
{
    public static function formatDate($date, $format = 'd/m/Y')
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }
        
        return $date->format($format);
    }

    public static function timeAgo($date)
    {
        return human_time_diff(strtotime($date), current_time('timestamp')) . ' ago';
    }

    public static function isWeekend($date = null)
    {
        $date = $date ?: current_time('Y-m-d');
        $dayOfWeek = date('N', strtotime($date));
        return $dayOfWeek >= 6;
    }

    public static function addDays($date, $days)
    {
        $datetime = new \DateTime($date);
        $datetime->add(new \DateInterval('P' . $days . 'D'));
        return $datetime->format('Y-m-d');
    }

    public static function getCurrentTimestamp()
    {
        return current_time('mysql');
    }
}