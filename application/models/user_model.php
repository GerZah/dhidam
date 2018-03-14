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

      $result = 0; // Error: unexpected error

      $q = $this->db
      ->select(["shasalt", "shapwd"])
      ->from("user")
      ->where(["id" => $currentUser])
      ->get();

      if ($q->num_rows() == 1) { // User found?
        $user = $q->row_array();
        $shapwd = sha1($oldpassword.$user["shasalt"]);

        if ($shapwd == $user["shapwd"]) { // Old password correct?

          $newpassword = trim($newpassword);
          if ($newpassword != "") { // Non-empty new password?

            if ($newpassword == $cnfpassword) { // New password correctly confirmed?

              $shasalt = sha1(openssl_random_pseudo_bytes(1024)); // 1k salt entropy
              $shapwd = sha1($newpassword.$shasalt);
              // echo "<pre>$shasalt / $shapwd</pre>";

              $q = $this->db
              ->where("id", $currentUser)
              ->update("user", [
                "shapwd" => $shapwd,
                "shasalt" => $shasalt
              ]);

              if ($this->db->affected_rows()==1) {
                $result = 6; // Success: No Error
              }
              else { $result = 5; } // Error while upddating password
            }
            else { $result = 4; } // Error: Wrong password confirmation
          }
          else { $result = 3; } // Error: Empty new password
        }
        else { $result = 2; } // Error: Wrong old password
      }
      else { $result = 1; } // Error: User not found

      return $result;

    }

    // ---------------------------------------------------------------------------

  }

?>
