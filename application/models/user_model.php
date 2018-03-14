<?php

  class User_model extends CI_model {

    // ---------------------------------------------------------------------------

    function logout() {
      session_destroy();
    }

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

    // ---------------------------------------------------------------------------

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

    // ---------------------------------------------------------------------------

    function do_change_password($currentUser, $oldpassword, $newpassword, $cnfpassword) {
      // echo "<pre>$currentUser / $oldpassword / $newpassword / $cnfpassword</pre>";

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
