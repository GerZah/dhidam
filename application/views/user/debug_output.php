<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php

  echo "<pre>". print_r($this->session->userdata(),true) ."</pre>\n";

  if ($currentUser) {
    // echo "<pre>". $currentUser ."</pre>\n";
    echo "<pre>currentUserData:<br />". print_r($currentUserData, true) ."</pre>\n";
  }
?>
