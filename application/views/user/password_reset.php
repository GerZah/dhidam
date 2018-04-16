<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $note = "<strong>Note:</strong>";
  $err = "<strong>Error:</strong>";
  $errorMessages = [
    0 => "$err Unknown error.",
    1 => "$note User name can not be left blank.",
  ];
?>

<script>
  $(function() {
    $("#resetpwdform").submit(function(event){
      var error = 0;
      var username = $("#username").val().trim();
      if (!username) { error = 1; }
      if (error) {
        var errMsg = "";
        switch (error) {
          case 1: {
            errMsg="<?= $errorMessages[1] ?>";
            $("#username").focus();
          } break;
        }
        $("#alert").hide().html(errMsg).show("slow");
        event.preventDefault();
      }
    });
  });
</script>

<h1>Request Password Reset</h1>

<?php
  $alertDisplay = ( $pwdRestError ? "block" : "none" );

  echo "<div id='alert' class='alert' style='display:$alertDisplay'>\n";

  $errorMessage = "";
  if ($pwdRestError) {
    $errorMessage = (
      $errorMessages[$pwdRestError]
      ? $errorMessages[$pwdRestError]
      : $errorMessages[0]
    );
  }
  echo $errorMessage;

  echo "</div>\n";

  $this->load->helper('form');

  echo form_open("/user/request_password_reset", [
    "class" => "form-horizontal",
    "method" => "post",
    "id" => "resetpwdform"
  ]);

  echo form_label("Username:", "username");
  $usernameAttr = array(
    "id" => "username",
    "name" => "username",
    "placeholder" => "Please enter your user name",
    "autofocus" => 1,
    // "class" => "form-control",
    // "required" => 1,
  );
  echo form_input($usernameAttr);

  // echo "<div><br />";
  // echo form_label("Click to Submit:", "loginBtn");
  echo form_submit([
    "id" => "resetPwdBtn",
    "value" => "Initiate Password Reset",
    "class" => "btn btn-success"
  ]);
  // echo "</div>";

  echo form_close();

?>

<p>
  <a href="<?= site_url("user") ?>" class="btn">Back</a>
</p>
