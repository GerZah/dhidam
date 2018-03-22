<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<script src="<?=base_url()?>public/js/generate_password.js"></script>
<script>
  $(function() {
    $("#generatePwdBtn").click(function (){
      $("#newpassword").val(generatePassword());
    });
  });
</script>

<h1>Create New User</h1>

<?php

  if ($result) {
    $msg = "";
    $err = "<strong>Error:</strong>";

    switch ($result) {
      case 10: $msg = "$err User could not be created."; break;
      case  9: $msg = "$err Application Administrator may not create a user of this role."; break;
      case  8: $msg = "$err Technical Administrator may not create a user of this role."; break;
      case  7: $msg = "$err Super Administrator may not create a user of this role."; break;
      case  6: $msg = "$err Password may not be left blank."; break;
      case  5: $msg = "$err E-mail adddress already in use."; break;
      case  4: $msg = "$err E-mail address may not be left blank."; break;
      case  3: $msg = "$err User name already exists."; break;
      case  2: $msg = "$err User name may not be left blank."; break;
    }

    echo "<div class='alert'>$msg</div>";
  }

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
  if (in_array($result, [2,3])) { $usernameAttr["autofocus"] = 1; }
  if ($defUsername) { $usernameAttr["value"] = $defUsername; }
  echo form_input($usernameAttr);

  echo form_label("E-Mail:", "email");
  $emailAttr = array(
    "id" => "email",
    "name" => "email",
    "placeholder" => "Please enter new user's e-mail address",
    // "class" => "form-control",
  );
  if (in_array($result, [4,5])) { $emailAttr["autofocus"] = 1; }
  if ($defEmail) { $emailAttr["value"] = $defEmail; }
  echo form_input($emailAttr);

  echo form_label("Password:", "newpassword");
  $passwordAttr = array(
    "id" => "newpassword",
    "name" => "newpassword",
    "placeholder" => "Please enter new user's password",
  );
  if (in_array($result, [6])) { $passwordAttr["autofocus"] = 1; }
  if ($defNewPassword) { $passwordAttr["value"] = $defNewPassword; }
  // echo form_password($passwordAttr);
  echo form_input($passwordAttr);

  echo form_button([
    "id" => "generatePwdBtn",
    "content" => "Generate Random Password",
    "class" => "btn"
  ]);

  echo form_label("User Role:", "userrole");
  $roleAttr = array(
    "id" => "userrole",
    "name" => "userrole",
    "options" => $roles,
  );
  if (in_array($result, [7,8,9])) { $roleAttr["autofocus"] = 1; }
  if ($defUserRole) { $roleAttr["selected"] = $defUserRole; }
  echo form_dropdown($roleAttr);

  // echo form_label("Click to Submit:", "createBtn");
  echo form_submit([
    "id" => "createBtn",
    "value" => "Create User",
    "class" => "btn btn-success"
  ]);

  echo form_close();

?>

<p><a href="<?= site_url("user") ?>" class="btn">Back</a></p>
