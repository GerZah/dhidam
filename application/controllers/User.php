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

		$viewData = [
			"currentUser" => $this->user_model->currentUser(),
			"currentUserData" => $this->user_model->currentUserData(),
			"isSuperAdmin" => $this->user_model->isSuperAdmin(),
			"isTechAdmin" => $this->user_model->isTechAdmin(),
			"isAppAdmin" => $this->user_model->isAppAdmin(),
		];
		$this->load->view("inc/login_logout_etc.php", $viewData);
		$this->load->view("user/debug_output.php", $viewData);

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
	public function login() {
		$this->user_model->enforceNoLogin(); // ... or else ...

		$this->load->view("inc/header_view.php");
		$this->load->view("user/login_form.php");
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
		else {
			$this->session->set_flashdata([
				"notification" => "<strong>Login failed</strong> – please try again.",
				"defUsername" => $username
			]);
			redirect("/user/login/");
		}
	}

	// ---------------------------------------------------------------------------

	// ### Display form to change a user's password
	public function change_password() {
		$this->user_model->enforceLogin(); // ... or else ...

		$viewData = [
			"username" => $this->user_model->currentUserData()["username"],
			"result" => $this->session->flashdata("passChangeResult")
		];

		$this->load->view("inc/header_view.php");
		$this->load->view("user/change_password_form.php", $viewData);
		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	// ### Actually carry out the password change, if possible (old / new password entered correctly)
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

	// ### Display a form to create a new user, if user has sufficient privileges
	public function create_user() {
		$this->user_model->enforceLogin(); // ... or else ...
		$this->user_model->enforceAppAdmin(); // ... or else ...

		$isSuperAdmin = $this->user_model->isSuperAdmin();
		$isTechAdmin = $this->user_model->isTechAdmin();
		$isAppAdmin = $this->user_model->isAppAdmin();

		$userRoles = $this->user_model->userRoles;

		$roles = array();
		if ($isAppAdmin)   { $roles[$userRoles["user"]]      = $userRoles[$userRoles["user"]];  }
		if ($isTechAdmin)  { $roles[$userRoles["appAdmin"]]  = $userRoles[$userRoles["appAdmin"]];  }
		if ($isSuperAdmin) { $roles[$userRoles["techAdmin"]] = $userRoles[$userRoles["techAdmin"]];  }

		$viewData = [
			"roles" => $roles,
			"defUsername" => $this->session->flashdata("defUsername"),
			"defEmail" => $this->session->flashdata("defEmail"),
			"defNewPassword" => $this->session->flashdata("defNewPassword"),
			"defUserRole" => $this->session->flashdata("defUserRole"),
			"result" => $this->session->flashdata("createUserResult"),
		];

		$this->load->view("inc/header_view.php");
		$this->load->view("user/create_user.php", $viewData);
		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	// ### Actually create a new user, according to the post variables
	public function do_create_user() {
		$this->user_model->enforceLogin(); // ... or else ...
		$this->user_model->enforceAppAdmin(); // ... or else ...

		$username = $this->input->post("username");
		$email = $this->input->post("email");
		$newpassword = $this->input->post("newpassword");
		$userrole = intval($this->input->post("userrole"));

		$result = $this->user_model->create_user(
			$username, $email, $newpassword, $userrole
		);

		if ($result!=10) { # +#+#+# 10 == no error
			$this->session->set_flashdata([
				"defUsername" => $username,
				"defEmail" => $email,
				"defNewPassword" => $newpassword,
				"defUserRole" => $userrole,
				"createUserResult" => $result,
			]);
			redirect("/user/create_user");
		}


	}

	// ---------------------------------------------------------------------------

}
