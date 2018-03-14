<?php

  class User_model extends CI_model {

    function logout() {
      session_destroy();
    }

    function do_login($username, $password) {
      $q = $this->db
      ->select(["id", "shasalt", "shapwd"])
      ->from("user")
      ->where(["username" => $username])
      ->get();

      $user = array();
      if ($q->num_rows() == 1) {
        $user = $q->row_array();
        $shapwd = sha1($password.$user["shasalt"]);

        if ($shapwd == $user["shapwd"]) {
          $this->session->set_userdata(array(
            "userID" => $user["id"],
          ));
        }
        else {
          $this->logout();
        }
      }

      $this->_currentUser = $this->session->userdata("userID");
      $this->_currentUserData = $this->get_UserData($this->_currentUser);
    }

    function get_userData($userId) {
      $result = array();

      if ($userId) {
        $q = $this->db
        ->select(["id", "username", "role"])
        ->from("user")
        ->where(["id" => $userId])
        ->get();

        $result = $q->row_array();
      }

      return $result;
    }

  }

?>
