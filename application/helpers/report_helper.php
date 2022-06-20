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
		$total_reported = count($dest_report);
		if($total_reported >= $total_report) {
			return true;
		}
        return false;
    }
}

if (!function_exists('get_request_participants_array')) {
    function get_request_participants_array($req_id)
    {   
        $ci = &get_instance();
        $request = $ci->db->select('ear.*, u.username as requestor_name')->from('ea_requests ear')
		->join('tb_userapp u', 'u.id = ear.requestor_id')
		->where('ear.id', $req_id)
		->get()->row_array();
        $persons = [];
		if($request['employment'] == 'Just for me') {
            $persons = [$request['requestor_name']];
        } else if ($request['employment'] == 'On behalf') {
            if($request['employment_status'] == 'Group') {
                $persons = [$request['participant_group_name']];
            } else {
                $participants = $ci->db->select('*')->from('ea_requests_participants')
                ->where('request_id', $req_id)
                ->get()->result_array();
                $names = array_column($participants, 'name');
                $persons = $names;
            }
		} else {
            if($request['employment_status'] == 'Group') {
                $persons = [$request['requestor_name'], $request['participant_group_name']];
            } else {
                $participants = $ci->db->select('*')->from('ea_requests_participants')
                ->where('request_id', $req_id)
                ->get()->result_array();
                $names = array_column($participants, 'name');
                $persons = $names;
                array_unshift($persons, $request['requestor_name']);
            }
        }
        return $persons;
    }
}

if (!function_exists('get_request_participants')) {
    function get_request_participants($req_id)
    {   
        $ci = &get_instance();
        $request = $ci->db->select('ear.*, u.username as requestor_name')->from('ea_requests ear')
		->join('tb_userapp u', 'u.id = ear.requestor_id')
		->where('ear.id', $req_id)
		->get()->row_array();
        $persons = '';
		if($request['employment'] == 'Just for me') {
            $persons = $request['requestor_name'];
        } else if ($request['employment'] == 'On behalf') {
            if($request['employment_status'] == 'Group') {
                $persons = $request['participant_group_name'];
            } else {
                $participants = $ci->db->select('*')->from('ea_requests_participants')
                ->where('request_id', $req_id)
                ->get()->result_array();
                $names = array_column($participants, 'name');
                $persons = implode ("; ", $names);
            }
		} else {
            if($request['employment_status'] == 'Group') {
                $group_name = $request['participant_group_name'];
                $requestor_name = $request['requestor_name'];
                $persons = "$requestor_name; $group_name";
            } else {
                $participants = $ci->db->select('*')->from('ea_requests_participants')
                ->where('request_id', $req_id)
                ->get()->result_array();
                $names = array_column($participants, 'name');
                $persons = implode ("; ", $names);
                $persons = $request['requestor_name'] . "; $persons";
            }
        }
		
        return $persons;
    }
}

if (!function_exists('get_total_refund_or_reimburst')) {
    function get_total_refund_or_reimburst($req_id)
    {   
        $ci = &get_instance();
        $ci->load->model('Report_Model', 'report');
        $destinations = $ci->db->select('*')
        ->from('ea_requests_destinations')
        ->where('request_id', $req_id)
        ->get()->result_array();
        $request = $ci->db->select('total_advance')
        ->from('ea_requests')
        ->where('id', $req_id)
        ->get()->row_array();
        $total_advance = $request['total_advance'];
        $total_expense = 0;
        $total_dest = count($destinations);
        for ($i = 0; $i < $total_dest; $i++) {
            $total_costs_arr = [];
            $night = $destinations[$i]['night'];
            for ($x = 0; $x < $night; $x++) {
                $total_cost_per_night = 0;
                $lodging = $ci->report->get_actual_costs_by_night($destinations[$i]['id'], 1, $x + 1);
                if($lodging) {
                    $total_cost_per_night += $lodging['cost'];
                }
                $other_items_by_name = $ci->report->get_other_items_group_by_name($destinations[$i]['id'], $x + 1);
                if($other_items_by_name) {
                    for($z = 0; $z<count($other_items_by_name); $z++) {
                        $item_cost = 0;
                        $items_by_name = $ci->report->get_other_items_by_name($destinations[$i]['id'], $other_items_by_name[$z]['item_name'], $x + 1);
                        foreach($items_by_name as $i_name) {
                            $item_cost += $i_name['cost'];
                        }
                        $other_items_by_name[$z]['total_cost'] = $item_cost;
                        $total_cost_per_night += $item_cost;
                    }
                }
                $provided_meals =  $ci->report->get_provided_meals($destinations[$i]['id'],  $x + 1);
                if($provided_meals) {
                    $total_cost_per_night += $provided_meals['cost'];
                }
                array_push($total_costs_arr, $total_cost_per_night);
            }
            for($g=0;$g < count($total_costs_arr); $g++) {
                $total_expense += $total_costs_arr[$g];
            }
        }
        $total = $total_advance - $total_expense;
		if($total < 0) {
			$data = [
                'status' => 'Reimburst',
                'type' => 2,
                'total' => $total * -1,
            ];
		} else {
			$data = [
                'status' => 'Refund',
                'type' => 1,
                'total' => $total,
            ];
		}
        return $data;
    }
}


