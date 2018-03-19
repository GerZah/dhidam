<?php

class User extends CI_Controller {

	// ---------------------------------------------------------------------------

	public function __construct() {
		parent::__construct();
		$this->load->model("user_model");
	}

	// ---------------------------------------------------------------------------

	public function index() {
		$this->load->view("inc/header_view.php");

		$viewData = array(
			"currentUser" => $this->user_model->currentUser(),
			"currentUserData" => $this->user_model->currentUserData(),
		);
		$this->load->view("user/debug_output.php", $viewData);
		$this->load->view("inc/login_logout_etc.php", $viewData);

		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	// ### Invalidate session and display logout message
	public function logout() {
		$this->user_model->enforceLogin(); // ... or else ...
		$this->user_model->logout();

		$this->load->view("inc/header_view.php");
		$this->load->view("user/logout_message.php");
		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	// ### Display login form -- or redirect to logged in page
	public function login($defUsername = false) {
		$this->user_model->enforceNoLogin(); // ... or else ...

		$this->load->view("inc/header_view.php");
		$this->load->view("user/login_form.php", ["defUsername" => $defUsername]);
		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	// ### React on login form data -- and log in user / or redirect back (or whatever)
	public function do_login() {
		$this->user_model->enforceNoLogin(); // ... or else ...

		$username = $this->input->post("username");
		$password = $this->input->post("password");
		$this->user_model->do_login($username, $password);

		if ($this->user_model->currentUser()) { redirect("/user/"); }
		else { redirect("/user/login/$username"); }
	}

	// ---------------------------------------------------------------------------

	// ### Display form to change a user's password

	public function change_password() {
		$this->user_model->enforceLogin(); // ... or else ...

		$viewData = array(
			"username" => $this->user_model->currentUserData()["username"],
			"result" => $this->session->flashdata("passChangeResult")
		);

		$this->load->view("inc/header_view.php");
		$this->load->view("user/change_password_form.php", $viewData);
		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	public function do_change_password() {
		$this->user_model->enforceLogin(); // ... or else ...

		$oldpassword = $this->input->post("oldpassword");
		$newpassword = $this->input->post("newpassword");
		$cnfpassword = $this->input->post("cnfpassword");

		$result = $this->user_model->do_change_password(
			$oldpassword, $newpassword, $cnfpassword
		);

		if ($result!=6) {
			$this->session->set_flashdata( [ "passChangeResult" => $result ] );
			redirect("/user/change_password");
		}

		$this->load->view("inc/header_view.php");
		$this->load->view("user/password_changed.php");
		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

}
