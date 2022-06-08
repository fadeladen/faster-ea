<?php 


class Base_data extends MY_Controller {

    function project_number () {

        $this->db
        ->from('tb_project');

        if ($this->input->get('select2')) {
            $this->db->select('gfas as id, gfas as text');
        }
        if ($this->input->get('q')) {
            $this->db->like('gfas', $this->input->get('q'));
        }

        $response['result'] = $this->db->get()->result();
        $this->send_json($response);
    }
}