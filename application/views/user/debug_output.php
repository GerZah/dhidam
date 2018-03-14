<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php

  echo "<pre>". print_r($this->session->userdata(),true) ."</pre>\n";

  if ($currentUser) {
    // echo "<pre>". $currentUser ."</pre>\n";
    echo "<pre>". print_r($currentUserData, true) ."</pre>\n";
  }
?>

<p>
  <a class="btn" href="<?php echo base_url(); ?>user/login">Login</a>
  <a class="btn" href="<?php echo base_url(); ?>user/logout">Logout</a>
</p>
