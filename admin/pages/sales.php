<?php

    if(!isset($_SESSION['ADM_LOGGED'])){
      echo '<script>location.href="'.APP_URL.'/admin/login";</script>';
      exit;
    }

    include_once 'inc/header.php';

    $transaction  = new Transaction();
    $clients      = new Clients();

    $status_get   = false;

    if(isset($_GET['status'])){
      $status_get = $_GET['status'];
    }


    $itens_por_pagina = 10;

    if(!isset($_GET['idpage'])){
      $pagina_atual = 1;
    }else{
      $pagina_atual = $_GET['idpage'];
    }

    $inicio  = ($pagina_atual - 1) * $itens_por_pagina;
    $list    = $transaction->list($inicio, $itens_por_pagina, $status_get);
    $count_t = $transaction->listAll($status_get);

    $total_paginas = ceil($count_t / $itens_por_pagina);

    $product = new Product();
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
                  <h6>Últimas transações</h6>
                  <p id="response_sale" ></p>
                </div>
                <div class="col-lg-6 col-5 my-auto text-end">
                  <div class="dropdown float-lg-end pe-4">
                    <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa fa-ellipsis-v text-secondary"></i>
                    </a>
                    <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                      <li><a class="dropdown-item border-radius-md" href="?notfilter" >Todos</a></li>
                      <li><a class="dropdown-item border-radius-md" href="?status=approved" >Aprovados</a></li>
                      <li><a class="dropdown-item border-radius-md" href="?status=pending" >Pendentes</a></li>
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
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Valor</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Comprador</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produto</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opções</th>
                    </tr>
                  </thead>
                  <tbody>


                    <?php if($list){ ?>

                      <?php foreach($list as $key => $transactionInfo){

                        $clientInfo = false;

                        if(json_decode($transactionInfo->info_data)){
                          $info_data   = json_decode($transactionInfo->info_data);

                          if($transactionInfo->plataform !== "MERCADOLIVRE"){
                            $client_id   = $info_data->client_id;
                            $clientInfo  = $clients->getClientById($client_id);
                          }else{
                            $clientInfo = (object)array('nome' => $info_data->buyer->first_name);
                          }
     
                        }
                    
                        $isProd = $product->getProductById($transactionInfo->product_id);
                        $product_name = $isProd ? $isProd->name : "------";

                      ?>

                    <tr>
                      <td>
                        <span style="margin-left:10px;" ><?= $transactionInfo->id; ?></span> <br />
                        <?php if($transactionInfo->tag != NULL){ ?>
                          <?= $transactionInfo->tag == 'Processando' ? '<span style="font-size:7px;" class="badge bg-warning" >Processando</span>' : '<span style="font-size:7px;" class="badge bg-success" >Concluído</span>'; ?>
                        <?php } ?>
                      </td>
                      <td>
                        <span ><?= date('d/m/Y H:i', strtotime($transactionInfo->created)); ?></span>
                      </td>
                      <td class="">
                        <span class=""> R$ <?= $transactionInfo->valor; ?></span>
                      </td>
                      <td class="">
                       <?= $transactionInfo->status == 'approved' ? '<span class="badge bg-success" >Aprovado</span>' : '<span class="badge bg-info" >Pendente</span>'; ?>
                        <br>
                       <span style="font-size:11px;color:gray;" ><?= 'Venda realizada por: <b>' . ucfirst(strtolower($transactionInfo->plataform)) . '</b>'; ?></span>
                      </td>
                      <td class="">
                        <?= $clientInfo ? '<img src="https://ui-avatars.com/api/?name='.$clientInfo->nome.'&size=25&background=random" class="avatar avatar-sm me-2 border-radius-lg" alt="user1">' : "" ?>
                        <?= $clientInfo ? $clientInfo->nome : "<i style='font-size:12px;color:gray;' >Desconhecido</i>" ?>
                      </td>
                      <td>
                          <?= substr($product_name, 0, 20); ?> <?= strlen($product_name) > 20 ? "..." : ""; ?>
                      </td>
                      <td class="">
                        <button onclick="getTransactionByDetail(<?= $transactionInfo->id; ?>);";type="button" class="btn btn-sm btn-info" name="button"><i style="font-size: 20px;" class="material-icons opacity-10">visibility</i></button>
                      </td>
                    </tr>

                  <?php } }else{ ?>

                    <tr>
                      <td colspan="7" class="text-center">
                        <h5 style="color:gray;" > <i>Nenhuma transação</i> </h5>
                      </td>
                  </tr>
                  <?php } ?>


                  </tbody>
                </table>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <nav aria-label="Navegação de página exemplo">
                    <ul class="pagination">

                      <?php
                      $next_link = $status_get ? "?status={$status_get}&idpage=" : "?idpage=";

                      // Constants
                      $MAX_ITEMS = 5;
                      $MAX_LEFT = ($MAX_ITEMS - 1) / 2;
                      $ellipsis = '<li class="page-item disabled"><span class="page-link">...</span></li>';

                      // Calculate first and end page
                      $current = $pagina_atual;
                      $first = max($current - $MAX_LEFT, 1);
                      $endPage = min($total_paginas, $pagina_atual + 2); // Adjusted to match maxPagesToShow
                      
                      // Initialize paginationHTML array
                      $paginationHTML = [];

                      // Previous page
                      if ($pagina_atual > 1 && $pagina_atual >= ($MAX_ITEMS - 1)) {
                        $paginationHTML[] = '<li class="page-item"><a href="' . $next_link . '1" class="page-link"><i class="fa fa-angle-double-left"></i></a></li>';
                      }

                      // Before page
                      $beforePage = $pagina_atual > 1 ? ($pagina_atual - 1) : false;
                      if ($beforePage) {
                        $paginationHTML[] = '<li class="page-item"><a href="' . $next_link . $beforePage . '" class="page-link"><i class="fa fa-angle-left"></i></a></li>';
                      } else {
                        $paginationHTML[] = '<li class="page-item disabled"><span class="page-link"><i class="fa fa-angle-left"></i></span></li>';
                      }

                      // Loop through pages
                      $pagesListed = [];
                      for ($i = 0; $i < min($MAX_ITEMS, $total_paginas); $i++) {
                        $page = $i + $first;

                        if ($i === 0 && $page > 1) {
                          $paginationHTML[] = $ellipsis;
                        }

                        if ($page <= $total_paginas) {
                          if ($page == $pagina_atual) {
                            $paginationHTML[] = '<li class="page-item active"><a href="' . $next_link . $page . '" class="page-link">' . $page . '</a></li>';
                          } else {
                            $paginationHTML[] = '<li class="page-item"><a href="' . $next_link . $page . '" class="page-link">' . $page . '</a></li>';
                          }
                        }

                        if ($i === $MAX_ITEMS - 1 && $page < $total_paginas) {
                          $paginationHTML[] = $ellipsis;
                        }

                        $pagesListed[] = $page;
                      }

                      // Next page
                      $nextPage = $pagina_atual + 1;
                      if ($endPage == $pagina_atual) {
                        $paginationHTML[] = '<li class="page-item disabled"><span class="page-link"><i class="fa fa-angle-right"></i></span></li>';
                      } else {
                        $paginationHTML[] = '<li class="page-item"><a href="' . $next_link . $nextPage . '" class="page-link"><i class="fa fa-angle-right"></i></a></li>';
                      }

                      // Last page
                      if (!in_array($total_paginas, $pagesListed)) {
                        $paginationHTML[] = '<li class="page-item"><a href="' . $next_link . $total_paginas . '" class="page-link"><i class="fa fa-angle-double-right"></i></a></li>';
                      }

                      // Output pagination
                      echo implode('', $paginationHTML);
                      ?>

                    </ul>
                  </nav>
                </div>
              </div>

            </div>
          </div>
        </div>

      </div>

     <!-- Modal edit product -->
      <div class="modal fade" id="modalViewTransaction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Ver Transação <span style="float: right;font-size: 8px;" class="badge" id="tag_transaction_modal" ></span> </h5>
              <a style="cursor:pointer;" onclick="$('#modalViewTransaction').modal('toggle');" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </a>
            </div>
            <div class="modal-body">
              <div class="row">

                <input type="hidden" id="idview_transaction" name="" value="">



                <div class="col-md-12">
                    <div class="table-responsive">
                    <table class="table">

                      <tbody>
                          
                         <tr>
                          <th>Produto</th>
                          <th><span id="product_name" ></span> </th>
                        </tr>

                        <tr>
                          <th>Valor</th>
                          <th>R$ <span id="valor_transaction" ></span> </th>
                        </tr>

                        <tr>
                          <th>Origem</th>
                          <th><span style="display: block;max-width: 236px;overflow:hidden;" id="origem_transaction" ></span> </th>
                        </tr>

                        <tr>
                          <th>Data</th>
                          <th><span id="date_transaction" ></span> </th>
                        </tr>


                        <tr>
                            <th>Status</th>
                            <th> <span id="status_transaction"></span> </th>
                        </tr>

                        <tr>
                            <th>Quantidade</th>
                            <th> <span id="qtd_transaction"></span> </th>
                        </tr>

                        <tr>
                            <th>Cliente</th>
                            <th> <img src="" id="avatar_client" class="avatar avatar-sm me-1 border-radius-lg" alt="user1"> <span id="nome_client" ></span> </th>
                        </tr>

                        <tr>
                            <th>Email cliente</th>
                            <th> <span id="email_client" ></span> </th>
                        </tr>
                        
                        <tr>
                            <th>Número Cliente</th>
                            <th> <span id="number_transaction" ></span> </th>
                        </tr>

                        <tr>
                            <th>Arquivo da compra</th>
                            <th> <a id="link_transaction" href="#" target="_blank">carregando</a> </th>
                        </tr>

                      </tbody>
                    </table>

                </div>

                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" onclick="$('#modalViewTransaction').modal('toggle');">Fechar</button>
            </div>
          </div>
        </div>
      </div>



    <script type="text/javascript" src="assets/js/sales.js?v=1" ></script>

    <?php include_once 'inc/header.php';  ?>
