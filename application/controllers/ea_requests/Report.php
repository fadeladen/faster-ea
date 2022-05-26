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

	public function excel_report($id) {
		$this->load->helper('report');
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
		$sheet->setCellValue($dest1Row . '20', $dest1['actual_lodging']);
		$sheet->setCellValue($dest1Row . '21', $dest1['actual_meals']);
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
		if($dest1['night'] > 1) {
			$lodging_meals_row = $dest1Row;
			for ($x = 0; $x < $dest1['night']; $x++) {
				$sheet->setCellValue($lodging_meals_row . '20', $dest1['actual_lodging']);
				$sheet->setCellValue($lodging_meals_row . '21', $dest1['actual_meals']);
				if($lodging_meals_row == 'I') {
					$lodging_meals_row = 'B';
				}
				$lodging_meals_row++;
			}	
		}
		if(!empty($dest1['other_items'])) {
			$other_items = $this->get_other_items_cell($dest1['other_items'], $dest1Row);
			foreach($other_items as $item) {
				$sheet->setCellValue($item['cell'],  $item['value']);
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
			$sheet->setCellValue($destRow . '20', $dest['actual_lodging']);
			$sheet->setCellValue($destRow . '21', $dest['actual_meals']);
			$row = $destRow;
			$arriv_date = strtotime($dest['arrival_date']);
			$depar_date = strtotime($dest['departure_date']);
			$datediff = $depar_date - $arriv_date;
			$days = ($datediff / (60 * 60 * 24));
			for ($x = 1; $x <= $days; $x++) {
				$next_day = strtotime("+$day day", strtotime($dest['arrival_date']));
				$sheet->setCellValue($row . '9', $dest['city']);
				$sheet->setCellValue($row . '8', date('d/M/y', $next_day));
				if($row == 'I') {
					$row = 'B';
				}
				$day++;
				$row++;
			}
			if($dest['night'] > 1) {
				$lodging_meals_row = $destRow;
				for ($x = 0; $x < $dest['night']; $x++) {
					$sheet->setCellValue($lodging_meals_row . '20', $dest['actual_lodging']);
					$sheet->setCellValue($lodging_meals_row . '21', $dest['actual_meals']);
					if($lodging_meals_row == 'I') {
						$lodging_meals_row = 'B';
					}
					$lodging_meals_row++;
				}	
			}
			if(!empty($dest['other_items'])) {
				$other_items = $this->get_other_items_cell($dest['other_items'], $destRow);
				foreach($other_items as $item) {
					$sheet->setCellValue($item['cell'],  $item['value']);
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
			$sheet->setCellValue($destRow . '20', $dest['actual_lodging']);
			$sheet->setCellValue($destRow . '21', $dest['actual_meals']);
			$row = $destRow;
			$arriv_date = strtotime($dest['arrival_date']);
			$depar_date = strtotime($dest['departure_date']);
			$datediff = $depar_date - $arriv_date;
			$days = ($datediff / (60 * 60 * 24));
			for ($x = 1; $x <= $days; $x++) {
				$next_day = strtotime("+$day day", strtotime($dest['arrival_date']));
				$sheet->setCellValue($row . '9', $dest['city']);
				$sheet->setCellValue($row . '8', date('d/M/y', $next_day));
				if($row == 'I') {
					$row = 'B';
				}
				$day++;
				$row++;
			}
			if($dest['night'] > 1) {
				$lodging_meals_row = $destRow;
				for ($x = 0; $x < $dest['night']; $x++) {
					$sheet->setCellValue($lodging_meals_row . '20', $dest['actual_lodging']);
					$sheet->setCellValue($lodging_meals_row . '21', $dest['actual_meals']);
					if($lodging_meals_row == 'I') {
						$lodging_meals_row = 'B';
					}
					$lodging_meals_row++;
				}	
			}
			if(!empty($dest['other_items'])) {
				$other_items = $this->get_other_items_cell($dest['other_items'], $destRow);
				foreach($other_items as $item) {
					$sheet->setCellValue($item['cell'],  $item['value']);
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
		// $this->delete_signature();
	}

	private function get_other_items_cell($items, $row) {
		$cells = [];
		foreach($items as $item) {
			$item_name = $item['item'];
			if($item_name == 'Ticket Cost') {
				array_push($cells, ['cell' => $row . '15', 'value' => $item['cost']]);
			}
			if($item_name == 'Mileage') {
				array_push($cells, ['cell' => $row . '16', 'value' => $item['cost']]);
			}
			if($item_name == 'Parking') {
				array_push($cells, ['cell' => $row . '17', 'value' => $item['cost']]);
			}
			if($item_name == 'Airport Tax') {
				array_push($cells, ['cell' => $row . '18', 'value' => $item['cost']]);
			}
			if($item_name == 'Visa Fee') {
				array_push($cells, ['cell' => $row . '19', 'value' => $item['cost']]);
			}
			if($item_name == 'Auto Rental') {
				array_push($cells, ['cell' => $row . '23', 'value' => $item['cost']]);
			}
			if($item_name == 'Registration') {
				array_push($cells, ['cell' => $row . '24', 'value' => $item['cost']]);
			}
			if($item_name == 'Communication') {
				array_push($cells, ['cell' => $row . '25', 'value' => $item['cost']]);
			}
			if($item_name == 'Internet Charges') {
				array_push($cells, ['cell' => $row . '26', 'value' => $item['cost']]);
			}
			if($item_name == 'Taxi (Home to hotel)') {
				array_push($cells, ['cell' => $row . '27', 'value' => $item['cost']]);
			}
			if($item_name == 'Taxi (Hotel to home)') {
				array_push($cells, ['cell' => $row . '28', 'value' => $item['cost']]);
			}
			if($item_name == 'Other') {
				array_push($cells, ['cell' => $row . '29', 'value' => $item['cost']]);
			}
		}
		return $cells;
	}
}
