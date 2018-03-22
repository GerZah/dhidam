<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1>Login Page</h1>

<?php

  $loginError = $this->session->flashdata("loginError");

  if ($loginError) {
    echo "<div class='alert'>\n";
    switch ($loginError) {
      case 1: echo "<strong>Note:</strong> User name can not be left blank."; break;
      case 2: echo "<strong>Note:</strong> Password field can not be left blank."; break;
      case 3: echo "<strong>Error:</strong> Login failed. Please try again."; break;
    }
    echo "</div>\n";
  }

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
  if (!$defUsername) { $usernameAttr["autofocus"] = 1; }
  echo form_input($usernameAttr);

  echo form_label("Password:", "password");
  $passwordAttr = array(
    "id" => "password",
    "name" => "password",
    "placeholder" => "Please enter your password",
  );
  if ($defUsername) { $passwordAttr["autofocus"] = 1; }
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
