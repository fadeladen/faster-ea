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
				$updated = $this->request->update_status($req_id, $approver_id, $status, $level);
				if($updated) {
					if ($level == 'country_director') {
						$finance_teams = $this->base_model->get_finance_teams();
						foreach($finance_teams as $user) {
							$email_sent = $this->send_email_to_finance_teams($req_id, $approver_name, $user);
						}
					} else {
						$email_sent = $this->send_email_to_country_director($req_id);
					}
					if($email_sent) {
						$response['success'] = true;
						$response['message'] = 'Request has been approved and email has been sent!';
						$status_code = 200;
					} else {
						$this->request->update_status($req_id, $approver_id, 1, $level);
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
		$enc_req_id = encrypt($detail['r_id']);
		$mail->setFrom('no-reply@faster.bantuanteknis.id', 'FASTER-FHI360');
		$data['preview'] = '<p>TER #EA-'.$detail['r_id'].' has been approved by '.$approver_name.'</p>
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
									 <td> <a href="'.base_url('ea_report/incoming/detail').'/'.$enc_req_id.'" target="_blank">DETAILS</a> </td>
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
									<td> <a <a href="'.base_url('ea_report/report_confirmation').'?req_id='.$enc_req_id.'&approver_id='.$user['id'].'&status=3&level=finance" target="_blank">REJECT</a> </td>
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
}
