<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1>User Administration</h1>

<?php

  $this->load->helper('form');

  echo form_open("/user/do_edit_user", [
    "class" => "form-horizontal",
    "method" => "post",
    "id" => "useradminform",
  ], [
    "id" => $userData["id"],
  ]);

  echo form_label("Modified Username:", "username");
  $usernameAttr = array(
    "id" => "username",
    "name" => "username",
    "placeholder" => "Please enter modified user name",
    "value" => $userData["username"],
    // "class" => "form-control",
    // "required" => 1,
  );
  echo form_input($usernameAttr);

  echo form_label("Modified E-Mail:", "email");
  $emailAttr = array(
    "id" => "email",
    "name" => "email",
    "placeholder" => "Please enter modified e-mail address",
    "value" => $userData["email"],
    // "class" => "form-control",
    // "required" => 1,
  );
  echo form_input($emailAttr);

  echo form_label("User Role:", "userrole");
  $roleAttr = array(
    "id" => "userrole",
    "name" => "userrole",
    "options" => $roles,
    "selected" => $userData["role"],
  );
  // $roleAttr["selected"] = $defUserRole;
  echo form_dropdown($roleAttr);

  echo form_label("New Password:", "newpassword");
  $passwordAttr = array(
    "id" => "newpassword",
    "name" => "newpassword",
    "placeholder" => "Leave empty to keep password unchanged",
    // "required" => 1,
  );
  echo form_input($passwordAttr);

  // echo form_label("Click to Submit:", "createBtn");
  echo form_submit([
    "id" => "updateBtn",
    "value" => "Update User",
    "class" => "btn btn-success"
  ]);

  echo form_close();

?>

<p><a href="<?= site_url("user/user_table") ?>" class="btn">Back</a></p>
