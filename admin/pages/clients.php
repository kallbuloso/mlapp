<?php

if (!isset($_SESSION['ADM_LOGGED'])) {
  echo '<script>location.href="' . APP_URL . '/admin/login";</script>';
  exit;
}

include_once 'inc/header.php';

$clients = new Clients();
$transaction = new Transaction();

$itens_por_pagina = 10;

$desc = false;

if (isset($_GET['desc'])) {
  $desc = $_GET['desc'] == 'true' ? true : false;
}

if (!isset($_GET['idpage'])) {
  $pagina_atual = 1;
} else {
  $pagina_atual = $_GET['idpage'];
}

$inicio = ($pagina_atual - 1) * $itens_por_pagina;
$list = $clients->list($inicio, $itens_por_pagina, $desc);
$count_t = $clients->listAll();

$total_paginas = ceil($count_t / $itens_por_pagina);


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
                <div class="col-lg-4 col-7">
                  <h6>Clientes</h6>
                  <p id="response_clients"></p>
                </div>
                <div class="col-lg-4">

                  <div class="input-group-outline input-group">
                    <input style="max-height: 42px;border-radius: 10px 0px 0px 10px!important;" type="text" class="form-control" id="search_client" placeholder="Procurar"
                      aria-label="Procurar" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button style="max-height: 42px;border-radius: 0px 10px 10px 0px!important;" class="btn btn-outline-secondary" id="initSearch" type="button">Procurar</button>
                    </div>
                  </div>

                </div>
                <div class="col-lg-4 col-5 my-auto text-end">
                  <select style="border: 1px solid gray;height: 30px;font-size: 15px;border-radius: 8px;color: #666666;"
                    id="orderList" onchange="listOrder()">
                    <option>Ordenar</option>
                    <option value="desc">Maior comprador</option>
                    <option value="asc">Menor comprador</option>
                  </select>

                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nome</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Whatsapp</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">E-mail</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Qtd de compras
                      </th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opções</th>
                    </tr>
                  </thead>
                  <tbody>


                    <?php if ($list) { ?>

                      <?php foreach ($list as $key => $clientInfo) {

                        $qtd_compras = $transaction->getAllTransactionsByClient($clientInfo->id);

                        ?>

                        <tr>
                          <td class="">
                             <span class="copy_info text-info pointer" data-copy="<?= $clientInfo->nome.' '.$clientInfo->email . ' '. DDI . str_replace(['-',' ','(',')','+'],'' ,$clientInfo->number); ?> " >&nbsp;&nbsp; <?= $clientInfo->id; ?></span>
                          </td>
                          <td class="">
                            <?= '<img src="https://ui-avatars.com/api/?name=' . $clientInfo->nome . '&size=25&background=random" class="avatar avatar-sm me-2 border-radius-lg" alt="user1">'; ?>
                            <span class="copy_info pointer" data-copy="<?= $clientInfo->nome; ?>" ><?= $clientInfo->nome; ?></span>
                          </td>
                          <td class="">
                             <span class="copy_info text-info pointer" data-copy="<?= DDI . str_replace(['-',' ','(',')','+'],'' ,$clientInfo->number); ?>" > <i class="fa fa-whatsapp" ></i> <?= $clientInfo->number; ?></span>
                          </td>
                          <td class="">
                              <span class="copy_info text-info pointer" data-copy="<?= $clientInfo->email; ?>" > <i class="fa fa-envelope" ></i> <?= $clientInfo->email; ?></span>
                          </td>

                          <td>
                            <?= $qtd_compras; ?>
                          </td>

                          <td class="">
                              
                             <button onclick="viewBuys(<?= $clientInfo->id; ?>);" class="btn btn-sm btn-info" >
                                 <i style="font-size: 20px;"
                                class="material-icons opacity-10">inventory</i>
                             </button>
                              
                            <button onclick="removeClient(<?= $clientInfo->id; ?>);" ;type="button"
                              class="btn btn-sm btn-danger" name="button"><i style="font-size: 20px;"
                                class="material-icons opacity-10">delete</i></button>
                          </td>
                        </tr>

                      <?php }
                    } else { ?>

                      <tr>
                        <td colspan="6" class="text-center">
                          <h5 style="color:gray;font-size:12px;"> <i>Nenhum cliente</i> </h5>
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
                      $next_link = "?idpage=";

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
      <div class="modal fade" id="modalViewBuys" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Todos os produtos comprado pelo cliente</h5>
              <a style="cursor:pointer;" onclick="$('#modalViewBuys').modal('toggle');" data-dismiss="modal"
                aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </a>
            </div>
            <div class="modal-body">
              <div class="row" id="bodyViewBuys" >
                    
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default"
                onclick="$('#modalViewBuys').modal('toggle');">Fechar</button>
            </div>
          </div>
        </div>
      </div>

      <script type="text/javascript" src="assets/js/client.js?v=8"></script>

      <?php include_once 'inc/footer.php'; ?>