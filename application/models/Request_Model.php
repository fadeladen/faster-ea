<?php


class Request_Model extends CI_Model
{

    protected $fillable_columns = [
        "request_base",
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
        "max_budget",
        "requestor_id",
        "requestor_name",
        "requestor_email",
    ];

    function get_request_by_id($id) {
        $request_data =  $this->db->select('r.id as r_id, 
        DATE_FORMAT(r.departure_date, "%d %M %Y") as d_date, DATE_FORMAT(r.return_date, "%d %M %Y") as r_date,
        r.*, st.*')
            ->from('ea_requests r')
            ->join('ea_requests_status st', 'st.request_id = r.id', 'left')
            ->where('r.id', $id)
            ->get()->row_array();
        $destinations = $this->db->select('*, format(meals,2,"de_DE") as d_meals, format(lodging,2,"de_DE") as d_lodging,
        format(total_lodging_and_meals,2,"de_DE") as d_total_lodging_and_meals, format(total,2,"de_DE") as d_total,
        DATE_FORMAT(departure_date, "%d %M %Y") as depar_date, DATE_FORMAT(arrival_date, "%d %M %Y") as arriv_date
        ')
        ->from('ea_requests_destinations')
        ->where('request_id', $id)
        ->get()->result_array();
        $participants = $this->db->select('*')
        ->from('ea_requests_participants')
        ->where('request_id', $id)
        ->get()->result_array();
        $total_destinations_cost = 0;
        foreach($destinations as $dest) {
            $total_destinations_cost += $dest['total'];
        }
        $request_data['total_destinations_cost'] = $total_destinations_cost;
        $request_data['destinations'] = $destinations;
        $request_data['participants'] = $participants;
        return $request_data;
    }

    function insert_request($data)
    {   
        $data['departure_date'] = date('Y-m-d', strtotime($data['departure_date']));
		$data['return_date'] = date('Y-m-d', strtotime($data['return_date']));
        $data['hotel_check_in'] = ($data['hotel_check_in'] == '' ? null : date('Y-m-d', strtotime($data['hotel_check_in'])) );
        $data['hotel_check_out'] = ($data['hotel_check_out'] == '' ? null : date('Y-m-d', strtotime($data['hotel_check_out'])) );

        $employment = $data['employment'];
        
        if($employment == 'On behalf') {
            $employment_status = $data['employment_status'];
            $participants_name = $data['participant_name'];
            if($employment_status || $employment_status == 'Consultant' || $employment_status == 'Other') {
                $data['number_of_participants'] = count($participants_name);
            }
        }

        $request_data = array_intersect_key($data, array_flip($this->fillable_columns));
        $this->db->trans_start();

        $this->db->insert('ea_requests', $request_data);
        $request_id =  $this->db->insert_id();

        // Request status
        $this->db->insert('ea_requests_status', [
            'request_id' => $request_id,
            'head_of_units_id' => 9999,
            'head_of_units_email' => $data['head_of_units_email'],
        ]);

        // Save destinations
        $destinations_city = $data['destination_city'];
        for ($i = 0; $i < count($destinations_city); $i++) {
            $this->db->insert('ea_requests_destinations', [
                'request_id' => $request_id,
                'order' => $data['destination_order'][$i],
                'city' => $data['destination_city'][$i],
                'departure_date' => date('Y-m-d', strtotime($data['destination_departure_date'][$i])),
                'arrival_date' => date('Y-m-d', strtotime($data['destination_arrival_date'][$i])),
                'project_number' => $data['project_number'][$i],
                'budget_monitor' => $data['destination_budget_monitor'][$i],
                'lodging' => $data['lodging'][$i],
                'meals' => $data['meals'][$i],
                'total_lodging_and_meals' => $data['meals_lodging_total'][$i],
                'night' => $data['night'][$i],
                'total' => $data['total'][$i],
            ]);
        }

        // Save participants
        if($employment == 'On behalf') {
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
        return $this->db->trans_status();
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
}