if (!function_exists('get_total_advance')) {
    function get_total_advance($req_id)
    {   
        $ci = &get_instance();
        $request = $ci->db->select('total_advance')
        ->from('ea_requests')
        ->where('id', $req_id)
        ->get()->row_array();
        $total_advance = $request['total_advance'];
        return number_format($total_advance,2,',','.');
    }
}

if (!function_exists('get_total_expense')) {
    function get_total_expense($req_id)
    {   
        $ci = &get_instance();
        $ci->load->model('Report_Model', 'report');
        $destinations = $ci->db->select('*')
        ->from('ea_requests_destinations')
        ->where('request_id', $req_id)
        ->get()->result_array();
        $total_expense = 0;
        $total_dest = count($destinations);
        for ($i = 0; $i < $total_dest; $i++) {
            $total_costs_arr = [];
            $night = $destinations[$i]['night'];
            for ($x = 0; $x < $night; $x++) {
                $total_cost_per_night = 0;
                $lodging = $ci->report->get_actual_costs_by_night($destinations[$i]['id'], 1, $x + 1);
                if($lodging) {
                    $total_cost_per_night += $lodging['cost'];
                }
                $other_items_by_name = $ci->report->get_other_items_group_by_name($destinations[$i]['id'], $x + 1);
                if($other_items_by_name) {
                    for($z = 0; $z<count($other_items_by_name); $z++) {
                        $item_cost = 0;
                        $items_by_name = $ci->report->get_other_items_by_name($destinations[$i]['id'], $other_items_by_name[$z]['item_name'], $x + 1);
                        foreach($items_by_name as $i_name) {
                            $item_cost += $i_name['cost'];
                        }
                        $other_items_by_name[$z]['total_cost'] = $item_cost;
                        $total_cost_per_night += $item_cost;
                    }
                }
                $provided_meals =  $ci->report->get_provided_meals($destinations[$i]['id'],  $x + 1);
                if($provided_meals) {
                    $total_cost_per_night += $provided_meals['cost'];
                }
                array_push($total_costs_arr, $total_cost_per_night);
            }
            for($g=0;$g < count($total_costs_arr); $g++) {
                $total_expense += $total_costs_arr[$g];
            }
        }
        return number_format($total_expense,2,',','.');
    }
}

if (!function_exists('get_total_all_ter_expense')) {
    function get_total_all_ter_expense($req_id)
    {   
        $ci = &get_instance();
        $ci->load->model('Report_Model', 'report');
        $items = $ci->db->select('cost')
        ->from('ea_actual_costs')
        ->where('request_id', $req_id)
        ->where_in('item_type', [1,3])
        ->get()->result_array();
        $total_expense = 0;
        foreach($items as $item) {
            $total_expense += $item['cost'];
        }        
        return number_format($total_expense,2,',','.');
    }
}

if (!function_exists('get_total_expense_by_ter')) {
    function get_total_expense_by_ter($req_id, $ter_id)
    {   
        $ci = &get_instance();
        $ci->load->model('Report_Model', 'report');
        $destinations = $ci->db->select('*')
        ->from('ea_requests_destinations')
        ->where('request_id', $req_id)
        ->get()->result_array();
        $total_expense = 0;
        $total_dest = count($destinations);
        for ($i = 0; $i < $total_dest; $i++) {
            $total_costs_arr = [];
            $night = $destinations[$i]['night'];
            for ($x = 0; $x < $night; $x++) {
                $total_cost_per_night = 0;
                $lodging = $ci->report->get_actual_costs_by_night_and_ter($ter_id, $destinations[$i]['id'], 1, $x + 1);
                if($lodging) {
                    $total_cost_per_night += $lodging['cost'];
                }
                $other_items_by_name = $ci->report->get_other_items_group_by_name_and_ter($ter_id, $destinations[$i]['id'], $x + 1);
                if($other_items_by_name) {
                    for($z = 0; $z<count($other_items_by_name); $z++) {
                        $item_cost = 0;
                        $items_by_name = $ci->report->get_other_items_by_name_and_ter($ter_id, $destinations[$i]['id'], $other_items_by_name[$z]['item_name'], $x + 1);
                        foreach($items_by_name as $i_name) {
                            $item_cost += $i_name['cost'];
                        }
                        $other_items_by_name[$z]['total_cost'] = $item_cost;
                        $total_cost_per_night += $item_cost;
                    }
                }
                $provided_meals =  $ci->report->get_provided_meals_by_ter($ter_id, $destinations[$i]['id'],  $x + 1);
                if($provided_meals) {
                    $total_cost_per_night += $provided_meals['cost'];
                }
                array_push($total_costs_arr, $total_cost_per_night);
            }
            for($g=0;$g < count($total_costs_arr); $g++) {
                $total_expense += $total_costs_arr[$g];
            }
        }
        return number_format($total_expense,2,',','.');
    }
}

