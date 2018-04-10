<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1>List of Users</h1>

<?php

  // $this->table->heading[]=[ "data" => "Admin"];
  $this->table->set_heading( ["User ID", "Username", "E-Mail", "Role", "Role Verbatim", "Admin"] );
  $this->table->set_template( ["table_open" => "<table class='table'>"] );

  foreach(array_keys($tablePage) as $row) {
    $tablePage[$row][]="<a href='".site_url("user")."/admin/".$tablePage[$row]["id"]."'>foo</a>";
  }

  echo $this->table->generate($tablePage);
?>

<p><a href="<?= site_url("user") ?>" class="btn">Back</a></p>
