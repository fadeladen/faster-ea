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

    public function reporting($id = null)
	{
		$id = decrypt($id);
		$detail = $this->report->get_report_data_by_id($id);
		if($detail) {
			$requestor_data = $this->request->get_requestor_data($detail['requestor_id']);
			$this->load->helper('report');
			// $reporting_is_finished = reporting_is_finished($detail);
			$data = [
				'detail' => $detail,
				'requestor_data' => $requestor_data,
				'reporting_is_finished' => true,
			];
			$this->template->set('page', 'Reporting #' . $detail['ea_number']);
			$this->template->render('ea_report/outgoing/reporting', $data);
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
		$night = $this->input->get('night');
		$item_id = $this->input->get('item_id');
		$max_budget = $this->input->get('max_budget') + 0;
		$current_lodging = $this->input->get('current_lodging_budget');
		$current_meals = $this->input->get('current_meals_budget');
		$item_type = $this->input->get('item_type');
		$max_budget = $this->report->get_dest_max_budget($dest_id);
		$max_lodging_budget = $max_budget['max_lodging_budget'] + 0;
		$data = [
			'dest_id' => $dest_id,
			'item_type' => $item_type,
			'night' => $night,
			'max_budget' => $max_budget,
			'max_lodging_budget' => $max_lodging_budget,
			'current_budget' => $current_lodging + $current_meals,
			'current_lodging' => $current_lodging,
			'current_meals' => $current_meals,
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

	public function edit_items_modal() {
		$item_id = $this->input->get('item_id');	
		$detail = $this->report->get_items_detail($item_id);	
		$data = [
			'detail' => $detail,
		];
		$this->load->view('ea_report/modal/edit_items', $data);
	}

	public function insert_actual_costs() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('cost', 'Actual cost', 'required');
			$item_type = $this->input->post('item_type');
			if($item_type == 3) {
				$this->form_validation->set_rules('item_name', 'Items', 'required');
			}
			if ($this->input->post('method_') != 'PUT') {
				if (empty($_FILES['receipt']['name']))
				{
					$this->form_validation->set_rules('receipt', 'Receipt', 'required');
				}
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
					$updated = $this->report->update_actual_costs($item_id, $payload);
				} else {
					if($this->upload->do_upload('receipt')) {
						$receipt = $this->upload->data('file_name');
						$item_id = $this->input->post('item_id');
						$item_detail = $this->db->select('*')->from('ea_actual_costs')->where('id', $item_id)->get()->row_array();
						if($this->input->post('method_') == 'PUT') {
							$item_id = $this->input->post('item_id');
							$payload = [
								'cost' => $clean_actual_cost,
								'receipt' => $receipt,
							];
							unlink(FCPATH . 'uploads/ea_items_receipt/' . $item_detail['receipt']);
							$updated = $this->report->update_actual_costs($item_id, $payload);
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
							$updated = $this->report->insert_actual_costs($payload);
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

	public function excel_report($id) {
		$detail = $this->report->get_excel_report_by_id($id);
		$inputFileName = FCPATH.'assets/excel/ea_report.xlsx';
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		$spreadsheet = $reader->load($inputFileName);
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('B5', 'Name: ' . $detail['requestor_name']);
		$sheet->setCellValue('G5', date('d-M-y'));
		$sheet->setCellValue('K5', $detail['departure_date'] . ' - ' . $detail['return_date']);
		$total_dest = count($detail['destinations']);
		
		// 1st Destinations
		$dest1 = $detail['destinations'][0];
	
		$dest1Row = get_destination_row($dest1['arrival_date']);
		$sheet->setCellValue($dest1Row . '8', $dest1['arriv_date']);
		$sheet->setCellValue($dest1Row . '9', $dest1['city']);
		$sheet->setCellValue($dest1Row . '20', $dest1['actual_lodging'][0]['cost']);
		$sheet->setCellValue($dest1Row . '21', $dest1['actual_meals'][0]['cost']);
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
			if($row == 'I') {
				$row = 'B';
			}
			$day++;
			$row++;
		}
		if(!empty($dest1['other_items'][0])) {
			$current_night_items = $this->report->get_other_items_by_night($dest1['id'], 1);
			$other_items_cell = $this->get_other_items_cell($current_night_items, $dest1Row);
			foreach($other_items_cell as $item) {
				$sheet->setCellValue($item['cell'],  $item['value']);
			}
		}
		if($dest1['night'] > 1) {
			$lodging_meals_row = $dest1Row;
			$night = 1;
			for ($x = 0; $x < $dest1['night']; $x++) {
				$sheet->setCellValue($lodging_meals_row . '20', $dest1['actual_lodging'][$x]['cost']);
				$sheet->setCellValue($lodging_meals_row . '21', $dest1['actual_meals'][$x]['cost']);
				if($lodging_meals_row == 'I') {
					$lodging_meals_row = 'B';
				}
				$current_night_items = $this->report->get_other_items_by_night($dest1['id'], $night++);
				$other_items_cell = $this->get_other_items_cell($current_night_items, $lodging_meals_row);
				foreach($other_items_cell as $item) {
					$sheet->setCellValue($item['cell'],  $item['value']);
				}
				$lodging_meals_row++;
			}	
		}

		if($total_dest > 1 ) {
			// 2nd Destinations
			$dest = $detail['destinations'][1];
			$destRow = get_destination_row($dest['arrival_date']);
			$day = 0;
			if($detail['destinations'][0]['departure_date'] == $dest['arrival_date']) {
				$sheet->setCellValue($destRow . '12', $dest['city']);
				$destRow++;
				$day = 1;
			}
			$sheet->setCellValue($destRow . '8', $dest['arriv_date']);
			$sheet->setCellValue($destRow . '9', $dest['city']);
			$sheet->setCellValue($destRow . '20', $dest['actual_lodging'][0]['cost']);
			$sheet->setCellValue($destRow . '21', $dest['actual_meals'][0]['cost']);
			$row = $destRow;
			$arriv_date = strtotime($dest['arrival_date']);
			$depar_date = strtotime($dest['departure_date']);
			$datediff = $depar_date - $arriv_date;
			$days = ($datediff / (60 * 60 * 24));
			$x = 1;
			if($days == 1) {
				$x = 0;
			} 
			for ($x; $x <= $days; $x++) {
				$next_day = strtotime("+$day day", strtotime($dest['arrival_date']));
				$sheet->setCellValue($row . '9', $dest['city']);
				$sheet->setCellValue($row . '8', date('d/M/y', $next_day));
				if($row == 'I') {
					$row = 'B';
				}
				$day++;
				$row++;
			}
			if(!empty($dest['other_items'][0])) {
				$current_night_items = $this->report->get_other_items_by_night($dest['id'], 1);
				$other_items_cell = $this->get_other_items_cell($current_night_items, $destRow);
				// echo json_encode($current_night_items);
				foreach($other_items_cell as $item) {
					$sheet->setCellValue($item['cell'], $item['value']);
				}
			}
			if($dest['night'] > 1) {
				$lodging_meals_row = $destRow;
				$night = 1;
				for ($x = 0; $x < $dest['night']; $x++) {
					$sheet->setCellValue($lodging_meals_row . '20', $dest['actual_lodging'][$x]['cost']);
					$sheet->setCellValue($lodging_meals_row . '21', $dest['actual_meals'][$x]['cost']);
					if($lodging_meals_row == 'I') {
						$lodging_meals_row = 'B';
					}
					$current_night_items = $this->report->get_other_items_by_night($dest['id'], $night++);
					$other_items_cell = $this->get_other_items_cell($current_night_items, $lodging_meals_row);
					foreach($other_items_cell as $item) {
						$sheet->setCellValue($item['cell'],  $item['value']);
					}
					$lodging_meals_row++;
				}	
			}
		}

		if($total_dest > 2 ) {
			// 3rd Destinations
			$dest = $detail['destinations'][2];
			$destRow = get_destination_row($dest['arrival_date']);
			$day = 0;
			if($detail['destinations'][1]['departure_date'] == $dest['arrival_date']) {
				$sheet->setCellValue($destRow . '12', $dest['city']);
				$destRow++;
				$day = 1;
			}
			$sheet->setCellValue($destRow . '8', $dest['arriv_date']);
			$sheet->setCellValue($destRow . '9', $dest['city']);
			$sheet->setCellValue($destRow . '20', $dest['actual_lodging'][0]['cost']);
			$sheet->setCellValue($destRow . '21', $dest['actual_meals'][0]['cost']);
			$row = $destRow;
			$arriv_date = strtotime($dest['arrival_date']);
			$depar_date = strtotime($dest['departure_date']);
			$datediff = $depar_date - $arriv_date;
			$days = ($datediff / (60 * 60 * 24));
			$x = 1;
			if($days == 1) {
				$x = 0;
			} 
			for ($x; $x <= $days; $x++) {
				$next_day = strtotime("+$day day", strtotime($dest['arrival_date']));
				$sheet->setCellValue($row . '9', $dest['city']);
				$sheet->setCellValue($row . '8', date('d/M/y', $next_day));
				if($row == 'I') {
					$row = 'B';
				}
				$day++;
				$row++;
			}
			if(!empty($dest['other_items'][0])) {
				$current_night_items = $this->report->get_other_items_by_night($dest['id'], 1);
				$other_items_cell = $this->get_other_items_cell($current_night_items, $destRow);
				foreach($other_items_cell as $item) {
					$sheet->setCellValue($item['cell'],  $item['value']);
				}
			}
			if($dest['night'] > 1) {
				$lodging_meals_row = $destRow;
				$night = 1;
				for ($x = 0; $x < $dest['night']; $x++) {
					$sheet->setCellValue($lodging_meals_row . '20', $dest['actual_lodging'][$x]['cost']);
					$sheet->setCellValue($lodging_meals_row . '21', $dest['actual_meals'][$x]['cost']);
					if($lodging_meals_row == 'I') {
						$lodging_meals_row = 'B';
					}
					$current_night_items = $this->report->get_other_items_by_night($dest['id'], $night++);
					$other_items_cell = $this->get_other_items_cell($current_night_items, $lodging_meals_row);
					foreach($other_items_cell as $item) {
						$sheet->setCellValue($item['cell'],  $item['value']);
					}
					$lodging_meals_row++;
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

		$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		$drawing->setName('Supervisor signature');
		$signature = $this->extractImageFromAPI($detail['head_of_units_signature']);
		$drawing->setPath($signature['image_path']); // put your path and image here
		$drawing->setCoordinates('G34');
		$drawing->setHeight(35);
		$drawing->setOffsetY(-15);
		$drawing->setWorksheet($spreadsheet->getActiveSheet());

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
		return $cells;
	}
}
