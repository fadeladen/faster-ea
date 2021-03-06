<?php


class Report_Model extends CI_Model
{

    function get_report_data_by_id($id) {
        $request_data =  $this->db->select('r.id as r_id, CONCAT("EA", r.id) AS ea_number, DATE_FORMAT(r.created_at, "%d %M %Y - %H:%i") as request_date,
        DATE_FORMAT(r.departure_date, "%d %M %Y") as d_date, DATE_FORMAT(r.return_date, "%d %M %Y") as r_date, DATE_FORMAT(st.finance_status_at, "%d %M %Y") as payment_date,
        r.*, format(r.total_advance,2,"de_DE") as d_total_advance
        ')
        ->from('ea_requests r')
        ->join('ea_requests_status st', 'st.request_id = r.id')
        ->where('r.id', $id)
        ->get()->row_array();
        if(!$request_data) {
            return false;
        }
        $destinations = $this->db->select('*, format(meals,2,"de_DE") as d_meals, format(lodging,2,"de_DE") as d_lodging,
        format(total_lodging_and_meals,2,"de_DE") as d_total_lodging_and_meals, format(total,2,"de_DE") as d_total,
        DATE_FORMAT(departure_date, "%d %M %Y") as depar_date, DATE_FORMAT(arrival_date, "%d %M %Y") as arriv_date,
        format(actual_meals,2,"de_DE") as d_actual_meals, format(actual_lodging,2,"de_DE") as d_actual_lodging,
        DATE_FORMAT(arrival_date, "%Y/%m/%d") as d_arriv_date,
        ')
        ->from('ea_requests_destinations')
        ->where('request_id', $id)
        ->get()->result_array();
        $total_destinations_cost = 0;
        $total_expense = 0;
        $total_dest = count($destinations);
        for ($i = 0; $i < $total_dest; $i++) {
            $total_destinations_cost += $destinations[$i]['total'];
            $max_meals_lodging = $this->get_dest_max_budget($destinations[$i]['id']);
            $destinations[$i]['max_lodging_cost'] = number_format($max_meals_lodging['max_lodging_budget'],2,',','.');
            $destinations[$i]['max_meals_cost'] =  number_format($max_meals_lodging['max_meals_budget'],2,',','.');
            $night = $destinations[$i]['night'];
            $lodging_arr = [];
            $meals_arr = [];
            $meals_text_arr = [];
            $other_items_arr = [];
            $other_items_by_name_arr = [];
            $total_costs_arr = [];
            for ($x = 0; $x < $night; $x++) {
                $total_cost_per_night = 0;
                $lodging = $this->get_actual_costs_by_night($destinations[$i]['id'], 1, $x + 1);
                if($lodging) {
                    $total_cost_per_night += $lodging['cost'];
                    array_push($lodging_arr, $lodging);
                } else {
                    array_push($lodging_arr, [
                        'item_id' => null,
                        'item_name' => null,
                        'cost' => null,
                        'd_cost' => null,
                        'receipt' => null,
                    ]);
                }
                $meals = $this->get_actual_costs_by_night($destinations[$i]['id'], 2, $x + 1);
                if($meals) {
                    array_push($meals_arr, $meals);
                } else {
                    array_push($meals_arr, [
                        'item_id' => null,
                        'item_name' => null,
                        'cost' => null,
                        'd_cost' => null,
                        'receipt' => null,
                    ]);
                }
                $other_items = $this->get_other_items_by_night($destinations[$i]['id'], $x + 1);
                if($other_items) {
                    array_push($other_items_arr, $other_items);
                } else {
                    array_push($other_items_arr, []);
                }
                $other_items_by_name = $this->get_other_items_group_by_name($destinations[$i]['id'], $x + 1);
                if($other_items_by_name) {
                    for($z = 0; $z<count($other_items_by_name); $z++) {
                        $item_cost = 0;
                        $items_by_name = $this->get_other_items_by_name($destinations[$i]['id'], $other_items_by_name[$z]['item_name'], $x + 1);
                        foreach($items_by_name as $i_name) {
                            $item_cost += $i_name['cost'];
                        }
                        $other_items_by_name[$z]['total_cost'] = $item_cost;
                        $total_cost_per_night += $item_cost;
                    }
                    array_push($other_items_by_name_arr, $other_items_by_name);
                } else {
                    array_push($other_items_by_name_arr, []);
                }
                $provided_meals =  $this->get_provided_meals($destinations[$i]['id'],  $x + 1);
                if($provided_meals) {
                    array_push($meals_text_arr, [
                        'id' =>  $provided_meals['id'],
                        'meals_text' => $provided_meals['meals_text'],
                        'is_first_day' => $provided_meals['is_first_day'],
                        'is_last_day' => $provided_meals['is_last_day'],
                        'cost' => $provided_meals['cost'],
                        'd_cost' => $provided_meals['d_cost'],
                    ]);
                    $total_cost_per_night += $provided_meals['cost'];
                } else {
                    array_push($meals_text_arr, [
                        'id' => null,
                        'meals_text' => '',
                        'is_first_day' => 0,
                        'is_last_day' => 0,
                        'cost' => null,
                        'd_cost' => null,
                    ]);
                }
                array_push($total_costs_arr, $total_cost_per_night);
            }
            $destinations[$i]['actual_lodging_items'] = $lodging_arr;
            $destinations[$i]['actual_meals_items'] = $meals_arr;
            $destinations[$i]['other_items'] = $other_items_arr;
            $destinations[$i]['other_items_by_name'] = $other_items_by_name_arr;
            $destinations[$i]['meals_text'] = $meals_text_arr;
            $destinations[$i]['total_costs_per_night'] = $total_costs_arr;
            for($g=0;$g < count($total_costs_arr); $g++) {
                $total_expense += $total_costs_arr[$g];
            }
        }
        $request_data['total_destinations_cost'] = $total_destinations_cost;
        $request_data['destinations'] = $destinations;
        $request_data['total_expense'] = $total_expense;
        return $request_data;
    }

    function get_destination_other_items($dest_id) {
        $other_items = $this->db->select('id, receipt, item, cost ,format(cost,2,"de_DE") as text_cost')
        ->from('ea_requests_other_items')
        ->where('destination_id', $dest_id)
        ->get()->result_array();
        return $other_items;
    }

    function get_actual_costs($dest_id, $item_type) {
        $data = $this->db->select('id, dest_id, receipt, item_type, cost ,format(cost,0,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('dest_id', $dest_id)
        ->where('item_type', $item_type)
        ->order_by('night', 'asc')
        ->get()->result_array();
        return $data;
    }

    function get_actual_costs_by_night($dest_id, $item_type, $night) {
        $data = $this->db->select('id, dest_id, is_approved_by_finance ,receipt, item_type, cost ,format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('dest_id', $dest_id)
        ->where('item_type', $item_type)
        ->where('night', $night)
        ->get()->row_array();
        return $data;
    }

    function get_ter_expenses($dest_id, $item_type, $night) {
        $data = $this->db->select('id, dest_id, receipt, item_type, cost ,format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('dest_id', $dest_id)
        ->where('item_type', $item_type)
        ->where('night', $night)
        ->where('cost !=', 0.00)
        ->get()->row_array();
        return $data;
    }

    function get_other_items_by_night($dest_id, $night) {
        $data = $this->db->select('id, dest_id, night, receipt, item_type, item_name, cost , format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('dest_id', $dest_id)
        ->where('item_type', 3)
        ->where('night', $night)
        ->where('item_name !=', 'List meals')
        ->order_by('item_name', 'asc')
        ->get()->result_array();
        return $data;
    }

    function get_other_items_group_by_name($dest_id, $night) {
        $data = $this->db->select('id, dest_id, night, receipt, item_type, item_name, cost , format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('dest_id', $dest_id)
        ->where('item_type', 3)
        ->where('night', $night)
        ->where('item_name !=', 'List meals')
        ->order_by('item_name', 'asc')
        ->group_by('item_name')
        ->get()->result_array();
        return $data;
    }

    function get_excel_other_items_by_night($dest_id, $night) {
        $data = $this->db->select('id, dest_id, night, receipt, item_type, item_name, meals_text, cost ,format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('dest_id', $dest_id)
        ->where('item_type', 3)
        ->where('night', $night)
        ->order_by('item_name', 'asc')
        ->get()->result_array();
        return $data;
    }

    function get_other_items_by_name($dest_id, $item_name, $night) {
        $data = $this->db->select('id, dest_id, night, is_approved_by_finance ,receipt, item_type, item_name, meals_text, cost ,format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('dest_id', $dest_id)
        ->where('item_type', 3)
        ->where('night', $night)
        ->where('item_name', $item_name)
        ->get()->result_array();
        return $data;
    }

    function get_provided_meals($dest_id, $night) {
        $data = $this->db->select('id, dest_id, night, is_last_day, is_first_day, is_approved_by_finance ,receipt, item_type, item_name, meals_text, cost ,format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('dest_id', $dest_id)
        ->where('item_type', 3)
        ->where('night', $night)
        ->where('item_name', 'List meals')
        ->get()->row_array();
        return $data;
    }

    function insert_other_items($payload) {
        $this->db->insert('ea_requests_other_items', $payload);
        return $this->db->insert_id();
    }

    function insert_actual_costs($payload) {
        $this->db->insert('ea_actual_costs', $payload);
        return $this->db->insert_id();
    }

    function update_actual_costs($item_id, $data) {
        $updated = $this->db->where('id', $item_id)->update('ea_actual_costs', $data);
        if($updated) {
            return true;
        }
        return false;
    }

    function update_other_items($item_id, $data) {
        $updated = $this->db->where('id', $item_id)->update('ea_requests_other_items', $data);
        if($updated) {
            return true;
        }
        return false;
    }

    function get_items_detail($id) {
        return $this->db->select('*, format(cost,0,"de_DE") as clean_cost')->from('ea_requests_other_items')->where('id', $id)->get()->row_array();
    }

    function get_actual_cost_detail($id) {
        return $this->db->select('*, format(cost,0,"de_DE") as clean_cost')->from('ea_actual_costs')->where('id', $id)->get()->row_array();
    }

    function get_dest_max_budget($dest_id) {
        $dest =  $this->db->select('is_edited_by_ea, night, country, lodging, lodging_usd, meals, total_lodging_and_meals, max_lodging_budget, max_lodging_budget_usd, max_meals_budget')
        ->from('ea_requests_destinations')
        ->where('id', $dest_id)
        ->get()->row_array();
        if($dest['is_edited_by_ea'] == 1) {
            $data = [
                'country' => $dest['country'],
                'total_night' => $dest['night'],
                'max_lodging_budget' => $dest['max_lodging_budget'],
                'max_lodging_budget_usd' => $dest['max_lodging_budget_usd'],
                'max_meals_budget' => $dest['max_meals_budget'],
                'total_max_budget' => $dest['max_lodging_budget'] + $dest['max_meals_budget'],
            ];
        } else {
            $data = [
                'country' => $dest['country'],
                'total_night' => $dest['night'],
                'max_lodging_budget' => $dest['lodging'],
                'max_lodging_budget_usd' => $dest['lodging_usd'],
                'max_meals_budget' => $dest['meals'],
                'total_max_budget' => $dest['total_lodging_and_meals'],
            ];
        }
        return $data;
    }

    function get_total_max_lodging_budget($req_id){
        $destinations = $this->db->select('id, night')->from('ea_requests_destinations')->where('request_id', $req_id)->get()->result_array();
		$total_max_lodging_budget = 0;
		foreach($destinations as $dest) {
			$max_budget = $this->report->get_dest_max_budget($dest['id']);
			$max_budget_per_night = $max_budget['max_lodging_budget'] * $dest['night'];
			$total_max_lodging_budget += $max_budget_per_night;
		}
		return $total_max_lodging_budget;
    }

    function submit_report($payload) {
        $this->db->where('id', $payload['request_id'])->update('ea_requests', ['is_ter_submitted' => 1]);
        $this->db->insert('ea_report_status', $payload);
        return $this->db->insert_id();
    }

    function resubmit_report($req_id) {
        $this->db->where('id', $req_id)->update('ea_requests', ['is_ter_submitted' => 1, 'is_ter_rejected' => 0]);
        $this->db->where('request_id', $req_id)->update('ea_report_status', [
            'head_of_units_status' => 1,
            'country_director_status' => 1,
            'finance_status' => 1,
			'submitted_at' =>  date("Y-m-d H:i:s"),
        ]);
        return true;
    }

    function update_status($request_id, $approver_id, $status, $level, $rejected_reason = null) {
        if($status == 3) {
            $this->db->where('id', $request_id)->update('ea_requests', ['is_ter_submitted' => 0, 'is_ter_rejected' => 1]);
        }
        $this->db->where('request_id', $request_id)->update('ea_report_status', [
            $level . '_status' => $status,
            $level . '_status_at' => date("Y-m-d H:i:s"),
            $level . '_id' => $approver_id,
            'rejected_reason' => $rejected_reason,
        ]);
        return true;
    }

    function update_ter_payment($request_id, $payload) {
        $this->db->where('request_id', $request_id)->update('ea_report_status', $payload);
        return $this->db->affected_rows() === 1;
    }

    function update_report_confirmation($request_id) {
        $payload = [
            'is_need_confirmation' => 1,
            'finance_status' => 2,
            'finance_id' => $this->user_data->userId,
            'finance_status_at' =>  date("Y-m-d H:i:s"),
        ];
        $this->db->where('request_id', $request_id)->update('ea_report_status', $payload);
        return $this->db->affected_rows() === 1;
    }

    function confirm_ter($request_id, $fco_monitor_id) {
        $payload = [
            'is_need_confirmation' => 0,
            'country_director_status' => 2,
            'country_director_id' => $fco_monitor_id,
            'country_director_status_at' =>  date("Y-m-d H:i:s"),
        ];
        $this->db->where('request_id', $request_id)->update('ea_report_status', $payload);
        return $this->db->affected_rows() === 1;
    }

    function get_approved_expense_by_night($dest_id, $night) {
        $total_expense = 0;
        $items = $this->db->select('cost')
        ->from('ea_actual_costs')
        ->where('is_approved_by_finance', 1)
        ->where('dest_id', $dest_id)
        ->where('night', $night)
        ->get()->result_array();
        foreach($items as $item) {
            $total_expense += $item['cost']; 
        }
        return $total_expense;
    }

    function get_approved_expense_by_night_and_ter($ter_id, $dest_id, $night) {
        $total_expense = 0;
        $items = $this->db->select('cost')
        ->from('ea_actual_costs')
        ->where('is_approved_by_finance', 1)
        ->where('ter_id', $ter_id)
        ->where('dest_id', $dest_id)
        ->where('night', $night)
        ->get()->result_array();
        foreach($items as $item) {
            $total_expense += $item['cost']; 
        }
        return $total_expense;
    }

    function get_report_status($req_id) {
        return $this->db->select('*')->from('ea_report_status')->where('request_id', $req_id)->get()->row_array();
    }

    function update_ter_item($item_id, $payload) {
        $this->db->where('id', $item_id)->update('ea_actual_costs', $payload);
        return true;
    }

    function get_participants_data($request_id) {
        return $this->db->select('*')
        ->from('ea_ter')
        ->where('request_id', $request_id)
        ->get()->result_array();
    }

    
    function get_ter_details($id) {
        $request_data =  $this->db->select('r.id as r_id, CONCAT("EA", r.id) AS ea_number, DATE_FORMAT(r.created_at, "%d %M %Y - %H:%i") as request_date,
        DATE_FORMAT(r.departure_date, "%d %M %Y") as d_date, DATE_FORMAT(r.return_date, "%d %M %Y") as r_date,
        r.*, uh.username as head_of_units_name, uh.email as head_of_units_email, st.head_of_units_status, uh.id as head_of_units_id,
        ufc.username as country_director_name, ufc.email as country_director_email, DATE_FORMAT(st.submitted_at, "%d %M %Y - %H:%i") as submitted_at,
        ufc.purpose as country_director_purpose, ufc.signature as country_director_signature, st.is_need_confirmation,
        ufi.username as finance_name, ufi.email as finance_email, DATE_FORMAT(st.head_of_units_status_at, "%d %M %Y - %H:%i") as head_of_units_status_at,
        DATE_FORMAT(st.country_director_status_at, "%d %M %Y - %H:%i") as country_director_status_at, st.country_director_status, st.finance_status,
        DATE_FORMAT(st.finance_status_at, "%d %M %Y - %H:%i") as finance_status_at, st.payment_type, format(st.total_payment,2,"de_DE") as total_payment,
        format(r.total_advance,2,"de_DE") as d_total_advance,
        (
            CASE 
                WHEN head_of_units_status = "1" THEN "Pending"
                WHEN head_of_units_status = "2" THEN "Approved"
                WHEN head_of_units_status = "3" THEN "Rejected"
            END) AS head_of_units_status_text,
        (
            CASE 
                WHEN country_director_status = "1" THEN "Pending"
                WHEN country_director_status = "2" THEN "Approved"
                WHEN country_director_status = "3" THEN "Rejected"
            END) AS country_director_status_text,
        (
            CASE 
                WHEN finance_status = "1" THEN "Pending"
                WHEN finance_status = "2" THEN "Approved"
                WHEN finance_status = "3" THEN "Rejected"
            END) AS finance_status_text,
        ')
        ->from('ea_requests r')
        ->join('ea_report_status st', 'st.request_id = r.id', 'left')
        ->join('tb_userapp ur', 'r.requestor_id = ur.id', 'left')
        ->join('tb_userapp uh', 'st.head_of_units_id = uh.id', 'left')
        ->join('tb_userapp ufc', 'st.country_director_id = ufc.id', 'left')
        ->join('tb_userapp ufi', 'st.finance_id = ufi.id', 'left')
        ->where('r.id', $id)
        ->get()->row_array();
        if(!$request_data) {
            return false;
        }
        $reports = $this->db->select('*, name as report_for')->from('ea_ter')->where('request_id', $id)->get()->result_array();
        for ($r = 0; $r < count($reports); $r++) {
            $destinations = $this->db->select('id, total, night, order, country, city, format(total,2,"de_DE") as d_total,
            DATE_FORMAT(departure_date, "%d %M %Y") as depar_date, DATE_FORMAT(arrival_date, "%d %M %Y") as arriv_date,
            DATE_FORMAT(arrival_date, "%Y/%m/%d") as d_arriv_date,
            ')
            ->from('ea_requests_destinations')
            ->where('request_id', $id)
            ->get()->result_array();
            $total_destinations_cost = 0;
            $total_dest = count($destinations);
            for ($i = 0; $i < $total_dest; $i++) {
                $total_destinations_cost += $destinations[$i]['total'];
                $night = $destinations[$i]['night'];
                $max_meals_lodging = $this->get_dest_max_budget($destinations[$i]['id']);
                $destinations[$i]['max_lodging_cost'] = number_format($max_meals_lodging['max_lodging_budget'],2,',','.');
                $destinations[$i]['max_meals_cost'] =  number_format($max_meals_lodging['max_meals_budget'],2,',','.');
                $lodging_arr = [];
                $meals_arr = [];
                $meals_text_arr = [];
                $other_items_arr = [];
                $total_costs_arr = [];
                $expenses_arr = [];
                for ($x = 0; $x < $night; $x++) {
                    $total_cost_per_night = 0;
                    $lodging = $this->get_actual_costs_by_night_and_ter($reports[$r]['id'], $destinations[$i]['id'], 1, $x + 1);
                    if($lodging) {
                        $total_cost_per_night += $lodging['cost'];
                        array_push($lodging_arr, $lodging);
                    } else {
                        array_push($lodging_arr, [
                            'id' => null,
                            'is_approved_by_finance' => 1,
                            'item_id' => null,
                            'item_name' => null,
                            'cost' => null,
                            'd_cost' => null,
                            'receipt' => null,
                        ]);
                    }
                    $meals = $this->get_actual_costs_by_night_and_ter($reports[$r]['id'], $destinations[$i]['id'], 2, $x + 1);
                    if($meals) {
                        array_push($meals_arr, $meals);
                    } else {
                        array_push($meals_arr, [
                            'id'=> null,
                            'is_approved_by_finance' => 1,
                            'item_id' => null,
                            'item_name' => null,
                            'cost' => null,
                            'd_cost' => null,
                            'receipt' => null,
                        ]);
                    }
                    $other_items = $this->get_other_items_group_by_name_and_ter($reports[$r]['id'], $destinations[$i]['id'], $x + 1);
                    if($other_items) {
                        for($z = 0; $z<count($other_items); $z++) {
                            $item_cost = 0;
                            $items_by_name = $this->get_other_items_by_name_and_ter($reports[$r]['id'], $destinations[$i]['id'], $other_items[$z]['item_name'], $x + 1);
                            foreach($items_by_name as $i_name) {
                                $item_cost += $i_name['cost'];
                            }
                            $other_items[$z]['total_cost'] = $item_cost;
                            $total_cost_per_night += $item_cost;
                        }
                        array_push($other_items_arr, $other_items);
                    } else {
                        array_push($other_items_arr, [
                        ]);
                    }
                    $provided_meals =  $this->get_provided_meals_by_ter($reports[$r]['id'], $destinations[$i]['id'],  $x + 1);
                    if($provided_meals) {
                        array_push($meals_text_arr, [
                            'id' =>  $provided_meals['id'],
                            'is_approved_by_finance' => $provided_meals['is_approved_by_finance'],
                            'meals_text' => $provided_meals['meals_text'],
                            'is_first_day' => $provided_meals['is_first_day'],
                            'is_last_day' => $provided_meals['is_last_day'],
                            'cost' => $provided_meals['cost'],
                            'd_cost' => $provided_meals['d_cost'],
                        ]);
                        $total_cost_per_night += $provided_meals['cost'];
                    } else {
                        array_push($meals_text_arr, [
                            'id' =>  null,
                            'is_approved_by_finance' => 1,
                            'meals_text' => '',
                            'is_first_day' => 0,
                            'is_last_day' => 0,
                            'cost' => 0,
                            'd_cost' => 0,
                        ]);
                    }
                    array_push($total_costs_arr, $total_cost_per_night);
                    $expenses_by_night =  $this->get_approved_expense_by_night_and_ter($reports[$r]['id'], $destinations[$i]['id'],  $x + 1);
                    if($expenses_by_night) {
                        array_push($expenses_arr, $expenses_by_night);
                    } else {
                        array_push($expenses_arr, 0);
                    }
                }
                $destinations[$i]['actual_lodging_items'] = $lodging_arr;
                $destinations[$i]['actual_meals_items'] = $meals_arr;
                $destinations[$i]['other_items'] = $other_items_arr;
                $destinations[$i]['meals_text'] = $meals_text_arr;
                $destinations[$i]['total_costs_per_night'] = $total_costs_arr;
                $destinations[$i]['total_approved_expenses_by_night'] = $expenses_arr;
            }
            $reports[$r]['destinations'] = $destinations;
        }
        $request_data['total_destinations_cost'] = $total_destinations_cost;
        $request_data['reports'] = $reports;
        return $request_data;
    }

    function get_report_data_by_ter($request_id, $ter_id) {
        $request_data =  $this->db->select('r.id as r_id, CONCAT("EA", r.id) AS ea_number, DATE_FORMAT(r.created_at, "%d %M %Y - %H:%i") as request_date,
        DATE_FORMAT(r.departure_date, "%d %M %Y") as d_date, DATE_FORMAT(r.return_date, "%d %M %Y") as r_date, DATE_FORMAT(st.finance_status_at, "%d %M %Y") as payment_date,
        r.*, format(r.total_advance,2,"de_DE") as d_total_advance, ter.name as report_for, DATE_FORMAT(st.submitted_at, "%d-%M-%y") as submitted_at,
        ur.signature as requestor_signature ,r.originating_city,
        uh.username as head_of_units_name, uh.signature as head_of_units_signature, uh.email as head_of_units_email, st.head_of_units_status, uh.id as head_of_units_id,
        ufc.username as country_director_name, ufc.signature as country_director_signature, ufc.email as country_director_email, DATE_FORMAT(st.submitted_at, "%d-%M-%y") as submitted_at,
        ufc.purpose as country_director_purpose, ufc.signature as country_director_signature,
        ufi.username as finance_name, ufi.email as finance_email, DATE_FORMAT(st.head_of_units_status_at, "%d %M %Y - %H:%i") as head_of_units_status_at,
        DATE_FORMAT(st.country_director_status_at, "%d %M %Y - %H:%i") as country_director_status_at, st.country_director_status, st.finance_status,
        DATE_FORMAT(st.finance_status_at, "%d %M %Y - %H:%i") as finance_status_at, st.payment_type, format(st.total_payment,2,"de_DE") as total_payment,
        ')
        ->from('ea_requests r')
        ->join('ea_report_status st', 'st.request_id = r.id', 'left')
        ->join('tb_userapp ur', 'r.requestor_id = ur.id', 'left')
        ->join('tb_userapp uh', 'st.head_of_units_id = uh.id', 'left')
        ->join('tb_userapp ufc', 'st.country_director_id = ufc.id', 'left')
        ->join('tb_userapp ufi', 'st.finance_id = ufi.id', 'left')
        ->join('ea_ter ter', 'ter.request_id = r.id')
        ->where('r.id', $request_id)
        ->where('ter.id', $ter_id)
        ->get()->row_array();
        if(!$request_data) {
            return false;
        }
        $destinations = $this->db->select('*, format(meals,2,"de_DE") as d_meals, format(lodging,2,"de_DE") as d_lodging,
        format(total_lodging_and_meals,2,"de_DE") as d_total_lodging_and_meals, format(total,2,"de_DE") as d_total,
        DATE_FORMAT(departure_date, "%d %M %Y") as depar_date, DATE_FORMAT(arrival_date, "%d %M %Y") as arriv_date,
        format(actual_meals,2,"de_DE") as d_actual_meals, format(actual_lodging,2,"de_DE") as d_actual_lodging,
        DATE_FORMAT(arrival_date, "%Y/%m/%d") as d_arriv_date,
        ')
        ->from('ea_requests_destinations')
        ->where('request_id', $request_id)
        ->get()->result_array();
        $total_destinations_cost = 0;
        $total_expense = 0;
        $total_dest = count($destinations);
        for ($i = 0; $i < $total_dest; $i++) {
            $total_destinations_cost += $destinations[$i]['total'];
            $max_meals_lodging = $this->get_dest_max_budget($destinations[$i]['id']);
            $destinations[$i]['max_lodging_cost'] = number_format($max_meals_lodging['max_lodging_budget'],2,',','.');
            $destinations[$i]['max_meals_cost'] =  number_format($max_meals_lodging['max_meals_budget'],2,',','.');
            $night = $destinations[$i]['night'];
            $lodging_arr = [];
            $meals_arr = [];
            $meals_text_arr = [];
            $other_items_arr = [];
            $other_items_by_name_arr = [];
            $total_costs_arr = [];
            for ($x = 0; $x < $night; $x++) {
                $total_cost_per_night = 0;
                $lodging = $this->get_actual_costs_by_night_and_ter($ter_id, $destinations[$i]['id'], 1, $x + 1);
                if($lodging) {
                    $total_cost_per_night += $lodging['cost'];
                    array_push($lodging_arr, $lodging);
                } else {
                    array_push($lodging_arr, [
                        'item_id' => null,
                        'item_name' => null,
                        'cost' => null,
                        'd_cost' => null,
                        'receipt' => null,
                    ]);
                }
                $meals = $this->get_actual_costs_by_night_and_ter($ter_id, $destinations[$i]['id'], 2, $x + 1);
                if($meals) {
                    array_push($meals_arr, $meals);
                } else {
                    array_push($meals_arr, [
                        'item_id' => null,
                        'item_name' => null,
                        'cost' => null,
                        'd_cost' => null,
                        'receipt' => null,
                    ]);
                }
                $other_items = $this->get_other_items_by_night_and_ter($ter_id, $destinations[$i]['id'], $x + 1);
                if($other_items) {
                    array_push($other_items_arr, $other_items);
                } else {
                    array_push($other_items_arr, []);
                }
                $other_items_by_name = $this->get_other_items_group_by_name_and_ter($ter_id, $destinations[$i]['id'], $x + 1);
                if($other_items_by_name) {
                    for($z = 0; $z<count($other_items_by_name); $z++) {
                        $item_cost = 0;
                        $items_by_name = $this->get_other_items_by_name_and_ter($ter_id, $destinations[$i]['id'], $other_items_by_name[$z]['item_name'], $x + 1);
                        foreach($items_by_name as $i_name) {
                            $item_cost += $i_name['cost'];
                        }
                        $other_items_by_name[$z]['total_cost'] = $item_cost;
                        $total_cost_per_night += $item_cost;
                    }
                    array_push($other_items_by_name_arr, $other_items_by_name);
                } else {
                    array_push($other_items_by_name_arr, []);
                }
                $provided_meals =  $this->get_provided_meals_by_ter($ter_id, $destinations[$i]['id'],  $x + 1);
                if($provided_meals) {
                    array_push($meals_text_arr, [
                        'id' =>  $provided_meals['id'],
                        'meals_text' => $provided_meals['meals_text'],
                        'is_first_day' => $provided_meals['is_first_day'],
                        'is_last_day' => $provided_meals['is_last_day'],
                        'cost' => $provided_meals['cost'],
                        'd_cost' => $provided_meals['d_cost'],
                    ]);
                    $total_cost_per_night += $provided_meals['cost'];
                } else {
                    array_push($meals_text_arr, [
                        'id' => null,
                        'meals_text' => '',
                        'is_first_day' => 0,
                        'is_last_day' => 0,
                        'cost' => null,
                        'd_cost' => null,
                    ]);
                }
                array_push($total_costs_arr, $total_cost_per_night);
            }
            $destinations[$i]['actual_lodging_items'] = $lodging_arr;
            $destinations[$i]['actual_meals_items'] = $meals_arr;
            $destinations[$i]['other_items'] = $other_items_arr;
            $destinations[$i]['other_items_by_name'] = $other_items_by_name_arr;
            $destinations[$i]['provided_meals'] = $meals_text_arr;
            $destinations[$i]['total_costs_per_night'] = $total_costs_arr;
            for($g=0;$g < count($total_costs_arr); $g++) {
                $total_expense += $total_costs_arr[$g];
            }
        }
        $request_data['total_destinations_cost'] = $total_destinations_cost;
        $request_data['destinations'] = $destinations;
        $request_data['total_expense'] = $total_expense;
        return $request_data;
    }

    function get_actual_costs_by_night_and_ter($ter_id, $dest_id, $item_type, $night) {
        $data = $this->db->select('id, dest_id, is_approved_by_finance ,receipt, item_type, cost ,format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('ter_id', $ter_id)
        ->where('dest_id', $dest_id)
        ->where('item_type', $item_type)
        ->where('night', $night)
        ->get()->row_array();
        return $data;
    }

    function get_other_items_by_night_and_ter($ter_id, $dest_id, $night) {
        $data = $this->db->select('id, dest_id, night, receipt, item_type, item_name, cost , format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('ter_id', $ter_id)
        ->where('dest_id', $dest_id)
        ->where('item_type', 3)
        ->where('night', $night)
        ->where('item_name !=', 'List meals')
        ->order_by('item_name', 'asc')
        ->get()->result_array();
        return $data;
    }

    function get_other_items_group_by_name_and_ter($ter_id, $dest_id, $night) {
        $data = $this->db->select('id, dest_id, night, receipt, item_type, item_name, cost , format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('ter_id', $ter_id)
        ->where('dest_id', $dest_id)
        ->where('item_type', 3)
        ->where('night', $night)
        ->where('item_name !=', 'List meals')
        ->order_by('item_name', 'asc')
        ->group_by('item_name')
        ->get()->result_array();
        return $data;
    }

    function get_other_items_by_name_and_ter($ter_id, $dest_id, $item_name, $night) {
        $data = $this->db->select('id, dest_id, night, is_approved_by_finance ,receipt, item_type, item_name, meals_text, cost ,format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('ter_id', $ter_id)
        ->where('dest_id', $dest_id)
        ->where('item_type', 3)
        ->where('night', $night)
        ->where('item_name', $item_name)
        ->get()->result_array();
        return $data;
    }

    function get_provided_meals_by_ter($ter_id, $dest_id, $night) {
        $data = $this->db->select('id, dest_id, night, is_last_day, is_first_day, is_approved_by_finance ,receipt, item_type, item_name, meals_text, cost ,format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('ter_id', $ter_id)
        ->where('dest_id', $dest_id)
        ->where('item_type', 3)
        ->where('night', $night)
        ->where('item_name', 'List meals')
        ->get()->row_array();
        return $data;
    }

    function get_excel_other_items_by_night_and_ter($ter_id, $dest_id, $night) {
        $data = $this->db->select('id, dest_id, night, receipt, item_type, item_name, meals_text, cost ,format(cost,2,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('ter_id', $ter_id)
        ->where('dest_id', $dest_id)
        ->where('item_type', 3)
        ->where('night', $night)
        ->order_by('item_name', 'asc')
        ->get()->result_array();
        return $data;
    }

    function get_total_excel_report_by_id($id) {
        $request_data =  $this->db->select('r.id as r_id, CONCAT("EA", r.id) AS ea_number, DATE_FORMAT(r.created_at, "%d %F %Y") as request_date,
        DATE_FORMAT(r.departure_date, "%d/%M/%y") as departure_date, DATE_FORMAT(r.return_date, "%d/%M/%y") as return_date,
        r.requestor_id, ur.username as requestor_name, ur.signature as requestor_signature ,r.originating_city,
        uh.username as head_of_units_name, uh.signature as head_of_units_signature, uh.email as head_of_units_email, st.head_of_units_status, uh.id as head_of_units_id,
        ufc.username as country_director_name, ufc.signature as country_director_signature, ufc.email as country_director_email, DATE_FORMAT(st.submitted_at, "%d-%M-%y") as submitted_at,
        ufc.purpose as country_director_purpose, ufc.signature as country_director_signature,
        ufi.username as finance_name, ufi.email as finance_email, DATE_FORMAT(st.head_of_units_status_at, "%d %M %Y - %H:%i") as head_of_units_status_at,
        DATE_FORMAT(st.country_director_status_at, "%d %M %Y - %H:%i") as country_director_status_at, st.country_director_status, st.finance_status,
        DATE_FORMAT(st.finance_status_at, "%d %M %Y - %H:%i") as finance_status_at, st.payment_type, format(st.total_payment,2,"de_DE") as total_payment,
        format(r.total_advance,2,"de_DE") as d_total_advance
        ')
            ->from('ea_requests r')
            ->join('ea_report_status st', 'st.request_id = r.id', 'left')
            ->join('tb_userapp ur', 'r.requestor_id = ur.id', 'left')
            ->join('tb_userapp uh', 'st.head_of_units_id = uh.id', 'left')
            ->join('tb_userapp ufc', 'st.country_director_id = ufc.id', 'left')
            ->join('tb_userapp ufi', 'st.finance_id = ufi.id', 'left')
            ->where('r.id', $id)
            ->get()->row_array();
        if(!$request_data) {
            return false;
        }
        $destinations = $this->db->select('id, total, city, night, actual_lodging,
        actual_meals, departure_date, arrival_date, project_number,
        DATE_FORMAT(first_depar_time, "%H:%i") as first_depar_time, DATE_FORMAT(first_arriv_time, "%H:%i") as first_arriv_time,
        second_city, DATE_FORMAT(second_depar_time, "%H:%i") as second_depar_time, DATE_FORMAT(second_arriv_time, "%H:%i") as second_arriv_time,
        DATE_FORMAT(departure_date, "%d/%M/%y") as depar_date, DATE_FORMAT(arrival_date, "%d/%M/%y") as arriv_date
        ')
        ->from('ea_requests_destinations')
        ->where('request_id', $id)
        ->get()->result_array();
        $total_destinations_cost = 0;
        $total_dest = count($destinations);
        for ($i = 0; $i < $total_dest; $i++) {
            $total_destinations_cost += $destinations[$i]['total'];
            $night = $destinations[$i]['night'];
            $other_items_arr = [];
            $lodging_arr = [];
            for ($x = 0; $x < $night; $x++) {
                $other_items = $this->get_total_excel_other_items_by_night($id, $destinations[$i]['id'], $x + 1);
                if($other_items) {
                    array_push($other_items_arr, $other_items);
                }
                $actual_lodging = $this->get_total_actual_costs_per_night($id, $destinations[$i]['id'], 1, $x + 1);
                array_push($lodging_arr, $actual_lodging);
            }
            $max_meals_lodging = $this->get_dest_max_budget($destinations[$i]['id']);
            $destinations[$i]['max_meals_cost'] =  $max_meals_lodging['max_meals_budget'];
            $destinations[$i]['actual_lodging'] = $lodging_arr;
            $destinations[$i]['other_items'] = $other_items_arr;
          }
        $request_data['total_destinations_cost'] = $total_destinations_cost;
        $request_data['destinations'] = $destinations;
        return $request_data;
    }

    function get_total_actual_costs_per_night($request_id, $dest_id, $item_type, $night) {
        $items = $this->db->select('id, dest_id, night,receipt, item_type, cost ,format(cost,0,"de_DE") as d_cost')
        ->from('ea_actual_costs')
        ->where('request_id', $request_id)
        ->where('dest_id', $dest_id)
        ->where('night', $night)
        ->where('item_type', $item_type)
        ->order_by('night', 'asc')
        ->get()->result_array();
        $total_cost = 0;
        for($x=0; $x < count($items); $x++) {
            $total_cost += $items[$x]['cost'];
            $items[$x]['total_all_cost'] = $total_cost; 
        }
        $total = [
            'cost' => $total_cost,
        ];
        return $total;
    }

    function get_total_excel_other_items_by_night($request_id, $dest_id, $night) {
        $items = $this->db->select('id, dest_id, night, receipt, item_type, item_name, meals_text, cost')
        ->from('ea_actual_costs')
        ->where('request_id', $request_id)
        ->where('dest_id', $dest_id)
        ->where('item_type', 3)
        ->where('night', $night)
        ->group_by('item_name')
        ->get()->result_array();
        for($x=0; $x < count($items); $x++) {
            $total_cost = 0;
            $item = $this->db->select('cost')->from('ea_actual_costs')
            ->where('request_id', $request_id)
            ->where('dest_id', $dest_id)
            ->where('night', $night)
            ->where('item_name', $items[$x]['item_name'])
            ->get()->result_array();
            foreach($item as $i) {
                $total_cost += $i['cost'];
            }
            $items[$x]['cost'] = $total_cost; 
            $items[$x]['meals_text'] = '-'; 
        }
        return $items;
    }
}
