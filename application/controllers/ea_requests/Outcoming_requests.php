<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Outcoming_requests extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Request_Model', 'request');
		$this->load->model('Base_Model', 'base_model');
		$this->template->set('pageParent', 'Outcoming Requests');
		$this->template->set_default_layout('layouts/default');
	}

	public function pending()
	{
		$this->template->set('page', 'Pending requests');
		$requests = $this->db->select('*')->from('ea_requests')->get()->result();
		$data['requests'] = $requests;
		$data['status'] = 'pending';
		$this->template->render('outcoming_requests/pending', $data);
	}

	public function rejected()
	{
		$this->template->set('page', 'Rejected requests');
		$requests = $this->db->select('*')->from('ea_requests')->get()->result();
		$data['requests'] = $requests;
		$data['status'] = 'rejected';
		$this->template->render('outcoming_requests/rejected', $data);
	}

	public function done()
	{
		$this->template->set('page', 'Done requests');
		$requests = $this->db->select('*')->from('ea_requests')->get()->result();
		$data['requests'] = $requests;
		$data['status'] = 'done';
		$this->template->render('outcoming_requests/done', $data);
	}

	public function create()
	{
		$this->template->set('assets_css', [
			site_url('assets/css/demo1/pages/wizard/wizard-3.css')
		]);
		$user_id = $this->user_data->userId;
		if(is_head_of_units()){
			$data['head_of_units'] = $this->base_model->get_line_supervisor($user_id);
		} else {
			$data['head_of_units'] = $this->base_model->get_head_of_units($user_id);
		}
		$data['requestor_data'] = $this->request->get_requestor_data($user_id);
		$data['locations'] = $this->base_model->get_cities();
		$this->template->set('page', 'Create request');
		$this->template->render('outcoming_requests/create', $data);
	}

	public function detail($id = null)
	{
		$id = decrypt($id);
		$detail = $this->request->get_request_by_id($id);
		if($detail) {
			$user_id = $this->user_data->userId;
			$requestor_data = $this->request->get_requestor_data($detail['requestor_id']);
			
			$head_of_units_btn = '';
			if($detail['head_of_units_status'] != 1 || $detail['head_of_units_id'] != $user_id) {
				$head_of_units_btn = 'invisible';
			}
			$ea_assosiate_btn = '';
			if($detail['ea_assosiate_status'] != 1 || $detail['head_of_units_status'] != 2  || !is_ea_assosiate()) {
				$ea_assosiate_btn = 'invisible';
			}
			$fco_monitor_btn = '';
			if($detail['fco_monitor_status'] != 1 || $detail['ea_assosiate_status'] != 2  || !is_fco_monitor()) {
				$fco_monitor_btn = 'invisible';
			}
			$finance_btn = '';
			if($detail['finance_status'] != 1 || $detail['fco_monitor_status'] != 2  || !is_finance_teams()) {
				$finance_btn = 'invisible';
			}
			$detail['clean_max_budget_idr'] = $detail['max_budget_idr'] + 0;
			$detail['clean_max_budget_usd'] = $detail['max_budget_usd'] + 0;
			$data = [
				'detail' => $detail,
				'requestor_data' => $requestor_data,
				'head_of_units_btn' => $head_of_units_btn,
				'ea_assosiate_btn' => $ea_assosiate_btn,
				'fco_monitor_btn' => $fco_monitor_btn,
				'finance_btn' => $finance_btn,
				'request_status' => get_requests_status($detail['r_id']),
			];
			$this->template->set('pageParent', 'Requests');
			$this->template->set('page', 'Requests detail');
			$this->template->render('outcoming_requests/detail', $data);
		} else {
			show_404();
		}
	}

	public function store()
	{	

		$this->form_validation->set_rules('request_base', 'Request base', 'required');
		$this->form_validation->set_rules('departure_date', 'Departure date', 'required');
		$this->form_validation->set_rules('return_date', 'Return date', 'required');
		$this->form_validation->set_rules('originating_city', 'City', 'required');
		$this->form_validation->set_rules('country_director_notified', 'Country director notified', 'required');
		$this->form_validation->set_rules('travel_advance', 'Travel advance', 'required');
		$this->form_validation->set_rules('need_documents', 'Need documents', 'required');
		$this->form_validation->set_rules('car_rental', 'Car rental', 'required');
		$this->form_validation->set_rules('hotel_reservations', 'Hotel reservations', 'required');
		$this->form_validation->set_rules('other_transportation', 'Other trasportation', 'required');
		$this->form_validation->set_rules('head_of_units_id', 'Head of units', 'required');

		if ($this->form_validation->run()) {

			$payload = $this->input->post();
			if ($_FILES['exteral_invitation']['name']) {
				$dir = './uploads/exteral_invitation/';
				if (!is_dir($dir)) {
					mkdir($dir, 0777, true);
				}

				$config['upload_path']          = $dir;
				$config['allowed_types']        = 'xls|xlsx|pdf|jpg|png|jpeg';
				$config['max_size']             = 10048;
				$config['encrypt_name']         = true;

				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if ($this->upload->do_upload('exteral_invitation')) {
					$payload['exteral_invitation_file'] = $this->upload->data('file_name');
				} else {
					$response = ['status' => false, 'message' => strip_tags($this->upload->display_errors())]; die;
				}
			} else {
				$payload['exteral_invitation_file'] = null;
			}

			if ($_FILES['car_rental_memo']['name']) {
				$dir2 = './uploads/car_rental_memo/';
				if (!is_dir($dir2)) {
					mkdir($dir2, 0777, true);
				}

				$config2['upload_path']          = $dir2;
				$config2['allowed_types']        = 'xls|xlsx|pdf|jpg|png|jpeg';
				$config2['max_size']             = 10048;
				$config2['encrypt_name']         = true;

				$this->load->library('upload', $config2);
				$this->upload->initialize($config2);

				if ($this->upload->do_upload('car_rental_memo')) {
					$payload['car_rental_memo'] = $this->upload->data('file_name');
				} else {
					$response = ['status' => false, 'message' => strip_tags($this->upload->display_errors())]; die;
				}
			} else {
				$payload['car_rental_memo'] = null;
			}
			$request_id = $this->request->insert_request($payload);
			if($request_id) {
				$sent = $this->send_email_to_head_of_units($request_id);
				if($payload['country_director_notified'] == 'Yes') {
					$director = $this->base_model->get_country_director();
					if($director) {
						$this->send_email_to_country_director($request_id, $director);
					}
				}
				if($sent) {
					$response['message'] = 'Your request has been sent';
					$status_code = 200;
				} else {
					$response['message'] = 'Failed to send email notification to head_of_units';
					$status_code = 400;
				}
			} else {
				$response['errors'] = $this->form_validation->error_array();
				$response['message'] = 'Failed to send request';
				$status_code = 400;
			}
		} else {
			$response['errors'] = $this->form_validation->error_array();
			$response['message'] = 'Please fill all required fields';
			$status_code = 422;
		}
		$this->delete_signature();
		$this->send_json($response, $status_code);
	}

	public function datatable($status = null)
    {	
        $this->datatable->select('CONCAT("EA", ea.id) AS ea_number, u.username as requestor_name, ea.request_base, ea.employment, ea.originating_city,
		DATE_FORMAT(ea.departure_date, "%d %M %Y") as departure_date, DATE_FORMAT(ea.return_date, "%d %M %Y") as return_date,
		DATE_FORMAT(ea.created_at, "%d %M %Y - %H:%i") as created_at, ea.id', true);
        $this->datatable->from('ea_requests ea');
        $this->datatable->join('tb_userapp u', 'u.id = ea.requestor_id');
        $this->datatable->join('ea_requests_status st', 'ea.id = st.request_id');
		if($status == 'pending') {
			$this->datatable->where('st.head_of_units_status !=', 3);
			$this->datatable->where('st.ea_assosiate_status !=', 3);
			$this->datatable->where('st.fco_monitor_status !=', 3);
			$this->datatable->where('st.finance_status !=', 3);
			$this->datatable->where('st.finance_status !=', 2);
		}
		if($status == 'rejected') {
			$this->datatable->where('st.head_of_units_status =', 3);
			$this->datatable->or_where('st.ea_assosiate_status =', 3);
			$this->datatable->or_where('st.fco_monitor_status =', 3);
			$this->datatable->or_where('st.finance_status =', 3);
		}
		if($status == 'done') {
			$this->datatable->where('st.head_of_units_status =', 2);
			$this->datatable->where('st.ea_assosiate_status =', 2);
			$this->datatable->where('st.fco_monitor_status =', 2);
			$this->datatable->where('st.finance_status =', 2);
		}
		$this->datatable->where('ea.requestor_id =', $this->user_data->userId);
		$this->datatable->edit_column('id', "$1", 'encrypt(id)');
		$this->datatable->edit_column('ea_number', '<span style="font-size: 1rem;"
		class="badge badge-success fw-bold">$1</span>', 'ea_number');
        echo $this->datatable->generate();
    }

	private function send_email_to_head_of_units($request_id) {
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

		// $assosiate = $this->base_model->get_ea_assosiate();
		// if($detail['travel_advance'] == 'Yes') {
		// 	if($assosiate) {
		// 		$approver_name = $approver_name . ' / ' . $assosiate['username'];
		// 	}
		// }

		$data['preview'] = '<p>You have EA Request #EA-'.$detail['r_id'].' from <b>'.$requestor['username'].'</b> and it need your review. Please check on attachment</p>';
        
        $data['content'] = '
            <tr>
                <td>
                    <p>Dear <b>'.$approver_name.'</b>,</p>
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

					<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                        <tbody>
                        <tr>
                            <td align="left">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tbody>
                                <tr>
									<td> <a href="'.base_url('ea_requests/requests_confirmation').'?req_id='.$enc_req_id.'&approver_id='.$approver_id.'&status=2&level=head_of_units" target="_blank">APPROVE</a> </td>
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
									<td> <a <a href="'.base_url('ea_requests/requests_confirmation').'?req_id='.$enc_req_id.'&approver_id='.$approver_id.'&status=3&level=head_of_units" target="_blank">REJECT</a> </td>
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
										<td> <a <a href="'.base_url('ea_requests/requests_confirmation/ea_form/'). $request_id . '" target="_blank">DOWNLOAD EA FORM</a> </td>
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
		// $excel = $this->attach_ea_form($request_id);
		// if(!empty($excel)) {
		// 	$mail->addAttachment($excel['path'], $excel['file_name']);
		// }
        $mail->addAddress($detail['head_of_units_email']);
		// if($detail['travel_advance'] == 'Yes') {
		// 	if($assosiate) {
		// 		$mail->addCC($assosiate['email']);
		// 	}
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

	private function send_email_to_country_director($request_id, $director) {
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

		$data['preview'] = '<p>EA Request notify for country director #EA-'.$detail['r_id'].' from <b>'.$requestor['username'].'</b>. Please check on attachment</p>';
        
        $data['content'] = '
            <tr>
                <td>
                    <p>Dear <b>'.$director['username'].'</b>,</p>
                    <p>'.$data['preview'].'</p>
                </td>
            </tr>
			<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-detail">
						<tbody>
							<tr>
								<td align="left">
								<table role="presentation" border="0" cellpadding="0" cellspacing="0">
									<tbody>
									<tr>
										<td> <a <a href="'.base_url('ea_requests/requests_confirmation/ea_form/'). $request_id . '" target="_blank">DOWNLOAD EA FORM</a> </td>
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
		// $excel = $this->attach_ea_form($request_id);
		// if(!empty($excel)) {
		// 	$mail->addAttachment($excel['path'], $excel['file_name']);
		// }
        $mail->addAddress($director['email']);
        $mail->Subject = "EA Request notification for Country Director";
        $mail->isHTML(true);
        $mail->Body = $text;
        $sent=$mail->send();

		if ($sent) {
			return true;
		} else {
			return false;
		}
	}

	public function ea_form($req_id) {

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
		$drawing->setCoordinates('I85');
		$drawing->setHeight(40);
		$drawing->setWorksheet($spreadsheet->getActiveSheet());

		if($detail['head_of_units_status'] == 2) {
			$drawing2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing2->setName('Head of units signature');
			// $signature = $this->extractImage($detail['head_of_units_signature']);
			$signature = $this->extractImageFromAPI($detail['head_of_units_signature']);
			$drawing2->setPath($signature['image_path']);  
			$drawing2->setCoordinates('I89');
			$drawing2->setHeight(40);
			$drawing2->setOffsetY(-5); 
			$drawing2->setWorksheet($spreadsheet->getActiveSheet());

		} 

		if($detail['fco_monitor_status'] == 2) {
			$drawing4 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing4->setName('FCO signature');
			// $signature = $this->extractImage($detail['fco_monitor_signature']);
			$signature = $this->extractImageFromAPI($detail['fco_monitor_signature']);
			$drawing4->setPath($signature['image_path']); 
			$drawing4->setCoordinates('V28');
			$drawing4->setHeight(30);
			$drawing4->setWorksheet($spreadsheet->getActiveSheet());

		} 

		if($detail['finance_status'] == 2) {
			$drawing5 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
			$drawing5->setName('Finance signature');
			// $signature = $this->extractImage($detail['finance_signature']);
			$signature = $this->extractImageFromAPI($detail['finance_signature']);
			$drawing5->setPath($signature['image_path']);
			$drawing5->setCoordinates('AK89');
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
				$drawing6->setHeight(30);
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
				$drawing7->setCoordinates('V54');
				$drawing7->setHeight(30);
				$drawing7->setWorksheet($spreadsheet->getActiveSheet());
			} 
		}

		if($detail['travel_advance'] == 'Yes') {
			$sheet->setCellValue('V68', 'X');
			$sheet->setCellValue('AL79', '80%');
			$total_advance = ($detail['total_destinations_cost'] + 1000000) * 0.8;
			$sheet->setCellValue('AL81', $total_advance);
		} else {
			$sheet->setCellValue('Y68', 'X');
			$sheet->setCellValue('AL79', '');
			$sheet->setCellValue('AL81', $detail['total_destinations_cost'] + 1000000);
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
        $filename = "$ea_number Request_Form/$current_time";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=$filename.xlsx");
        $writer->save('php://output');
		$this->delete_signature();
	}

	public function edit_costs_modal() {
		$dest_id = $this->input->get('dest_id');	
		$detail = $this->db->select('ed.id, er.requestor_id, ed.arrival_date, ed.departure_date ,format(ed.meals,0,"de_DE") as meals, format(ed.lodging,0,"de_DE") as lodging')
					->from('ea_requests_destinations ed')
					->join('ea_requests er', 'er.id = ed.request_id')
					->where('ed.id', $dest_id)
					->get()->row_array();	
		$data = [
			'detail' => $detail,
		];
		$this->load->view('outcoming_requests/modal/edit_costs', $data);
	}

	public function update_costs($dest_id) {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('meals', 'Meals', 'required');
			$this->form_validation->set_rules('lodging', 'Lodging', 'required');

			if ($this->form_validation->run()) {
				
				$meals = $this->input->post('meals');
				$clean_meals = str_replace('.', '',  $meals);
				$lodging = $this->input->post('lodging');
				$clean_lodging = str_replace('.', '',  $lodging);
				$payload = [
					'arrival_date' => $this->input->post('arrival_date'),
					'departure_date' => $this->input->post('departure_date'),
					'meals' => $clean_meals,
					'lodging' => $clean_lodging,
				];
				if(is_ea_assosiate()) {
					$updated = $this->request->update_max_budget($dest_id, $payload);
				} else {
					$updated = $this->request->update_costs($dest_id, $payload);
				}
				if($updated) {
					$response['success'] = true;
					$response['message'] = 'Data has been updated!';
					$status_code = 200;
				} else {
					$response['success'] = false;
					$response['message'] = 'Failed to update data, please try again later';
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

	public function resubmit_request() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$request_id = $this->input->post('request_id');
			$level = rejected_by($request_id);
			$email_sent = $this->send_resubmit_request_email($request_id, $level);
			if($email_sent) {
				$updated = $this->request->resubmit_request($request_id, $level);
				if($updated) {
					$response['success'] = true;
					$response['message'] = 'Request has been submited!';
					$status_code = 200;
				} else {
					$response['success'] = false;
					$response['message'] = 'Failed to submit request, please try again later';
					$status_code = 400;
				}
				$this->delete_signature();
			} else {
				$response['success'] = false;
				$response['message'] = 'Failed to submit request, please try again later';
				$status_code = 400;
			}		
			$this->send_json($response, $status_code);
		} else {
			exit('No direct script access allowed');
		}
	}

	private function send_resubmit_request_email($request_id, $level) {
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
		if($level ==  'head_of_units') {
			$approver_name = $detail['head_of_units_name'];
			$approver_id = $detail['head_of_units_id'];
			$email = $detail['head_of_units_email'];
		} else if($level == 'ea_assosiate') {
			$approver_name = $detail['ea_assosiate_name'];
			$approver_id = $detail['ea_assosiate_id'];
			$email = $detail['ea_assosiate_email'];
		} else if($level == 'fco_monitor') {
			$approver_name = $detail['fco_monitor_name'];
			$approver_id = $detail['fco_monitor_id'];
			$email = $detail['fco_monitor_email'];
		} else if($level == 'finance') {
			$approver_name = $detail['finance_name'];
			$approver_id = $detail['finance_id'];
			$email = $detail['finance_email'];
		} 
		$enc_req_id = encrypt($detail['r_id']);
		$approve_btn = '<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
							<tbody>
							<tr>
								<td align="left">
								<table role="presentation" border="0" cellpadding="0" cellspacing="0">
									<tbody>
									<tr>
										<td> <a href="'.base_url('ea_requests/requests_confirmation').'?req_id='.$enc_req_id.'&approver_id='.$approver_id.'&status=2&level='.$level.'" target="_blank">APPROVE</a> </td>
									</tr>
									</tbody>
								</table>
								</td>
							</tr>
							</tbody>
						</table>';

		if($level == 'finance' || $level == 'ea_assosiate') {
			$approve_btn = '';
		} 
		
		$data['preview'] = '<p>You have EA Request #EA-'.$detail['r_id'].' from <b>'.$requestor['username'].'</b> and it need your review. Please check on attachment</p>';
        
        $data['content'] = '
            <tr>
                <td>
                    <p>Dear <b>'.$approver_name.'</b>,</p>
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
									<td> <a <a href="'.base_url('ea_requests/requests_confirmation').'?req_id='.$enc_req_id.'&approver_id='.$approver_id.'&status=3&level='.$level.'" target="_blank">REJECT</a> </td>
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
										<td> <a <a href="'.base_url('ea_requests/requests_confirmation/ea_form/'). $request_id . '" target="_blank">DOWNLOAD EA FORM</a> </td>
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
		if($level == 'finance') {
			$payment_pdf = $this->attach_payment_request($request_id);
			$mail->addStringAttachment($payment_pdf, 'Payment form request.pdf');
		} 
        $mail->addAddress($email);
        $mail->Subject = "Resubmit EA Request";
        $mail->isHTML(true);
        $mail->Body = $text;
        $sent=$mail->send();

		if ($sent) {
			return true;
		} else {
			return false;
		}
	}

	private function send_resubmit_requests_to_finance($request_id) {
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
	
		$enc_req_id = encrypt($detail['r_id']);

		$data['preview'] = '<p>You have EA Request #EA-'.$detail['r_id'].' from <b>'.$requestor['username'].'</b> and it need your review. Please check on attachment</p>';
        
        $data['content'] = '
            <tr>
                <td>
                    <p>Dear <b>Finance teams</b>,</p>
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
									<td> <a <a href="'.base_url('ea_requests/requests_confirmation').'?req_id='.$enc_req_id.'&approver_id=null&status=3&level=finance" target="_blank">REJECT</a> </td>
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
										<td> <a <a href="'.base_url('ea_requests/requests_confirmation/ea_form/'). $request_id . '" target="_blank">DOWNLOAD EA FORM</a> </td>
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
		$finance_teams = $this->base_model->get_finance_teams();
		foreach($finance_teams as $user) {
			$mail->addAddress($user['email']);
		}
        $mail->Subject = "Resubmit EA Request";
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
