<?php

if (!function_exists('encrypt')) {
	function encrypt($str)
	{
		$ci = &get_instance();

		return $ci->encryption->encrypt($str);
	}
}

if (!function_exists('decrypt')) {
	function decrypt($str)
	{
		$ci = &get_instance();

		return $ci->encryption->decrypt($str);
	}
}

if (!function_exists('selected')) {
    function selected($val, $array)
    {
        if (is_array($array)) {
            foreach ($array as $a) {
                if ($a == $val) {
                    return (string) $val === (string) $a ? 'selected="selected"' : '';
                }
            }

            return '';
        }

        return $val == $array ? 'selected="selected"' : '';
    }
}

if (!function_exists('is_budget_reviewer')) {
    function is_budget_reviewer()
    {   
        $ci = &get_instance();

        if($ci->user_data->isBudgetRiviewer == 1) {
            return true;
        }

        return false;
    }
}

if (!function_exists('is_fco_monitor')) {
    function is_fco_monitor()
    {   
        $ci = &get_instance();

        if($ci->user_data->isBudgetRiviewer == 1) {
            return true;
        }

        return false;
    }
}

if (!function_exists('is_tor_approver')) {
    function is_tor_approver()
    {   
        $ci = &get_instance();

        if($ci->user_data->isTorApprover == 1) {
            return true;
        }

        return false;
    }
}

if (!function_exists('is_head_of_units')) {
    function is_head_of_units()
    {   
        $ci = &get_instance();

        if($ci->user_data->isHeadUnit == 1) {
            return true;
        }

        return false;
    }
}

if (!function_exists('is_line_supervisor')) {
    function is_line_supervisor()
    {   
        $ci = &get_instance();

        if($ci->user_data->isDirectSupervisor == 1) {
            return true;
        }

        return false;
    }
}

if (!function_exists('is_ea_assosiate')) {
    function is_ea_assosiate()
    {   
        $ci = &get_instance();

        if($ci->user_data->username == 'mlisna@fhi360.org' || $ci->user_data->fullName == 'Mega Lisna' || $ci->user_data->username == 'fadelassosiate') {
            return true;
        }

        return false;
    }
}

if (!function_exists('is_finance_teams')) {
    function is_finance_teams()
    {   
        $ci = &get_instance();
        // If units = Finance
        if($ci->user_data->unitsId == 3) {
            return true;
        }

        return false;
    }
}

if (!function_exists('is_country_director')) {
    function is_country_director()
    {   
        $ci = &get_instance();
        if($ci->user_data->isCountryDirector == 1) {
            return true;
        }

        return false;
    }
}

if (!function_exists('get_total_request_costs')) {
    function get_total_request_costs($req_id)
    {   
        $ci = &get_instance();
        $destinations = $ci->db->select('total')
        ->from('ea_requests_destinations')
        ->where('request_id', $req_id)
        ->get()->result_array();
        $total_destinations_cost = 0;
        foreach($destinations as $dest) {
            $total_destinations_cost += $dest['total'];
        }
        return number_format($total_destinations_cost,2,',','.');
    }
}

if (!function_exists('get_requests_status')) {
    function get_requests_status($req_id)
    {   
        $ci = &get_instance();
        $request = $ci->db->select('head_of_units_status, ea_assosiate_status, fco_monitor_status, finance_status')
        ->from('ea_requests_status')
        ->where('request_id', $req_id)
        ->get()->row_array();
        $status = [
            'text' => 'Pending',
            'badge_color' => 'info',
        ];
        if($request['head_of_units_status'] == 3 || $request['ea_assosiate_status'] == 3 || $request['fco_monitor_status'] == 3 || $request['finance_status'] == 3) {
            $status = [
                'text' => 'Rejected',
                'badge_color' => 'danger',
            ];
        } else if($request['head_of_units_status'] == 2 && $request['ea_assosiate_status'] == 2 && $request['fco_monitor_status'] == 2 && $request['finance_status'] == 2) {
            $status = [
                'text' => 'Done',
                'badge_color' => 'success',
            ];
        }
        return $status;
    }
}

if (!function_exists('is_expired_request')) {
    function is_expired_request($req_id, $level)
    {   
        $ci = &get_instance();
        $request = $ci->db->select('head_of_units_status, ea_assosiate_status, fco_monitor_status, finance_status')
        ->from('ea_requests_status')
        ->where('request_id', $req_id)
        ->get()->row_array();
        $expired = false;
        if($level == 'head_of_units' ) {
            if($request['head_of_units_status'] != 1) {
                $expired = true;
            }
        } 
        if($level == 'ea_assosiate' ) {
            if($request['ea_assosiate_status'] != 1) {
                $expired = true;
            }
        } 
        if($level == 'fco_monitor' ) {
            if($request['fco_monitor_status'] != 1) {
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

if (!function_exists('rejected_by')) {
    function rejected_by($req_id)
    {   
        $ci = &get_instance();
        $request = $ci->db->select('head_of_units_status, ea_assosiate_status, fco_monitor_status, finance_status')
        ->from('ea_requests_status')
        ->where('request_id', $req_id)
        ->get()->row_array();
        $rejected_by = '';
        if($request['finance_status'] == 3) {
            $rejected_by = 'finance';
        }  
        if($request['fco_monitor_status'] == 3) {
            $rejected_by = 'fco_monitor';
        } 
        if($request['ea_assosiate_status'] == 3) {
            $rejected_by = 'ea_assosiate';
        } 
        if($request['head_of_units_status'] == 3) {
            $rejected_by = 'head_of_units';
        }
        return $rejected_by;
    }
}