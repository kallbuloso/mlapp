<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);


    if(!isset($_SESSION['ADM_LOGGED'])){
      echo '<script>location.href="'.APP_URL.'/admin/login";</script>';
      exit;
    }

    include_once 'inc/header.php';

    $transaction = new Transaction();
    $dashboard   = new Dashboard();
    $product     = new Product();
    $clients     = new Clients();

    $init_products     = json_decode($product->init());
    $init_transactions = json_decode($transaction->init());
    $init_dashboard    = json_decode($dashboard->init());
    $init_clients      = json_decode($clients->init());
    $transactions_day  = json_decode($transaction->sum_transactions_by_client_id());
    
    


  ?>

 
<body class="g-sidenav-show  bg-gray-200">

  <?php include_once 'inc/sidebar.php'; ?>


  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

    <?php include_once 'inc/navbar.php'; ?>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">loyalty</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Vendas Hoje</p>
                <h4 class="mb-0"><?= $init_transactions->qtd_transactions; ?></h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
              <?= $init_transactions->diff_sales; ?>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">signpost</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Acessos hoje</p>
                <h4 class="mb-0"><?= $init_dashboard->pageViewsToday; ?></h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
              <?= $init_dashboard->diff_pviews; ?>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">person</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Novos clientes essa semana</p>
                <h4 class="mb-0"><?= $init_clients->getUsersLastWeek; ?></h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
              <?= $init_clients->diff_users; ?>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">paid</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Receita esse mês</p>
                <h4 class="mb-0">R$ <?= json_decode($init_transactions->getTransactionsMonthCurrent)->v2; ?></h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
              <?= $init_transactions->diffTransactions; ?>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row mt-2 mb-2">
          <div class="col-md-12">
              <div class="card">
                  <div class="text-sm card-body pt-3 p-3 pt-2">
                      Vendas de novos clientes hoje: R$ <?= $transactions_day->novas; ?> | Venda de Clientes recorrentes hoje: R$ <?= $transactions_day->recorrentes; ?>
                  </div>
              </div>
          </div>
      </div>
      
      <div class="row mt-4">
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 ">Visitas nos produtos</h6>
              <p class="text-sm ">Desempenho de suas campanhas</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card z-index-2  ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-dark shadow-success border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 "> Vendas </h6>
               <?= $init_transactions->diffTransactionMonth; ?>
            </div>
          </div>
        </div>
        <div class="col-lg-4 mt-4 mb-3">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-success shadow-dark border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 ">Novos clientes</h6>
              <p class="text-sm ">Clientes que submeteram o formulário</p>
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <div class="row">
                <div class="col-lg-6 col-7">
                  <h6>últimas vendas</h6>
                  <p class="text-sm mb-0">
                   Últimos 10 registros
                  </p>
                </div>
                <div class="col-lg-6 col-5 my-auto text-end">
                  <div class="dropdown float-lg-end pe-4">
                    <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa fa-ellipsis-v text-secondary"></i>
                    </a>
                    <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                      <li><a class="dropdown-item border-radius-md" href="sales">Ver todas as transações</a></li>
                      <li><a class="dropdown-item border-radius-md" href="clients">Ver todos os clientes</a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Valor</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Data</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produto</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente</th>
                    </tr>
                  </thead>
                  <tbody>


                    <?php if($init_transactions->listLast10Dashboard != NULL){ ?>

                      <?php foreach($init_transactions->listLast10Dashboard as $key => $transaction){

                        $productInfo = $product->getProductById($transaction->product_id);
                        $clientInfo = false;

                        if(json_decode($transaction->info_data)){
                          $info_data   = json_decode($transaction->info_data);

                          if($transaction->plataform !== "MERCADOLIVRE"){
                            $client_id   = $info_data->client_id;
                            $clientInfo  = $clients->getClientById($client_id);
                          }else{
                            $clientInfo = (object)array('nome' => $info_data->buyer->first_name);
                          }
     
                        }

                      ?>

                    <tr>
                      <td>
                        <span class="text-success" >+ R$ <?= $transaction->valor; ?></span>
                      </td>
                      <td class="">
                        <span class=""> <?= date('d/m/Y H:i', strtotime($transaction->created)); ?> </span>
                      </td>
                      <td class="">
                        <?= $productInfo ? $productInfo->name : "<i style='font-size:12px;color:gray;' >Não disponível</i>" ?>
                      </td>
                      <td class="">
                        <?= $clientInfo ? '<img src="https://ui-avatars.com/api/?name='.$clientInfo->nome.'&size=25&background=random" class="avatar avatar-sm me-2 border-radius-lg" alt="user1">' : "" ?>
                        <?= $clientInfo ? $clientInfo->nome : "<i style='font-size:12px;color:gray;' >Não disponível</i>" ?>
                      </td>
                    </tr>

                  <?php } } ?>


                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>

       <?php include_once 'inc/footer.php';  ?>
