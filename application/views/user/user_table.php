<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1>List of Users</h1>

<?php

  if (isset($updateSuccess)) {
    ?>
<div id="alert" class="alert" style="display:none;">User updated successfully.</div>
<script>
  $(function() {
    $("#alert").show("slow").delay(2500).hide("slow");

    var row=$("#id<?= $updateSuccess ?>").closest("tr");
    var maxSat = 50;

    function colorTween(now,fx) {
      var perc = fx.now; // of fx.end == 100, as specified in .animate
      var colVal = Math.floor(255-perc*maxSat/100);
      row.css("background-color", "rgb("+colVal+",255,255)")
    }

    row.animate( { _perc: 100 }, { duration: 500, step: colorTween });
    setTimeout(function (){
      row.animate( { _perc: 0 }, { duration: 500, step: colorTween,
        complete: function() { row.css("background-color", "") }
      });
    }, 5000);
  });
</script>
    <?php
  }

  // $this->table->heading[]=[ "data" => "Admin"];
  $this->table->set_heading( ["User ID", "Username", "E-Mail", "Role", "Role Verbatim", "Admin"] );
  $this->table->set_template( ["table_open" => "<table class='table'>"] );

  foreach(array_keys($tablePage) as $row) {
    $id = $tablePage[$row]["id"];
    $tablePage[$row]["id"] = "<span id='id$id'>$id</span>";
    // admin link only if current user's role is of higher privilege
    $tablePage[$row][] = (
      $tablePage[$row]["role"] <= $this->user_model->currentUserData()["role"]
      ? "—"
      : "<a href='".site_url("user")."/edit_user"
        ."/".$id
        ."/".$page
        ."'>Edit User</a>"
    );
  }

  // echo "<pre>".print_r($tablePage,1)."</pre>"; die();

  echo $this->table->generate($tablePage);
?>

<p><a href="<?= site_url("user") ?>" class="btn">Back</a></p>