if (!function_exists('get_total_approved_expense')) {
    function get_total_approved_expense($req_id)
    {   
        $ci = &get_instance();
        $ci->load->model('Report_Model', 'report');
        $destinations = $ci->db->select('*')
        ->from('ea_requests_destinations')
        ->where('request_id', $req_id)
        ->get()->result_array();
        $total_expense = 0;
        foreach($destinations as $dest) {
			$items = $ci->db->select('cost')
			->from('ea_actual_costs')
			->where('is_approved_by_finance', 1)
			->where('dest_id', $dest['id'])
			->get()->result_array();
			foreach($items as $item) {
				$total_expense += $item['cost']; 
			}
		}
        return number_format($total_expense,2,',','.');
    }
}

if (!function_exists('get_approved_total_refund_or_reimburst')) {
    function get_approved_total_refund_or_reimburst($req_id)
    {   
        $ci = &get_instance();
        $ci->load->model('Report_Model', 'report');
        $destinations = $ci->db->select('*')
        ->from('ea_requests_destinations')
        ->where('request_id', $req_id)
        ->get()->result_array();
        $request = $ci->db->select('total_advance')
        ->from('ea_requests')
        ->where('id', $req_id)
        ->get()->row_array();
        $total_advance = $request['total_advance'];
        $total_expense = 0;
        foreach($destinations as $dest) {
			$items = $ci->db->select('cost')
			->from('ea_actual_costs')
			->where('is_approved_by_finance', 1)
			->where('dest_id', $dest['id'])
			->get()->result_array();
			foreach($items as $item) {
				$total_expense += $item['cost']; 
			}
		}
        $total = $total_advance - $total_expense;
		if($total < 0) {
			$data = [
                'status' => 'Reimburst',
                'type' => 2,
                'total' => $total * -1,
            ];
		} else {
			$data = [
                'status' => 'Refund',
                'type' => 1,
                'total' => $total,
            ];
		}
        return $data;
    }
}

if (!function_exists('get_total_approved_reimburst')) {
    function get_total_approved_reimburst($req_id)
    {   
        $ci = &get_instance();
        $ci->load->model('Report_Model', 'report');
        $destinations = $ci->db->select('*')
        ->from('ea_requests_destinations')
        ->where('request_id', $req_id)
        ->get()->result_array();
        $request = $ci->db->select('total_advance')
        ->from('ea_requests')
        ->where('id', $req_id)
        ->get()->row_array();
        $total_expense = 0;
        $total_advance = $request['total_advance'];
        foreach($destinations as $dest) {
			$items = $ci->db->select('cost')
			->from('ea_actual_costs')
			->where('is_approved_by_finance', 1)
			->where('dest_id', $dest['id'])
			->get()->result_array();
			foreach($items as $item) {
				$total_expense += $item['cost']; 
			}
		}
		$total = $total_advance - $total_expense;
		if($total > 0) {
			return '-';
        } else {
            $total = $total * -1;
            return number_format($total,2,',','.');
		}
    }
}

if (!function_exists('get_total_approved_refund')) {
    function get_total_approved_refund($req_id)
    {   
        $ci = &get_instance();
        $ci->load->model('Report_Model', 'report');
        $destinations = $ci->db->select('*')
        ->from('ea_requests_destinations')
        ->where('request_id', $req_id)
        ->get()->result_array();
        $request = $ci->db->select('total_advance')
        ->from('ea_requests')
        ->where('id', $req_id)
        ->get()->row_array();
        $total_expense = 0;
        $total_advance = $request['total_advance'];
        foreach($destinations as $dest) {
			$items = $ci->db->select('cost')
			->from('ea_actual_costs')
			->where('is_approved_by_finance', 1)
			->where('dest_id', $dest['id'])
			->get()->result_array();
			foreach($items as $item) {
				$total_expense += $item['cost']; 
			}
		}
        $total = $total_advance - $total_expense;
		if($total < 0) {
            $total = $total * -1;
			return '-';
		} else {
            return number_format($total,2,',','.');
		}
    }
}

if (!function_exists('is_all_items_clear')) {
    function is_all_items_clear($req_id)
    {   
        $ci = &get_instance();
        $destinations = $ci->db->select('id')->from('ea_requests_destinations')
						->where('request_id', $req_id)->get()->result_array();
		$all_items = [];
		foreach($destinations as $dest) {
			$where = [
				'dest_id' => $dest['id'],
				'cost !=' => 0,
				'item_name !=' => 'Meals', 
			];
			$items = $ci->db->select('id, item_name, cost, is_approved_by_finance')
			->from('ea_actual_costs')
			->where($where)
			->get()->result_array();
			foreach($items as $item) {
				$all_items[] = $item; 
			}
		}
		$data['total_items'] = count($all_items);
		$data['items'] = $all_items;
		$status = array_column($all_items, 'is_approved_by_finance');
        if(in_array(0, $status)) {
			return false;
		}
        return true;
    }
}

if (!function_exists('is_ter_report_finished')) {
    function is_ter_report_finished($req_id, $ter_id)
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
		->where('eac.ter_id', $ter_id)
		->where_in('eac.item_type', [1,2])
		->get()->result_array();
		$total_reported = count($dest_report);
		if($total_reported >= $total_report) {
			return true;
		}
        return false;
    }
}