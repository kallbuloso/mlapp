<?php

  if(!isset($_SESSION['ADM_LOGGED'])){
    echo '<script>location.href="'.APP_URL.'/admin/login";</script>';
    exit;
  }

  include_once 'inc/header.php';

  $pixel = new Pixel();
  $pixel_facebook = $pixel->getPixelFacebook();

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
                  <h6>Pixel facebook</h6>
                  <p>
                    Defina o PIXEL do facebook para enviar alertas de pagamentos para a rede social
                  </p>
                  <p id="response_pixel" ></p>
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

                <div class="col-md-4 ml-2">
                  <label class="">ID Pixel</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="ID pixel" name="" id="id_pixel" value="<?= $pixel_facebook->id_pixel ? $pixel_facebook->id_pixel : ""; ?>">
                  </div>

                </div>

                <div class="col-md-4 ml-2">
                  <label class="">Token Pixel</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="Token pixel" name="" id="token_pixel" value="<?= $pixel_facebook->token_pixel ? $pixel_facebook->token_pixel : ""; ?>">
                  </div>

                </div>


                <div class="col-md-4 ml-2">
                  <label class="">&nbsp;</label>
                  <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                    <button id="savePixel" type="button" class="btn btn-success" name="button">Salvar</button>
                  </div>
                </div>


              </div>

            </div>
          </div>
        </div>

      </div>

      <script type="text/javascript" src="assets/js/pixel.js" ></script>

    <?php include_once 'inc/header.php';  ?>
