<!doctype html>
<html lang="en">
  <head>
    <title>home inc header_view title</title>
    <link rel="stylesheet" href="<?=base_url()?>public/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=base_url()?>public/css/style.css">
    <script src="<?=base_url()?>public/js/jquery.min.js"></script>
    <script src="<?=base_url()?>public/js/bootstrap.min.js"></script>
  </head>
  <body>

    <!--<header>inc header_view</header>-->

    <div class="wrapper"> <!-- start:wrapper -->

<?php if ($this->session->flashdata("notification")) { ?>
      <div class="alert alert-success"><?= $this->session->flashdata("notification") ?></div>
<?php } ?>
