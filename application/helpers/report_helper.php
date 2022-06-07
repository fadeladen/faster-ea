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

if (!function_exists('get_total_actual_costs')) {
    function get_total_actual_costs($req_id)
    {   
        $ci = &get_instance();
        $costs = $ci->db->select('ac.cost')
        ->from('ea_actual_costs ac')
        ->join('ea_requests_destinations ed', 'ac.dest_id = ed.id')
        ->join('ea_requests ea', 'ea.id = ed.request_id')
        ->where('ea.id', $req_id)
        ->get()->result_array();
        $actual_costs = 0;
        foreach($costs as $cost) {
            $actual_costs += $cost['cost'];
        }
        return number_format($actual_costs,2,',','.');
    }
}

if (!function_exists('get_total_days')) {
    function get_total_days($req_id)
    {   
        $ci = &get_instance();
        $destinations = $ci->db->select('*')
        ->from('ea_requests_destinations')
        ->where('request_id', $req_id)
        ->get()->result_array();
        $total_dest = count($destinations);
        $arriv_date = strtotime($destinations[0]['arrival_date']);
        $depar_date = strtotime($destinations[$total_dest - 1]['departure_date']);
        $datediff = $depar_date - $arriv_date;
        $total_days = ($datediff / (60 * 60 * 24));
        return $total_days + 1;
    }
}

if (!function_exists('is_report_finished')) {
    function is_report_finished($req_id)
    {   
        $ci = &get_instance();
        $destinations = $ci->db->select('*')->from('ea_requests_destinations')
		->where('request_id', $req_id)
		->get()->result_array();
		$total_report = 0;
		foreach($destinations as $dest) {
			$total_row = $dest['night'] * 2;
			$total_report += $total_row;
		}
		$dest_report =  $ci->db->select('eac.*')->from('ea_actual_costs eac')
		->join('ea_requests_destinations erd', 'erd.id = eac.dest_id')
		->join('ea_requests ear', 'erd.request_id = ear.id')
		->where('ear.id', $req_id)
		->where_in('eac.item_type', [1,2])
		->get()->result_array();
		$total_actual_cost = count($dest_report);
		if($total_report == $total_actual_cost) {
			return true;
		}
        return false;
    }
}