<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1>Create New User</h1>

<?php

  $this->load->helper('form');

  echo form_open("/user/do_create_user", [
    "class" => "form-horizontal",
    "method" => "post"
  ]);

  echo form_label("Username:", "username");
  $usernameAttr = array(
    "id" => "username",
    "name" => "username",
    "placeholder" => "Please enter new user name",
    // "class" => "form-control",
  );
  // if ($defUsername === false) { $usernameAttr["autofocus"] = 1; }
  echo form_input($usernameAttr);

  echo form_label("E-Mail:", "email");
  $usernameAttr = array(
    "id" => "email",
    "name" => "email",
    "placeholder" => "Please enter new user's e-mail address",
    // "class" => "form-control",
  );
  echo form_input($usernameAttr);

  echo form_label("Password:", "password");
  $passwordAttr = array(
    "id" => "password",
    "name" => "password",
    "placeholder" => "Please enter new user's password",
  );
  echo form_password($passwordAttr);

  echo form_label("User Role:", "userrole");
  $roleAttr = array(
    "id" => "userrole",
    "name" => "userrole",
    "options" => $roles,
  );
  echo form_dropdown($roleAttr);

  // echo form_label("Click to Submit:", "loginBtn");
  echo form_submit([
    "id" => "createBtn",
    "value" => "Create User",
    "class" => "btn btn-success"
  ]);

  echo form_close();

?>

<p><a href="<?= site_url("user") ?>" class="btn">Back</a></p>
