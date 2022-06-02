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

if (!function_exists('is_expired_report')) {
    function is_expired_report($req_id, $level)
    {   
        $ci = &get_instance();
        $request = $ci->db->select('head_of_units_status, country_director_status, finance_status')
        ->from('ea_report_status')
        ->where('request_id', $req_id)
        ->get()->row_array();
        $expired = false;
        if($level == 'head_of_units' ) {
            if($request['head_of_units_status'] != 1) {
                $expired = true;
            }
        }
        if($level == 'country_director' ) {
            if($request['country_director_status'] != 1) {
                $expired = true;
            }
        } 
        if($level == 'finance' ) {
            if($request['finance_status'] != 1) {
                $expired = true;
            }
        } 
        return $expired;
    }
}