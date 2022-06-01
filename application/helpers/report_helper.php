<?php

if (!function_exists('get_destination_row')) {
	function get_destination_row($date)
	{
		$day = date("l", strtotime($date));
        $row = 'A';
        if($day == 'Monday') {
            $row = 'C';
        } else if($day == 'Tuesday') {
            $row = 'D';
        } else if($day == 'Wednesday') {
            $row = 'E';
        } else if($day == 'Thursday') {
            $row = 'F';
        } else if($day == 'Friday') {
            $row = 'G';
        } else if($day == 'Saturday') {
            $row = 'H';
        } else if($day == 'Sunday') {
            $row = 'I';
        }
        return $row;
	}
}

if (!function_exists('ordinal')) {
	function ordinal($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
            return $number. $ends[$number % 10];
    }
}