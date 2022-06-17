<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Spipu\Html2Pdf\Html2Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Outgoing extends MY_Controller {


    function __construct()
	{
		parent::__construct();
		$this->load->model('Request_Model', 'request');
		$this->load->model('Report_Model', 'report');
		$this->load->model('Base_Model', 'base_model');
		$this->template->set('pageParent', 'Outgoing report');
		$this->template->set_default_layout('layouts/default');
		$this->load->helper('report');
	}

	public function index()
	{   
        $this->template->set('page', 'New report');
		$this->template->render('ea_report/outgoing/index');
	}

	public function pending()
	{   
        $this->template->set('page', 'Pending report');
		$this->template->render('ea_report/outgoing/pending');
	}

	public function rejected()
	{   
        $this->template->set('page', 'Rejected report');
		$this->template->render('ea_report/outgoing/rejected');
	}
	public function approved()
	{   
        $this->template->set('page', 'Approved report');
		$this->template->render('ea_report/outgoing/approved');
	}

	public function paid()
	{   
        $this->template->set('page', 'Paid report');
		$this->template->render('ea_report/outgoing/paid');
	}

	public function refund_reimburst()
	{   
        $this->template->set('page', 'Refund/reimburst report');
		$this->template->render('ea_report/outgoing/refund_reimburst');
	}

    public function reporting($id = null)
	{
		$id = decrypt($id);
		$detail = $this->report->get_report_data_by_id($id);
		if($detail) {
			if($detail['requestor_id'] == $this->user_data->userId) {
				$requestor_data = $this->request->get_requestor_data($detail['requestor_id']);
				$this->load->helper('report');
				$data = [
					'detail' => $detail,
					'requestor_data' => $requestor_data,
					'is_report_finished' => is_report_finished($id),
					'participants' => get_request_participants($id),
					'refund_or_reimburst' => get_total_refund_or_reimburst($id),
				];
				$this->template->set('page', 'Reporting TER #' . $detail['ea_number']);
				$this->template->render('ea_report/outgoing/reporting', $data);
			} else {
				show_404();
			}
		} else {
			show_404();
		}
	}

	public function get_total_refund($id) {
		$total = get_total_refund_or_reimburst($id);
		if($total < 0) {
			echo 'reimburst';
		} else {
			echo 'refund';
		}
	}
	
    public function ter_detail($id = null)
	{
		$id = decrypt($id);
		$detail = $this->report->get_ter_details($id);
		$report_detail = $this->report->get_report_data_by_id($id);
		if($detail) {
			$requestor_data = $this->request->get_requestor_data($detail['requestor_id']);
			$user_id = $this->user_data->userId;
			$head_of_units_btn = '';
			if($detail['head_of_units_status'] != 1 || $detail['head_of_units_id'] != $user_id) {
				$head_of_units_btn = 'invisible';
			}
			$finance_btn = '';
			if($detail['finance_status'] != 1 || $detail['head_of_units_status'] != 2  || !is_finance_teams()) {
				$finance_btn = 'invisible';
			}
			$country_director_btn = '';
			if($detail['country_director_status'] != 1 || $detail['finance_status'] != 2  || !is_fco_monitor()) {
				$country_director_btn = 'invisible';
			}
			$submit_btn = 'd-none';
			if($detail['requestor_id'] == $this->user_data->userId && $detail['is_ter_rejected'] == 1) {
				$submit_btn = '';
			}
			$edit_btn = 'disabled';
			if($detail['requestor_id'] == $this->user_data->userId || is_finance_teams()) {
				$edit_btn = '';
			}
			$data = [
				'detail' => $detail,
				'report_detail' => $report_detail,
				'requestor_data' => $requestor_data,
				'head_of_units_btn' => $head_of_units_btn,
				'country_director_btn' => $country_director_btn,
				'finance_btn' => $finance_btn,
				'submit_btn' => $submit_btn,
				'edit_btn' => $edit_btn,
				'total_actual_costs' => get_total_actual_costs($id),
				'is_report_finished' => is_report_finished($id),
				'participants' => get_request_participants($id),
				'refund_or_reimburst' => get_total_refund_or_reimburst($id),
			];
			$this->template->set('pageParent', 'Travel Expense Report (TER)');
			$this->template->set('page', 'EA#' . $detail['r_id']);
			$this->template->render('ea_report/outgoing/ter_detail', $data);
		} else {
			show_404();
		}
	}

    public function datatable()
    {	

		$this->datatable->select('CONCAT("EA", ea.id) AS ea_number, u.username as requestor_name, ea.id as participants,
        ea.originating_city, ea.id as total_cost, DATE_FORMAT(st.date_of_transfer, "%d %M %Y"), ea.id, TIMESTAMP(ea.created_at) as timestamp', true);
        $this->datatable->from('ea_requests ea');
        $this->datatable->join('tb_userapp u', 'u.id = ea.requestor_id');
        $this->datatable->join('ea_requests_status st', 'ea.id = st.request_id');
        $this->datatable->where('st.head_of_units_status =', 2);
        $this->datatable->where('st.ea_assosiate_status =', 2);
        $this->datatable->where('st.fco_monitor_status =', 2);
        $this->datatable->where('st.finance_status =', 2);
		$this->datatable->where('ea.requestor_id =', $this->user_data->userId);
        $this->datatable->where('ea.is_ter_submitted =', 0);
        $this->datatable->where('ea.is_ter_rejected =', 0);
		$this->datatable->edit_column('id', "$1", 'encrypt(id)');
		$this->datatable->edit_column('participants', "$1", 'get_request_participants(participants)');
		$this->datatable->edit_column('total_cost', '<span style="font-size: 1rem;"
		class="badge badge-pill badge-secondary fw-bold">$1</span>', 'get_total_request_costs(total_cost)');
		$this->datatable->edit_column('ea_number', '<span style="font-size: 1rem;"
		class="badge badge-success fw-bold">$1</span>', 'ea_number');
        echo $this->datatable->generate();
    }

	public function refund_reimburst_datatable() {
		$this->datatable->select('CONCAT("EA", ea.id) AS ea_number, ea.id as request_for,
			ea.id as total_advance, ea.id as total_expense, ea.id as refund, ea.id as reimburst,
			ea.id as action,TIMESTAMP(srt.submitted_at) as timestamp', true);
        $this->datatable->from('ea_requests ea');
        $this->datatable->join('tb_userapp u', 'u.id = ea.requestor_id');
        $this->datatable->join('ea_report_status srt', 'ea.id = srt.request_id');
		$this->datatable->where('srt.head_of_units_status =', 2);
		$this->datatable->where('srt.finance_status =', 2);
		$this->datatable->where('srt.country_director_status =', 2);
		$this->datatable->where('srt.is_need_confirmation =', 0);
		$this->datatable->where('srt.is_paid =', 1);
        $this->datatable->where('ea.is_ter_submitted =', 1);
		$this->datatable->where('ea.requestor_id =', $this->user_data->userId);
		$this->datatable->edit_column('action', "$1", 'encrypt(action)');
		$this->datatable->edit_column('request_for', "$1", 'get_request_participants(request_for)');
		$this->datatable->edit_column('total_advance', '<span style="font-size: 1rem;"
		class="badge badge-pill badge-secondary fw-bold">$1</span>', 'get_total_advance(total_advance)');
		$this->datatable->edit_column('total_expense', '<span style="font-size: 1rem;"
		class="badge badge-pill badge-secondary fw-bold">$1</span>', 'get_total_expense(total_expense)');
		$this->datatable->edit_column('refund', '<span style="font-size: 1rem;"
		class="badge badge-pill badge-secondary fw-bold">$1</span>', 'get_total_refund(refund)');
		$this->datatable->edit_column('reimburst', '<span style="font-size: 1rem;"
		class="badge badge-pill badge-secondary fw-bold">$1</span>', 'get_total_reimburst(reimburst)');
		$this->datatable->edit_column('ea_number', '<span style="font-size: 1rem;"
		class="badge badge-success fw-bold">$1</span>', 'ea_number');
        echo $this->datatable->generate();
	}

    public function ter_datatable($status = null)
    {	

		$this->datatable->select('CONCAT("EA", ea.id) AS ea_number, u.username as requestor_name, ea.request_base,
			ea.originating_city, ea.id as total_cost, DATE_FORMAT(srt.submitted_at, "%d %M %Y - %H:%i") as created_at ,ea.id, TIMESTAMP(srt.submitted_at) as timestamp', true);
        $this->datatable->from('ea_requests ea');
        $this->datatable->join('tb_userapp u', 'u.id = ea.requestor_id');
        $this->datatable->join('ea_requests_status st', 'ea.id = st.request_id');
        $this->datatable->join('ea_report_status srt', 'ea.id = srt.request_id', 'LEFT');
        $this->datatable->where('st.head_of_units_status =', 2);
        $this->datatable->where('st.ea_assosiate_status =', 2);
        $this->datatable->where('st.fco_monitor_status =', 2);
        $this->datatable->where('st.finance_status =', 2);
		$this->datatable->where('ea.requestor_id =', $this->user_data->userId);
		if($status == 'pending') {
			$this->datatable->where('srt.head_of_units_status !=', 3);
			$this->datatable->where('srt.country_director_status !=', 3);
			$this->datatable->where('srt.country_director_status !=', 2);
			$this->datatable->where('srt.finance_status !=', 3);
			$this->datatable->where('srt.finance_status !=', 2);
		}
		if($status == 'approved') {
			$this->datatable->where('srt.head_of_units_status =', 2);
			$this->datatable->where('srt.country_director_status =', 2);
			$this->datatable->where('srt.finance_status =', 2);
			$this->datatable->where('srt.is_paid =', 1);
		}
		if($status == 'paid') {
			$this->datatable->where('srt.head_of_units_status =', 2);
			$this->datatable->where('srt.country_director_status =', 2);
			$this->datatable->where('srt.finance_status =', 2);
			$this->datatable->where('srt.is_paid =', 2);
		}
        $this->datatable->where('ea.is_ter_submitted =', 1);
		$this->datatable->edit_column('id', "$1", 'encrypt(id)');
		$this->datatable->edit_column('total_cost', '<span style="font-size: 1rem;"
		class="badge badge-pill badge-secondary fw-bold">$1</span>', 'get_total_actual_costs(total_cost)');
		$this->datatable->edit_column('ea_number', '<span style="font-size: 1rem;"
		class="badge badge-success fw-bold">$1</span>', 'ea_number');
        echo $this->datatable->generate();
    }

	public function pending_datatable()
    {	

		$this->datatable->select('CONCAT("EA", ea.id) AS ea_number, ea.id as request_for,
			ea.id as total_advance, ea.id as total_expense, ea.id as action,TIMESTAMP(srt.submitted_at) as timestamp', true);
        $this->datatable->from('ea_requests ea');
        $this->datatable->join('tb_userapp u', 'u.id = ea.requestor_id');
        $this->datatable->join('ea_report_status srt', 'ea.id = srt.request_id');

		$this->datatable->where('srt.head_of_units_status =', 2);
		$this->datatable->where('srt.finance_status =', 2);
		$this->datatable->where('srt.country_director_status =', 1);
		$this->datatable->where('srt.is_need_confirmation =', 1);
        $this->datatable->where('ea.is_ter_submitted =', 1);
		$this->datatable->where('ea.requestor_id =', $this->user_data->userId);
		$this->datatable->edit_column('action', "$1", 'encrypt(action)');
		$this->datatable->edit_column('request_for', "$1", 'get_request_participants(request_for)');
		$this->datatable->edit_column('total_advance', '<span style="font-size: 1rem;"
		class="badge badge-pill badge-secondary fw-bold">$1</span>', 'get_total_advance(total_advance)');
		$this->datatable->edit_column('total_expense', '<span style="font-size: 1rem;"
		class="badge badge-pill badge-secondary fw-bold">$1</span>', 'get_total_expense(total_expense)');
		$this->datatable->edit_column('ea_number', '<span style="font-size: 1rem;"
		class="badge badge-success fw-bold">$1</span>', 'ea_number');
        echo $this->datatable->generate();
    }

	public function rejected_datatable()
    {	

		$this->datatable->select('CONCAT("EA", ea.id) AS ea_number, u.username as requestor_name, ea.request_base,
        ea.originating_city, ea.id as total_cost,srt.rejected_reason , DATE_FORMAT(srt.submitted_at, "%d %M %Y - %H:%i") as created_at ,ea.id, TIMESTAMP(srt.submitted_at) as timestamp', true);
        $this->datatable->from('ea_requests ea');
        $this->datatable->join('tb_userapp u', 'u.id = ea.requestor_id');
        $this->datatable->join('ea_requests_status st', 'ea.id = st.request_id');
        $this->datatable->join('ea_report_status srt', 'ea.id = srt.request_id', 'LEFT');
		$this->datatable->where('st.head_of_units_status =', 2);
        $this->datatable->where('st.ea_assosiate_status =', 2);
        $this->datatable->where('st.fco_monitor_status =', 2);
        $this->datatable->where('st.finance_status =', 2);
		$this->datatable->where('ea.requestor_id =', $this->user_data->userId);
        $this->datatable->where('ea.is_ter_rejected =', 1);
		$this->datatable->where('srt.head_of_units_status =', 3);
		$this->datatable->or_where('srt.country_director_status =', 3);
		$this->datatable->where('ea.requestor_id =', $this->user_data->userId);
		$this->datatable->or_where('srt.finance_status =', 3);
		$this->datatable->where('ea.requestor_id =', $this->user_data->userId);
		$this->datatable->edit_column('id', "$1", 'encrypt(id)');
		$this->datatable->edit_column('total_cost', '<span style="font-size: 1rem;"
		class="badge badge-pill badge-secondary fw-bold">$1</span>', 'get_total_actual_costs(total_cost)');
		$this->datatable->edit_column('ea_number', '<span style="font-size: 1rem;"
		class="badge badge-success fw-bold">$1</span>', 'ea_number');
        echo $this->datatable->generate();
    }

	public function meals_lodging_modal() {
		$dest_id = $this->input->get('dest_id');
		$night = $this->input->get('night');
		$item_id = $this->input->get('item_id');
		$item_type = $this->input->get('item_type');
		$max_budget = $this->report->get_dest_max_budget($dest_id);
		$max_lodging_budget = $max_budget['max_lodging_budget'] + 0;
		$max_lodging_budget_usd = $max_budget['max_lodging_budget_usd'] + 0;
		$max_meals_budget = $max_budget['max_meals_budget'] + 0;
		$country = $max_budget['country'];
		$meals = [];
		if($item_id != 0 && $item_type == 2) {
			$item = $this->db->select('meals_text')->from('ea_actual_costs')->where([
				'dest_id' => $dest_id,
				'night' => $night,
				'item_type' => 3,
				'item_name' => 'List meals',
			])->get()->row_array();
			$meals = explode(',', $item['meals_text']);
		}
		$data = [
			'dest_id' => $dest_id,
			'item_type' => $item_type,
			'night' => $night,
			'max_budget' => $max_budget,
			'max_lodging_budget' => $max_lodging_budget,
			'max_lodging_budget_usd' => $max_lodging_budget_usd,
			'max_meals_budget' => $max_meals_budget,
			'meals' => $meals,
			'country' => $country,
		];
		if($item_id != 0) {
			$data['detail'] = $this->report->get_actual_cost_detail($item_id);
		}
		$this->load->view('ea_report/modal/meals_lodging', $data);
	}

	public function add_items_modal() {
		$dest_id = $this->input->get('dest_id');		
		$night = $this->input->get('night');	
		$item_id = $this->input->get('item_id');
		$data = [
			'dest_id' => $dest_id,
			'night' => $night,
		];
		if($item_id != 0) {
			$data['detail'] = $this->report->get_actual_cost_detail($item_id);
		}
		$this->load->view('ea_report/modal/add_items', $data);
	}

	public function payment_modal() {
		$req_id = decrypt($this->input->get('req_id'));		
		$payment_type =  get_total_refund_or_reimburst($req_id);
		$data = [
			'req_id' => $req_id,
			'payment_type' => $payment_type['status'],
			'total_payment' => $payment_type['total'],
		];		
		$this->load->view('ea_report/modal/payment_modal', $data);
	}

	public function edit_items_modal() {
		$dest_id = $this->input->get('dest_id');		
		$night = $this->input->get('night');	
		$item_id = $this->input->get('item_id');
		$detail = $this->report->get_actual_cost_detail($item_id);
		$data = [
			'dest_id' => $dest_id,
			'night' => $night,
			'detail' => $detail,
		];
		$this->load->view('ea_report/modal/edit_items', $data);
	}

	public function other_items_detail() {
		$dest_id =$this->input->get('dest_id');
		$req_id =$this->input->get('req_id');
		$item_name =$this->input->get('item_name');
		$night =$this->input->get('night');
		$dest_id =$this->input->get('dest_id');
		$request = $this->db->select('requestor_id')->from('ea_requests')->where('id', $req_id)->get()->row_array();
		$edit_btn = 'disabled';
		if($request['requestor_id'] == $this->user_data->userId || is_finance_teams()) {
			$edit_btn = '';
		}
		$items = $this->report->get_other_items_by_name($dest_id, $item_name, $night);
		$data = [
			'items' => $items,
			'edit_btn' => $edit_btn,
		];
		$this->load->view('ea_report/modal/other_items_detail', $data);
	}

	public function edit_other_items_modal() {
		$dest_id =$this->input->get('dest_id');
		$item_name =$this->input->get('item_name');
		$night =$this->input->get('night');
		$dest_id =$this->input->get('dest_id');
		$items = $this->report->get_other_items_by_name($dest_id, $item_name, $night);	
		$total_cost = 0;
		foreach($items as $item) {
			$total_cost += $item['cost'];
		}
		$data = [
			'items' => $items,
			'night' => $night,
			'dest_id' => $dest_id,
			'total_cost' => $total_cost,
		];
		$this->load->view('ea_report/modal/edit_items_by_name', $data);
	}

	public function insert_actual_costs() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('cost', 'Actual cost', 'required');
			$item_type = $this->input->post('item_type');
			if($item_type == 3) {
				$this->form_validation->set_rules('item_name', 'Items', 'required');
				if($this->input->post('method_') != 'PUT') {
					if (empty($_FILES['receipt']['name']))
					{
						$this->form_validation->set_rules('receipt', 'Receipt', 'required');
					}
				}
			}
			if($item_type == 2) {
				$this->form_validation->set_rules('meals[]', 'Items', 'required');
			}

			if ($this->form_validation->run()) {
				$actual_cost = $this->input->post('cost');
				$clean_actual_cost = str_replace('.', '',  $actual_cost);
				if($item_type == 1) {
					$dest_id = $this->input->post('dest_id');
					$dest_max_budget = $this->report->get_dest_max_budget($dest_id);
					$max_lodging_budget = $dest_max_budget['max_lodging_budget'];
					if($clean_actual_cost > $max_lodging_budget ) {
						$response['success'] = false;
						$response['max_budget_error'] = true;
						$max = number_format($max_lodging_budget,2,',','.');
						$response['max_budget_message'] = "Only IDR $max allowed!";
						$response['message'] = 'Not enough budget!';
						$status_code = 400;
						return $this->send_json($response, $status_code);
					}
				}

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

				$dest_id = $this->input->post('dest_id');
				if($item_type == 1) {
					$item_name = 'Lodging';
				} else if($item_type == 2) {
					$item_name = 'Meals';
				} else {
					$item_name = $this->input->post('item_name');
				}

				if (empty($_FILES['receipt']['name']) && $this->input->post('method_') == 'PUT') {
					$item_id = $this->input->post('item_id');
					$item_detail = $this->db->select('*')->from('ea_actual_costs')->where('id', $item_id)->get()->row_array();
					$receipt = $item_detail['receipt'];
					$payload = [
						'cost' => $clean_actual_cost,
						'receipt' => $receipt,
					];
					if($item_type == 2) {
						$meals = $this->input->post('meals');
						$night = $this->input->post('night');
						$day_status = $this->input->post('day_status');
						$update_meals = $this->update_provided_meals($dest_id, $night, $meals, $day_status);
						if($update_meals) {
							$updated = $this->report->update_actual_costs($item_id, $payload);
						} else {
							$updated = false;
						}
					} else {
						$updated = $this->report->update_actual_costs($item_id, $payload);
					}
				} else {
					if($item_type == 3) {
						$uploaded = $this->upload->do_upload('receipt');
						if($uploaded) {
							$receipt = $this->upload->data('file_name');
						} else {
							$uploaded = false;
						}
					} else {
						if(empty($_FILES['receipt']['name'])) {
							$uploaded = true;
							$receipt = null;
						} else {
							$uploaded = $this->upload->do_upload('receipt');
							$receipt = $this->upload->data('file_name');;
						}
					}
					if($uploaded) {
						$item_id = $this->input->post('item_id');
						$item_detail = $this->db->select('*')->from('ea_actual_costs')->where('id', $item_id)->get()->row_array();
						if($this->input->post('method_') == 'PUT') {
							$item_id = $this->input->post('item_id');
							$payload = [
								'cost' => $clean_actual_cost,
								'receipt' => $receipt,
							];
							if($item_type == 2) {
								$meals = $this->input->post('meals');
								$night = $this->input->post('night');
								$day_status = $this->input->post('day_status');
								$update_meals = $this->update_provided_meals($dest_id, $night, $meals, $day_status);
								if($update_meals) {
									$updated = $this->report->update_actual_costs($item_id, $payload);
								} else {
									$updated = false;
								}
							} else {
								$updated = $this->report->update_actual_costs($item_id, $payload);
							}
							if($item_detail['receipt'] !== null) {
								unlink(FCPATH . 'uploads/ea_items_receipt/' . $item_detail['receipt']);
							}
						} else {
							$night = $this->input->post('night');
							$payload = [
								'dest_id' => $dest_id,
								'cost' => $clean_actual_cost,
								'item_type' =>$item_type,
								'item_name' =>$item_name,
								'night' => $night,
								'receipt' => $receipt,
							];
							if($item_type == 2) {
								$meals = $this->input->post('meals');
								$day_status = $this->input->post('day_status');
								$insert_meals = $this->insert_provided_meals($dest_id, $night, $meals, $day_status);
								if($insert_meals) {
									$updated = $this->report->insert_actual_costs($payload);
								} else {
									$updated = false;
								}
							} else {
								$updated = $this->report->insert_actual_costs($payload);
							}
						}
					} else {
						$response = [
							'errors' => $this->upload->display_errors(),
							'success' => false, 
							'message' => strip_tags($this->upload->display_errors()),
						];
						$status_code = 422;
						return $this->send_json($response, $status_code);
					}
				}
				
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
				$response['message'] = 'Please fill all required fields!';
				$status_code = 422;
			}
			$this->send_json($response, $status_code);
		} else {
			exit('No direct script access allowed');
		}
	}

	private function insert_provided_meals($dest_id, $night, $meals, $day_status) {
		$text = implode(',', $meals);
		$dest_max_budget = $this->report->get_dest_max_budget($dest_id);
		$meals_budget = $dest_max_budget['max_meals_budget'] + 0;
		$total_night = $dest_max_budget['total_night'];
		$is_last_night = false;
		$is_last_day = 0;
		if($total_night == $night || $day_status == 2) {
			$is_last_night = true;
			$is_last_day = 1;
		}
		$is_first_day = 0;
		if($night == 1) {
			$is_first_day = 1;
		}
		$cost = $this->count_meals($meals_budget, $text, $night, $is_last_night);
		$payload = [
			'dest_id' => $dest_id,
			'cost' => $cost,
			'item_type' => 3,
			'item_name' => 'List meals',
			'meals_text' => $text,
			'night' => $night,
			'is_last_day' => $is_last_day,
			'is_first_day' => $is_first_day,
		];
		if($day_status == 2) {
			$night += 1;
			for($x = $night; $x <= $total_night; $x++) {
				$other_payload = [
					'dest_id' => $dest_id,
					'cost' => 0,
					'item_type' => 3,
					'item_name' => 'List meals',
					'meals_text' => '-',
					'night' => $night,
				];
				$meals_payload = [
					'dest_id' => $dest_id,
					'cost' => $meals_budget,
					'item_type' => 2,
					'item_name' => 'Meals',
					'meals_text' => null,
					'night' => $night,
				];
				$lodging_payload = [
					'dest_id' => $dest_id,
					'cost' => 0,
					'item_type' => 1,
					'item_name' => 'Lodging',
					'meals_text' => null,
					'night' => $night,
				];
				$this->report->insert_actual_costs($other_payload);
				$this->report->insert_actual_costs($meals_payload);
				$this->report->insert_actual_costs($lodging_payload);
				$night++;
			}
		}
		$updated = $this->report->insert_actual_costs($payload);
		if($updated) {
			return true;
		}
		return false;
	}

	private function update_provided_meals($dest_id, $night, $meals, $day_status) {
		$text = implode(',', $meals);
		$dest_max_budget = $this->report->get_dest_max_budget($dest_id);
		$meals_budget = $dest_max_budget['max_meals_budget'] + 0;
		$total_night = $dest_max_budget['total_night'];
		$is_last_night = false;
		$is_last_day = 0;
		if($total_night == $night || $day_status == 2) {
			$is_last_night = true;
			$is_last_day = 1;
		}
		$is_first_day = 0;
		if($night == 1) {
			$is_first_day = 1;
		}
		$cost = $this->count_meals($meals_budget, $text, $night, $is_last_night);
		$payload = [
			'meals_text' => $text,
			'cost' => $cost,
			'is_last_day' => $is_last_day,
			'is_first_day' => $is_first_day,
		];
		$updated = $this->db->where([
			'dest_id' => $dest_id,
			'item_type' => 3,
			'item_name' => 'List meals',
			'night' => $night,
		])->update('ea_actual_costs', $payload);
		if($day_status == 2) {
			$night += 1;
			for($x = $night; $x <= $total_night; $x++) {
				$other_payload = [
					'dest_id' => $dest_id,
					'cost' => 0,
					'item_type' => 3,
					'item_name' => 'List meals',
					'meals_text' => '-',
					'night' => $night,
				];
				$meals_payload = [
					'dest_id' => $dest_id,
					'cost' => $meals_budget,
					'item_type' => 2,
					'item_name' => 'Meals',
					'meals_text' => null,
					'night' => $night,
				];
				$lodging_payload = [
					'dest_id' => $dest_id,
					'cost' => 0,
					'item_type' => 1,
					'item_name' => 'Lodging',
					'meals_text' => null,
					'night' => $night,
				];
				$deleted = $this->db->where([
					'dest_id' => $dest_id,
					'night' => $night,
				])->delete('ea_actual_costs');
				if($deleted) {
					$this->report->insert_actual_costs($other_payload);
					$this->report->insert_actual_costs($meals_payload);
					$this->report->insert_actual_costs($lodging_payload);
					$night++;
				}
			}
		}
		if($updated) {
			return true;
		}
		return false;
	}

	private function count_meals($meals_budget, $text, $night, $is_last_night) {
		if($night == 1 || $is_last_night) {
			$meals_budget = $meals_budget * 0.75;
		}
		if(str_contains($text, 'L')) {
			$subtr = $meals_budget * 0.25;
			$meals_budget -= $subtr; 
		} 
		if(str_contains($text, 'B')) {
			$subtr = $meals_budget * 0.15;
			$meals_budget -= $subtr; 
		} 
		if(str_contains($text, 'D')) {
			$subtr = $meals_budget * 0.4;
			$meals_budget -= $subtr; 
		}
		if(str_contains($text, '-') || $text == '') {
			$meals_budget = 0;
		}
		return $meals_budget;
	}

	public function insert_other_items() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('item_name', 'Item', 'required');
			$this->form_validation->set_rules('cost[]', 'Cost', 'required');
			$dest_id = $this->input->post('dest_id');
			if (empty($_FILES['receipt']['name']))
			{
				$this->form_validation->set_rules('receipt', 'Receipt', 'required');
			}

			if ($this->form_validation->run()) {
				$receipts = [];
				$filesCount = count($_FILES['receipt']['name']); 
				$dir = './uploads/ea_items_receipt/';
				if (!is_dir($dir)) {
					mkdir($dir, 0777, true);
				} 
                for($i = 0; $i < $filesCount; $i++){ 
                    $_FILES['file']['name']     = $_FILES['receipt']['name'][$i]; 
                    $_FILES['file']['type']     = $_FILES['receipt']['type'][$i]; 
                    $_FILES['file']['tmp_name'] = $_FILES['receipt']['tmp_name'][$i]; 
                    $_FILES['file']['error']     = $_FILES['receipt']['error'][$i]; 
                    $_FILES['file']['size']     = $_FILES['receipt']['size'][$i]; 
                     
                    // File upload configuration 
                    $config['upload_path']          = $dir;
					$config['allowed_types']        = 'pdf|jpg|png|jpeg';
					$config['max_size']             = 10048;
					$config['encrypt_name']         = true;
					$this->load->library('upload', $config);
					$this->upload->initialize($config);
                     
                    // Upload file to server 
                    if($this->upload->do_upload('file')){ 
                        // Uploaded file data 
                        $fileData = $this->upload->data(); 
                        $receipts[$i] = $fileData['file_name']; 
                    } else {
						$response = [
							'errors' => $this->upload->display_errors(),
							'success' => false, 
							'message' => strip_tags($this->upload->display_errors()),
						];
						$status_code = 422;
						return $this->send_json($response, $status_code);
					}
                } 
				$item_name = $this->input->post('item_name');
				$night = $this->input->post('night');
				$costs = $this->input->post('cost');
				for($i = 0; $i < count($costs); $i++) {
					$clean_cost = str_replace('.', '',  $costs[$i]);
					$payload = [
						'dest_id' => $dest_id,
						'item_type' => 3,
						'item_name' => $item_name,
						'cost' => $clean_cost,
						'night' => $night,
						'receipt' => $receipts[$i],
					];
					$saved = $this->report->insert_actual_costs($payload);
				}
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
		$deleted = $this->db->where('id', $id)->delete('ea_actual_costs');
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
	

	public function ter_form($id) {
		$detail = $this->report->get_excel_report_by_id($id);
		$total_days = get_total_days($id);
		$total_max_lodging_budget = $this->report->get_total_max_lodging_budget($id);
		if($total_days <= 7) {
			$excel_config = [
				'file_name' => 'ea_report.xlsx',
				'date_submitted_cell' => 'G5',
				'travel_date_cell' => 'K5',
				'last_cell' => 'I',
				'max_lodging_budget_cell' => 'K20',
				'project_number_cell' => 'L20',
			];
		} else if($total_days > 7 && $total_days <= 14) {
			$excel_config = [
				'file_name' => 'ea_report_2_minggu.xlsx',
				'date_submitted_cell' => 'N5',
				'travel_date_cell' => 'R5',
				'last_cell' => 'P',
				'max_lodging_budget_cell' => 'R20',
				'project_number_cell' => 'S20',
			];
		} else if($total_days > 14 && $total_days <= 21) {
			$excel_config = [
				'file_name' => 'ea_report_3_minggu.xlsx',
				'date_submitted_cell' => 'U5',
				'travel_date_cell' => 'Y5',
				'last_cell' => 'W',
				'max_lodging_budget_cell' => 'Y20',
				'project_number_cell' => 'Z20',
			];
		} else {
			$excel_config = [
				'file_name' => 'ea_report_4_minggu.xlsx',
				'date_submitted_cell' => 'AB5',
				'travel_date_cell' => 'AF5',
				'last_cell' => 'AD',
				'max_lodging_budget_cell' => 'AF20',
				'project_number_cell' => 'AG20',
			];
		}
		$inputFileName = FCPATH.'assets/excel/' . $excel_config['file_name'];
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		$spreadsheet = $reader->load($inputFileName);
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('B5', 'Name: ' . $detail['requestor_name']);
		$sheet->setCellValue($excel_config['date_submitted_cell'], $detail['submitted_at']);
		$sheet->setCellValue($excel_config['travel_date_cell'], $detail['departure_date'] . ' - ' . $detail['return_date']);
		$sheet->setCellValue($excel_config['max_lodging_budget_cell'], $total_max_lodging_budget);
		$total_dest = count($detail['destinations']);
		
		// 1st Destinations
		$dest1 = $detail['destinations'][0];
		$project_number = $dest1['project_number'];
		$sheet->setCellValue($excel_config['project_number_cell'], " $project_number");
		$sheet->setCellValue('C39', " $project_number");
		$dest1Row = get_destination_row($dest1['arrival_date']);
		$sheet->setCellValue($dest1Row . '8', $dest1['arriv_date']);
		$sheet->setCellValue($dest1Row . '9', $dest1['city']);
		$sheet->setCellValue($dest1Row . '20', $dest1['actual_lodging'][0]['cost']);
		$sheet->setCellValue($dest1Row . '21', $dest1['max_meals_cost']);
		$row = $dest1Row;
		$arriv_date = strtotime($dest1['arrival_date']);
		$depar_date = strtotime($dest1['departure_date']);
		$datediff = $depar_date - $arriv_date;
		$days = ($datediff / (60 * 60 * 24));
		$day = 0;
		for ($x = 0; $x <= $days; $x++) {
			$next_day = strtotime("+$day day", strtotime($dest1['arrival_date']));
			$sheet->setCellValue($row . '9', $dest1['city']);
			$sheet->setCellValue($row . '8', date('d/M/y', $next_day));
			if($row == $excel_config['last_cell']) {
				$row = 'B';
			}
			$day++;
			$row++;
		}
		if(!empty($dest1['other_items'][0])) {
			$current_night_items = $this->report->get_excel_other_items_by_night($dest1['id'], 1);
			$other_items_cell = $this->get_other_items_cell($current_night_items, $dest1Row);
			foreach($other_items_cell as $item) {
				$sheet->setCellValue($item['cell'],  $item['value']);
			}
		}
		$last_row = '';
		if($dest1['night'] > 1) {
			$lodging_meals_row = $dest1Row;
			$night = 1;
			for ($x = 0; $x < $dest1['night']; $x++) {
				$sheet->setCellValue($lodging_meals_row . '20', $dest1['actual_lodging'][$x]['cost']);
				$sheet->setCellValue($lodging_meals_row . '21', $dest1['max_meals_cost']);
				$current_night_items = $this->report->get_excel_other_items_by_night($dest1['id'], $night++);
				$other_items_cell = $this->get_other_items_cell($current_night_items, $lodging_meals_row);
				foreach($other_items_cell as $item) {
					$sheet->setCellValue($item['cell'],  $item['value']);
				}
				if($lodging_meals_row == $excel_config['last_cell']) {
					$lodging_meals_row = 'B';
				}
				$lodging_meals_row++;
				$last_row = $lodging_meals_row;
			}	
		}

		if($total_dest > 1 ) {
			for($z=1; $z < $total_dest; $z++) {
				$dest = $detail['destinations'][$z];
				$destRow = $last_row;
				$last_depar_date = strtotime($detail['destinations'][$z-1]['departure_date']);
				$current_arriv_date = strtotime($dest['arrival_date']);
				$diff = $current_arriv_date - $last_depar_date;
				$day_diffs = ($diff / (60 * 60 * 24));
				$day = 0;
				for ($v = 0; $v < $day_diffs; $v++) {
					$destRow++;
				}
				if($detail['destinations'][$z - 1]['departure_date'] == $dest['arrival_date']) {
					$sheet->setCellValue($destRow . '12', $dest['city']);
					$day = 1;
					$lodging_meals_row = $destRow;
					$destRow++;
				} else {
					$lodging_meals_row = $destRow;
				}
				$sheet->setCellValue($destRow . '8', $dest['arriv_date']);
				$sheet->setCellValue($destRow . '9', $dest['city']);
				$sheet->setCellValue($destRow . '20', $dest['actual_lodging'][0]['cost']);
				$sheet->setCellValue($destRow . '21', $dest['max_meals_cost']);
				$row = $destRow;
				$arriv_date = strtotime($dest['arrival_date']);
				$depar_date = strtotime($dest['departure_date']);
				$datediff = $depar_date - $arriv_date;
				$days = ($datediff / (60 * 60 * 24));
				$x = 1;
				if($days == 1) {
					$x = 0;
				} 
				if($total_dest == $z + 1 && $detail['destinations'][$z - 1]['departure_date'] != $dest['arrival_date']) {
					$x = 0;
				}
				for ($x; $x <= $days; $x++) {
					$next_day = strtotime("+$day day", strtotime($dest['arrival_date']));
					$sheet->setCellValue($row . '9', $dest['city']);
					$sheet->setCellValue($row . '8', date('d/M/y', $next_day));
					if($row == $excel_config['last_cell']) {
						$row = 'B';
					}
					$day++;
					$last_row = $row;
					$row++;
				}
				if(!empty($dest['other_items'][0])) {
					$current_night_items = $this->report->get_excel_other_items_by_night($dest['id'], 1);
					$other_items_cell = $this->get_other_items_cell($current_night_items, $destRow);
					foreach($other_items_cell as $item) {
						$sheet->setCellValue($item['cell'], $item['value']);
					}
				}
				if($dest['night'] > 1) {
					$night = 1;
					for ($x = 0; $x < $dest['night']; $x++) {
						if($lodging_meals_row == 'B') {
							$lodging_meals_row = $excel_config['last_cell'];
							if($detail['destinations'][$z - 1]['departure_date'] == $dest['arrival_date']) {
								$sheet->setCellValue($lodging_meals_row . '12', $dest['city']);
							}
						}
						$sheet->setCellValue($lodging_meals_row . '20', $dest['actual_lodging'][$x]['cost']);
						$sheet->setCellValue($lodging_meals_row . '21', $dest['max_meals_cost']);
						$current_night_items = $this->report->get_excel_other_items_by_night($dest['id'], $night++);
						$other_items_cell = $this->get_other_items_cell($current_night_items, $lodging_meals_row);
						foreach($other_items_cell as $item) {
							$sheet->setCellValue($item['cell'],  $item['value']);
						}
						if($lodging_meals_row == $excel_config['last_cell']) {
							$lodging_meals_row = 'B';
						}
						$lodging_meals_row++;
					}	
				}
			}
		}

		// Signature
		$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		$drawing->setName('Traveler signature');
		$signature = $this->extractImageFromAPI($detail['requestor_signature']);
		$drawing->setPath($signature['image_path']); // put your path and image here
		$drawing->setCoordinates('C34');
		$drawing->setHeight(35);
		$drawing->setOffsetY(-15);
		$drawing->setWorksheet($spreadsheet->getActiveSheet());

		if($detail['head_of_units_status'] == 2) {
			$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing->setName('Supervisor signature');
			$signature = $this->extractImageFromAPI($detail['head_of_units_signature']);
			$drawing->setPath($signature['image_path']); // put your path and image here
			$drawing->setCoordinates('G34');
			$drawing->setHeight(35);
			$drawing->setOffsetY(-15);
			$drawing->setWorksheet($spreadsheet->getActiveSheet());
		}
		
		if($detail['country_director_status'] == 2) {
			$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing->setName('Country Director signature');
			$signature = $this->extractImageFromAPI($detail['country_director_signature']);
			$drawing->setPath($signature['image_path']); // put your path and image here
			$drawing->setCoordinates('L34');
			$drawing->setHeight(35);
			$drawing->setOffsetY(-15);
			$drawing->setWorksheet($spreadsheet->getActiveSheet());
		}

		$writer = new Xlsx($spreadsheet);
		$ea_number = $detail['ea_number'];
        $current_time = date('d-m-Y h:i:s');
        $filename = "$ea_number Report_Form/$current_time";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=$filename.xlsx");
        $writer->save('php://output');
		$this->delete_signature();
	}

	private function get_other_items_cell($items, $row) {
		$cells = [];
		$total_parking = $total_ticket_cost = $total_mileage = $total_airport = $total_visa = $total_rental = $total_registration = $total_communication = $total_internet = $total_taxi_home = $total_taxi_hotel = $total_other = 0;
		$meals_text = '';
		$meals_cost = 0;
		foreach($items as $item) {
			$item_name = $item['item_name'];
			if($item_name == 'Parking') {
				$total_parking += $item['cost'];
			}
			if($item_name == 'Ticket Cost') {
				$total_ticket_cost += $item['cost'];				
			}
			if($item_name == 'Mileage') {
				$total_mileage += $item['cost'];				
			}
			if($item_name == 'Airport Tax') {
				$total_airport += $item['cost'];				
			}
			if($item_name == 'Visa Fee') {
				$total_visa += $item['cost'];				
			}
			if($item_name == 'Auto Rental') {
				$total_rental += $item['cost'];				
			}
			if($item_name == 'Registration') {
				$total_registration += $item['cost'];				
			}
			if($item_name == 'Communication') {
				$total_communication += $item['cost'];				
			}
			if($item_name == 'Internet Charges') {
				$total_internet += $item['cost'];				
			}
			if($item_name == 'Taxi (Home to hotel)') {
				$total_taxi_home += $item['cost'];				
			}
			if($item_name == 'Taxi (Hotel to home)') {
				$total_taxi_hotel += $item['cost'];				
			}
			if($item_name == 'Other') {
				$total_other += $item['cost'];				
			}
			if($item_name == 'List meals') {
				$meals_text = $item['meals_text'];				
				$meals_cost = $item['cost'];				
			}
		}
		if($total_ticket_cost != 0) {
			array_push($cells, ['cell' => $row . '15', 'value' => $total_ticket_cost]);
		}
		if($total_mileage != 0) {
			array_push($cells, ['cell' => $row . '16', 'value' => $total_mileage]);
		}
		if($total_parking != 0) {
			array_push($cells, ['cell' => $row . '17', 'value' => $total_parking]);
		}
		if($total_airport != 0) {
			array_push($cells, ['cell' => $row . '18', 'value' => $total_airport]);
		}
		if($total_visa != 0) {
			array_push($cells, ['cell' => $row . '19', 'value' => $total_visa]);
		}
		if($total_rental != 0) {
			array_push($cells, ['cell' => $row . '23', 'value' => $total_rental]);
		}
		if($total_registration != 0) {
			array_push($cells, ['cell' => $row . '24', 'value' => $total_registration]);
		}
		if($total_communication != 0) {
			array_push($cells, ['cell' => $row . '25', 'value' => $total_communication]);
		}
		if($total_internet != 0) {
			array_push($cells, ['cell' => $row . '26', 'value' => $total_internet]);
		}
		if($total_taxi_home != 0) {
			array_push($cells, ['cell' => $row . '27', 'value' => $total_taxi_home]);
		}
		if($total_taxi_hotel != 0) {
			array_push($cells, ['cell' => $row . '28', 'value' => $total_taxi_hotel]);
		}
		if($total_other != 0) {
			array_push($cells, ['cell' => $row . '29', 'value' => $total_other]);
		}
		if($meals_text != '') {
			array_push($cells, ['cell' => $row . '22', 'value' => $meals_text]);
			array_push($cells, ['cell' => $row . '21', 'value' => $meals_cost]);
		}
		return $cells;
	}

	public function submit_report() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$req_id = $this->input->post('req_id');
			$head_of_units = $this->db->select('head_of_units_id')->from('ea_requests_status')->where('request_id', $req_id)->get()->row_array();
			$email_sent = $this->send_ter_to_head_of_units($req_id);
			if($email_sent) {
				$payload = [
					'request_id' => $req_id,
					'head_of_units_id' => $head_of_units['head_of_units_id'],
					'head_of_units_status' => 1,
					'submitted_at' =>  date("Y-m-d H:i:s"),
				];
				$already_reported = $this->report->get_report_status($req_id);
				if($already_reported) {
					$reported = $this->report->resubmit_report($req_id);
				} else {
					$reported = $this->report->submit_report($payload);
				}
				if($reported) {
					$response['success'] = true;
					$response['message'] = 'Report has been submitted and email has been sent!';
					$status_code = 200;
				} else {
					$response['success'] = false;
					$response['message'] = 'Something wrong, please try again later';
					$status_code = 400;
				}
			} else {
				$response['success'] = false;
				$response['message'] = 'Something wrong, please try again later';
				$status_code = 400;
			}
			$this->send_json($response, $status_code);
		} else {
			exit('No direct script access allowed');
		}
	}

	public function send_ter_to_head_of_units($request_id) {
		$this->load->library('Phpmailer_library');
        $mail = $this->phpmailer_library->load();
        $mail->isSMTP();
		$email_config = $this->config->item('email');
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $email_config['host'];
        $mail->Port = 465;
        $mail->SMTPDebug = 0; 
        $mail->SMTPAuth = true;
        $mail->Username = $email_config['username'];
        $mail->Password = $email_config['password'];
		$detail = $this->request->get_request_by_id($request_id);
		$requestor = $this->request->get_requestor_data($detail['requestor_id']);
		$approver_name = $detail['head_of_units_name'];
		$enc_req_id = encrypt($detail['r_id']);
		$approver_id = $detail['head_of_units_id'];

		$data['preview'] = '<p>You have TER #EA-'.$detail['r_id'].' from <b>'.$requestor['username'].'</b> and it need your review. Please check on attachment</p>';
        
        $data['content'] = '
            <tr>
                <td>
                    <p>Hi <b>'.$approver_name.'</b>,</p>
                    <p>'.$data['preview'].'</p>

					<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                        <tbody>
                        <tr>
                            <td align="left">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
									<td> <a href="'.base_url('ea_report/report_confirmation').'?req_id='.$enc_req_id.'&approver_id='.$approver_id.'&status=2&level=head_of_units" target="_blank">APPROVE</a> </td>
                                </tr>
                                </tbody>
                            </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
					
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-danger">
                        <tbody>
                        <tr>
                            <td align="left">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
									<td> <a <a href="'.base_url('ea_report/report_confirmation').'?req_id='.$enc_req_id.'&approver_id='.$approver_id.'&status=3&level=head_of_units" target="_blank">REJECT</a> </td>
                                </tr>
                                </tbody>
                            </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>

					<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-detail">
						<tbody>
							<tr>
								<td align="left">
								<table role="presentation" border="0" cellpadding="0" cellspacing="0">
									<tbody>
									<tr>
										<td> <a <a href="'.base_url('ea_report/report_confirmation/ter_form/'). $request_id . '" target="_blank">DOWNLOAD TER FORM</a> </td>
									</tr>
									</tbody>
								</table>
								</td>
							</tr>
						</tbody>
					 </table>

                    
                </td>
            </tr>';

        $text = $this->load->view('template/email', $data, true);
        $mail->setFrom('no-reply@faster.bantuanteknis.id', 'FASTER-FHI360');
        $mail->addAddress($detail['head_of_units_email']);
        $mail->Subject = "TER";
        $mail->isHTML(true);
        $mail->Body = $text;
        $sent=$mail->send();

		if ($sent) {
			return true;
		} else {
			return false;
		}
	}
}
