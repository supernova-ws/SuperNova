<?php
  ob_start();
  header('location: overview.php');
  ob_end_flush();
  die();
?>