<?php

  class User_model extends CI_model {

    // ---------------------------------------------------------------------------

  	protected $_currentUser;
  	protected $_currentUserData;

    // ----------------------

    public function currentUser() { return $this->_currentUser; }
    public function currentUserData() { return $this->_currentUserData; }

    // ---------------------------------------------------------------------------

    public function __construct() {
  		parent::__construct();

      $this->_currentUser = $this->session->userdata("userID");
  		$this->_currentUserData = $this->get_UserData($this->_currentUser);
    }

    // ---------------------------------------------------------------------------

    public function enforceNoLogin($redirectTarget = "/user/") {
      if ($this->_currentUser) { redirect($redirectTarget); }
    }

    // ----------------------

    public function enforceLogin($redirectTarget = "/user/") {
      if (!$this->_currentUser) { redirect($redirectTarget); }
    }

    // ---------------------------------------------------------------------------

    public $userRoles = array(
      "superAdmin" => 0, 0 => "Super Administrator",
      "techAdmin"  => 1, 1 => "Technical Administrator",
      "appAdmin"   => 2, 2 => "Application Administrator",
      "user"       => 3, 3 => "Regular User",
    );

    // ----------------------

    public function isSuperAdmin() {
      return (
        ($this->_currentUser)
        and ($this->_currentUserData["role"] == $this->userRoles["superAdmin"])
      );
    }

    public function enforceSuperAdmin($redirectTarget = "/user/") {
      if (!$this->isSuperAdmin()) { redirect($redirectTarget); }
    }

    // ----------------------

    public function isTechAdmin() {
      return
      (
        (
          ($this->_currentUser)
          and ($this->_currentUserData["role"] == $this->userRoles["techAdmin"])
        )
        or ( $this->isSuperAdmin() )
      );
    }

    public function enforceTechAdmin($redirectTarget = "/user/") {
      if (!$this->isTechAdmin()) { redirect($redirectTarget); }
    }

    // ----------------------

    public function isAppAdmin() {
      return
      (
        (
          ($this->_currentUser)
          and ($this->_currentUserData["role"] == $this->userRoles["appAdmin"])
        )
        or ( $this->isTechAdmin() )
      );
    }

    public function enforceAppAdmin($redirectTarget = "/user/") {
      if (!$this->isAppAdmin()) { redirect($redirectTarget); }
    }

    // ---------------------------------------------------------------------------

    function logout() { session_destroy(); }

    // ---------------------------------------------------------------------------

    function do_login($username, $password) {
      $q = $this->db
      ->select(["id", "shasalt", "shapwd"])
      ->from("user")
      ->where(["username" => $username])
      ->get();

      $password = trim($password);

      $user = array();
      if ($q->num_rows() == 1) {
        $user = $q->row_array();
        $shapwd = sha1($password.$user["shasalt"]);

        if ($shapwd == $user["shapwd"]) {
          $this->session->set_userdata([ "userID" => $user["id"], ]);
          $this->_currentUser = $user["id"];
          $this->_currentUserData = $this->get_UserData($this->_currentUser);
        }
      }
    }

    // ---------------------------------------------------------------------------

    function get_userData($userId) {
      $result = array();

      if ($userId) {
        $q = $this->db
        ->select(["id", "username", "email", "role"])
        ->from("user")
        ->where(["id" => $userId])
        ->get();

        $result = $q->row_array();
      }

      return $result;
    }

    // ---------------------------------------------------------------------------

    function do_change_password($oldpassword, $newpassword, $cnfpassword) {
      // echo "<pre>$currentUser / $oldpassword / $newpassword / $cnfpassword</pre>";

      $currentUser = $this->_currentUser;

      $q = $this->db
      ->select(["shasalt", "shapwd"])
      ->from("user")
      ->where(["id" => $currentUser])
      ->get();

      if ($q->num_rows() != 1) { return 1; } // Error: User not found
      // else

      $user = $q->row_array();
      $shapwd = sha1($oldpassword.$user["shasalt"]);

      if ($shapwd != $user["shapwd"]) { return 2; } // Error: Wrong old password
      // else

      $newpassword = trim($newpassword);
      if ($newpassword == "") { return 3; } // Error: Empty new password
      // else

      if ($newpassword != $cnfpassword) { return 4; } // Error: Wrong password confirmation
      // else

      $shasalt = sha1(openssl_random_pseudo_bytes(1024)); // 1k salt entropy
      $shapwd = sha1($newpassword.$shasalt);
      // echo "<pre>$shasalt / $shapwd</pre>";

      $q = $this->db
      ->where("id", $currentUser)
      ->update("user", [
        "shapwd" => $shapwd,
        "shasalt" => $shasalt
      ]);

      if ($this->db->affected_rows()!=1) { return 5; } // Error while upddating password
      // else

      return 6;  // Success: No Error

    }

    // ---------------------------------------------------------------------------

  }

?>
