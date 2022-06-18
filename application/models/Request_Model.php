<?php


class Request_Model extends CI_Model
{

    protected $fillable_columns = [
        "request_base",
        "ea_online_number",
        "tor_number",
        "employment",
        "employment_status",
        "participant_group_name",
        "participant_group_email",
        "participant_group_contact_person",
        "number_of_participants",
        "originating_city",
        "departure_date",
        "return_date",
        "country_director_notified",
        "travel_advance",
        "need_documents",
        "document_description",
        "car_rental",
        "hotel_reservations",
        "hotel_check_in",
        "hotel_check_out",
        "preferred_hotel",
        "other_transportation",
        "special_instructions",
        "max_budget_idr",
        "max_budget_usd",
        "requestor_id",
        "exteral_invitation_file",
        "car_rental_memo",
        "purpose",
        "proof_of_approval",
        "detail_address",
    ];

    function get_request_by_id($id) {
        $request_data =  $this->db->select('r.id as r_id, CONCAT("EA", r.id) AS ea_number, DATE_FORMAT(r.created_at, "%d %M %Y - %H:%i") as request_date,
        DATE_FORMAT(r.departure_date, "%d %M %Y") as d_date, DATE_FORMAT(r.return_date, "%d %M %Y") as r_date,
        r.*, st.*, uh.username as head_of_units_name, uh.email as head_of_units_email, uea.username as ea_assosiate_name,
        uea.email as ea_assosiate_email, ufc.username as fco_monitor_name, ufc.email as fco_monitor_email, 
        ufc.purpose as fco_monitor_purpose, ufc.signature as fco_monitor_signature,
        ufi.username as finance_name, ufi.email as finance_email, DATE_FORMAT(st.head_of_units_status_at, "%d %M %Y - %H:%i") as head_of_units_status_at,
        DATE_FORMAT(st.ea_assosiate_status_at, "%d %M %Y - %H:%i") as ea_assosiate_status_at,
        DATE_FORMAT(st.fco_monitor_status_at, "%d %M %Y - %H:%i") as fco_monitor_status_at, st.fco_monitor_status_at as fco_signature_date,
        DATE_FORMAT(st.finance_status_at, "%d %M %Y - %H:%i") as finance_status_at, st.payment_receipt, format(r.total_advance,2,"de_DE") as d_total_advance,
        (
            CASE 
                WHEN head_of_units_status = "1" THEN "Pending"
                WHEN head_of_units_status = "2" THEN "Approved"
                WHEN head_of_units_status = "3" THEN "Rejected"
            END) AS head_of_units_status_text,
        (
            CASE 
                WHEN ea_assosiate_status = "1" THEN "Pending"
                WHEN ea_assosiate_status = "2" THEN "Approved"
                WHEN ea_assosiate_status = "3" THEN "Rejected"
            END) AS ea_assosiate_status_text,
        (
            CASE 
                WHEN fco_monitor_status = "1" THEN "Pending"
                WHEN fco_monitor_status = "2" THEN "Approved"
                WHEN fco_monitor_status = "3" THEN "Rejected"
            END) AS fco_monitor_status_text,
        (
            CASE 
                WHEN finance_status = "1" THEN "Pending"
                WHEN finance_status = "2" THEN "Paid"
                WHEN finance_status = "3" THEN "Rejected"
            END) AS finance_status_text,
        ')
            ->from('ea_requests r')
            ->join('ea_requests_status st', 'st.request_id = r.id', 'left')
            ->join('tb_userapp uh', 'st.head_of_units_id = uh.id', 'left')
            ->join('tb_userapp uea', 'st.ea_assosiate_id = uea.id', 'left')
            ->join('tb_userapp ufc', 'st.fco_monitor_id = ufc.id', 'left')
            ->join('tb_userapp ufi', 'st.finance_id = ufi.id', 'left')
            ->where('r.id', $id)
            ->get()->row_array();
        if(!$request_data) {
            return false;
        }
        $destinations = $this->db->select('*, format(meals,2,"de_DE") as d_meals, format(lodging,2,"de_DE") as d_lodging,
        format(lodging_usd,0,"de_DE") as d_lodging_usd, format(meals_usd,0,"de_DE") as d_meals_usd,
        format(total_lodging_and_meals_usd,0,"de_DE") as d_total_lodging_and_meals_usd, format(total_usd,0,"de_DE") as d_total_usd,
        format(total_lodging_and_meals,2,"de_DE") as d_total_lodging_and_meals, format(total,2,"de_DE") as d_total,
        DATE_FORMAT(departure_date, "%d %M %Y") as depar_date, DATE_FORMAT(arrival_date, "%d %M %Y") as arriv_date,
        format(actual_meals,2,"de_DE") as d_actual_meals, format(actual_lodging,2,"de_DE") as d_actual_lodging,
        format(max_lodging_budget,2,"de_DE") as d_max_lodging_budget, format(max_meals_budget,2,"de_DE") as d_max_meals_budget,
        format(max_lodging_budget_usd,0,"de_DE") as d_max_lodging_budget_usd, format(max_meals_budget_usd,0,"de_DE") as d_max_meals_budget_usd
        ')
        ->from('ea_requests_destinations')
        ->where('request_id', $id)
        ->get()->result_array();
        $participants = $this->db->select('*')
        ->from('ea_requests_participants')
        ->where('request_id', $id)
        ->get()->result_array();

        $total_destinations_cost = 0;
        $total_destinations_cost_usd = 0;
        foreach($destinations as $dest) {
            $total_destinations_cost += $dest['total'];
            $total_destinations_cost_usd += $dest['total_usd'];
            $other_items = $this->get_destination_other_items($dest['id']);
            $dest['other_items'] = $other_items;
        }
        $request_data['total_destinations_cost'] = $total_destinations_cost;
        $request_data['total_destinations_cost_usd'] = $total_destinations_cost_usd;
        $request_data['destinations'] = $destinations;
        $request_data['participants'] = $participants;
        return $request_data;
    }

    function get_requestor_data($user_id) {
        return $this->db->select('u.id, u.username, u.email, u.employee_id, p.project_name, un.unit_name, u.purpose, u.signature')
        ->from('tb_userapp u')
        ->join('tb_project p', 'u.project_id = p.project_id')
        ->join('tb_units un', 'u.unit_id = un.id')
        ->where('u.id', $user_id)
        ->get()->row_array();
    }

    function insert_request($data)
    {   
        $data['departure_date'] = date('Y-m-d', strtotime($data['departure_date']));
		$data['return_date'] = date('Y-m-d', strtotime($data['return_date']));
        $data['hotel_check_in'] = ($data['hotel_check_in'] == '' ? null : date('Y-m-d', strtotime($data['hotel_check_in'])) );
        $data['hotel_check_out'] = ($data['hotel_check_out'] == '' ? null : date('Y-m-d', strtotime($data['hotel_check_out'])) );

        $employment = $data['employment'];
        
        if($employment == 'On behalf' || $employment == 'For me and on behalf') {
            $employment_status = $data['employment_status'];
            $participants_name = $data['participant_name'];
            if($employment_status || $employment_status == 'Consultant' || $employment_status == 'Other') {
                $data['number_of_participants'] = count($participants_name);
                if($employment == 'For me and on behalf') {
                    $data['number_of_participants'] += 1;
                }
            }
        }

        $request_data = array_intersect_key($data, array_flip($this->fillable_columns));
        $this->db->trans_start();

        $this->db->insert('ea_requests', $request_data);
        $request_id =  $this->db->insert_id();

        // Request status
        $this->db->insert('ea_requests_status', [
            'request_id' => $request_id,
            'head_of_units_id' => $data['head_of_units_id'],
        ]);

        // Save destinations
        $destinations = $data['destination_country'];
        for ($i = 0; $i < count($destinations); $i++) {
            // Remove number formatting
            $clean_lodging = str_replace('.', '',  $data['lodging'][$i]);
            $clean_meals = str_replace('.', '',  $data['meals'][$i]);
            $clean_meals_lodging_total = str_replace('.', '',  $data['meals_lodging_total'][$i]);
            $clean_total = str_replace('.', '',  $data['total'][$i]);
            // USD
            $clean_lodging_usd = str_replace('.', '',  $data['lodging_usd'][$i]);
            $clean_meals_usd = str_replace('.', '',  $data['meals_usd'][$i]);
            $clean_meals_lodging_total_usd = str_replace('.', '',  $data['meals_lodging_total_usd'][$i]);
            $clean_total_usd = str_replace('.', '',  $data['total_usd'][$i]);
            $country = $data['destination_country'][$i];
            $city = $data['destination_city'][$i];
            if($country == 2) {
                $city = $data['destination_country_name'][$i];
            }
            $this->db->insert('ea_requests_destinations', [
                'request_id' => $request_id,
                'order' => $data['destination_order'][$i],
                'country' => $country,
                'city' => $city,
                'departure_date' => date('Y-m-d', strtotime($data['destination_departure_date'][$i])),
                'arrival_date' => date('Y-m-d', strtotime($data['destination_arrival_date'][$i])),
                'project_number' => $data['project_number'][$i],
                'budget_monitor' => $data['destination_budget_monitor'][$i],
                'lodging' => $clean_lodging,
                'lodging_usd' => $clean_lodging_usd,
                'meals' => $clean_meals,
                'meals_usd' => $clean_meals_usd,
                'total_lodging_and_meals' => $clean_meals_lodging_total,
                'total_lodging_and_meals_usd' => $clean_meals_lodging_total_usd,
                'night' => $data['night'][$i],
                'total' => $clean_total,
                'total_usd' => $clean_total_usd,
                'konversi_usd' => 14500,
            ]);
        }

        // Save participants
        if($employment == 'On behalf' || $employment == 'For me and on behalf') {
            if($employment_status == 'Consultant' || $employment_status == 'Other') {
                $participants_email = $data['participant_email'];
                $participants_title = $data['participant_title'];
                for ($i = 0; $i < count($participants_name); $i++) {
                    $this->db->insert('ea_requests_participants', [
                        'request_id' => $request_id,
                        'name' => $participants_name[$i],
                        'email' => $participants_email[$i],
                        'title' => $participants_title[$i],
                    ]);
                }
            }
        }

        $this->db->trans_complete();
        if($this->db->trans_status()) {
            return $request_id;
        }
        return false;
    }

    function update_request($request_id, $data)
    {
        $update_data = array_intersect_key($data, array_flip($this->fillable_columns));
        $this->db->where('id', $request_id)->update('ea_requests', $update_data);
        return $this->db->affected_rows() === 1;
    }

    function delete_request($request_id)
    {
        $this->db->where('id', $request_id)->delete('ea_requests');
        return $this->db->affected_rows() === 1;
    }

    function update_status($request_id, $approver_id, $status, $level, $rejected_reason = null) {
        $this->db->where('request_id', $request_id)->update('ea_requests_status', [
            $level . '_status' => $status,
            $level . '_status_at' => date("Y-m-d H:i:s"),
            $level . '_id' => $approver_id,
            'rejected_reason' => $rejected_reason,
        ]);
        return $this->db->affected_rows() === 1;
    }

    function resubmit_request($request_id, $level) {
        $this->db->where('request_id', $request_id)->update('ea_requests_status', [
            $level . '_status' => 1,
            $level . '_status_at' => date("Y-m-d H:i:s"),
        ]);
        return $this->db->affected_rows() === 1;
    }

    function set_total_advance($req_id) {
        $destinations = $this->db->select('total')
        ->from('ea_requests_destinations')
        ->where('request_id', $req_id)
        ->get()->result_array();
        $total_destination_cost = 0;
        foreach($destinations as $dest) {
            $total_destination_cost += $dest['total'];
        }
        $total_advance = $total_destination_cost + 1000000;
        $request = $this->db->select('travel_advance')->from('ea_requests')->where('id', $req_id)->get()->row_array();
        if($request['travel_advance'] == 'Yes') {
            $total_advance = $total_advance * 0.8;
        }
		$this->db->where('id', $req_id)->update('ea_requests', ['total_advance' => $total_advance]);
        return true;
    }

    function update_payment_status($request_id, $payload) {
        $this->db->where('request_id', $request_id)->update('ea_requests_status', $payload);
        return $this->db->affected_rows() === 1;
    }

    function get_excel_data_by_id($id) {
        $request_data =  $this->db->select('r.id as r_id, CONCAT("EA", r.id) AS ea_number, r.*, DATE_FORMAT(r.created_at, "%d-%M-%y") as request_date,
        DATE_FORMAT(r.departure_date, "%d-%M-%y") as departure_date, DATE_FORMAT(r.return_date, "%d-%M-%y") as return_date,
        r.requestor_id, st.*, r.country_director_notified, format(r.max_budget_usd,0,"de_DE") as max_budget_usd,r.originating_city,
        uh.username as head_of_units_name, uh.signature as head_of_units_signature, uea.username as ea_assosiate_name, ufc.username as fco_monitor_name,
        ufc.purpose as fco_monitor_purpose, ufc.signature as fco_monitor_signature, uea.signature as ea_assosiate_signature,
        ufi.username as finance_name, DATE_FORMAT(st.head_of_units_status_at, "%d %M %Y - %H:%i") as head_of_units_status_at,
        DATE_FORMAT(st.ea_assosiate_status_at, "%d %M %Y - %H:%i") as ea_assosiate_status_at,
        DATE_FORMAT(st.fco_monitor_status_at, "%d %M %Y - %H:%i") as fco_monitor_status_at, st.fco_monitor_status_at as fco_signature_date,
        DATE_FORMAT(st.finance_status_at, "%d %M %Y - %H:%i") as finance_status_at, ufi.signature as finance_signature
        ')
            ->from('ea_requests r')
            ->join('ea_requests_status st', 'st.request_id = r.id', 'left')
            ->join('tb_userapp uh', 'st.head_of_units_id = uh.id', 'left')
            ->join('tb_userapp uea', 'st.ea_assosiate_id = uea.id', 'left')
            ->join('tb_userapp ufc', 'st.fco_monitor_id = ufc.id', 'left')
            ->join('tb_userapp ufi', 'st.finance_id = ufi.id', 'left')
            ->where('r.id', $id)
            ->get()->row_array();
        if(!$request_data) {
            return false;
        }
        $destinations = $this->db->select('*, DATE_FORMAT(departure_date, "%d-%M-%y") as depar_date, DATE_FORMAT(arrival_date, "%d-%M-%y") as arriv_date
        ')
        ->from('ea_requests_destinations')
        ->where('request_id', $id)
        ->get()->result_array();
        $participants = $this->db->select('*')
        ->from('ea_requests_participants')
        ->where('request_id', $id)
        ->get()->result_array();
        $total_destinations_cost = 0;
        $total_destinations_cost_usd = 0;
        foreach($destinations as $dest) {
            $total_destinations_cost += $dest['total'];
            $total_destinations_cost_usd += $dest['total_usd'];
        }
        $request_data['total_destinations_cost'] = $total_destinations_cost;
        $request_data['total_destinations_cost_usd'] = $total_destinations_cost_usd;
        $request_data['destinations'] = $destinations;
        $request_data['participants'] = $participants;
        return $request_data;
    }

    function get_destination_other_items($dest_id) {
        $other_items = $this->db->select('*, format(cost,2,"de_DE") as cost')
        ->from('ea_requests_other_items')
        ->where('destination_id', $dest_id)
        ->get()->result_array();
        return $other_items;
    }

    function update_costs($dest_id, $payload) {
        $range = strtotime($payload['departure_date']) - strtotime($payload['arrival_date']);
        $night = round($range / (60 * 60 * 24));
        if($night == 0) {
            $night = 1;
        }
        $meals = $payload['meals'];
        $lodging = $payload['lodging'];
        $total_lodging_and_meals = $meals + $lodging;
        $total = $total_lodging_and_meals * $night;
        //USD
        $meals_usd = $payload['meals_usd'];
        $lodging_usd = $payload['lodging_usd'];
        $total_lodging_and_meals_usd = $meals_usd + $lodging_usd;
        $total_usd = $total_lodging_and_meals_usd * $night;
        $data = [
            'arrival_date' => date('Y-m-d', strtotime($payload['arrival_date'])),
            'departure_date' => date('Y-m-d', strtotime($payload['departure_date'])),
            'meals' => $meals,
            'lodging' => $lodging,
            'night' => $night,
            'total_lodging_and_meals' => $total_lodging_and_meals,
            'meals_usd' => $meals_usd,
            'lodging_usd' => $lodging_usd,
            'total_lodging_and_meals_usd' => $total_lodging_and_meals_usd,
            'total' => $total,
            'total_usd' => $total_usd,
        ];
        $updated = $this->db->where('id', $dest_id)->update('ea_requests_destinations', $data);
        if($updated) {
            return true;
        }
        return false;
    }

    function update_max_budget($dest_id, $payload) {
        $max_lodging_budget = $payload['lodging'];
        $max_meals_budget = $payload['meals'];
        $konversi_usd = $payload['konversi_usd'];
        $lodging_usd = $payload['lodging_usd'];
        $meals_usd = $payload['meals_usd'];
        $data = [
            'max_lodging_budget' => $max_lodging_budget,
            'max_meals_budget' => $max_meals_budget,
            'max_lodging_budget_usd' => $lodging_usd,
            'max_meals_budget_usd' => $meals_usd,
            'konversi_usd' => $konversi_usd,
            'is_edited_by_ea' => 1,
        ];
        $updated = $this->db->where('id', $dest_id)->update('ea_requests_destinations', $data);
        if($updated) {
            return true;
        }
        return false;
    }
}
