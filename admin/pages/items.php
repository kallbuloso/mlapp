<?php

    if(!isset($_SESSION['ADM_LOGGED'])){
      echo '<script>location.href="'.APP_URL.'/admin/login";</script>';
      exit;
    }

    include_once 'inc/header.php';

    $product_id = false;

    if(isset($_GET['product_id'])){
      $product_id  = $_GET['product_id'];
    }

    if(!is_numeric($product_id)){
      $product_id = false;
    }


    if($product_id ){
      $product  = new Product($product_id);
      $getItemsProduct     = $product->getItemsProduct();
    }else{
      echo '<script>location.href="'.APP_URL.'/admin/products";</script>';
      exit;
    }



  ?>

<body class="g-sidenav-show  bg-gray-200">

  <?php include_once 'inc/sidebar.php'; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

    <?php include_once 'inc/navbar.php'; ?>

    <div class="container-fluid py-4">

      <input type="hidden" value="<?= $product_id; ?>" id="product_id" />

      <div class="row mb-4">
        <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <div class="row">
                <div class="col-lg-6 col-7">
                  <h6>Itens do produto</h6>
                  <p class="text-sm mb-0">
                      Itens do produto
                  </p>

                  <?php if(isset($_GET['success'])){ ?>
                    <p class="alert alert-success">Itens adicionado</p>
                  <?php } ?>
                  <?php if(isset($_GET['error'])){ ?>
                    <p class="alert alert-danger">Desculpe, tente novamente</p>
                  <?php } ?>

                  <p id="response_items" ></p>
                </div>
                <div class="col-lg-12 col-12">
                  <button class="btn btn-success" onclick="$('#modalAddItem').modal('show');" > Adicionar itens <i class="fa fa-upload"></i></button>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Item</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Download</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opções</th>
                    </tr>
                  </thead>
                  <tbody>


                    <?php if($getItemsProduct){ ?>

                      <?php foreach($getItemsProduct as $key => $itemInfo){ ?>

                    <tr>
                      <td style="padding-left:30px;">
                        <span class="" ><?= $itemInfo->item; ?></span>
                      </td>
                      <td>
                        <a href="<?= APP_URL.'/download/'.$itemInfo->item; ?>?admin=true" class="btn btn-info btn-sm" > Download </a>
                      </td>
                      <td class="">
                        <button onclick="deleteItem(<?= $itemInfo->id; ?>);" type="button" class="btn btn-sm btn-primary" name="button"><i style="font-size: 20px;" class="material-icons opacity-10">delete</i></button>
                      </td>
                    </tr>

                  <?php } }else{ ?>

                    <tr>
                       <td colspan="3" class="text-center">
                          <h4 class="text-secondary" >Nenhum item cadastrado neste produto</h4>
                       </td>
                    </tr>

                  <?php } ?>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>


      <!-- Modal -->
      <div class="modal fade" id="modalAddItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Adicionar Produto</h5>
              <a style="cursor:pointer;" onclick="$('#modalAddProduct').modal('toggle');" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </a>
            </div>
            <div class="modal-body">
              <div class="row">

                <div class="col-md-12 text-center">
                  <form action="Controller/Items.php" id="uploadFiles" method="post" enctype="multipart/form-data">
                    <label for="iptn_files" class="btn btn-info"> <i class="fa fa-upload"></i> Selecionar arquivos</label>
                    <input onchange="itensSelected();" style="display:none;" id="iptn_files" type="file" name="files[]" multiple>
                    <input type="hidden" name="type"  value="add">
                    <input type="hidden" name="product" value="<?= $product_id; ?>">
                  </form>
                </div>

                <div class="col-md-12">
                  <p id="response_add" ></p>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" onclick="$('#modalAddItem').modal('toggle');">Fechar</button>
              <button type="button" class="btn btn-primary" onclick="$('#uploadFiles').submit();" >Adicionar</button>
            </div>
          </div>
        </div>
      </div>


      <!-- Modal edit product -->
      <div class="modal fade" id="moedalEditProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Editar Produto</h5>
              <a style="cursor:pointer;" onclick="$('#moedalEditProduct').modal('toggle');" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </a>
            </div>
            <div class="modal-body">
              <div class="row">

                <input type="hidden" id="edit_product_id" name="" value="">

                <div class="col-md-6">
                  <label for="">Título do produto</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="Titulo" name="" id="edit_product_name" value="">
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="">Preço do produto</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="0,00" name="" id="edit_product_price" value="">
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="">Status</label>
                  <div class="input-group input-group-outline">
                     <select class="form-control" name="" id="edit_product_status" >
                       <option value="1">Ativo</option>
                       <option value="0">Inativo</option>
                     </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="">Descrição</label>
                  <div class="input-group input-group-outline">
                    <textarea name="name" id="edit_product_description" class="form-control" placeholder="Descrição do produto aqui..." rows="8" cols="80"></textarea>
                  </div>
                </div>

                <div class="col-md-12">
                  <p id="response_edit" ></p>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" onclick="$('#moedalEditProduct').modal('toggle');">Fechar</button>
              <button type="button" class="btn btn-primary" id="btnEditProd" >Salvar</button>
            </div>
          </div>
        </div>
      </div>

      <script type="text/javascript" src="assets/js/product.js" ></script>

    <?php include_once 'inc/header.php';  ?>
