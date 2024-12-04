<?php

if (!isset($_SESSION['ADM_LOGGED'])) {
  echo '<script>location.href="' . APP_URL . '/admin/login";</script>';
  exit;
}

include_once 'inc/header.php';

$marketplace = new Marketplace();
$accounts    = $marketplace->getMarketpalces();

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
                <div class="col-lg-6 col-md-6 col-12">
                  <h6>Configurações de contas Market Place</h6>
                  <p style="font-size:11px;" >
                    Adicione uma ou mais contas do Mercado Pago/Livre
                    <br />
                    Lembre-se que o access token Mercado Pago, funciona na conta Mercado Livre vinculada.
                  </p>
                </div>
                <div class="col-lg-6 col-md-6 col-12" >
                    <button onclick="modalAddAccountMKT();" class="btn btn-success" >Cadastrar nova conta <i class="fa fa-plus" ></i> </button>
                </div>
              </div>
            </div>
            <div class="card-body px-0 ">

        
             <?php if($accounts){ foreach($accounts as $k => $account){ $data_account = json_decode($account->data_account); ?>
              <div class="row p-4">
                    <div class="col-md-12 col-lg-12 col-12 p-2" style="border: 2px dotted gray;" >
                        <div class="row" >
                               <div class="col-md-12">
                                  <h4>Mercado Pago/Livre </h4>
                                  <p class="text-secondary" >
                                      [#<?= $data_account->nickname;?>] - <?= $data_account->first_name . ' '. $data_account->last_name;?> (<?= $data_account->email;?>)
                                  </p>
                                </div>
                
                                <div class="col-md-6 ml-2 mb-3">
                                  <label class="">Access token Mercado Pago</label>
                                  <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" placeholder="Access Token" name="" id="access_token_<?= $account->id; ?>"
                                      value="<?= $account->access_token ? $account->access_token : ""; ?>">
                                  </div>
                                </div>
                
                                <div class="col-md-6 ml-2 mb-3">
                                  <label class="">Webhook</label>
                                  <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" disabled placeholder="Url callback" name="" id="url_callback"
                                      value="<?= LOCAL ? NGROK_LINK : APP_URL; ?>/callback/<?= $account->id; ?>">
                                  </div>
                                  <small style="font-size:12px;">Defina está Webhook em (Webhook e IPN) na sua conta de desenvolvedor
                                    Mercado Pago</small>
                                </div>
                                
                                <div class="col-md-12" >
                                    <button id="btnRemove_<?= $account->id; ?>" onclick="removeAccount(<?= $account->id; ?>);" class="btn btn-danger" > <i class="fa fa-trash" ></i> </button>
                                    <button id="btnSave_<?= $account->id; ?>"  onclick="saveAccount(<?= $account->id; ?>);" class="btn btn-success" > <i class="fa fa-save" ></i> </button>
                                </div>
                        </div>
                    </div>
              </div>
              <?php } }else{ ?>
                <div class="row p-4 text-center">
                    <h5 class="text-secondary" >Nenhuma conta cadastrada</h5>
               </div>
              <?php } ?>


            </div>
          </div>
        </div>

      </div>
      
      
      
      <!-- Modal -->
      <div class="modal fade" id="modalAddAccountMKT" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Adicionar nova conta</h5>
              <a style="cursor:pointer;" onclick="$('#modalAddAccountMKT').modal('toggle');" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </a>
            </div>
            <div class="modal-body">
              <div class="row">

                <div class="col-md-12 text-left">
                    <div class="row p-4">
                        <div class="col-md-12">
                          <h4>Mercado Pago </h4>
                        </div>
        
                        <div class="col-md-12 ml-2 mb-3">
                          <label class="">Access token Mercado Pago</label>
                          <div class="input-group input-group-outline">
                            <input type="text" class="form-control" placeholder="Access Token" name="" id="access_token_new_account"
                              value="">
                          </div>
                        </div>
                        
                        <div style="display:none;" id="divWebhookNewAccount" class="col-md-12 ml-2 mb-3">
                          <label class="">Webhook para esta integração</label>
                          <div class="input-group input-group-outline">
                            <input type="text" class="form-control" placeholder="Webhooko" name="" id="webhook_lasted_inserted"
                              value="">
                          </div>
                          <p class="text-white mt-2 alert alert-warning" >
                              Adicione esta URL de Webhook em "Webhooks" e "IPN".
                          </p>
                        </div>
                        
                      </div>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" onclick="$('#modalAddAccountMKT').modal('toggle');">Fechar</button>
              <button type="button" class="btn btn-primary" id="btnAddAccountMKT" onclick="modalAddAccountMKT('now')" >Adicionar</button>
            </div>
          </div>
        </div>
      </div>

      <script type="text/javascript" src="assets/js/marketplace.js"></script>

      <?php include_once 'inc/header.php'; ?>