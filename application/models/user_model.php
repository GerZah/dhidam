<?php

  class User_model extends CI_model {

    // ---------------------------------------------------------------------------

    // ### hidden storage for currently logged on user details
  	protected $_currentUser;
  	protected $_currentUserData;

    // ----------------------

    // ### getter functions for current user ID / current user data
    public function currentUser() { return $this->_currentUser; }
    public function currentUserData() { return $this->_currentUserData; }

    // ---------------------------------------------------------------------------

    // ### constructor -- take care of retrieving user data of currently logged on user
    public function __construct() {
  		parent::__construct();

      $this->_currentUser = $this->session->userdata("userID");
      if (!$this->_currentUser) { $this->logout(); }
      else {
        $this->_currentUserData = $this->get_UserData($this->_currentUser);
        if (!$this->_currentUserData) { $this->logout(); }
      }

    }

    // ---------------------------------------------------------------------------

    public function isLoggedIn() { return !!($this->_currentUserData); }
    public function isNotLoggedIn() { return !($this->isLoggedIn());  }

    // ### checks that no user is logged on, otherwise redirect to user page
    public function enforceNoLogin($redirectTarget = "/user/") {
      if ($this->isLoggedIn()) { redirect($redirectTarget); }
    }

    // ----------------------

    // ### checks that a user is logged in, otherwise redirect to user page
    public function enforceLogin($redirectTarget = "/user/") {
      if ($this->isNotLoggedIn()) { redirect($redirectTarget); }
    }

    // ---------------------------------------------------------------------------

    // ### user roles -- hiararchical, i.e. a superAdmin is also a techAdmin etc.
    public $userRoles = array(
      "superAdmin" => 0, 0 => "Super Administrator",
      "techAdmin"  => 1, 1 => "Technical Administrator",
      "appAdmin"   => 2, 2 => "Application Administrator",
      "user"       => 3, 3 => "Regular User",
    );

    // ----------------------

    // ### check wether or not the currently logged on user is a SuperAdmin
    public function isSuperAdmin() {
      return (
        ($this->isLoggedIn())
        and ($this->_currentUserData["role"] == $this->userRoles["superAdmin"])
      );
    }

    // ### checks that the currently logged on user is a SuperAdmin, otherwise redirect to user page
    public function enforceSuperAdmin($redirectTarget = "/user/") {
      if (!$this->isSuperAdmin()) { redirect($redirectTarget); }
    }

    // ----------------------

    // ### check wether or not the currently logged on user is a TechAdmin
    public function isTechAdmin() {
      return
      (
        (
          ($this->isLoggedIn())
          and ($this->_currentUserData["role"] == $this->userRoles["techAdmin"])
        )
        or ( $this->isSuperAdmin() )
      );
    }

    // ### checks that the currently logged on user is a TechAdmin, otherwise redirect to user page
    public function enforceTechAdmin($redirectTarget = "/user/") {
      if (!$this->isTechAdmin()) { redirect($redirectTarget); }
    }

    // ----------------------

    // ### check wether or not the currently logged on user is an AppAdmin
    public function isAppAdmin() {
      return
      (
        (
          ($this->isLoggedIn())
          and ($this->_currentUserData["role"] == $this->userRoles["appAdmin"])
        )
        or ( $this->isTechAdmin() )
      );
    }

    // ### checks that the currently logged on user is an AppAdmin, otherwise redirect to user page
    public function enforceAppAdmin($redirectTarget = "/user/") {
      if (!$this->isAppAdmin()) { redirect($redirectTarget); }
    }

    // ---------------------------------------------------------------------------

    // ### log out currently logged on user, i.e. destroy the current login session
    function logout() {
      $this->session->unset_userdata("userID");
      $this->_currentUser = false;
      $this->_currentUserData = array();
    }

    // ---------------------------------------------------------------------------

    // ### encapsulate the SHA random salt / hash generation
    function sha1SaltedHash($password, $shasalt = false) {
      if (!$shasalt) {
        $shasalt = sha1(openssl_random_pseudo_bytes(1024)); // 1k salt entropy
      }
      $shapwd = sha1($password.$shasalt);
      return [ "shasalt" => $shasalt, "shapwd" => $shapwd ];
    }

    // ---------------------------------------------------------------------------

    // ### try logging on a user, specified by their username and password
    function do_login($username, $password) {
      $q = $this->db
      ->select(["id", "shasalt", "shapwd"])
      ->from("user")
      ->where(["username" => $username])
      ->get();

      $success = false; // pessimistic sanity state

      if ($q->num_rows() == 1) {
        $user = $q->row_array();
        $password = trim($password);
        $shapwd = $this->sha1SaltedHash($password, $user["shasalt"])["shapwd"];

        if ($shapwd == $user["shapwd"]) {
          $this->session->set_userdata([ "userID" => $user["id"], ]);
          $this->_currentUser = $user["id"];
          $this->_currentUserData = $this->get_UserData($this->_currentUser);
          $success = true;
        }
      }

      if (!$success) { $this->logout(); }
    }

    // ---------------------------------------------------------------------------

    // ### retrieve the currently logged on user's data from the database
    function get_userData($userId) {
      $result = array();

      if ($userId) {
        $q = $this->db
        ->select(["id", "username", "email", "role"])
        ->from("user")
        ->where(["id" => $userId])
        ->get();

        if ($q->num_rows() == 1) { $result = $q->row_array(); }
      }

      return $result;
    }

    // ---------------------------------------------------------------------------

    // ### change currently logged on user's password
    function do_change_password($oldpassword, $newpassword, $cnfpassword) {

      $q = $this->db
      ->select(["shasalt", "shapwd"])
      ->from("user")
      ->where(["id" => $this->_currentUser])
      ->get();

      if ($q->num_rows() != 1) { return 2; } // Error: User not found
      // else

      $oldpassword = trim($oldpassword);
      if ($oldpassword == "") { return 3; } // Error: Empty old password
      // else

      $user = $q->row_array();
      $shapwd = $this->sha1SaltedHash($oldpassword, $user["shasalt"])["shapwd"];

      if ($shapwd != $user["shapwd"]) { return 4; } // Error: Wrong old password
      // else

      $newpassword = trim($newpassword);
      if ($newpassword == "") { return 5; } // Error: Empty new password
      // else

      $cnfpassword = trim($cnfpassword);
      if ($cnfpassword == "") { return 6; } // Error: Empty conf password
      // else

      if ($newpassword != $cnfpassword) { return 7; } // Error: Wrong password confirmation
      // else

      $shaSaltAndPwd = $this->sha1SaltedHash($newpassword);

      $q = $this->db
      ->where("id", $this->_currentUser)
      ->update("user", $shaSaltAndPwd);

      if ($this->db->affected_rows()!=1) { return 8; } // Error while upddating password
      // else

      return 1;  // Success: No Error

    }

    // ---------------------------------------------------------------------------

    // ### create a user account with given credentials
    public function create_user($username, $email, $newpassword, $userrole) {

      $username = trim($username);
      if ($username == "") { return 2; } // Error: User name may not be left blank
      // else

      $q = $this->db
      ->select(["id"])
      ->from("user")
      ->where(["username" => $username])
      ->get();

      if ($q->num_rows() == 1) { return 3; } // Error: Username alredy exists
      // else

      $email = trim($email);
      if ($email == "") { return 4; } // Error: User name may not be left blank
      // else

      $email_valid = (filter_var($email, FILTER_VALIDATE_EMAIL));
      if (!$email_valid) { return 11; } // Error: Invalid e-mail address
      // else

      $q = $this->db
      ->select(["id"])
      ->from("user")
      ->where(["email" => $email])
      ->get();

      if ($q->num_rows() == 1) { return 5; } // Error: E-mail address already exists
      // else

      $newpassword = trim($newpassword);
      if ($newpassword == "") { return 6; } // Error: Empty new password
      // else

      $isSuperAdmin = $this->isSuperAdmin();
      $isTechAdmin = $this->isTechAdmin();
      $isAppAdmin = $this->isAppAdmin();

      $canCreateRoles = array();
      if ($isSuperAdmin) { $canCreateRoles[] = $this->userRoles["techAdmin"]; }
      if ($isTechAdmin)  { $canCreateRoles[] = $this->userRoles["appAdmin"]; }
      if ($isAppAdmin)   { $canCreateRoles[] = $this->userRoles["user"]; }

      if (($isSuperAdmin) and (!in_array($userrole, $canCreateRoles))) { return 7; } // Error: SuperAdmin no privileges
      // else
      if (($isTechAdmin) and  (!in_array($userrole, $canCreateRoles))) { return 8; } // Error: TechAdmin no privileges
      // else
      if (($isAppAdmin) and   (!in_array($userrole, $canCreateRoles))) { return 9; } // Error: AppAdmin no privileges
      // else

      // $shasalt = sha1(openssl_random_pseudo_bytes(1024)); // 1k salt entropy
      // $shapwd = sha1($newpassword.$shasalt);

      $newUser = array(
        "username" => $username,
        "email" => $email,
        "role" => $userrole,
      );

      $newUser = array_merge($newUser, $this->sha1SaltedHash($newpassword));

      $q = $this->db->insert("user", $newUser);
      if ($this->db->affected_rows()!=1) { return 10; } // Error while upddating password
      // else

      return 1; # Success!!

    }

    // ---------------------------------------------------------------------------

    // ### retrieve (segment of) user list including all user data, to be displayed in a table
    public function getUserTable($page = 0) {

      $numPerPage = 15;

      $cnt = $this->db->count_all('user');
      $maxPage = intval(($cnt-1)/$numPerPage);

      $page = ($page < 0 ? 0 : $page);
      $page = ($page > $maxPage ? $maxPage : $page);

      $skip = $page * $numPerPage;

      $qu = $this->db
      ->select(["id", "username", "email", "role"])
      ->get("user", $numPerPage, $skip);

      $result = array();

      foreach($qu->result_array() as $row) {
        $row["roleVerb"] = $this->userRoles[ $row["role"] ];
        $result[] = $row;
      }

      return $result;

    }

    // ---------------------------------------------------------------------------

    // ### Update a user -- obviously an admin function, based on the admin's respective privileges
    public function updateUser( $id, $username, $email, $newpassword, $userrole, $roles) {

      if ($id === false) { return 2; } // no user ID
      // else

      $userData = $this->get_userData($id);
      if (!$userData) { return 3; } // user not found
      // else

      if ( !in_array($userrole, $roles) ) { return 4; } // insufficient privileges to update role
      // else

      $username = trim($username);
      if (!$username) { return 5; } // empty user name
      // else

      if ($username != $userData["username"]) {
        $q = $this->db
        ->select(["id"])
        ->from("user")
        ->where(["username" => $username])
        ->get();
        if ($q->num_rows() == 1) { return 6; } // user name already exists
      }

      $email = trim($email);
      if (!$email) { return 7; } // empty e-mail
      // else

      if ($email != $userData["email"]) {
        $q = $this->db
        ->select(["id"])
        ->from("user")
        ->where(["email" => $email])
        ->get();
        if ($q->num_rows() == 1) { return 8; } // e-mail already exists
      }

      $email_valid = (filter_var($email, FILTER_VALIDATE_EMAIL));
      if (!$email_valid) { return 9; } // Error: Invalid e-mail address
      // else

      $updates = [];

      $newpassword = trim($newpassword);
      if ($newpassword) {
        $updates = array_merge($updates, $this->sha1SaltedHash($newpassword));
      }

      if ($username != $userData["username"]) { $updates["username"] = $username; }
      if ($email != $userData["email"]) { $updates["email"] = $email; }
      if ($userrole != $userData["role"]) { $updates["role"] = $userrole; }

      // echo "<pre>".print_r($updates,1)."</pre>";

      if (!$updates) { return 10; } // no updates
      // else

      $q = $this->db
      ->where("id", $id)
      ->update("user", $updates);
      if ($this->db->affected_rows()!=1) { return 11; } // Error while upddating user
      else

      return 1; # Success!!

    }

    // ---------------------------------------------------------------------------
  }

?>
