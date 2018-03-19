<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1>Login Page</h1>

<?php

  $defUsername = $this->session->flashdata("defUsername");

  $this->load->helper('form');

  echo form_open("/user/do_login", [
    "class" => "form-horizontal",
    "method" => "post"
  ]);

  echo form_label("Username:", "username");
  $usernameAttr = array(
    "id" => "username",
    "name" => "username",
    "placeholder" => "Please enter your user name",
    "value" => $defUsername,
    // "class" => "form-control",
  );
  if ($defUsername === false) { $usernameAttr["autofocus"] = 1; }
  echo form_input($usernameAttr);

  echo form_label("Password:", "password");
  $passwordAttr = array(
    "id" => "password",
    "name" => "password",
    "placeholder" => "Please enter your password",
  );
  if ($defUsername !== false) { $passwordAttr["autofocus"] = 1; }
  echo form_password($passwordAttr);

  // echo form_label("Click to Submit:", "loginBtn");
  echo form_submit([
    "id" => "loginBtn",
    "value" => "Login",
    "class" => "btn btn-success"
  ]);

  echo form_close();

?>

<p><a href="<?= site_url("user") ?>" class="btn">Back</a></p>
