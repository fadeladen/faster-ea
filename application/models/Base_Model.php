<?php


class Base_Model extends CI_Model
{

    function get_head_of_units($user_id) {
        return $this->db->select('u.id, u.username, u.email')
        ->from('tb_pr_approver pr')
        ->join('tb_userapp u', 'u.id = pr.kepala_unit_id')
        ->join('tb_pr_approver_members pm', 'pm.pr_approver_id = pr.id')
        ->where('pm.member_id', $user_id)
        ->get()->result_array();
    }

    function get_line_supervisor($user_id) {
        return $this->db->select('u.id, u.username, u.email')
        ->from('tb_pr_approver pr')
        ->join('tb_pr_approver_supervisor prs', 'pr.id = prs.pr_approver_id')
        ->join('tb_userapp u', 'u.id = prs.supervisor_id')
        ->where('pr.kepala_unit_id', $user_id)
        ->get()->result_array();
    }

    function get_ea_assosiate() {
        return $this->db->select('id, username, email')
        ->from('tb_userapp')
        ->where('account_name', 'mlisna@fhi360.org')
        ->get()->row_array();
    }

    function get_country_director() {
        return $this->db->select('id, username, email')
        ->from('tb_userapp')
        ->where('account_name', 'eaditya@fhi360.org')
        ->get()->row_array();
    }

    function get_fco_monitor() {
        return $this->db->select('id, username, email')
        ->from('tb_userapp')
        ->where('is_budget_reviewer', 1)
        ->get()->row_array();
    }

    function get_finance_teams() {
        return $this->db->select('u.id, u.username, u.email, un.unit_name')
        ->from('tb_userapp u')
        ->join('tb_units un', 'u.unit_id = un.id')
        ->where('un.unit_name', 'Finance')
        ->get()->result_array();
    }

    function get_cities() {
        return $this->db->select('*')
        ->from('tb_wilayah_kabupaten')
        ->order_by('nama', 'asc')
        ->get()->result_array();
    }
}
