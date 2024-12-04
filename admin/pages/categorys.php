<?php

    if(!isset($_SESSION['ADM_LOGGED'])){
      echo '<script>location.href="'.APP_URL.'/admin/login";</script>';
      exit;
    }

    include_once 'inc/header.php';

    $categorys  = new Categorys();
    $list     = $categorys->list();

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
                  <h6>Categorias de produtos</h6>

                  <p id="response_product" ></p>
                </div>
                <div class="col-lg-6 col-5 my-auto text-end">
                  <div class="dropdown float-lg-end pe-4">
                    <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa fa-ellipsis-v text-secondary"></i>
                    </a>
                    <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                      <li><a class="dropdown-item border-radius-md" onclick="$('#modalAddCategory').modal('show');" >Adicionar novo</a></li>
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
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nome</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opções</th>
                    </tr>
                  </thead>
                  <tbody>


                    <?php if($list){ ?>

                      <?php foreach($list as $key => $cateInfo){ ?>

                    <tr>
                      <td class="">
                        <span class=""> <?= $cateInfo->id; ?></span>
                      </td>
                      <td class="">
                        <span class=""> <?= $cateInfo->nome; ?></span>
                      </td>
                      <td class="">
                        <button onclick="getCategoryByEdit(<?= $cateInfo->id; ?>);";type="button" class="btn btn-sm btn-info" name="button"><i style="font-size: 20px;" class="material-icons opacity-10">edit</i></button>
                        <button onclick="deleteCategory(<?= $cateInfo->id; ?>);" type="button" class="btn btn-sm btn-primary" name="button"><i style="font-size: 20px;" class="material-icons opacity-10">delete</i></button>
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


      <!-- Modal -->
      <div class="modal fade" id="modalAddCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Adicionar Categoria</h5>
              <a style="cursor:pointer;" onclick="$('#modalAddCategory').modal('toggle');" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </a>
            </div>
            <div class="modal-body">
              <div class="row">

                <div class="col-md-6">
                  <label for="">Nome da categoria</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="Titulo" name="" id="categoria_name" value="">
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="">Descrição categoria</label>
                  <div class="input-group input-group-outline">
                    <textarea name="description_price" id="description_price" class="form-control" rows="8" cols="80"></textarea>
                  </div>
                </div>


                <div class="col-md-12">
                  <p id="response_add" ></p>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" onclick="$('#modalAddCategory').modal('toggle');">Fechar</button>
              <button type="button" class="btn btn-primary" id="btnAddCate" >Adicionar</button>
            </div>
          </div>
        </div>
      </div>


      <!-- Modal edit product -->
      <div class="modal fade" id="moedalEditCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Editar Categoria</h5>
              <a style="cursor:pointer;" onclick="$('#moedalEditCategory').modal('toggle');" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </a>
            </div>
            <div class="modal-body">
              <div class="row">

                <input type="hidden" id="edit_category_id" name="" value="">

                <div class="col-md-6">
                  <label for="">Título do produto</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="Titulo" name="" id="edit_category_name" value="">
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="edit_product_description" >Descrição</label>
                  <div class="input-group input-group-outline">
                    <textarea class="form-control" name="edit_product_description" id="edit_category_description" rows="8" cols="80"></textarea>
                  </div>
                </div>


                <div class="col-md-12">
                  <p id="response_edit" ></p>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" onclick="$('#moedalEditCategory').modal('toggle');">Fechar</button>
              <button type="button" class="btn btn-primary" id="btnEditCategory" >Salvar</button>
            </div>
          </div>
        </div>
      </div>

      <script type="text/javascript" src="assets/js/product.js" ></script>

    <?php include_once 'inc/header.php';  ?>
