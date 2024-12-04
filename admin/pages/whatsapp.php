<?php

  if(!isset($_SESSION['ADM_LOGGED'])){
    echo '<script>location.href="'.APP_URL.'/admin/login";</script>';
    exit;
  }

  include_once 'inc/header.php';

  $whatsapp  = new Whatsapp();
  $is_connected = $whatsapp->getStatus();
 
?>

<body class="g-sidenav-show  bg-gray-200">

  <?php include_once 'inc/sidebar.php'; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

    <?php include_once 'inc/navbar.php'; ?>

    <div class="container-fluid py-4">

      <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <div class="row">
                <div class="col-lg-6 col-7">
                  <h6>API Whatsapp</h6>
                  <p>
                    Conecte seu whatsapp para enviar mensagens de recuperação de carrinho
                  </p>
                  <p id="response_whatsapp_api" ></p>
                </div>
                <div class="col-lg-6 col-5 my-auto text-end">
                  <div class="dropdown float-lg-end pe-4">
                    <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa fa-ellipsis-v text-secondary"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">

              <div class="row p-4">

                    

                        <div <?php if($is_connected){ ?> style="display:none;" <?php } ?> id="cardConnect" class="col-md-12 text-center">
                            <h1>Não conectado com Whatsapp</h1>
                            <img width="30%" src="<?= APP_URL; ?>/assets/img/not_connected.webp" alt="">
                            <br>
                            <button class="btn btn-lg btn-success" id="connectWpp" >Conectar <i class="fa fa-plug"></i> </button>
                        </div>

                        <div id="cardQrcode" style="display:none;" class="col-md-12 text-center">
                            <h4>Aponte seu telefone para o qrcode</h4>
                            <img src="" id="qrcodeWpp" alt="" class="mb-3">
                            <br>
                            <button class="btn btn-lg btn-info" onclick="location.href='';" >Recarregar <i class="fa fa-refresh"></i> </button>
                        </div>

                        <div id="cardConected" <?php if(!$is_connected){ ?> style="display:none;" <?php } ?> class="col-md-12 text-center">
                            <h1>Conectado com Whatsapp!</h1>
                            <img width="10%" class="mb-3" src="<?= APP_URL; ?>/assets/img/checked.png" alt="">
                            <br>
                            <button class="btn btn-lg btn-danger" id="disconnect" >Desconectar <i class="fa fa-unlink"></i> </button>
                        </div>


              </div>

            </div>
          </div>
        </div>

      </div>

      <script type="text/javascript" src="assets/js/whatsapp.js" ></script>

    <?php include_once 'inc/header.php';  ?>
