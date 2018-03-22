<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $err = "<strong>Error:</strong>";
  $errorMessages = [
     0 => "$err Unknown error.",
     2 => "$err User name may not be left blank.",
     3 => "$err User name already exists.",
     4 => "$err E-mail address may not be left blank.",
     5 => "$err E-mail address already in use.",
     6 => "$err Password may not be left blank.",
     7 => "$err Super Administrator may not create a user of this role.",
     8 => "$err Technical Administrator may not create a user of this role.",
     9 => "$err Application Administrator may not create a user of this role.",
    10 => "$err User could not be created.",
    11 => "$err Invalid e-mail address.",
  ];
?>

<script src="<?=base_url()?>public/js/generate_password.js"></script>
<script src="<?=base_url()?>public/js/email_regex.js"></script>
<script>
  $(function() {

    $("#generatePwdBtn").click(function (){
      $("#newpassword").val(generatePassword());
    });

    $("#createuserform").submit(function(event){
      var error = 0;
      var username = $("#username").val().trim();
      var email = $("#email").val().trim();
      var validEmail = validateEmail(email);
      var newpassword = $("#newpassword").val().trim();
      if (!username) { error = 2; }
      else if (!email) { error = 4; }
      else if (!validEmail) { error = 11; }
      else if (!newpassword) { error = 6; }
      if (error) {
        var errMsg = "";
        switch (error) {
          case 2: {
            errMsg="<?= $errorMessages[2] ?>";
            $("#username").focus();
          } break;
          case 4: {
            errMsg="<?= $errorMessages[4] ?>";
            $("#email").focus();
          } break;
          case 6: {
            errMsg="<?= $errorMessages[6] ?>";
            $("#newpassword").focus();
          } break;
          case 11: {
            errMsg="<?= $errorMessages[11] ?>";
            $("#email").focus();
          } break;
        }
        $("#alert").hide().html(errMsg).show("slow");
        event.preventDefault();
      }
    });

  });
</script>

<h1>Create New User</h1>

<?php

  $alertDisplay = ( $createUserResult ? "block" : "none" );

  echo "<div id='alert' class='alert' style='display:$alertDisplay'>\n";

  $errorMessage = "";
  if ($createUserResult) {
    $errorMessage = (
      $errorMessages[$createUserResult]
      ? $errorMessages[$createUserResult]
      : $errorMessages[0]
    );
  }
  echo $errorMessage;

  echo "</div>\n";

  $this->load->helper('form');

  echo form_open("/user/do_create_user", [
    "class" => "form-horizontal",
    "method" => "post",
    "id" => "createuserform",
  ]);

  echo form_label("Username:", "username");
  $usernameAttr = array(
    "id" => "username",
    "name" => "username",
    "placeholder" => "Please enter new user name",
    // "class" => "form-control",
    // "required" => 1,
  );
  if (in_array($createUserResult, [2,3])) { $usernameAttr["autofocus"] = 1; }
  if ($defUsername) { $usernameAttr["value"] = $defUsername; }
  echo form_input($usernameAttr);

  echo form_label("E-Mail:", "email");
  $emailAttr = array(
    "id" => "email",
    "name" => "email",
    "placeholder" => "Please enter new user's e-mail address",
    // "class" => "form-control",
    // "required" => 1,
  );
  if (in_array($createUserResult, [4,5,11])) { $emailAttr["autofocus"] = 1; }
  if ($defEmail) { $emailAttr["value"] = $defEmail; }
  echo form_input($emailAttr);

  echo form_label("Password:", "newpassword");
  $passwordAttr = array(
    "id" => "newpassword",
    "name" => "newpassword",
    "placeholder" => "Please enter new user's password",
    // "required" => 1,
  );
  if (in_array($createUserResult, [6])) { $passwordAttr["autofocus"] = 1; }
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
  if (in_array($createUserResult, [7,8,9])) { $roleAttr["autofocus"] = 1; }
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
