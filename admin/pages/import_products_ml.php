<?php

    if(!isset($_SESSION['ADM_LOGGED'])){
      echo '<script>location.href="'.APP_URL.'/admin/login";</script>';
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
                <div class="col-lg-12 col-12">
                  <h6>Importar produtos</h6>
                  <p class="text-sm mb-0">
                      Após importar os produtos, você deve adicionar seu conteúdo digital em "Produtos".
                  </p>

        
                  <p id="response_import" ></p>
                </div>
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="input-group input-group-outline">
                   <select class="form-control" id="account_import" >
                           <option value="" >Selecionar conta</option>
                           <?php if($accounts){ foreach($accounts as $k => $account){ $data_account = json_decode($account->data_account); ?>
                              <option value="<?= $account->id; ?>" ><?= $data_account->nickname; ?> - <?= $data_account->first_name; ?> (<?= $data_account->email; ?>)</option>
                           <?php } }else{ ?>
                             <option value="" >Nenhuma conta cadastrada</option>
                           <?php } ?>
                       </select>
                   </div>
                </div>
                <div class="col-lg-4 col-md-4 col-12">
                  <button class="btn btn-success" onclick="importMlProducts();" id="btnimport" > Iniciar Importação</button>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Importado</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Imagem</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Titulo</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Preço</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opções</th>

                    </tr>
                  </thead>
                  <tbody id="tableProductsImport">

                    <tr>
                       <td colspan="6" class="text-center">
                          <h4 class="text-secondary" >Aperte em "Iniciar importação" </h4>
                       </td>
                    </tr>

 
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>

      <script type="text/javascript" src="assets/js/mercadolivre.js" ></script>

    <?php include_once 'inc/footer.php';  ?>
