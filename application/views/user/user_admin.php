<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $err = "<strong>Error:</strong>";
  $errorMessages = [
    0 => "$err Unknown error.",
    2 => "$err No user ID.",
    3 => "$err User not found.",
    4 => "$err Insufficient privileges to update user.",
    5 => "$err User name may not be left empty.",
    6 => "$err Specified user name already exists.",
    7 => "$err E-mail address may not be left empty.",
    8 => "$err E-mail address already exists.",
    9 => "$err Invalid e-mail address.",
    10 => "$err No changes to user data.",
    11 => "$err Error while updating user.",
  ];

  $defUsername = ($defUsername ? $defUsername : $userData["username"]);
  $defEmail = ($defEmail ? $defEmail : $userData["email"]);
  $defUserrole = ($defUserrole ? $defUserrole : $userData["role"]);
?>

<script src="<?=base_url()?>public/js/email_regex.js"></script>
<script>
  $(function() {
    $("#useradminform").submit(function(event){
      var error = 0;
      var username = $("#username").val().trim();
      var email = $("#email").val().trim();
      var validEmail = validateEmail(email);
      var newpassword = $("#newpassword").val().trim();
      var userrole = $("#userrole").val();
      console.log(username, email, userrole);
      if (!username) { error = 5; }
      else if (!email) { error = 7; }
      else if (!validEmail) { error = 9; }
      else if (
        (username == "<?= $defUsername ?>")
        && (email == "<?= $defEmail ?>")
        && (userrole == <?= $defUserrole ?>)
        && (newpassword == "")
      ) { error = 10; }
      if (error) {
        var errMsg = "";
        switch (error) {
          case 5: {
            errMsg="<?= $errorMessages[5] ?>";
            $("#username").focus();
          } break;
          case 7: {
            errMsg="<?= $errorMessages[7] ?>";
            $("#email").focus();
          } break;
          case 9: {
            errMsg="<?= $errorMessages[9] ?>";
            $("#email").focus();
          } break;
          case 10: {
            errMsg="<?= $errorMessages[10] ?>";
            $("#newpassword").focus();
          } break;
        }
        $("#alert").hide().html(errMsg).show("slow");
        event.preventDefault();
      }
    });

  });
</script>
<h1>User Administration</h1>

<?php

  $alertDisplay = ( $editUserResult ? "block" : "none" );

  echo "<div id='alert' class='alert' style='display:$alertDisplay'>\n";

  $errorMessage = "";
  if ($editUserResult) {
    $errorMessage = (
      $errorMessages[$editUserResult]
      ? $errorMessages[$editUserResult]
      : $errorMessages[0]
    );
  }
  echo $errorMessage;

  echo "</div>\n";

  $this->load->helper('form');

  echo form_open("/user/do_edit_user", [
    "class" => "form-horizontal",
    "method" => "post",
    "id" => "useradminform",
  ], [
    "id" => $userData["id"],
    "page" => $page
  ]);

  echo form_label("Modified Username:", "username");
  $usernameAttr = array(
    "id" => "username",
    "name" => "username",
    "placeholder" => "Please enter modified user name",
    "value" => $defUsername,
    // "class" => "form-control",
    // "required" => 1,
  );
  if (in_array($editUserResult, [3,5,6])) { $usernameAttr["autofocus"] = 1; }
  echo form_input($usernameAttr);

  echo form_label("Modified E-Mail:", "email");
  $emailAttr = array(
    "id" => "email",
    "name" => "email",
    "placeholder" => "Please enter modified e-mail address",
    "value" => $defEmail,
    // "class" => "form-control",
    // "required" => 1,
  );
  if (in_array($editUserResult, [7,8,9])) { $emailAttr["autofocus"] = 1; }
  echo form_input($emailAttr);

  echo form_label("User Role:", "userrole");
  $roleAttr = array(
    "id" => "userrole",
    "name" => "userrole",
    "options" => $roles,
    "selected" => $defUserrole,
  );
  if (in_array($editUserResult, [4])) { $roleAttr["autofocus"] = 1; }
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

<p><a href="<?= site_url("user/user_table/$page") ?>" class="btn">Back</a></p>
