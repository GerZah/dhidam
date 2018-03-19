<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1>User</h1>

<p>
  <?php if (!$currentUser) { ?>
    <a class="btn" href="<?= site_url("user/login") ?>">Login</a>
  <?php } ?>
  <?php if ($currentUser) { ?>
    <a class="btn" href="<?= site_url("user/change_password") ?>">Change Password</a>
    <a class="btn" href="<?= site_url("user/logout") ?>">Logout</a>
  <?php } ?>
</p>
