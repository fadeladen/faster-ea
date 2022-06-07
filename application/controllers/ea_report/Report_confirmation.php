<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Spipu\Html2Pdf\Html2Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Report_Confirmation extends CI_Controller {


    function __construct()
	{
		parent::__construct();
		$this->load->model('Report_Model', 'report');
		$this->load->model('Request_Model', 'request');
		$this->load->model('Base_Model', 'base_model');
		$this->template->set_default_layout('layouts/blank');
		$this->load->helper('report');
	}

	public function index()
	{   
        $req_id = decrypt($this->input->get('req_id'));
        $approver_id = $this->input->get('approver_id');
        $status = $this->input->get('status');
        $level = $this->input->get('level');
		if(is_expired_report($req_id, $level)) {
			$this->template->render('ea_report/report_confirmation/expired_request');
		} else {
			if($status == 3) {
				$data = [
					'req_id' => $req_id,
					'approver_id' => $approver_id,
					'status' => $status,
					'level' => $level,
				];
				$this->template->render('ea_report/report_confirmation/rejecting', $data);
			} else {
				$updated = $this->report->update_status($req_id, $approver_id, $status, $level);
				if($updated) {
					if ($level == 'country_director') {
						$country_director = $this->base_model->get_country_director();
						$finance_teams = $this->base_model->get_finance_teams();
						foreach($finance_teams as $user) {
							$email_sent = $this->send_email_to_finance_teams($req_id, $country_director['username'], $user);
						}
					} else {
						$email_sent = $this->send_email_to_country_director($req_id);
					}
					if($email_sent) {
						$data['message'] = "TER #EA$req_id has been approved";
						$this->delete_signature();
					} else {
						$data['message'] = "Something wrong, please try again later";
						$this->report->update_status($req_id, $approver_id, 1, $level);
					}
				} else {
					$data['message'] = "Something wrong, please try again later";
				}
				$this->template->render('ea_report/report_confirmation/index', $data);
			}
		}
	}

    private function send_rejected_ter($req_id, $level) {
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
		$detail = $this->request->get_request_by_id($req_id);
		$requestor = $this->request->get_requestor_data($detail['requestor_id']);
		$report_status = $this->report->get_report_status($req_id);
		$enc_req_id = encrypt($detail['r_id']);
        if($level == 'head_of_units') {
            $rejected_by = $detail['head_of_units_name'];
        } else if($level == 'country_director') {
			$country_director = $this->base_model->get_country_director();
            $rejected_by = $country_director['username'];
        } else if($level == 'finance') {
            $rejected_by = 'Finance Team';
        }

		$data['preview'] = '<p>Your TER #EA-'.$detail['r_id'].' has been rejected by '.$rejected_by.'</p>
		<p style="margin-bottom: 2px;">Rejected reason:</p>
		<p><b>'.$report_status['rejected_reason'].'</b></p>';
        $data['content'] = '
                    <p>Dear '.$requestor['username'].',</p> 
                    <p>'.$data['preview'].'</p>
					<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-detail">
						 <tbody>
						 <tr>
							 <td align="left">
							 <table role="presentation" border="0" cellpadding="0" cellspacing="0">
								 <tbody>
								 <tr>
									 <td> <a href="'.base_url('ea_report/outgoing/ter_detail').'/'.$enc_req_id.'" target="_blank">DETAIL</a> </td>
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
										<td> <a <a href="'.base_url('ea_report/report_confirmation/ter_form/'). $req_id . '" target="_blank">DOWNLOAD TER FORM</a> </td>
									</tr>
									</tbody>
								</table>
								</td>
							</tr>
						</tbody>
					 </table>
                    ';

        $text = $this->load->view('template/email', $data, true);
        $mail->setFrom('no-reply@faster.bantuanteknis.id', 'FASTER-FHI360');
        $mail->addAddress($requestor['email']);
        $mail->Subject = "Rejected TER";
        $mail->isHTML(true);
        $mail->Body = $text;
        $sent=$mail->send();

		if ($sent) {
			return true;
		} else {
			return false;
		}
    }

	public function rejecting() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('rejected_reason', 'Reason', 'required');
			if ($this->form_validation->run()) {
				$req_id =  $this->input->post('id');
				$approver_id =  $this->input->post('approver_id');
				$status =  $this->input->post('status');
				$level =  $this->input->post('level');
				$rejected_reason =  $this->input->post('rejected_reason');
				$updated = $this->report->update_status($req_id, $approver_id, $status, $level, $rejected_reason);
				if($updated) {
					$email_sent = $this->send_rejected_ter($req_id, $level);
					if($email_sent) {
						$response['success'] = true;
						$response['message'] = 'Request has been rejected and email has been sent';
						$status_code = 200;
					} else {
						$response['success'] = false;
						$response['message'] = 'Failed to send email, please check your connection!';
						$status_code = 400;
						$this->report->update_status($req_id, $approver_id, 1, $level);
					}
				} else {
					$response['success'] = false;
					$response['message'] = 'Something wrong, please try again later';
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

    private function send_email_to_country_director($req_id) {
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
		$detail = $this->request->get_request_by_id($req_id);
		// $report_status = $this->report->get_report_status($req_id);
		// $country_director = $this->db->select('id, username, email')->from('tb_userapp')->where('id', $report_status['country_director_id'])->get()->row_array();
		$country_director = $this->base_model->get_country_director();
		$enc_req_id = encrypt($detail['r_id']);
		$mail->setFrom('no-reply@faster.bantuanteknis.id', 'FASTER-FHI360');
		$data['preview'] = '<p>TER #EA-'.$detail['r_id'].' has been approved by '.$detail['head_of_units_name'].' and it need your review</p>
		 ';
		 $data['content'] = '
					 <p>Hi '.$country_director['username'].',</p> 
					 <p>'.$data['preview'].'</p>
					 <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                        <tbody>
                        <tr>
                            <td align="left">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
									<td> <a href="'.base_url('ea_report/report_confirmation').'?req_id='.$enc_req_id.'&approver_id='.$country_director['id'].'&status=2&level=country_director" target="_blank">APPROVE</a> </td>
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
									<td> <a <a href="'.base_url('ea_report/report_confirmation').'?req_id='.$enc_req_id.'&approver_id='.$country_director['id'].'&status=3&level=country_director" target="_blank">REJECT</a> </td>
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
										<td> <a <a href="'.base_url('ea_report/report_confirmation/ter_form/'). $req_id . '" target="_blank">DOWNLOAD TER FORM</a> </td>
									</tr>
									</tbody>
								</table>
								</td>
							</tr>
						</tbody>
					 </table>
					 ';
		$text = $this->load->view('template/email', $data, true);
		$mail->addAddress($country_director['email']);
        $mail->Subject = "Approved TER";
        $mail->isHTML(true);
        $mail->Body = $text;
		$sent = $mail->send();
		if ($sent) {
			return true;
		} else {
			return false;
		}
    }

    private function send_email_to_finance_teams($req_id, $approver_name, $user) {
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
		$detail = $this->request->get_request_by_id($req_id);
		$requestor = $this->request->get_requestor_data($detail['requestor_id']);
		$enc_req_id = encrypt($detail['r_id']);
		$mail->setFrom('no-reply@faster.bantuanteknis.id', 'FASTER-FHI360');
		$data['preview'] = '<p>You have TER coming #EA-'.$detail['r_id'].' from '.$requestor['username'].' and has been approved by '.$approver_name.'</p>
						 <p>Please review and process it, check on following details</p>
		 ';
		 $data['content'] = '
					 <p>Dear '.$user['username'].',</p> 
					 <p>'.$data['preview'].'</p>
					 <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-detail">
						 <tbody>
						 <tr>
							 <td align="left">
							 <table role="presentation" border="0" cellpadding="0" cellspacing="0">
								 <tbody>
								 <tr>
									 <td> <a href="'.base_url('ea_report/outgoing/ter_detail').'/'.$enc_req_id.'" target="_blank">DETAIL/RECEIPT</a> </td>
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
										<td> <a <a href="'.base_url('ea_report/report_confirmation/ter_form/'). $req_id . '" target="_blank">DOWNLOAD TER FORM</a> </td>
									</tr>
									</tbody>
								</table>
								</td>
							</tr>
						</tbody>
					 </table>
					 ';
		$text = $this->load->view('template/email', $data, true);
		$mail->addAddress($user['email']);
        $mail->Subject = "Approved TER";
        $mail->isHTML(true);
        $mail->Body = $text;
		$sent = $mail->send();
		if ($sent) {
			return true;
		} else {
			return false;
		}
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
				$sheet->setCellValue($lodging_meals_row . '21', $dest1['actual_meals'][$x]['cost']);
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
				$day = 0;
				if($detail['destinations'][$z - 1]['departure_date'] == $dest['arrival_date']) {
					$lodging_meals_row = $destRow;
					$sheet->setCellValue($destRow . '12', $dest['city']);
					$destRow++;
					$day = 1;
				} else {
					$destRow++;
					$lodging_meals_row = $destRow;
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
				if($detail['destinations'][$z - 1]['departure_date'] != $dest['arrival_date']) {
					$days++;
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
						$sheet->setCellValue($lodging_meals_row . '20', $dest['actual_lodging'][$x]['cost']);
						$sheet->setCellValue($lodging_meals_row . '21', $dest['actual_meals'][$x]['cost']);
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
		}
		return $cells;
	}

    private function delete_signature() {
		$this->load->helper('file');
		$path = FCPATH . 'uploads/excel_signature';
		delete_files($path, TRUE); 
	}

    private function send_json($data, $status_code = 200) {
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($status_code)
            ->set_output(json_encode(array_merge($data, ['code' => $status_code])));
    }

    private function extractImageFromAPI($filename) {
		$token = $_ENV['ASSETS_TOKEN'];
		$file_url = $_ENV['ASSETS_URL'] . "$filename?subfolder=signatures&token=$token";
		$path = pathinfo($file_url);
		if (!is_dir('uploads/excel_signature')) {
			mkdir('./uploads/excel_signature', 0777, TRUE);
		
		}
        $imageTargetPath = 'uploads/excel_signature/' . time() . $filename;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $file_url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // <-- important to specify
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // <-- important to specify
        $resultImage = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpCode == 404) {
			$imageInfo["image_name"] = 'signature_not_found.jpg';
			$imageInfo["image_path"] = FCPATH . 'assets/images/signature_not_found.jpg';
		} else {
			$fp = fopen($imageTargetPath, 'wb');
			fwrite($fp, $resultImage);
			fclose($fp);
			$imageInfo["image_name"] = $path['basename'];
			$imageInfo["image_path"] = $imageTargetPath;
		}
        
        return $imageInfo;
	}

}
