<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Spipu\Html2Pdf\Html2Pdf;

class Incoming_requests extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Request_Model', 'request');
		$this->load->model('Base_Model', 'base_model');
		$this->template->set('pageParent', 'Incoming Requests');
		$this->template->set_default_layout('layouts/default');
	}

	public function requests_for_review()
	{
		$this->template->set('page', 'Requests for review');
		$data['status'] = 'requests_for_review';
		$this->template->render('incoming_requests/requests_for_review', $data);
	}

	public function pending()
	{
		$this->template->set('page', 'Pending requests');
		$requests = $this->db->select('*')->from('ea_requests')->get()->result();
		$data['requests'] = $requests;
		$data['status'] = 'pending';
		$this->template->render('incoming_requests/pending', $data);
	}

	public function rejected()
	{
		$this->template->set('page', 'Rejected requests');
		$requests = $this->db->select('*')->from('ea_requests')->get()->result();
		$data['requests'] = $requests;
		$data['status'] = 'rejected';
		$this->template->render('incoming_requests/rejected', $data);
	}

	public function done()
	{
		$this->template->set('page', 'Done requests');
		$requests = $this->db->select('*')->from('ea_requests')->get()->result();
		$data['requests'] = $requests;
		$data['status'] = 'done';
		$this->template->render('incoming_requests/done', $data);
	}

	public function create()
	{
		$this->template->set('assets_css', [
			site_url('assets/css/demo1/pages/wizard/wizard-3.css')
		]);
		$user_id = $this->user_data->userId;
		$data['head_of_units'] = $this->base_model->get_head_of_units($user_id);
		$data['requestor_data'] = $this->request->get_requestor_data($user_id);
		$this->template->set('page', 'Create request');
		$this->template->render('incoming_requests/create', $data);
	}

	public function datatable($status = null)
    {	
		$user_id = $this->user_data->userId;

		$this->datatable->select('CONCAT("EA", ea.id) AS ea_number, u.username as requestor_name, ea.request_base, ea.employment, ea.originating_city,
		DATE_FORMAT(ea.departure_date, "%d %M %Y") as departure_date, DATE_FORMAT(ea.return_date, "%d %M %Y") as return_date,
		DATE_FORMAT(ea.created_at, "%d %M %Y - %H:%i") as created_at, ea.id, TIMESTAMP(created_at) as timestamp', true);
        $this->datatable->from('ea_requests ea');
        $this->datatable->join('tb_userapp u', 'u.id = ea.requestor_id');
        $this->datatable->join('ea_requests_status st', 'ea.id = st.request_id');
		if($status == 'requests_for_review') {
			if (is_head_of_units() || is_line_supervisor()) {
				$this->datatable->where('st.head_of_units_status =', 1);
				$this->datatable->where('st.head_of_units_id =', $user_id);
			} else if (is_ea_assosiate()) {
				$this->datatable->where('st.head_of_units_status =', 2);
				$this->datatable->where('st.ea_assosiate_status =', 1);
			} else if (is_fco_monitor()) {
				$this->datatable->where('st.ea_assosiate_status =', 2);
				$this->datatable->where('st.fco_monitor_status =', 1);
			} else if (is_finance_teams()) {
				$this->datatable->where('st.fco_monitor_status =', 2);
				$this->datatable->where('st.finance_status =', 1);
			} else {
				$this->datatable->where('st.head_of_units_id =', null);
			}
		}

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
		$this->datatable->edit_column('id', "$1", 'encrypt(id)');
		$this->datatable->edit_column('ea_number', '<span style="font-size: 1rem;"
		class="badge badge-success fw-bold">$1</span>', 'ea_number');
        echo $this->datatable->generate();
    }

	public function set_status() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$req_id =  $this->input->post('id');
			$status =  $this->input->post('status');
			$level =  $this->input->post('level');
			$approver_id = $this->user_data->userId;
            $request_detail = $this->request->get_request_by_id($req_id);
            if($status == 3) {
				$this->form_validation->set_rules('rejected_reason', 'Reason', 'required');
				if ($this->form_validation->run()) {
					$rejector_name = $this->user_data->fullName;
					$rejected_reason =  $this->input->post('rejected_reason');
					$updated = $this->request->update_status($req_id, $approver_id, $status, $level, $rejected_reason);
					if($updated) {
						$email_sent = $this->send_rejected_requests($req_id, $rejector_name);
						if($email_sent) {
							$response['success'] = true;
							$response['message'] = 'Request has been rejected and email has been sent';
							$status_code = 200;
							$this->delete_signature();
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
			} else {
				$approver_name = $this->user_data->fullName;
				$updated = $this->request->update_status($req_id, $approver_id, $status, $level);
				if($updated) {
					$request_detail = $this->request->get_request_by_id($req_id);
					if ($level == 'fco_monitor') {
						$finance_teams = $this->base_model->get_finance_teams();
						foreach($finance_teams as $user) {
							$email_sent = $this->send_email_to_finance_teams($req_id, $approver_name, $user);
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
						$response['success'] = true;
						$response['message'] = 'Request has been approved and email has been sent!';
						$status_code = 200;
						$this->delete_signature();
					} else {
						$this->request->update_status($req_id, $approver_id, 1, $level);
						$this->delete_signature();
						$response['success'] = false;
						$response['message'] = 'Something wrong, please try again later';
						$status_code = 400;
					}
				} else {
					$response['success'] = false;
					$response['message'] = 'Failed to update status, please try again later';
					$status_code = 400;
				}
			}
			$this->send_json($response, $status_code);
		} else {
			exit('No direct script access allowed');
		}
	}

	private function send_rejected_requests($req_id, $rejector_name) {
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
		$data['preview'] = '<p>Your EA Request #EA-'.$detail['r_id'].' has been rejected by '.$rejector_name.'</p>
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
			return true;
		} else {
			return false;
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
                             <p>Please review following requests</p>';
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
		// $excel = $this->attach_ea_form($req_id);
		// if(!empty($excel)) {
		// 	$mail->addAttachment($excel['path'], $excel['file_name']);
		// }
        $mail->addAddress($email_detail['target_email']);
        $mail->Subject = "EA Requests";
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
        $mail->setFrom('no-reply@faster.bantuanteknis.id', 'FASTER-FHI360');
		$mail->addAddress($user['email']);
		$payment_pdf = $this->attach_payment_request($req_id);
		$mail->addStringAttachment($payment_pdf, 'Payment form request.pdf');
		// $excel = $this->attach_ea_form($req_id);
		// if(!empty($excel)) {
		// 	$mail->addAttachment($excel['path'], $excel['file_name']);
		// }
        $mail->Subject = "Approved EA Requests for review by Finance Teams";
        $mail->isHTML(true);
        $mail->Body = $text;
        $sent=$mail->send();

		if ($sent) {
			return true;
		} else {
			return false;
		}
    }

	public function update_budget() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('max_budget_idr', 'IDR Max budget', 'required');
			$this->form_validation->set_rules('max_budget_usd', 'USD Max budget', 'required');

			if ($this->form_validation->run()) {
				$request_id = $this->input->post('r_id');
				$clean_idr = str_replace('.', '',  $this->input->post('max_budget_idr'));
				$clean_usd = str_replace('.', '',  $this->input->post('max_budget_usd'));
				$payload = [
					'max_budget_idr' => $clean_idr,
					'max_budget_usd' => $clean_usd,
				];
				$updated = $this->db->where('id', $request_id)->update('ea_requests', $payload);
				if($updated) {
					$response['success'] = true;
					$response['message'] = 'Max budget has been updated!';
					$status_code = 200;
					$this->send_json($response, $status_code);
				} else {
					$response['success'] = false;
					$response['message'] = 'Failed to update max budget!';
					$status_code = 400;
					$this->send_json($response, $status_code);
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

	public function payment() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('date_of_transfer', 'Transfer date', 'required');

			if ($this->form_validation->run()) {
				$req_id = $this->input->post('r_id');
				if (!empty($_FILES['payment_receipt']['name'])) {
					$this->form_validation->set_rules('payment_receipt', 'Payment Receipt', 'required');
					$dir = './uploads/ea_payment_receipt/';
					if (!is_dir($dir)) {
						mkdir($dir, 0777, true);
					}
					$config['upload_path']          = $dir;
					$config['allowed_types']        = 'pdf|jpg|png|jpeg';
					$config['max_size']             = 10048;
					$config['encrypt_name']         = true;
					$this->load->library('upload', $config);
					$this->upload->initialize($config);
					if ($this->upload->do_upload('payment_receipt')) {
						$payment_receipt = $this->upload->data('file_name');
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
					$payment_receipt = null;
				}

				$payload = [
					'finance_id' => $this->user_data->userId,
					'finance_status' => 2,
					'finance_status_at' => date("Y-m-d H:i:s"),
					'date_of_transfer' => date('Y-m-d', strtotime($this->input->post('date_of_transfer'))),
					'payment_receipt' => $payment_receipt,
				];
				$updated = $this->request->update_payment_status($req_id, $payload);
				if($updated) {
					$email_sent = $this->send_payment_email($req_id);
					if($email_sent) {
						$response['success'] = true;
						$response['message'] = 'Payment process completed and email has been sent to requestor!';
						$status_code = 200;
					} else {
						$response['success'] = false;
						$response['message'] = 'Something wrong, please try again later';
						$status_code = 400;
					}
				} else {
					$payload = [
						'finance_id' => null,
						'finance_status' => 1,
						'finance_status_at' => null,
						'date_of_transfer' => null,
						'payment_receipt' => null,
					];
					$this->request->update_payment_status($req_id, $payload);
					$response['success'] = false;
					$response['message'] = 'Failed to process payment, please try again later';
					$status_code = 400;
				}
			} else {
				$response['errors'] = $this->form_validation->error_array();
				$response['message'] = 'Please fill all required fields';
				$status_code = 422;
			}
			$this->delete_signature();
			$this->send_json($response, $status_code);
		} else {
			exit('No direct script access allowed');
		}
	}

	public function get_payment_form($req_id) {
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
		// $this->load->view('template/form_payment_reimburstment', $data);
		$content = $this->load->view('template/form_payment_reimburstment', $data, true);
        $html2pdf = new Html2Pdf('P', [210, 330], 'en', true, 'UTF-8', array(15, 10, 15, 10));
        $html2pdf->setDefaultFont('arial');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->setTestTdInOnePage(false);
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $html2pdf->Output('Payment Request Form.pdf');
		$this->delete_signature();
	}

	private function send_payment_email($req_id) {
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
		$data['preview'] = '<p>Your EA Request #EA-'.$detail['r_id'].' has been transfered to your bank account</p>
		<p>Please check on attachment</p>';
        $data['content'] = '
                    <p>Dear, '.$requestor['username'].',</p> 
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
		// $payment_pdf = $this->attach_payment_request($req_id);
		// $mail->addStringAttachment($payment_pdf, 'Payment form request.pdf');
		if($detail['payment_receipt'] != null) {
			$receipt_path = FCPATH.'uploads/ea_payment_receipt/' . $detail['payment_receipt'];
			$mail->addAttachment($receipt_path, 'Payment receipt_'.$detail['payment_receipt']);
		}
        $mail->addAddress($requestor['email']);
        $mail->Subject = "EA Request Payment Confirmation";
        $mail->isHTML(true);
        $mail->Body = $text;
        $sent=$mail->send();

		if ($sent) {
			return true;
		} else {
			return false;
		}
    }

	public function get_rejected_modal() {	
		$data = [
			'id' => $this->input->get('id'),
			'status' => $this->input->get('status'),
			'level' => $this->input->get('level'),
		];
		$this->load->view('incoming_requests/modal/rejected_reason', $data);
	}
}
