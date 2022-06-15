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

	public function paid()
	{   
        $this->template->set('page', 'Paid report');
		$this->template->render('ea_report/incoming/paid');
	}

	public function datatable($status = null)
    {	
		$user_id = $this->user_data->userId;
		if($status == 'rejected') {
			$this->datatable->select('CONCAT("EA", ea.id) AS ea_number, u.username as requestor_name, ea.request_base,
        ea.originating_city, ea.id as total_cost,srt.rejected_reason , DATE_FORMAT(srt.submitted_at, "%d %M %Y - %H:%i") as created_at ,ea.id, TIMESTAMP(created_at) as timestamp', true);
		} else {
			$this->datatable->select('CONCAT("EA", ea.id) AS ea_number, u.username as requestor_name, ea.request_base,
      		  ea.originating_city, ea.id as total_cost, DATE_FORMAT(srt.submitted_at, "%d %M %Y - %H:%i") as created_at ,ea.id, TIMESTAMP(created_at) as timestamp', true);
		}
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
		}
		if($status == 'approved') {
			$this->datatable->where('srt.head_of_units_status =', 2);
			$this->datatable->where('srt.country_director_status =', 2);
			$this->datatable->where('srt.finance_status =', 2);
		}
		// if($status == 'paid') {
		// 	$this->datatable->where('srt.head_of_units_status =', 2);
		// 	$this->datatable->where('srt.country_director_status =', 2);
		// 	$this->datatable->where('srt.finance_status =', 2);
		// }
		if($status == 'rejected') {
			$this->datatable->where('ea.is_ter_rejected =', 1);
			$this->datatable->where('srt.head_of_units_status =', 3);
			$this->datatable->or_where('srt.country_director_status =', 3);
			$this->datatable->or_where('srt.finance_status =', 3);
		}

        $this->datatable->where('ea.is_ter_submitted =', 1);
		$this->datatable->edit_column('id', "$1", 'encrypt(id)');
		$this->datatable->edit_column('total_cost', '<span style="font-size: 1rem;"
		class="badge badge-pill badge-secondary fw-bold">$1</span>', 'get_total_actual_costs(total_cost)');
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

	public function ter_payment() {
		if ($this->input->is_ajax_request() && $this->input->server('REQUEST_METHOD') === 'POST') {
			$this->form_validation->set_rules('payment_type', 'Payment type', 'required');
			$this->form_validation->set_rules('total_payment', 'Total payment', 'required');
			if ($this->form_validation->run()) {
				$req_id = $this->input->post('req_id');
				$payment_type = $this->input->post('payment_type');
				$total_payment = $this->input->post('total_payment');
				$clean_cost = str_replace('.', '',  $total_payment);
				$req_id = $this->input->post('req_id');
				$payload = [
					'finance_id' => $this->user_data->userId,
					'finance_status' => 2,
					'finance_status_at' => date("Y-m-d H:i:s"),
					'payment_type' => $payment_type,
					'total_payment' => $clean_cost,
				];
				$updated = $this->report->update_ter_payment($req_id, $payload);
				if($updated) {
					$email_sent = $this->send_ter_payment_email($req_id);
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
					$this->request->update_ter_payment($req_id, $payload);
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

	private function send_ter_payment_email($req_id) {
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
			<p>Your TER EA#-'.$detail['r_id'].' has been accomplished. You need to refund of IDR '.$detail['total_payment'].', please do it soon to accomplished your report</p>
			';
		} else {
			$data['preview'] = '
			<p>Your TER EA#-'.$detail['r_id'].' has been accomplished. We will transfer you IDR '.$detail['total_payment'].', just relax and checking your bank account soon</p>
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
}
