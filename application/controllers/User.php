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

		$loginError = $this->session->flashdata("loginError");

		$this->load->view("inc/header_view.php");
		$this->load->view("user/login_form.php", [ "loginError" => $loginError ] );
		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	// ### React on login form data -- and log in user / or redirect back (or whatever)
	public function do_login() {
		$this->user_model->enforceNoLogin(); // ... or else ...

		$username = trim($this->input->post("username"));
		if (!$username) {
			$this->session->set_flashdata([ "loginError" => 1 ]); // empty user name
			redirect("/user/login/"); exit;
		}

		$password = trim($this->input->post("password"));
		if (!$password) { // empty password
			$this->session->set_flashdata([ "loginError" => 2, "defUsername" => $username ]);
			redirect("/user/login/"); exit;
		}

		$this->user_model->do_login($username, $password);

		if ($this->user_model->currentUser()) { redirect("/user/"); }
		else { // Login failed
			$this->session->set_flashdata([ "loginError" => 3, "defUsername" => $username ]);
			redirect("/user/login/");
		}
	}

	// ---------------------------------------------------------------------------

	// ### Display form to change a user's password
	public function change_password() {
		$this->user_model->enforceLogin(); // ... or else ...

		$viewData = [
			"username" => $this->user_model->currentUserData()["username"],
			"passChangeResult" => $this->session->flashdata("passChangeResult")
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

		$passChangeResult = $this->user_model->do_change_password(
			$oldpassword, $newpassword, $cnfpassword
		);

		if ($passChangeResult!=1) {
			$this->session->set_flashdata( [ "passChangeResult" => $passChangeResult ] );
			redirect("/user/change_password");
		}

		$this->load->view("inc/header_view.php");
		$this->load->view("user/password_changed.php");
		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	// ### create a (verbatim) array of acceptable user roles for the current admin
	// ### i.e. everything below the current level of admin

	protected function _fetchUserRoles() {
		$isSuperAdmin = $this->user_model->isSuperAdmin();
		$isTechAdmin = $this->user_model->isTechAdmin();
		$isAppAdmin = $this->user_model->isAppAdmin();

		$userRoles = $this->user_model->userRoles;

		$roles = array();
		if ($isAppAdmin)   { $roles[$userRoles["user"]]      = $userRoles[$userRoles["user"]];  }
		if ($isTechAdmin)  { $roles[$userRoles["appAdmin"]]  = $userRoles[$userRoles["appAdmin"]];  }
		if ($isSuperAdmin) { $roles[$userRoles["techAdmin"]] = $userRoles[$userRoles["techAdmin"]];  }

		return $roles;
	}

	// ---------------------------------------------------------------------------

	// ### Display a form to create a new user, if user has sufficient privileges
	public function create_user() {
		$this->user_model->enforceLogin(); // ... or else ...
		$this->user_model->enforceAppAdmin(); // ... or else ...

		$viewData = [
			"roles" => $this->_fetchUserRoles(),
			"defUsername" => $this->session->flashdata("defUsername"),
			"defEmail" => $this->session->flashdata("defEmail"),
			"defNewPassword" => $this->session->flashdata("defNewPassword"),
			"defUserRole" => $this->session->flashdata("defUserRole"),
			"createUserResult" => $this->session->flashdata("createUserResult"),
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

		$createUserResult = $this->user_model->create_user(
			$username, $email, $newpassword, $userrole
		);

		if ($createUserResult!=1) {
			$this->session->set_flashdata([
				"defUsername" => $username,
				"defEmail" => $email,
				"defNewPassword" => $newpassword,
				"defUserRole" => $userrole,
				"createUserResult" => $createUserResult,
			]);
			redirect("/user/create_user");
		}

		$this->load->view("inc/header_view.php");
		$this->load->view("user/user_created.php", ["username" => $username] );
		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	// ### present a user table, with user maintenance (according to privileges)
	public function user_table($page = 0) {
		$this->user_model->enforceLogin(); // ... or else ...
		$this->user_model->enforceAppAdmin(); // ... or else ...

		$tablePage = $this->user_model->getUserTable($page);

		$updateSuccess = $this->session->flashdata("updateSuccess");

		$this->load->library('table');

		$this->load->view("inc/header_view.php");
		$this->load->view("user/user_table.php", [
			"tablePage" => $tablePage,
			"page" => $page,
			"updateSuccess" => $updateSuccess,

		]);
		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	public function edit_user($id = false, $page = 0) {
		$this->user_model->enforceLogin(); // ... or else ...
		$this->user_model->enforceAppAdmin(); // ... or else ...

		$backToUserTable = "/user/user_table";
		if ($id === false) { redirect($backToUserTable); } // ... or else ..

		$userData = $this->user_model->get_userData($id);
		if (!$userData) { redirect($backToUserTable); }

		// Make sure that current user can edit only users in less privilgesd roles than their own
		if ($userData["role"] <= $this->user_model->currentUserData()["role"]) { redirect($backToUserTable); }

		$viewData = [
			"roles" => $this->_fetchUserRoles(),
			"userData" => $userData,
			"page" => $page,
			"editUserResult" => $this->session->flashdata("editUserResult"),
			"defUsername" => $this->session->flashdata("defUsername"),
			"defEmail" => $this->session->flashdata("defEmail"),
			"defUserrole" => $this->session->flashdata("defUserrole"),
		];

		$this->load->view("inc/header_view.php");
		$this->load->view("user/user_admin.php", $viewData );
		$this->load->view("inc/footer_view.php");
	}

	// ---------------------------------------------------------------------------

	public function do_edit_user() {
		$this->user_model->enforceLogin(); // ... or else ...
		$this->user_model->enforceAppAdmin(); // ... or else ...

		$id = intval($this->input->post("id"));
		$page = intval($this->input->post("page"));
		$username = $this->input->post("username");
		$email = $this->input->post("email");
		$newpassword = $this->input->post("newpassword");
		$userrole = intval($this->input->post("userrole"));
		$roles = array_keys($this->_fetchUserRoles());

		$editUserResult = $this->user_model->updateUser(
			$id, $username, $email, $newpassword, $userrole, $roles
		);

		if ($editUserResult!=1) {
			$this->session->set_flashdata([
				"editUserResult" => $editUserResult,
				"defUsername" => $username,
				"defEmail" => $email,
				"defUserrole" => $userrole,
			]);
			redirect("/user/edit_user/$id/$page");
		}
		else {
			$this->session->set_flashdata([ "updateSuccess" => $id ]);
			redirect("/user/user_table/$page");
		}

	}

	// ---------------------------------------------------------------------------

}
