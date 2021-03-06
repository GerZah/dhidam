<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
  $note = "<strong>Note:</strong>";
  $err = "<strong>Error:</strong>";
  $errorMessages = [
    0 => "$err Unknown error.",
    1 => "$note User name can not be left blank.",
    2 => "$note Password field can not be left blank.",
    3 => "$err Login failed. Please try again.",
    4 => "$note If you entered an existing user name, password reset instructions have been sent via e-mail.",
    5 => "$note Password change successfully – you may now log on.",
  ];
?>

<script>
  $(function() {
    $("#loginform").submit(function(event){
      var error = 0;
      var username = $("#username").val().trim();
      var password = $("#password").val().trim();
      if (!username) { error = 1; }
      else if (!password) { error = 2; }
      if (error) {
        var errMsg = "";
        switch (error) {
          case 1: {
            errMsg="<?= $errorMessages[1] ?>";
            $("#username").focus();
          } break;
          case 2: {
            errMsg="<?= $errorMessages[2] ?>";
            $("#password").focus();
          } break;
        }
        $("#alert").hide().html(errMsg).show("slow");
        event.preventDefault();
      }
    });
  });
</script>

<h1>Login Page</h1>

<?php
  $alertDisplay = ( $loginError ? "block" : "none" );

  echo "<div id='alert' class='alert' style='display:$alertDisplay'>\n";

  $errorMessage = "";
  if ($loginError) {
    $errorMessage = (
      $errorMessages[$loginError]
      ? $errorMessages[$loginError]
      : $errorMessages[0]
    );
  }
  echo $errorMessage;

  echo "</div>\n";

  $this->load->helper('form');

  echo form_open("/user/do_login", [
    "class" => "form-horizontal",
    "method" => "post",
    "id" => "loginform"
  ]);

  echo form_label("Username:", "username");
  $usernameAttr = array(
    "id" => "username",
    "name" => "username",
    "placeholder" => "Please enter your user name",
    "value" => $defUsername,
    // "class" => "form-control",
    // "required" => 1,
  );
  if (!$defUsername) { $usernameAttr["autofocus"] = 1; }
  echo form_input($usernameAttr);

  echo form_label("Password:", "password");
  $passwordAttr = array(
    "id" => "password",
    "name" => "password",
    "placeholder" => "Please enter your password",
    // "required" => 1,
  );
  if ($defUsername) { $passwordAttr["autofocus"] = 1; }
  echo form_password($passwordAttr);

  // echo "<div><br />";
  // echo form_label("Click to Submit:", "loginBtn");
  echo form_submit([
    "id" => "loginBtn",
    "value" => "Login",
    "class" => "btn btn-success"
  ]);
  // echo "</div>";

  echo form_close();

?>

<p>
  <a href="<?= site_url("user") ?>" class="btn">Back</a>
  <a href="<?= site_url("user/password_reset") ?>" class="btn btn-warning">Forgot password</a>
</p>
