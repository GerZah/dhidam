<?php

class User extends CI_Controller {

	// ---------------------------------------------------------------------------

	protected $_currentUser;

	// ---------------------------------------------------------------------------

	public function __construct() {
		parent::__construct();
		$this->load->model("user_model");

		$this->_currentUser = $this->session->userdata("userID");
		$this->_currentUserData = $this->user_model->get_UserData($this->_currentUser);
	}

	// ---------------------------------------------------------------------------

	public function index() {
		$this->load->view("inc/header_view.php");

		// redirect("/user/login");

		$viewData = array(
			"currentUser" => $this->_currentUser,
			"currentUserData" => $this->_currentUserData,
		);
		$this->load->view("user/debug_output.php", $viewData);
		$this->load->view("inc/login_logout_etc.php", $viewData);

		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	// ### Invalidate session and display logout message
	public function logout() {
		$this->user_model->logout();

		$this->load->view("inc/header_view.php");
		$this->load->view("user/logout_message.php");
		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	// ### Display login form -- or redirect to logged in page
	public function login($defUsername = false) {
		if ($this->_currentUser) { redirect("/user/"); }

		else {
			$this->load->view("inc/header_view.php");
			$this->load->view("user/login_form.php", ["defUsername" => $defUsername]);
			$this->load->view("inc/footer_view.php");
		}
	}

	// ---------------------------------------------------------------------------

	// ### React on login form data -- and log in user / or redirect back (or whatever)
	public function do_login() {
		$this->load->view("inc/header_view.php");

		$username = $this->input->post("username");
		$password = $this->input->post("password");
		$this->user_model->do_login($username, $password);

		if ($this->_currentUser) { redirect("/user/"); }
		else { redirect("/user/login/$username"); }

		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	// ### Display form to change a user's password

	public function change_password() {
		if (!$this->_currentUser) { redirect("/user/"); }

		else {
			$this->load->view("inc/header_view.php");
			$this->load->view("user/change_password_form.php");
			$this->load->view("inc/footer_view.php");
		}
	}

	// ---------------------------------------------------------------------------

	public function do_change_password() {
		if (!$this->_currentUser) { redirect("/user/"); }

		else {
			$this->load->view("inc/header_view.php");

			$oldpassword = $this->input->post("oldpassword");
			$newpassword = $this->input->post("newpassword");
			$cnfpassword = $this->input->post("cnfpassword");

			$result = $this->user_model->do_change_password(
				$this->_currentUser, $oldpassword, $newpassword, $cnfpassword
			);

			// echo "<pre>$result</pre>";

			$this->load->view("user/password_changed.php", ["result" => $result] );

			$this->load->view("inc/footer_view.php");
		}
	}

	// ---------------------------------------------------------------------------

}
