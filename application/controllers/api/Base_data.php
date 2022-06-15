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

    function get_project_number_by_tor($tor_number) {

        $data = $this->db->select('m.tor_number, d.activity, d.id_project, p.gfas as project_number')
		->from('tb_mini_proposal_new m')
		->join('tb_detail_monthly d', 'm.code_activity = d.kode_kegiatan')
		->join('tb_project p', 'd.id_project = p.project_id')
        ->where('m.tor_number', $tor_number)
		->get()->row_array();
        $response['data'] = $data;
        $this->send_json($response);
    }
}