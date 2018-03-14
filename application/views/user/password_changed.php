<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1>Change Password</h1>

<?php
  $msg = "";
  $err = "<strong>Error:</strong>";

  switch ($result) {
    case 6: $msg = "<strong>Success</strong>: Password changed successfully."; break;
    case 5: $msg = "$err Error while updating password."; break;
    case 4: $msg = "$err New password confirmation entered incorrectly."; break;
    case 3: $msg = "$err New password may not be left blank."; break;
    case 2: $msg = "$err Current password entered incorrectly."; break;
    case 1: $msg = "$err User not found."; break;
    case 0:
    default: $msg = "$err Unexpected Error."; break;
  }

  $url_suffix = ($result == 6 ? "" : "/change_password");

?>

<p><?= $msg; ?></p>

<p><a href="<?= site_url("user").$url_suffix ?>" class="btn">Back</a></p>
