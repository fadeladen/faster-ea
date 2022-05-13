<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
			];
			// echo json_encode($data);
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

		$this->send_json($response, $status_code);
	}

	public function datatable($status = null)
    {	
        $this->datatable->select('u.username as requestor_name, ea.request_base, ea.employment, ea.originating_city,
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
        $this->datatable->order_by('created_at', 'desc');
		$this->datatable->edit_column('id', "$1", 'encrypt(id)');
        echo $this->datatable->generate();
    }

	private function send_email_to_head_of_units($request_id) {
		$this->load->library('Phpmailer_library');
        $mail = $this->phpmailer_library->load();
        $mail->isSMTP();
        $mail->SMTPSecure = 'ssl';
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->Port = 465;
        $mail->SMTPDebug = 0; 
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['EMAIL_USERNAME'];
        $mail->Password = $_ENV['EMAIL_PASSWORD'];
		$detail = $this->request->get_request_by_id($request_id);
		$requestor = $this->request->get_requestor_data($detail['requestor_id']);
		$approver_name = $detail['head_of_units_name'];
		$enc_req_id = encrypt($detail['r_id']);
		$approver_id = $detail['head_of_units_id'];

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

                    
                </td>
            </tr>';

        // $this->load->view('template/email', $data);
        $text = $this->load->view('template/email', $data, true);
        $mail->setFrom('no-reply@faster.bantuanteknis.id', 'FASTER-FHI360');
        $mail->addAddress($requestor['email']);
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

	public function test() {
		echo json_encode($this->user_data);
	}

	public function test_excel($req_id) {

		$detail = $this->request->get_request_by_id($req_id);
		$requestor = $this->request->get_requestor_data($detail['requestor_id']);
		$inputFileName = FCPATH.'assets/excel/ea_form.xlsx';
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		$spreadsheet = $reader->load($inputFileName);
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('C7', $requestor['username']);

		$writer = new Xlsx($spreadsheet);
		$writer = new Xlsx($spreadsheet);
        $current_time = date('d-m-Y h:i:s');
        $filename = "EA Request_Form/$current_time";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=$filename.xlsx");
        $writer->save('php://output');

	}
}
