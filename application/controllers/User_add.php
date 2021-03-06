<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_add extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper(array('form','url'));
		$this->load->library(array('session', 'form_validation'));
		$this->load->database();
		$this->load->model('Model_user_add');
		
		ini_set('max_execution_time', 0); 
		ini_set('memory_limit','2048M');

        if ($this->session->userdata('login') == TRUE) {
			if ($this->session->userdata('login_app') <> 'istok-admin') {
				$this->session->sess_destroy();
				redirect('login');
			}
		} else {
			$this->session->sess_destroy();
			redirect('login');
		}
	}

	function index(){
		$datasesion = array(
			'user_id' => $this->session->userdata('user_id'),
			'user_level' => $this->session->userdata('user_level'),
			'user_name' => $this->session->userdata('user_name'),
			'user_name_full' => $this->session->userdata('user_name_full')
		);
	
		$data = array(
			'user_id' => $this->session->userdata('user_id'),
			'user_level' => $this->session->userdata('user_level'),
			'user_name' => $this->session->userdata('user_name'),
			'user_name_full' => $this->session->userdata('user_name_full'),
			'status_active' => 1
		);
		
		$data['get_list_enum'] = $this->Model_user_add->get_list_enum('mst_user', 'user_level');
		
		$this->load->view('header', $datasesion);
		$this->load->view('user_add', $data);
		$this->load->view('footer');
	}
	
	public function simpan() {
		// set form validation rules
		$this->form_validation->set_rules('user_name', 'name', 'trim|required');
		$this->form_validation->set_rules('user_name_full', 'name full', 'trim|required');
		$this->form_validation->set_rules('user_email', 'email', 'trim|required');
		$this->form_validation->set_rules('user_password', 'Password', 'trim|required');
		$this->form_validation->set_rules('cpassword', 'Confirm Password', 'required|matches[user_password]');

		// submit
		if ($this->form_validation->run() == FALSE){
			$this->index();
		} else {
			//Cek User ada/tidak di sascloud
			$cek = $this->Model_user_add->get_user($this->input->post('user_name'));
			if (count($cek) > 0){
				$this->session->set_flashdata('msg','<div class="alert alert-danger text-center">User already exist</div>');
				redirect('user_add/index');
			}
			
			date_default_timezone_set('Asia/Jakarta');			
			$data = array(
				'user_name' => $this->input->post('user_name'),
				'user_name_full' => $this->input->post('user_name_full'),
				'user_password' => $this->input->post('user_password'),
				'user_email' => $this->input->post('user_email'),
				'user_level' => $this->input->post('user_level')
			);
			if ($this->Model_user_add->insert_user($data)) {				
				$this->session->set_flashdata('msg','<div class="alert alert-success text-center">You are successfully Added!</div>');
				$this->index();
			} else {
				// error
				$this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Oops! Error.  Please try again!!!</div>');
				$this->index();
			}
			
		}

	}

}
