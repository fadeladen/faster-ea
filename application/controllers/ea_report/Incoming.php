<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Spipu\Html2Pdf\Html2Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Incoming extends MY_Controller {


    function __construct()
	{
		parent::__construct();
		$this->load->model('Request_Model', 'request');
		$this->load->model('Report_Model', 'report');
		$this->load->model('Base_Model', 'base_model');
		$this->template->set('pageParent', 'Incoming report');
		$this->template->set_default_layout('layouts/default');
		$this->load->helper('report');
	}

	public function index()
	{   
        $this->template->set('page', 'New report');
		$this->template->render('ea_report/incoming/index');
	}

	public function pending()
	{   
        $this->template->set('page', 'Pending report');
		$this->template->render('ea_report/incoming/pending');
	}

	public function rejected()
	{   
        $this->template->set('page', 'Rejected report');
		$this->template->render('ea_report/incoming/rejected');
	}
	public function approved()
	{   
        $this->template->set('page', 'Approved report');
		$this->template->render('ea_report/incoming/approved');
	}

	public function done()
	{   
        $this->template->set('page', 'Done report');
		$this->template->render('ea_report/incoming/done');
	}

	public function refund_reimburst()
	{   
        $this->template->set('page', 'Refund/reimburst report');
		$this->template->render('ea_report/incoming/refund_reimburst');
	}

	public function datatable($status = null)
    {	
		$user_id = $this->user_data->userId;
		$this->datatable->select('CONCAT("EA", ea.id) AS ea_number, ea.id as request_for,
			ea.id as total_advance, ea.id as total_expense, ea.id as refund, ea.id as reimburst,
			ea.id as action,TIMESTAMP(srt.submitted_at) as timestamp', true);
        $this->datatable->from('ea_requests ea');
        $this->datatable->join('tb_userapp u', 'u.id = ea.requestor_id');
        $this->datatable->join('ea_requests_status st', 'ea.id = st.request_id');
        $this->datatable->join('ea_report_status srt', 'ea.id = srt.request_id', 'LEFT');
        $this->datatable->where('st.head_of_units_status =', 2);
        $this->datatable->where('st.ea_assosiate_status =', 2);
        $this->datatable->where('st.fco_monitor_status =', 2);
        $this->datatable->where('st.finance_status =', 2);
		if($status == 'for_review') {
			if (is_head_of_units() || is_line_supervisor()) {
				$this->datatable->where('srt.head_of_units_status =', 1);
				$this->datatable->where('srt.head_of_units_id =', $user_id);
			} else if (is_country_director() || is_fco_monitor()) {
				$this->datatable->where('srt.finance_status =', 2);
				$this->datatable->where('srt.country_director_status =', 1);
			} else if (is_finance_teams()) {
				$this->datatable->where('srt.head_of_units_status =', 2);
				$this->datatable->where('srt.finance_status =', 1);
			} else {
				$this->datatable->where('srt.head_of_units_id =', null);
			}
		}
		if($status == 'pending') {
			$this->datatable->where('srt.head_of_units_status !=', 3);
			$this->datatable->where('srt.country_director_status !=', 3);
			$this->datatable->where('srt.country_director_status !=', 2);
			$this->datatable->where('srt.finance_status !=', 3);
			$this->datatable->where('srt.finance_status !=', 2);
			$this->datatable->where('srt.is_paid =', 1);
		}
		if($status == 'approved') {
			$this->datatable->where('srt.head_of_units_status =', 2);
			$this->datatable->where('srt.country_director_status =', 2);
			$this->datatable->where('srt.finance_status =', 2);
			$this->datatable->where('srt.is_paid =', 1);
		}
		if($status == 'done') {
			$this->datatable->where('srt.head_of_units_status =', 2);
			$this->datatable->where('srt.country_director_status =', 2);
			$this->datatable->where('srt.finance_status =', 2);
			$this->datatable->where('srt.is_paid =', 2);
		}

        $this->datatable->where('ea.is_ter_submitted =', 1);
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

	public function set_status() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$req_id =  $this->input->post('id');
			$status =  $this->input->post('status');
			$level =  $this->input->post('level');
			$approver_id = $this->user_data->userId;
            if($status == 3) {
				$this->form_validation->set_rules('rejected_reason', 'Reason', 'required');
				if ($this->form_validation->run()) {
					$rejector_name = $this->user_data->fullName;
					$rejected_reason =  $this->input->post('rejected_reason');
					$updated = $this->report->update_status($req_id, $approver_id, $status, $level, $rejected_reason);
					if($updated) {
						$email_sent = $this->send_rejected_ter($req_id, $rejector_name);
						if($email_sent) {
							$response['success'] = true;
							$response['message'] = 'Request has been rejected and email has been sent';
							$status_code = 200;
						} else {
							$this->report->update_status($req_id, $approver_id, 1, $level);
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
				$updated = $this->report->update_status($req_id, $approver_id, $status, $level);
				if($updated) {
					if ($level == 'head_of_units') {
						$finance_teams = $this->base_model->get_finance_teams();
						foreach($finance_teams as $user) {
							$email_sent = $this->send_email_to_finance_teams($req_id, $approver_name, $user);
						}
					} else {
						$payment=  get_total_refund_or_reimburst($req_id);	
						$payload = [
							'payment_type' => $payment['type'],
							'total_payment' => $payment['total'],
						];
						$this->report->update_ter_payment($req_id, $payload);
						$fco_monitor = $this->base_model->get_fco_monitor();
						$this->report->update_status($req_id, $fco_monitor['id'], 2, 'country_director');
						$email_sent = $this->send_refund_or_reimburst_email($req_id);
					}
					if($email_sent) {
						$response['success'] = true;
						$response['message'] = 'Request has been approved and email has been sent!';
						$status_code = 200;
					} else {
						$this->report->update_status($req_id, $approver_id, 1, $level);
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

	public function report_confirmation() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$req_id =  $this->input->post('req_id');
            $updated = $this->report->update_report_confirmation($req_id);
			if($updated) {
				$sent = $this->send_report_confirmation($req_id);
				if($sent) {
					$response['success'] = true;
					$response['message'] = 'Request has been approved and email has been sent!';
					$status_code = 200;
				} else {
					$payload = [
						'is_need_confirmation' => 0,
						'finance_status' => 1,
						'finance_status_by' => $this->user_data->userId,
						'finance_status_at' =>  null,
					];
					$this->db->where('request_id', $req_id)->update('ea_report_status', $payload);
					$response['success'] = false;
					$response['message'] = 'Failed to sending email, please check your connection!';
					$status_code = 400;
				}
			} else {
				$response['success'] = false;
				$response['message'] = 'Failed send report confirmation, please try again later';
				$status_code = 400;
			}
			$this->send_json($response, $status_code);
		} else {
			exit('No direct script access allowed');
		}
	}

	public function confirm_ter_by_requestor() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$req_id =  $this->input->post('req_id');
			$req_id = decrypt($req_id);
			$fco_monitor = $this->base_model->get_fco_monitor();
            $updated = $this->report->confirm_ter($req_id, $fco_monitor['id']);
			if($updated) {
				$response['success'] = true;
				$response['message'] = 'TER has been confirmed!';
				$status_code = 200;				
			} else {
				$response['success'] = false;
				$response['message'] = 'Failed to confirm TER, please try again later';
				$status_code = 400;
			}
			$this->send_json($response, $status_code);
		} else {
			exit('No direct script access allowed');
		}
	}

	private function send_rejected_ter($req_id, $rejected_by) {
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
			$this->delete_signature();
			return true;
		} else {
			return false;
		}
    }

	private function send_report_confirmation($req_id) {
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
		$data['preview'] = '
		<p>Finance team just reviewed your TER report #EA-'.$detail['r_id'].' , we revised/correction a little bit your report, please confirm it soon, prior to move to next step process.</p>
		<p>Regards,</p>
		<p>Finance team</p>';
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
        $mail->Subject = "TER Confirmation";
        $mail->isHTML(true);
        $mail->Body = $text;
        $sent=$mail->send();

		if ($sent) {
			return true;
		} else {
			return false;
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
		$country_director = $this->base_model->get_fco_monitor();
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

	public function ter_payment() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('total_payment', 'Total payment', 'required');
			if (empty($_FILES['payment_receipt']['name']))
			{
				$this->form_validation->set_rules('payment_receipt', 'Receipt', 'required');
			}
			if ($this->form_validation->run()) {

				if ($_FILES['payment_receipt']['name']) {
					$dir = './uploads/ter_payment_receipt/';
					if (!is_dir($dir)) {
						mkdir($dir, 0777, true);
					}
	
					$config2['upload_path']          = $dir;
					$config2['allowed_types']        = 'pdf|jpg|png|jpeg';
					$config2['max_size']             = 10048;
					$config2['encrypt_name']         = true;
	
					$this->load->library('upload', $config2);
					$this->upload->initialize($config2);
	
					if ($this->upload->do_upload('payment_receipt')) {
						$payment_receipt = $this->upload->data('file_name');
					} else {
						$response = [
							'errors' => $this->upload->display_errors(),
							'success' => false, 
							'message' => strip_tags($this->upload->display_errors()),
						];
						$status_code = 422;
						return $this->send_json($response, $status_code);
					}
				} else {
					$payment_receipt = null;
				}

				$req_id = $this->input->post('req_id');
				$payment_type = $this->input->post('payment_type');
				$total_payment = $this->input->post('total_payment');
				$req_id = $this->input->post('req_id');
				$payload = [
					'is_paid' => 2,
					'payment_type' => $payment_type,
					'total_payment' => $total_payment,
					'payment_receipt' => $payment_receipt,
					'date_of_transfer' => date('Y-m-d'),
				];
				$updated = $this->report->update_ter_payment($req_id, $payload);
				if($updated) {
					$response['success'] = true;
					$response['message'] = 'Data has been saved!';
					$status_code = 200;
				} else {
					$response['success'] = true;
					$response['message'] = 'Something went wrong! Please try again later!';
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

	private function send_refund_or_reimburst_email($req_id) {
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
		$detail = $this->report->get_ter_details($req_id);
		$requestor = $this->request->get_requestor_data($detail['requestor_id']);
		$enc_req_id = encrypt($detail['r_id']);
		$mail->setFrom('no-reply@faster.bantuanteknis.id', 'FASTER-FHI360');
		if($detail['payment_type'] == 1) {
			$data['preview'] = '
			<p>Your TER EA#-'.$detail['r_id'].' has been reviewed by Finance team. You need to refund of IDR '.$detail['total_payment'].', please do it soon to accomplished your report</p>
			';
		} else {
			$data['preview'] = '
			<p>Your TER EA#-'.$detail['r_id'].' has been reviewed by Finance team. We will transfer you IDR '.$detail['total_payment'].', just relax and checking your bank account soon</p>
			';
		}
		 $data['content'] = '
					 <p>Dear '.$requestor['username'].',</p> 
					 <p>'.$data['preview'].'</p>
					 <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
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
		$mail->addAddress($requestor['email']);
        $mail->Subject = "Accomplished TER";
        $mail->isHTML(true);
        $mail->Body = $text;
		$sent = $mail->send();
		if ($sent) {
			return true;
		} else {
			return false;
		}
    }

	public function edit_ter_item() {
		$item_id = $this->input->get('item_id');		
		$detail = $this->db->select('*')->from('ea_actual_costs')->where('id', $item_id)->get()->row_array();
		$detail = [
			'id' => $detail['id'],
			'cost' => $detail['cost'] + 0,
			'comment_by_finance' => $detail['comment_by_finance'],
		];
		$data['detail'] = $detail;
		$this->load->view('ea_report/modal/edit_ter_item', $data);
	}

	public function update_ter_item() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('cost', 'Cost', 'required');
			$this->form_validation->set_rules('comment', 'Comment', 'required');

			if ($this->form_validation->run()) {
				$cost = str_replace('.', '',  $this->input->post('cost'));
				$id = $this->input->post('id');
				$payload = [
					'cost' => $cost,
					'comment_by_finance' => $this->input->post('comment'),
				];
				$updated = $this->report->update_ter_item($id, $payload);
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
}
