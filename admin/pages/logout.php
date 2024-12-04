<?php

  @session_start();
  session_destroy();
  echo '<script>location.href="'.APP_URL.'/admin/login";</script>';
  exit;
  
?>
