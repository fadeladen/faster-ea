<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Spipu\Html2Pdf\Html2Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Report extends MY_Controller {


    function __construct()
	{
		parent::__construct();
		$this->load->model('Request_Model', 'request');
		$this->load->model('Report_Model', 'report');
		$this->load->model('Base_Model', 'base_model');
		$this->template->set('pageParent', 'Report');
		$this->template->set_default_layout('layouts/default');
	}

	public function index()
	{   
        $this->template->set('page', 'Request report');
        $data['status'] = 'done';
		$this->template->render('report/index', $data);
	}

    public function reporting($id = null)
	{
		$id = decrypt($id);
		$detail = $this->report->get_report_data_by_id($id);
		if($detail) {
			$requestor_data = $this->request->get_requestor_data($detail['requestor_id']);
			$data = [
				'detail' => $detail,
				'requestor_data' => $requestor_data,
			];
			$this->template->set('page', 'Reporting #' . $detail['ea_number']);
			$this->template->render('report/reporting', $data);
		} else {
			show_404();
		}
	}

    public function datatable()
    {	

		$this->datatable->select('CONCAT("EA", ea.id) AS ea_number, u.username as requestor_name, ea.request_base,
        ea.originating_city, ea.id as total_cost, DATE_FORMAT(ea.created_at, "%d %M %Y - %H:%i") as created_at ,ea.id', true);
        $this->datatable->from('ea_requests ea');
        $this->datatable->join('tb_userapp u', 'u.id = ea.requestor_id');
        $this->datatable->join('ea_requests_status st', 'ea.id = st.request_id');
        $this->datatable->where('st.head_of_units_status =', 2);
        $this->datatable->where('st.ea_assosiate_status =', 2);
        $this->datatable->where('st.fco_monitor_status =', 2);
        $this->datatable->where('st.finance_status =', 2);
        $this->datatable->order_by('created_at', 'desc');
		$this->datatable->where('ea.requestor_id =', $this->user_data->userId);
		$this->datatable->edit_column('id', "$1", 'encrypt(id)');
		$this->datatable->edit_column('total_cost', '<span style="font-size: 1rem;"
		class="badge badge-pill badge-secondary fw-bold">$1</span>', 'get_total_request_costs(total_cost)');
		$this->datatable->edit_column('ea_number', '<span style="font-size: 1rem;"
		class="badge badge-success fw-bold">$1</span>', 'ea_number');
        echo $this->datatable->generate();
    }

	public function meals_lodging_modal() {
		$dest_id = $this->input->get('dest_id');
		$detail = $this->db->select('actual_meals, actual_lodging')->from('ea_requests_destinations')->where('id', $dest_id)->get()->row_array();
		$field = $this->input->get('field');
		if($field == 'meals') {
			$actual_cost = $detail['actual_meals'];
		} else {
			$actual_cost = $detail['actual_lodging'];
		}
		$data = [
			'dest_id' => $dest_id,
			'actual_cost' => $actual_cost + 0,
			'field' => $field,
		];

		$this->load->view('report/modal/meals_lodging', $data);
	}

	public function add_items_modal() {
		$dest_id = $this->input->get('dest_id');		
		$data = [
			'dest_id' => $dest_id,
		];
		$this->load->view('report/modal/add_items', $data);
	}

	public function edit_items_modal() {
		$item_id = $this->input->get('item_id');	
		$detail = $this->report->get_items_detail($item_id);	
		$data = [
			'detail' => $detail,
		];
		$this->load->view('report/modal/edit_items', $data);
	}

	public function insert_actual_meals_lodging() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('actual_cost', 'Actual cost', 'required');
			$dest_id = $this->input->post('dest_id');
			$field = $this->input->post('field');
			$detail = $this->db->select('meals_receipt, lodging_receipt')->from('ea_requests_destinations')->where('id', $dest_id)->get()->row_array();
			if($field == 'lodging' && $detail['lodging_receipt'] == null) {
				if (empty($_FILES['receipt']['name']))
				{
					$this->form_validation->set_rules('receipt', 'Receipt', 'required');
				}
			} else if($field == 'meals' && $detail['meals_receipt'] == null) {
				if (empty($_FILES['receipt']['name']))
				{
					$this->form_validation->set_rules('receipt', 'Receipt', 'required');
				}
			} 

			if ($this->form_validation->run()) {
				$dir = './uploads/ea_items_receipt/';
				if (!is_dir($dir)) {
					mkdir($dir, 0777, true);
				}
				$config['upload_path']          = $dir;
				$config['allowed_types']        = 'pdf|jpg|png|jpeg';
				$config['max_size']             = 10048;
				$config['encrypt_name']         = true;
				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (empty($_FILES['receipt']['name']))
				{
					if($field == 'lodging') {
						$receipt = $detail['lodging_receipt'];
					} else if($field == 'meals') {
						$receipt = $detail['meals_receipt'];
					} 
				} else {
					if($this->upload->do_upload('receipt')) {
						$receipt = $this->upload->data('file_name');
					} else {
						$response = [
							'errors' => $this->upload->display_errors(),
							'success' => false, 
							'message' => strip_tags($this->upload->display_errors()),
						];
						$status_code = 400;
						return $this->send_json($response, $status_code);
					}
				}
				$actual_cost = $this->input->post('actual_cost');
				$clean_actual_cost = str_replace('.', '',  $actual_cost);
				$payload = [
					'actual_'. $field => $clean_actual_cost,
					$field . '_receipt' => $receipt,
				];
				$updated = $this->report->insert_actual_cost($dest_id, $payload);
				if($updated) {
					$response['success'] = true;
					$response['message'] = 'Data has been saved!';
					$status_code = 200;
				} else {
					$response['success'] = false;
					$response['message'] = 'Failed to saving data, please try again later';
					$status_code = 400;
				}
			} else {
				$response['errors'] = $this->form_validation->error_array();
				$response['message'] = 'Please fill all required fields';
				$status_code = 422;
			}
			$this->send_json($response, $status_code);
		} else {
			exit('No direct script access allowed');
		}
	}

	public function insert_other_items() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('item', 'Item', 'required');
			$this->form_validation->set_rules('cost', 'Cost', 'required');
			$dest_id = $this->input->post('dest_id');
			if (empty($_FILES['receipt']['name']))
			{
				$this->form_validation->set_rules('receipt', 'Receipt', 'required');
			}

			if ($this->form_validation->run()) {
				$dir = './uploads/ea_items_receipt/';
				if (!is_dir($dir)) {
					mkdir($dir, 0777, true);
				}
				$config['upload_path']          = $dir;
				$config['allowed_types']        = 'pdf|jpg|png|jpeg';
				$config['max_size']             = 10048;
				$config['encrypt_name']         = true;
				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if($this->upload->do_upload('receipt')) {
					$receipt = $this->upload->data('file_name');
					$item = $this->input->post('item');
					$cost = $this->input->post('cost');
					$clean_cost = str_replace('.', '',  $cost);
					$payload = [
						'destination_id' => $dest_id,
						'item' => $item,
						'cost' => $clean_cost,
						'receipt' => $receipt,
					];
					$saved = $this->report->insert_other_items($payload);
					if($saved) {
						$response['success'] = true;
						$response['message'] = 'Data has been saved!';
						$status_code = 200;
					} else {
						$response['success'] = false;
						$response['message'] = 'Failed to saving data, please try again later';
						$status_code = 400;
					}
				} else {
					$response = [
						'errors' => $this->upload->display_errors(),
						'success' => false, 
						'message' => strip_tags($this->upload->display_errors()),
					];
					$status_code = 400;
					return $this->send_json($response, $status_code);
				}
			} else {
				$response['errors'] = $this->form_validation->error_array();
				$response['message'] = 'Please fill all required fields';
				$status_code = 422;
			}
			$this->send_json($response, $status_code);
		} else {
			exit('No direct script access allowed');
		}
	}

	public function update_other_items($item_id) {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('item', 'Item', 'required');
			$this->form_validation->set_rules('cost', 'Cost', 'required');
			if ($this->form_validation->run()) {
				
				$detail = $this->report->get_items_detail($item_id);
				if (empty($_FILES['receipt']['name'])) {
					$receipt = $detail['receipt'];
				} else {
					$dir = './uploads/ea_items_receipt/';
					if (!is_dir($dir)) {
						mkdir($dir, 0777, true);
					}
					$config['upload_path']          = $dir;
					$config['allowed_types']        = 'pdf|jpg|png|jpeg';
					$config['max_size']             = 10048;
					$config['encrypt_name']         = true;
					$this->load->library('upload', $config);
					$this->upload->initialize($config);
					if($this->upload->do_upload('receipt')) {
						$receipt = $this->upload->data('file_name');
					} else {
						$response = [
							'errors' => $this->upload->display_errors(),
							'success' => false, 
							'message' => strip_tags($this->upload->display_errors()),
						];
						$status_code = 400;
						return $this->send_json($response, $status_code);
					}
				}
				$item = $this->input->post('item');
				$cost = $this->input->post('cost');
				$clean_cost = str_replace('.', '',  $cost);
				$payload = [
					'item' => $item,
					'cost' => $clean_cost,
					'receipt' => $receipt,
				];
				$updated = $this->report->update_other_items($item_id, $payload);
				if($updated) {
					$response['success'] = true;
					$response['message'] = 'Item has been updated!';
					$status_code = 200;
				} else {
					$response['success'] = false;
					$response['message'] = 'Failed to update item, please try again later';
					$status_code = 400;
				}
			} else {
				$response['errors'] = $this->form_validation->error_array();
				$response['message'] = 'Please fill all required fields';
				$status_code = 422;
			}
			$this->send_json($response, $status_code);
		} else {
			exit('No direct script access allowed');
		}
	}

	public function delete_other_items($id) {
		$deleted = $this->db->where('id', $id)->delete('ea_requests_other_items');
		if($deleted) {
			$response['success'] = true;
			$response['message'] = 'Item has been deleted';
			$status_code = 200;
		} else {
			$response['success'] = false;
			$response['message'] = 'Failed to delete item';
			$status_code = 422;
		}
		$this->send_json($response, $status_code);
	}
}