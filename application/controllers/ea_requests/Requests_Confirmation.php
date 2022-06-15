<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Spipu\Html2Pdf\Html2Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Requests_Confirmation extends CI_Controller {


    function __construct()
	{
		parent::__construct();
		$this->load->model('Request_Model', 'request');
		$this->load->model('Base_Model', 'base_model');
		$this->template->set_default_layout('layouts/blank');
	}

	public function index()
	{   
        $req_id = decrypt($this->input->get('req_id'));
        $approver_id = $this->input->get('approver_id');
        $status = $this->input->get('status');
        $level = $this->input->get('level');
		if(is_expired_request($req_id, $level)) {
			$this->template->render('requests_confirmation/expired_request');
		} else {
			if($status == 3) {
				$data = [
					'req_id' => $req_id,
					'approver_id' => $approver_id,
					'status' => $status,
					'level' => $level,
				];
				$this->template->render('requests_confirmation/rejecting', $data);
			} else {
				$request_detail = $this->request->get_request_by_id($req_id);
				$updated = $this->request->update_status($req_id, $approver_id, $status, $level);
				if($updated) {
					if ($level == 'fco_monitor') {
						$fco = $this->base_model->get_fco_monitor();
						$finance_teams = $this->base_model->get_finance_teams();
						foreach($finance_teams as $user) {
							$email_sent = $this->send_email_to_finance_teams($req_id, $fco['username'], $user);
						}
					} else {
						if($level == 'head_of_units') {
							$ea_assosiate = $this->base_model->get_ea_assosiate();
							$target_level = 'ea_assosiate';
							$email_data = [
								'approver_name' => $request_detail['head_of_units_name'],
								'target_id' => $ea_assosiate['id'],
								'target_name' => $ea_assosiate['username'],
								'target_email' => $ea_assosiate['email'],
							];
						} else if ($level == 'ea_assosiate') {
							$fco_monitor = $this->base_model->get_fco_monitor();
							$target_level = 'fco_monitor';
							$email_data = [
								'approver_name' => $request_detail['ea_assosiate_name'],
								'target_id' => $fco_monitor['id'],
								'target_name' => $fco_monitor['username'],
								'target_email' => $fco_monitor['email'],
							];
						}
						$email_sent = $this->send_approved_request($req_id, $target_level, $email_data);
					}
					if($email_sent) {
						$data['message'] = "EA Requests #EA$req_id has been approved";
						$this->delete_signature();
					} else {
						$data['message'] = "Something wrong, please try again later";
						$this->request->update_status($req_id, $approver_id, 1, $level);
					}
				} else {
					$data['message'] = "Something wrong, please try again later";
				}
				$this->template->render('requests_confirmation/index', $data);
			}
		}
	}

    private function send_rejected_requests($req_id, $level) {
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
		$enc_req_id = encrypt($detail['r_id']);
		$requestor = $this->request->get_requestor_data($detail['requestor_id']);
        if($level == 'head_of_units') {
            $rejected_by = $detail['head_of_units_name'];
        } else if($level == 'ea_assosiate') {
            $rejected_by = $detail['ea_assosiate_name'];
        } else if($level == 'fco_monitor') {
            $rejected_by = $detail['fco_monitor_name'];
        } else if($level == 'finance') {
            $rejected_by = 'Finance Team';
        }

		$data['preview'] = '<p>Your EA Request #EA-'.$detail['r_id'].' has been rejected by '.$rejected_by.'</p>
		<p style="margin-bottom: 2px;">Rejected reason:</p>
		<p><b>'.$detail['rejected_reason'].'</b></p>';
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
                                    <td> <a href="'.base_url('ea_requests/outcoming-requests/detail').'/'.$enc_req_id.'" target="_blank">DETAILS</a> </td>
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
										<td> <a <a href="'.base_url('ea_requests/requests_confirmation/ea_form/'). $req_id . '" target="_blank">DOWNLOAD EA FORM</a> </td>
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
        // $excel = $this->attach_ea_form($req_id);
		// if(!empty($excel)) {
		// 	$mail->addAttachment($excel['path'], $excel['file_name']);
		// }
        $mail->Subject = "Rejected EA Request";
        $mail->isHTML(true);
        $mail->Body = $text;
        $sent=$mail->send();

		if ($sent) {
			$this->delete_signature();
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
				$updated = $this->request->update_status($req_id, $approver_id, $status, $level, $rejected_reason);
				if($updated) {
					$email_sent = $this->send_rejected_requests($req_id, $level);
					if($email_sent) {
						$response['success'] = true;
						$response['message'] = 'Request has been rejected and email has been sent';
						$status_code = 200;
					} else {
						$this->request->update_status($req_id, $approver_id, 1, $level);
						$response['success'] = false;
						$response['message'] = 'Something wrong, please try again later';
						$status_code = 400;
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

    private function send_approved_request($req_id, $level, $email_detail) {
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
		$enc_req_id = encrypt($detail['r_id']);

		$approve_btn = '<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
		<tbody>
		<tr>
			<td align="left">
			<table role="presentation" border="0" cellpadding="0" cellspacing="0">
				<tbody>
				<tr>
					<td> <a href="'.base_url('ea_requests/requests_confirmation').'?req_id='.$enc_req_id.'&approver_id='.$email_detail['target_id'].'&status=2&level='.$level.'" target="_blank">APPROVE</a> </td>
				</tr>
				</tbody>
			</table>
			</td>
		</tr>
		</tbody>
	</table>';
		if($level == 'ea_assosiate') {
			$approve_btn = '';
		}

		$data['preview'] = '<p>EA Request #EA-'.$detail['r_id'].' has been approved by '.$email_detail['approver_name'].'</p>
                             <p>Please review following requests</p>
             ';
        $data['content'] = '
                    <p>Dear '.$email_detail['target_name'].',</p> 
                    <p>'.$data['preview'].'</p>
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-detail">
                        <tbody>
                        <tr>
                            <td align="left">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
                                    <td> <a href="'.base_url('ea_requests/outcoming-requests/detail').'/'.$enc_req_id.'" target="_blank">DETAILS</a> </td>
                                </tr>
                                </tbody>
                            </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>

					'.$approve_btn.'
					
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-danger">
                        <tbody>
                        <tr>
                            <td align="left">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
									<td> <a <a href="'.base_url('ea_requests/requests_confirmation').'?req_id='.$enc_req_id.'&approver_id='.$email_detail['target_id'].'&status=3&level='.$level.'" target="_blank">REJECT</a> </td>
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
										<td> <a <a href="'.base_url('ea_requests/requests_confirmation/ea_form/'). $req_id . '" target="_blank">DOWNLOAD EA FORM</a> </td>
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
        $mail->addAddress($email_detail['target_email']);
        // $excel = $this->attach_ea_form($req_id);
		// if(!empty($excel)) {
		// 	$mail->addAttachment($excel['path'], $excel['file_name']);
		// }
        $mail->Subject = "EA Request";
        $mail->isHTML(true);
        $mail->Body = $text;
        $sent=$mail->send();

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
		$enc_req_id = encrypt($detail['r_id']);
		$mail->setFrom('no-reply@faster.bantuanteknis.id', 'FASTER-FHI360');
		$data['preview'] = '<p>EA Request #EA-'.$detail['r_id'].' has been approved by '.$approver_name.'</p>
						 <p>Please process payment request, check on following details</p>
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
									 <td> <a href="'.base_url('ea_requests/outcoming-requests/detail').'/'.$enc_req_id.'" target="_blank">DETAILS</a> </td>
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
									<td> <a <a href="'.base_url('ea_requests/requests_confirmation').'?req_id='.$enc_req_id.'&approver_id='.$user['id'].'&status=3&level=finance" target="_blank">REJECT</a> </td>
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
										<td> <a <a href="'.base_url('ea_requests/requests_confirmation/ea_form/'). $req_id . '" target="_blank">DOWNLOAD EA FORM</a> </td>
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
        // $payment_pdf = $this->attach_payment_request($req_id);
        // $excel = $this->attach_ea_form($req_id);
		// if(!empty($excel)) {
		// 	$mail->addAttachment($excel['path'], $excel['file_name']);
		// }
		// $mail->addStringAttachment($payment_pdf, 'Payment form request.pdf');
        $mail->Subject = "Approved EA Requests for review by Finance Teams";
        $mail->isHTML(true);
        $mail->Body = $text;
		$sent = $mail->send();
		if ($sent) {
			return true;
		} else {
			return false;
		}
    }

    private function attach_payment_request($req_id) {
        ob_start();
		$detail = $this->request->get_request_by_id($req_id);
		$requestor = $this->request->get_requestor_data($detail['requestor_id']);
		$requestor_signature = $this->extractImageFromAPI($requestor['signature']);
		$requestor_signature_path = $requestor_signature['image_path'];
		$fco_signature = $this->extractImageFromAPI($detail['fco_monitor_signature']);
		$fco_signature_path = $fco_signature['image_path'];
		$data = [
			'requestor' => $requestor,
			'detail' => $detail,
			'requestor_signature' => $requestor_signature_path,
			'fco_signature' => $fco_signature_path,
		];
		$content = $this->load->view('template/form_payment_reimburstment', $data, true);
        $html2pdf = new Html2Pdf('P', [210, 330], 'en', true, 'UTF-8', array(15, 10, 15, 10));
        $html2pdf->setDefaultFont('arial');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->setTestTdInOnePage(false);
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $pdf = $html2pdf->Output('Payment Request Form.pdf', 'S');
		return $pdf;
	}

    private function attach_ea_form($req_id) {

		$detail = $this->request->get_excel_data_by_id($req_id);
		$requestor = $this->request->get_requestor_data($detail['requestor_id']);
		$inputFileName = FCPATH.'assets/excel/ea_form.xlsx';
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		$spreadsheet = $reader->load($inputFileName);
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('C7', $requestor['username']);
		$sheet->setCellValue('AK2', 'EA No. ' . $detail['ea_number']);
		$sheet->setCellValue('AD6', $detail['request_date']);
		$sheet->setCellValue('AD8', $detail['originating_city']);
		$sheet->setCellValue('AD10', $detail['departure_date']);
		$sheet->setCellValue('AM10', $detail['return_date']);
		$sheet->setCellValue('AG13', $requestor['project_name']);
		$sheet->setCellValue('C10', $requestor['project_name']);
		$sheet->setCellValue('C16', $requestor['email']);
		$sheet->setCellValue('G17', 'Employee #' . $requestor['employee_id']);
		$sheet->setCellValue('AK15', $detail['ea_assosiate_name']);
		$sheet->setCellValue('AL16', '$' . $detail['max_budget_usd']);
		$sheet->setCellValue('C77', $detail['special_instructions']);
		$sheet->setCellValue('C18', 'X');
		if($detail['country_director_notified'] == 'Yes') {
			$sheet->setCellValue('X18', 'X');
		}

		if($detail['employment'] == 'On behalf') {
			if($detail['employment_status'] == 'Consultant') {
				$sheet->setCellValue('C21', 'X');
				$first_participants = $detail['participants'][0];
				$sheet->setCellValue('G20', $first_participants['title']. ' # ' . $first_participants['name']);
			} else {
				$sheet->setCellValue('C23', 'X');
				if($detail['employment_status'] == 'Other') {
					$first_participants = $detail['participants'][0];
					$other_text = $first_participants['title']. ' # ' . $first_participants['name'];
				} else {
					$other_text = 'Group: ' . $detail['participant_group_name'] . ' - Number of participants: ' . $detail['number_of_participants'];
				}
				$sheet->setCellValue('K23', $other_text);
			}
		}

		// Signature
		$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		$drawing->setName('Traveler signature');
		// $signature = $this->extractImage($requestor['signature']);
		$signature = $this->extractImageFromAPI($requestor['signature']);
		$drawing->setPath($signature['image_path']); // put your path and image here
		$drawing->setPath($signature['image_path']);
		$drawing->setCoordinates('I84');
		$drawing->setHeight(40);
		$drawing->setWorksheet($spreadsheet->getActiveSheet());

		if($detail['head_of_units_status'] == 2) {
			$drawing2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing2->setName('Head of units signature');
			// $signature = $this->extractImage($detail['head_of_units_signature']);
			$signature = $this->extractImageFromAPI($detail['head_of_units_signature']);
			$drawing2->setPath($signature['image_path']); 
			$drawing2->setCoordinates('I88');
			$drawing2->setHeight(35);
			$drawing2->setWorksheet($spreadsheet->getActiveSheet());

		} 

		if($detail['fco_monitor_status'] == 2) {
			$drawing4 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing4->setName('FCO signature');
			// $signature = $this->extractImage($detail['fco_monitor_signature']);
			$signature = $this->extractImageFromAPI($detail['fco_monitor_signature']);
			$drawing4->setPath($signature['image_path']);
			$drawing4->setCoordinates('V28');
			$drawing4->setHeight(35);
			$drawing4->setWorksheet($spreadsheet->getActiveSheet());

		} 

		if($detail['finance_status'] == 2) {
			$drawing5 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing5->setName('Finance signature');
			// $signature = $this->extractImage($detail['finance_signature']);
			$signature = $this->extractImageFromAPI($detail['finance_signature']);
			$drawing5->setPath($signature['image_path']);
			$drawing5->setCoordinates('AI88');
			$drawing5->setOffsetY(-15); 
			$drawing5->setHeight(50);
			$drawing5->setWorksheet($spreadsheet->getActiveSheet());
		} 

		$destinations= $detail['destinations'];
		// 1st destination
		$sheet->setCellValue('G26', $destinations[0]['city']);
		$sheet->setCellValue('P26', $destinations[0]['arriv_date']);
		$sheet->setCellValue('W26', $destinations[0]['depar_date']);
		$sheet->setCellValue('C29', $destinations[0]['project_number']);
		$sheet->setCellValue('AL28', $destinations[0]['lodging'] + 0);
		$sheet->setCellValue('AL30', $destinations[0]['meals'] + 0);
		$sheet->setCellValue('AL32', $destinations[0]['total_lodging_and_meals'] + 0);
		$sheet->setCellValue('AL34', $destinations[0]['night'] + 0);
		$sheet->setCellValue('AL36', $destinations[0]['total'] + 0);

		if(count($destinations) > 1) {
			// 2nd destination
			$sheet->setCellValue('G39', $destinations[1]['city']);
			$sheet->setCellValue('P39', $destinations[1]['arriv_date']);
			$sheet->setCellValue('W39', $destinations[1]['depar_date']);
			$sheet->setCellValue('C42', $destinations[1]['project_number']);
			$sheet->setCellValue('AL41', $destinations[1]['lodging'] + 0);
			$sheet->setCellValue('AL43', $destinations[1]['meals'] + 0);
			$sheet->setCellValue('AL45', $destinations[1]['total_lodging_and_meals'] + 0);
			$sheet->setCellValue('AL47', $destinations[1]['night'] + 0);
			$sheet->setCellValue('AL49', $destinations[1]['total'] + 0);
			if($detail['fco_monitor_status'] == 2) {
				$drawing6 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
				$drawing6->setName('FCO signature');
				// $signature = $this->extractImage($detail['fco_monitor_signature']);
				$signature = $this->extractImageFromAPI($detail['fco_monitor_signature']);
				$drawing6->setPath($signature['image_path']); 
				$drawing6->setCoordinates('V41');
				$drawing6->setHeight(35);
				$drawing6->setWorksheet($spreadsheet->getActiveSheet());
			} 
		}

		if(count($destinations) > 2) {
			// 3rd destination
			$sheet->setCellValue('G52', $destinations[2]['city']);
			$sheet->setCellValue('P52', $destinations[2]['arriv_date']);
			$sheet->setCellValue('W52', $destinations[2]['depar_date']);
			$sheet->setCellValue('C55', $destinations[2]['project_number']);
			$sheet->setCellValue('AL54', $destinations[2]['lodging'] + 0);
			$sheet->setCellValue('AL56', $destinations[2]['meals'] + 0);
			$sheet->setCellValue('AL58', $destinations[2]['total_lodging_and_meals'] + 0);
			$sheet->setCellValue('AL60', $destinations[2]['night'] + 0);
			$sheet->setCellValue('AL62', $destinations[2]['total'] + 0);
			if($detail['fco_monitor_status'] == 2) {
				$drawing7 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
				$drawing7->setName('FCO signature');
				// $signature = $this->extractImage($detail['fco_monitor_signature']);
				$signature = $this->extractImageFromAPI($detail['fco_monitor_signature']);
				$drawing7->setPath($signature['image_path']);  // put your path and image here
				$drawing7->setPath($signature['image_path']);
				$drawing7->setCoordinates('V54');
				$drawing7->setHeight(35);
				$drawing7->setWorksheet($spreadsheet->getActiveSheet());
			} 
		}

		if($detail['travel_advance'] == 'Yes') {
			$sheet->setCellValue('V95', 'X');
			$sheet->setCellValue('AB95', '80%');
			$sheet->setCellValue('AL106', '80%');
			$total_advance = ($detail['total_destinations_cost'] + 1000000) * 0.8;
			$sheet->setCellValue('AL108', $total_advance);
		} else {
			$sheet->setCellValue('Y95', 'X');
			$sheet->setCellValue('AB95', '');
			$sheet->setCellValue('AL106', '');
			$sheet->setCellValue('AL108', $detail['total_destinations_cost'] + 1000000);
		}

		if($detail['need_documents'] == 'Yes') {
			$sheet->setCellValue('V71', 'X');
		} else {
			$sheet->setCellValue('Y71', 'X');
		}

		if($detail['car_rental'] == 'Yes') {
			$sheet->setCellValue('V72', 'X');
		} else {
			$sheet->setCellValue('Y72', 'X');
		}

		if($detail['hotel_reservations'] == 'Yes') {
			$sheet->setCellValue('V73', 'X');
		} else {
			$sheet->setCellValue('Y73', 'X');
		}

		if($detail['other_transportation'] == 'Yes') {
			$sheet->setCellValue('V74', 'X');
		} else {
			$sheet->setCellValue('Y74', 'X');
		}

		$writer = new Xlsx($spreadsheet);
		$ea_number = $detail['ea_number'];
        $current_time = date('d-m-Y h:i:s');
        $filename = "$ea_number Request_Form/$current_time.xlsx";
		$path = FCPATH.'assets/excel/sent_ea_form.xlsx';
		$writer->save($path);
        $excel = [
            'path' => $path,
            'file_name' => $filename,
        ];
        return $excel;
	}

	private function delete_ea_excel() {
		$this->load->helper('file');
		$excel_path = './assets/excel/sent_ea_form.xlsx';
		unlink($excel_path);
	}

	private function send_json($data, $status_code = 200) {
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($status_code)
            ->set_output(json_encode(array_merge($data, ['code' => $status_code])));
    }

	private function delete_signature() {
		$this->load->helper('file');
		$path = FCPATH . 'uploads/excel_signature';
		delete_files($path, TRUE); 
	}

	private function extractImage($filename) {
		$file_url = base_url('assets/images/signature/') . $filename;
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
	
	private function get_signature_from_api($filename) {
		$token = $_ENV['ASSETS_TOKEN'];
		$file_url = $_ENV['ASSETS_URL'] . "$filename?subfolder=signatures&token=$token";        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $file_url);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // <-- important to specify
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // <-- important to specify
        curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpCode == 404) {
			$file_url = base_url('assets/images/signature_not_found.jpg');
		}
		return $file_url;
	}

	public function ea_form($req_id) {

		$detail = $this->request->get_excel_data_by_id($req_id);
		$requestor = $this->request->get_requestor_data($detail['requestor_id']);
		$inputFileName = FCPATH.'assets/excel/ea_form_new.xlsx';
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		$spreadsheet = $reader->load($inputFileName);
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('C7', $requestor['username']);
		$sheet->setCellValue('AK2', 'EA No. ' . $detail['ea_number']);
		$sheet->setCellValue('AD6', $detail['request_date']);
		$sheet->setCellValue('AD8', $detail['originating_city']);
		$sheet->setCellValue('AD10', $detail['departure_date']);
		$sheet->setCellValue('AM10', $detail['return_date']);
		$sheet->setCellValue('AG13', $requestor['project_name']);
		$sheet->setCellValue('C10', $requestor['project_name']);
		$sheet->setCellValue('C16', $requestor['email']);
		$sheet->setCellValue('G17', 'Employee #' . $requestor['employee_id']);
		$sheet->setCellValue('AK15', $detail['ea_assosiate_name']);
		$sheet->setCellValue('AL16', '$' . $detail['max_budget_usd']);
		$sheet->setCellValue('C104', $detail['special_instructions']);
		$sheet->setCellValue('C18', 'X');
		if($detail['country_director_notified'] == 'Yes') {
			$sheet->setCellValue('X18', 'X');
		}

		if($detail['employment'] == 'On behalf') {
			if($detail['employment_status'] == 'Consultant') {
				$sheet->setCellValue('C21', 'X');
				$first_participants = $detail['participants'][0];
				$sheet->setCellValue('G20', $first_participants['title']. ' # ' . $first_participants['name']);
			} else {
				$sheet->setCellValue('C23', 'X');
				if($detail['employment_status'] == 'Other') {
					$first_participants = $detail['participants'][0];
					$other_text = $first_participants['title']. ' # ' . $first_participants['name'];
				} else {
					$other_text = 'Group: ' . $detail['participant_group_name'] . ' - Number of participants: ' . $detail['number_of_participants'];
				}
				$sheet->setCellValue('K23', $other_text);
			}
		}

		// Signature
		$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		$drawing->setName('Traveler signature');
		$signature = $this->extractImageFromAPI($requestor['signature']);
		$drawing->setPath($signature['image_path']); // put your path and image here
		$drawing->setCoordinates('I112');
		$drawing->setHeight(40);
		$drawing->setOffsetY(-5);
		$drawing->setWorksheet($spreadsheet->getActiveSheet());

		if($detail['head_of_units_status'] == 2) {
			$drawing2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing2->setName('Head of units signature');
			$signature = $this->extractImageFromAPI($detail['head_of_units_signature']);
			$drawing2->setPath($signature['image_path']);  
			$drawing2->setCoordinates('I116');
			$drawing2->setHeight(40);
			$drawing2->setOffsetY(-5); 
			$drawing2->setWorksheet($spreadsheet->getActiveSheet());

		} 

		if($detail['fco_monitor_status'] == 2) {
			$drawing4 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing4->setName('FCO signature');
			$signature = $this->extractImageFromAPI($detail['fco_monitor_signature']);
			$drawing4->setPath($signature['image_path']); 
			$drawing4->setCoordinates('V28');
			$drawing4->setHeight(30);
			$drawing4->setWorksheet($spreadsheet->getActiveSheet());

		} 

		if($detail['finance_status'] == 2) {
			$drawing5 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing5->setName('Finance signature');
			$signature = $this->extractImageFromAPI($detail['finance_signature']);
			$drawing5->setPath($signature['image_path']);
			$drawing5->setCoordinates('AK116');
			$drawing5->setOffsetY(-15); 
			$drawing5->setHeight(50);
			$drawing5->setWorksheet($spreadsheet->getActiveSheet());
		} 

		$destinations= $detail['destinations'];
		// 1st destination
		$cityCountry = $destinations[0]['city'];
		if($destinations[0]['country'] == 1) {
			$cityCountry =  $destinations[0]['city'] . '/Indonesia'; 
		} 
		$sheet->setCellValue('G26', $cityCountry);
		$sheet->setCellValue('P26', $destinations[0]['arriv_date']);
		$sheet->setCellValue('W26', $destinations[0]['depar_date']);
		$sheet->setCellValue('C29', $destinations[0]['project_number']);
		$sheet->setCellValue('AL28', $destinations[0]['lodging'] + 0);
		$sheet->setCellValue('AL30', $destinations[0]['meals'] + 0);
		$sheet->setCellValue('AL32', $destinations[0]['total_lodging_and_meals'] + 0);
		$sheet->setCellValue('AL34', $destinations[0]['night'] + 0);
		$sheet->setCellValue('AL36', $destinations[0]['total'] + 0);

		if(count($destinations) > 1) {
			// 2nd destination
			$cityCountry = $destinations[1]['city'];
			if($destinations[1]['country'] == 1) {
				$cityCountry =  $destinations[1]['city'] . '/Indonesia'; 
			} 
			$sheet->setCellValue('G40', $cityCountry);
			$sheet->setCellValue('P40', $destinations[1]['arriv_date']);
			$sheet->setCellValue('W40', $destinations[1]['depar_date']);
			$sheet->setCellValue('C43', $destinations[1]['project_number']);
			$sheet->setCellValue('AL42', $destinations[1]['lodging'] + 0);
			$sheet->setCellValue('AL44', $destinations[1]['meals'] + 0);
			$sheet->setCellValue('AL46', $destinations[1]['total_lodging_and_meals'] + 0);
			$sheet->setCellValue('AL48', $destinations[1]['night'] + 0);
			$sheet->setCellValue('AL50', $destinations[1]['total'] + 0);
			if($detail['fco_monitor_status'] == 2) {
				$drawing6 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
				$drawing6->setName('FCO signature');
				$signature = $this->extractImageFromAPI($detail['fco_monitor_signature']);
				$drawing6->setPath($signature['image_path']); 
				$drawing6->setCoordinates('V42');
				$drawing6->setHeight(30);
				$drawing6->setWorksheet($spreadsheet->getActiveSheet());
			} 
		}

		if(count($destinations) > 2) {
			// 3rd destination
			$cityCountry = $destinations[2]['city'];
			if($destinations[2]['country'] == 1) {
				$cityCountry =  $destinations[2]['city'] . '/Indonesia'; 
			} 
			$sheet->setCellValue('G53', $cityCountry);
			$sheet->setCellValue('P53', $destinations[2]['arriv_date']);
			$sheet->setCellValue('W53', $destinations[2]['depar_date']);
			$sheet->setCellValue('C56', $destinations[2]['project_number']);
			$sheet->setCellValue('AL55', $destinations[2]['lodging'] + 0);
			$sheet->setCellValue('AL57', $destinations[2]['meals'] + 0);
			$sheet->setCellValue('AL59', $destinations[2]['total_lodging_and_meals'] + 0);
			$sheet->setCellValue('AL61', $destinations[2]['night'] + 0);
			$sheet->setCellValue('AL63', $destinations[2]['total'] + 0);
			if($detail['fco_monitor_status'] == 2) {
				$drawing7 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
				$drawing7->setName('FCO signature');
				// $signature = $this->extractImage($detail['fco_monitor_signature']);
				$signature = $this->extractImageFromAPI($detail['fco_monitor_signature']);
				$drawing7->setPath($signature['image_path']);  // put your path and image here
				$drawing7->setCoordinates('V55');
				$drawing7->setHeight(30);
				$drawing7->setWorksheet($spreadsheet->getActiveSheet());
			} 
		}

		if(count($destinations) > 3) {
			// 4th destination
			$cityCountry = $destinations[3]['city'];
			if($destinations[3]['country'] == 1) {
				$cityCountry =  $destinations[3]['city'] . '/Indonesia'; 
			} 
			$sheet->setCellValue('G65', $cityCountry);
			$sheet->setCellValue('P65', $destinations[3]['arriv_date']);
			$sheet->setCellValue('W65', $destinations[3]['depar_date']);
			$sheet->setCellValue('C68', $destinations[3]['project_number']);
			$sheet->setCellValue('AL67', $destinations[3]['lodging'] + 0);
			$sheet->setCellValue('AL69', $destinations[3]['meals'] + 0);
			$sheet->setCellValue('AL71', $destinations[3]['total_lodging_and_meals'] + 0);
			$sheet->setCellValue('AL73', $destinations[3]['night'] + 0);
			$sheet->setCellValue('AL75', $destinations[3]['total'] + 0);
			if($detail['fco_monitor_status'] == 2) {
				$drawing7 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
				$drawing7->setName('FCO signature');
				// $signature = $this->extractImage($detail['fco_monitor_signature']);
				$signature = $this->extractImageFromAPI($detail['fco_monitor_signature']);
				$drawing7->setPath($signature['image_path']);  // put your path and image here
				$drawing7->setCoordinates('V67');
				$drawing7->setHeight(30);
				$drawing7->setWorksheet($spreadsheet->getActiveSheet());
			} 
		}

		if(count($destinations) > 4) {
			// 5th destination
			$cityCountry = $destinations[4]['city'];
			if($destinations[4]['country'] == 1) {
				$cityCountry =  $destinations[4]['city'] . '/Indonesia'; 
			} 
			$sheet->setCellValue('G79', $cityCountry);
			$sheet->setCellValue('P79', $destinations[4]['arriv_date']);
			$sheet->setCellValue('W79', $destinations[4]['depar_date']);
			$sheet->setCellValue('C82', $destinations[4]['project_number']);
			$sheet->setCellValue('AL81', $destinations[4]['lodging'] + 0);
			$sheet->setCellValue('AL83', $destinations[4]['meals'] + 0);
			$sheet->setCellValue('AL85', $destinations[4]['total_lodging_and_meals'] + 0);
			$sheet->setCellValue('AL87', $destinations[4]['night'] + 0);
			$sheet->setCellValue('AL89', $destinations[4]['total'] + 0);
			if($detail['fco_monitor_status'] == 2) {
				$drawing7 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
				$drawing7->setName('FCO signature');
				// $signature = $this->extractImage($detail['fco_monitor_signature']);
				$signature = $this->extractImageFromAPI($detail['fco_monitor_signature']);
				$drawing7->setPath($signature['image_path']);  // put your path and image here
				$drawing7->setCoordinates('V81');
				$drawing7->setHeight(30);
				$drawing7->setWorksheet($spreadsheet->getActiveSheet());
			} 
		}

		if($detail['travel_advance'] == 'Yes') {
			$sheet->setCellValue('V95', 'X');
			$sheet->setCellValue('AB95', '80%');
			$total_advance = ($detail['total_destinations_cost'] + 1000000) * 0.8;
			$sheet->setCellValue('AL108', $total_advance);
		} else {
			$sheet->setCellValue('Y95', 'X');
			$sheet->setCellValue('AB95', '');
			$sheet->setCellValue('AL108', $detail['total_destinations_cost'] + 1000000);
		}

		if($detail['need_documents'] == 'Yes') {
			$sheet->setCellValue('V98', 'X');
		} else {
			$sheet->setCellValue('Y98', 'X');
		}

		if($detail['car_rental'] == 'Yes') {
			$sheet->setCellValue('V99', 'X');
		} else {
			$sheet->setCellValue('Y99', 'X');
		}

		if($detail['hotel_reservations'] == 'Yes') {
			$sheet->setCellValue('V100', 'X');
		} else {
			$sheet->setCellValue('Y100', 'X');
		}

		if($detail['other_transportation'] == 'Yes') {
			$sheet->setCellValue('V101', 'X');
		} else {
			$sheet->setCellValue('Y101', 'X');
		}

		$writer = new Xlsx($spreadsheet);
		$ea_number = $detail['ea_number'];
        $current_time = date('d-m-Y h:i:s');
        $filename = "$ea_number Request_Form/$current_time";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=$filename.xlsx");
        $writer->save('php://output');
		$this->delete_signature();
	}
}
