<?php

if (!isset($_SESSION['ADM_LOGGED'])) {
  echo '<script>location.href="' . APP_URL . '/admin/login";</script>';
  exit;
}

include_once 'inc/header.php';

$settings = new Settings();
$settings_data = $settings->getSettings();



?>

<script src="
https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/trumbowyg.min.js
"></script>
<link href="
https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/ui/trumbowyg.min.css
" rel="stylesheet">

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
                  <h6>Configurações da conta</h6>
                  <p>
                    Para alterar o template do e-mail deve acessar a pasta: "template/email/index.html". <br>
                  </p>
                  <p id="response_setting"></p>
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
            <div class="card-body px-0 ">
                
                <div class="row p-4">

                <div class="col-md-12">
                  <h4>Template de E-mail</h4>
                </div>

                <div class="col-md-8 ml-2">
                    
                  <label class="">Titulo do email</label>
                  <div class="mb-3 input-group input-group-outline">
                     <input type="text" value="<?= $settings_data->title_template_email ? $settings_data->title_template_email : ""; ?>" id="title_template_email" placeholder="Titulo" class="form-control" >
                  </div>
                  
                  <label class="">Template HTML para envio de e-mail</label>
                  <div class="w-100">
                     <textarea name="template_ml_email" class="form-control" id="template_ml_email" cols="30" rows="10"><?= $settings_data->template_ml_email ? $settings_data->template_ml_email : ""; ?></textarea>
                  </div>
                </div>

                <div class="col-md-4 ml-2">
                  <label class="">Variaveis para usar na mensagem:</label>
                   <p>
                     <ul>
                        <li> <code>{first_name}</code> : Primeiro nome do comprador </li>
                        <li> <code>{last_name}</code> : Segundo nome do comprador <i style="font-size:10px;" >(Apenas Mercado Livre)</i> </li>
                        <li> <code>{link_download}</code> : Link de download do produto </li>
                        <li> <code>{username}</code> : Username do comprador no ML <i style="font-size:10px;" >(Apenas Mercado Livre)</i> </li>
                        <li> <code>{order_id}</code> : Id do pedido gerado no ML <i style="font-size:10px;" >(Apenas Mercado Livre)</i> </li>
                        <li> <code>{payment_id}</code> : Id do pagamento gerado pelo MP </li>
                        <li> <code>{external_reference}</code> : Referencia externa do pagamento </li>
                        <li> <code>{date}</code> : Data da aprovação do pagamento </li>
                        <li> <code>{product_name}</code> : Nome do produto </li>
                     </ul>
                   </p>
                </div>

              </div>

              <div class="row p-4">

                <div class="col-md-12">
                  <h4>Template Mensagem Chat Mercado Livre e Whatsapp</h4>
                </div>

                <div class="col-md-6 ml-2">
                  <label class="">Mensagem enviada para o comprador. Tanto pelo site ou pelo mercado livre</label>
                  <div class="input-group input-group-outline">
                     <textarea name="template_ml_message" class="form-control" id="template_ml_message" cols="30" rows="10"><?= $settings_data->template_ml_message ? $settings_data->template_ml_message : ""; ?></textarea>
                  </div>
                  <small>Máximo de 350 caracteres.</small>
                </div>

                <div class="col-md-6 ml-2">
                  <label class="">Variaveis para usar na mensagem:</label>
                   <p>
                     <ul>
                        <li> <code>{first_name}</code> : Primeiro nome do comprador </li>
                        <li> <code>{last_name}</code> : Segundo nome do comprador <i style="font-size:10px;" >(Apenas Mercado Livre)</i> </li>
                        <li> <code>{link_download}</code> : Link de download do produto </li>
                        <li> <code>{username}</code> : Username do comprador no ML <i style="font-size:10px;" >(Apenas Mercado Livre)</i> </li>
                        <li> <code>{order_id}</code> : Id do pedido gerado no ML <i style="font-size:10px;" >(Apenas Mercado Livre)</i> </li>
                        <li> <code>{payment_id}</code> : Id do pagamento gerado pelo MP </li>
                        <li> <code>{external_reference}</code> : Referencia externa do pagamento </li>
                        <li> <code>{date}</code> : Data da aprovação do pagamento </li>

                     </ul>
                   </p>
                </div>

              </div>


              <div class="row p-4">

                <div class="col-md-12">
                  <h4>Dados de acesso </h4>
                </div>

                <div class="col-md-6 ml-2">
                  <label class="">Username</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="Nome de usuário" name="" id="username"
                      value="<?= $settings_data->username ? $settings_data->username : ""; ?>">
                  </div>

                </div>

                <div class="col-md-6 ml-2">
                  <label class="">Password</label>
                  <div class="input-group input-group-outline">
                    <input type="password" class="form-control" placeholder="Senha do admin" name="" id="password"
                      value="">
                  </div>

                </div>


                <div class="col-md-12 mt-2 mb-2">
                  <hr>
                </div>



                <div class="col-md-12 ml-2">
                  <label class="">&nbsp;</label>
                  <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                    <button id="saveSetting" type="button" class="btn btn-success" name="button">Salvar</button>
                  </div>
                </div>

              </div>

            </div>
          </div>
        </div>

      </div>
      
      <script type="text/javascript" src="assets/js/settings.js?v=4"></script>

      <?php include_once 'inc/header.php'; ?>