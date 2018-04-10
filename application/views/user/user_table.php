<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1>List of Users</h1>

<?php

  // $this->table->heading[]=[ "data" => "Admin"];
  $this->table->set_heading( ["User ID", "Username", "E-Mail", "Role", "Role Verbatim", "Admin"] );
  $this->table->set_template( ["table_open" => "<table class='table'>"] );

  foreach(array_keys($tablePage) as $row) {
    // admin link only if current user's role is of higher privilege
    $tablePage[$row][] = (
      $tablePage[$row]["role"] <= $this->user_model->currentUserData()["role"]
      ? "—"
      : "<a href='".site_url("user")."/edit_user/".$tablePage[$row]["id"]."'>Edit User</a>"
    );
  }

  echo $this->table->generate($tablePage);
?>

<p><a href="<?= site_url("user") ?>" class="btn">Back</a></p>
