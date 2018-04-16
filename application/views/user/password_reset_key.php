<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $err = "<strong>Error:</strong>";
  $errorMessages = [
    0 => "$err Unknown error.",
    1 => "$err User name can not be left blank.",
    2 => "$err Username does not match password reset URL.",
    3 => "$err New password may not be left blank.",
    4 => "$err Password conformation may not be left blank.",
    5 => "$err New password confirmation entered incorrectly.",
    6 => "$err Error while updating password.",
  ];
?>

<script>
  // $(function() {
  //   $("#changepasswordform").submit(function(event){
  //     var error = 0;
  //     var username = $("#username").val().trim();
  //     var newpassword = $("#newpassword").val().trim();
  //     var cnfpassword = $("#cnfpassword").val().trim();
  //     if (!username) { error = 1; }
  //     else if (!newpassword) { error = 3; }
  //     else if (!cnfpassword) { error = 4; }
  //     else if (newpassword != cnfpassword) { error = 5; }
  //     if (error) {
  //       var errMsg = "";
  //       switch (error) {
  //         case 1: {
  //           errMsg="<?= $errorMessages[1] ?>";
  //           $("#username").focus();
  //         } break;
  //         case 3: {
  //           errMsg="<?= $errorMessages[3] ?>";
  //           $("#newpassword").focus();
  //         } break;
  //         case 4: {
  //           errMsg="<?= $errorMessages[4] ?>";
  //           $("#cnfpassword").focus();
  //         } break;
  //         case 5: {
  //           errMsg="<?= $errorMessages[5] ?>";
  //           $("#cnfpassword").focus();
  //         } break;
  //       }
  //       $("#alert").hide().html(errMsg).show("slow");
  //       event.preventDefault();
  //     }
  //   });
  // });
</script>

<h1>Set New Password</h1>

<?php

  $alertDisplay = ( $passChangeResult ? "block" : "none" );

  echo "<div id='alert' class='alert' style='display:$alertDisplay'>\n";

  $errorMessage = "";
  if ($passChangeResult) {
    $errorMessage = (
      $errorMessages[$passChangeResult]
      ? $errorMessages[$passChangeResult]
      : $errorMessages[0]
    );
  }
  echo $errorMessage;

  echo "</div>\n";

?>

<?php

  $this->load->helper('form');

  echo form_open("/user/do_reset_password", [
    "class" => "form-horizontal",
    "method" => "post",
    "id" => "resetpasswordform",
  ], [
    "resetkey" => $resetKey
  ]);

  echo form_label("Enter username as you did when requesting the password reset:", "username");
  $usernameAttr = array(
    "id" => "username",
    "name" => "username",
    "placeholder" => "Please enter your user name",
    "autofocus" => 1,
    // "class" => "form-control",
    // "required" => 1,
  );
  echo form_input($usernameAttr);

  echo form_label("New Password:", "newpassword");
  $newPasswordAttr = array(
    "id" => "newpassword",
    "name" => "newpassword",
    "placeholder" => "Please enter your new password",
    // "required" => 1,
  );
  echo form_password($newPasswordAttr);

  echo "<br />";

  $cnfpasswordAttr = array(
    "id" => "cnfpassword",
    "name" => "cnfpassword",
    "placeholder" => "Please confirm your new password",
    // "required" => 1,
  );
  echo form_password($cnfpasswordAttr);

  // echo form_label("Click to Submit:", "chgPswdBtn");
  echo form_submit([
    "id" => "chgPswdBtn",
    "value" => "Change Password",
    "class" => "btn btn-success"
  ]);

  echo form_close();

?>

<p><a href="<?= site_url("user") ?>" class="btn">Back</a></p>
