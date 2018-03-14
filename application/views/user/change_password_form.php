<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1>Change Password</h1>

<?php

  $this->load->helper('form');

  echo form_open("/user/do_change_password", [
    "class" => "form-horizontal",
    "method" => "post"
  ]);

  echo form_label("Current Password:", "oldpassword");
  $oldPasswordAttr = array(
    "id" => "oldpassword",
    "name" => "oldpassword",
    "placeholder" => "Please enter your current password",
  );
  echo form_password($oldPasswordAttr);

  echo form_label("New Password:", "newpassword");
  $newPasswordAttr = array(
    "id" => "newpassword",
    "name" => "newpassword",
    "placeholder" => "Please enter your new password",
  );
  echo form_password($newPasswordAttr);

  echo "<br />";

  $cnfpasswordAttr = array(
    "id" => "cnfpassword",
    "name" => "cnfpassword",
    "placeholder" => "Please confirm your new password",
  );
  echo form_password($cnfpasswordAttr);

  // echo form_label("Click to Submit:", "chgPswdBtn");
  echo form_submit([
    "id" => "chgPswdBtn",
    "value" => "Change Password",
    "class" => "btn btn-success"
  ]);

?>

<p><a href="<?= site_url("user") ?>" class="btn">Back</a></p>
