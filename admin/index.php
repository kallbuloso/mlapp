<?php

  require_once '../config.php';
  require_once '../autoload.php';

  require_once 'inc/header.php';

  if(isset($_GET['url'])){

    $explo_url = explode('/',$_GET['url']);
    $page      = $explo_url[0];
    
    if(is_file('pages/'.$page.'.php')){

      require_once 'pages/'.$page.'.php';

    }else{
      require_once 'pages/404.php';
    }

    require_once 'inc/footer.php';

  }else{
    require_once 'pages/dashboard.php';
    require_once 'inc/footer.php';
  }
