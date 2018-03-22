<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $err = "<strong>Error:</strong>";
  $errorMessages = [
    1 => "$err User not found.",
    2 => "$err Current password may not be left blank.",
    3 => "$err Current password entered incorrectly.",
    4 => "$err New password may not be left blank.",
    5 => "$err Password conformation may not be left blank.",
    6 => "$err New password confirmation entered incorrectly.",
    7 => "$err Error while updating password.",
    8 => "<strong>Success</strong>: Password changed successfully.",
    9 => "$err Unknown error.",
  ];
?>

<script>
  $(function() {
    $("#changepasswordform").submit(function(event){
      var error = 0;
      var oldpassword = $("#oldpassword").val().trim();
      var newpassword = $("#newpassword").val().trim();
      var cnfpassword = $("#cnfpassword").val().trim();
      if (!oldpassword) { error = 2; }
      else if (!newpassword) { error = 4; }
      else if (!cnfpassword) { error = 5; }
      else if (newpassword != cnfpassword) { error = 6; }
      if (error) {
        var errMsg = "";
        switch (error) {
          case 2: {
            errMsg="<?= $errorMessages[2] ?>";
            $("#oldpassword").focus();
          } break;
          case 4: {
            errMsg="<?= $errorMessages[4] ?>";
            $("#newpassword").focus(); break;
          } break;
          case 5: {
            errMsg="<?= $errorMessages[5] ?>";
            $("#cnfpassword").focus(); break;
          } break;
          case 6: {
            errMsg="<?= $errorMessages[6] ?>";
            $("#cnfpassword").focus(); break;
          } break;
        }
        $("#alert").hide().html(errMsg).show("slow");
        event.preventDefault();
      }
    });
  });
</script>

<h1>Change Password</h1>

<?php

  $alertDisplay = ( $passChangeResult ? "block" : "none" );

  echo "<div id='alert' class='alert' style='display:$alertDisplay'>\n";

  $errorMessage = "";
  if ($passChangeResult) {
    $errorMessage = (
      $errorMessages[$passChangeResult]
      ? $errorMessages[$passChangeResult]
      : $errorMessages[9]
    );
  }
  echo $errorMessage;

  echo "</div>\n";

?>

<p><strong>Current Username:</strong> <?= $username ?></p>

<?php

  $this->load->helper('form');

  echo form_open("/user/do_change_password", [
    "class" => "form-horizontal",
    "method" => "post",
    "id" => "changepasswordform",
  ]);

  echo form_label("Current Password:", "oldpassword");
  $oldPasswordAttr = array(
    "id" => "oldpassword",
    "name" => "oldpassword",
    "placeholder" => "Please enter your current password",
    "autofocus" => 1,
    // "required" => 1,
  );
  echo form_password($oldPasswordAttr);

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
