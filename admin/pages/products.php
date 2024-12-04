<?php

if (!isset($_SESSION['ADM_LOGGED'])) {
  echo '<script>location.href="' . APP_URL . '/admin/login";</script>';
  exit;
}

include_once 'inc/header.php';

$order = false;
$selected = false;
$search = false;


if(isset($_GET['term'])){
    $t = trim($_GET['term']);
    $search = "WHERE name LIKE '%{$t}%' OR description LIKE '%{$t}%' OR price LIKE '%{$t}%' OR identifier LIKE '%{$t}%'";
}

if(isset($_GET['order'])){
    if($_GET['order'] == "asc"){
      $order = "ORDER BY CAST(REPLACE(REPLACE(price, '.', ''), ',', '.') AS DECIMAL(10, 2)) ASC";
    }else if($_GET['order'] == "desc"){
      $order = "ORDER BY CAST(REPLACE(REPLACE(price, '.', ''), ',', '.') AS DECIMAL(10, 2)) DESC";  
    }else if($_GET['order'] == "ref_asc"){
      $order = "ORDER BY identifier ASC";  
    }else if($_GET['order'] == "ref_desc"){
      $order = "ORDER BY identifier DESC";  
    }
    
    $selected = $_GET['order'];
}

$product = new Product();
$list = $product->list($search, $order);

$marketplace = new Marketplace();
$accounts    = $marketplace->getMarketpalces();
?>

<style>
  .input-group.input-group-outline .form-control[disabled] {
    opacity: 0.7;
  }

  label,
  .form-label {
    margin-bottom: 0px !important;
  }
  .btn{
    margin-bottom:0px!important;
    padding: 6px!important;
  }
  

</style>

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
                  <h6>Produtos cadastrados</h6>
                  <p class="text-sm mb-0">
                    Produtos cadastrados
                  </p>
                  <p id="response_product"></p>
                </div>
                <div class="col-lg-4 ">
                   <button class="btn btn-success mt-2" onclick="$('#modalAddProduct').modal('show'); " > Novo produto <i class="fa fa-plus"></i> </button>
                   <button class="btn btn-warning mt-2" onclick="location.href='<?= APP_URL; ?>/admin/import_products_ml'" > Importar do Mercado Livre <i class="fa fa-plug"></i> </button>

                </div>
                
                
                <div class="col-lg-4 mt-2 ">

                  <div class="input-group-outline input-group">
                    <input style="max-height: 42px;border-radius: 10px 0px 0px 10px!important;" type="text" class="form-control" id="search_product" placeholder="Procurar"
                      aria-label="Procurar" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button style="height: 42px;border-radius: 0px 10px 10px 0px!important;" class="btn btn-outline-secondary" id="initSearchProds" type="button">Procurar</button>
                    </div>
                  </div>

                </div>
                <div class="col-lg-2 col-md-4 col-12 mt-2 my-auto text-end">
                  <select style="min-height: 42px;border-radius: 9px;width: 100%;"
                    id="orderListProds" onchange="listOrderProds()">
                    <option >Ordenar</option>
                        <option <?php if($selected != false){ if($selected == "asc"){ echo 'selected'; } } ?> value="asc" >Menor valor</option>
                        <option <?php if($selected != false){ if($selected == "desc"){ echo 'selected'; } } ?> value="desc" >Maior valor</option>
                        <option <?php if($selected != false){ if($selected == "ref_asc"){ echo 'selected'; } } ?> value="ref_asc" >Menor referência</option>
                        <option <?php if($selected != false){ if($selected == "ref_desc"){ echo 'selected'; } } ?> value="ref_desc" >Maior referência</option>

                  </select>

                </div>

              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Valor</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nome</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estoque</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opções</th>
                    </tr>
                  </thead>
                  <tbody>


                    <?php if ($list) { ?>

                      <?php foreach ($list as $key => $productInfo) {

                        $getItemsProduct = $product->getItemsProductByid($productInfo->id);
                        $qtdItensProd = $getItemsProduct ? count($getItemsProduct) : 0;
                        $messageAlert = $productInfo->uniq_link == "imported" ? "<span class='blink material-icons'>info</span> &nbsp; Você deve definir o conteúdo de download do produto. Clique em editar." : "";
                        $messageInfo = $productInfo->id_mercadolivre != "" && $productInfo->id_mercadolivre != NULL ? "<i style='font-size:10px;' >Produto vinculado ao Mercado Livre &nbsp; <span class='fa fa-handshake'></span></i>" : "";
                        
                        $link_product = $productInfo->id_mercadolivre != "" && $productInfo->id_mercadolivre != NULL ? $productInfo->permalink_ml : APP_URL . '/produto/' . $productInfo->id;
                        
                        $marketplace->id = $productInfo->account_mkt;
                        $accountLinked = $marketplace->getMarketPlaceById();

                        ?>

                        <tr>
                          <td>
                            <span class="">R$
                              <?= $productInfo->price; ?>
                            </span>
                          </td>
                          <td class="">
                            <span class="">
                                <?php if($accountLinked){ 
                                   $data_account = json_decode($accountLinked[0]->data_account); 
                                   echo '<a href="'.$data_account->permalink.'" target="_blank"><i style="font-size:10px;color:gray;" >(<i class="fa fa-link" ></i> '.$data_account->nickname.')</i></a> &nbsp; '; 
                                } ?>
                              <?= $messageInfo; ?>
                              <br>
                              <?= $productInfo->name; ?>
                              <br
                              <?php if($productInfo->identifier != "" && $productInfo->identifier != NULL){ 
                                echo '<a href="#" ><i style="font-size:10px;color:gray;" >(REF:'.$productInfo->identifier.')</i></a> &nbsp; ';
                              } ?>
                            </span>
                            <br>
                            <span class="text-warning" style="font-size:13px;flex-direction: row;display: flex;" >
                               <?= $messageAlert; ?>
                            </span>
                          </td>
                          <td>
                            <?php if ($productInfo->uniq_link == "not") { ?>
                              <span>
                                <?= $qtdItensProd; ?>
                                <?php if ($productInfo->recycle_file == "yes") { ?>
                                  <i class="fa fa-recycle"></i>
                                <?php } ?>
                              </span>
                            <?php } else { ?>
                              <a href="<?= $productInfo->uniq_link; ?>" target="_blank" rel="noopener noreferrer"> <i class="fa fa-download"></i> Ver Link de
                                download</a>
                            <?php } ?>
                          </td>
                          <td class="">
                            <?= $productInfo->status == '1' ? '<span class="badge bg-success" >Ativo</span>' : '<span class="badge bg-danger" >Inativo</span>'; ?>
                          </td>
                          <td class="">
                          <button type="button"
                              class="btn btn-sm btn-warning" onclick="linkProduct('<?= $link_product; ?>');" name="button"><i style="font-size: 20px;"
                                class="fa fa-link opacity-10"></i></button>
                            <button onclick="getProductByEdit(<?= $productInfo->id; ?>);" type="button"
                              class="btn btn-sm btn-info" name="button"><i style="font-size: 20px;"
                                class="material-icons opacity-10">edit</i></button>
                            <button onclick="deleteProd(<?= $productInfo->id; ?>);" type="button"
                              class="btn btn-sm btn-primary" name="button"><i style="font-size: 20px;"
                                class="material-icons opacity-10">delete</i></button>
                            <?php if ($productInfo->uniq_link == "not") { ?>
                              <button onclick="location.href='<?= APP_URL . '/admin/items?product_id=' . $productInfo->id ?>';"
                                type="button" class="btn btn-sm btn-success" name="button"><i style="font-size: 20px;"
                                  class="fa fa-upload opacity-10"></i></button>
                            <?php } ?>
                          </td>
                        </tr>

                      <?php }
                    } ?>


                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>


      <!-- Modal -->
      <div class="modal fade" id="modalAddProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Adicionar Produto</h5>
              <a style="cursor:pointer;" onclick="$('#modalAddProduct').modal('toggle');" data-dismiss="modal"
                aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </a>
            </div>
            <div class="modal-body">
              <div class="row">
                  
                <div class="col-md-12 mb-2">
                  <label for="">Selecionar conta Marketplace</label>
                  <div class="input-group input-group-outline">
                    <select class="form-control" name="" id="account_mkt">
                        <option value="" >Selecionar conta</option>
                           <?php if($accounts){ foreach($accounts as $k => $account){ $data_account = json_decode($account->data_account); ?>
                              <option value="<?= $account->id; ?>" ><?= $data_account->nickname; ?> - <?= $data_account->first_name; ?> (<?= $data_account->email; ?>)</option>
                           <?php } }else{ ?>
                             <option value="" >Nenhuma conta cadastrada</option>
                           <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Título do produto</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="Titulo" name="" id="product_name" value="">
                  </div>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Download do produto</label>
                  <div class="input-group input-group-outline">
                    <select class="form-control" name="" id="product_type_download">
                      <option value="upload">Upload</option>
                      <option value="link">Link</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Armazenamento do upload</label>
                  <div class="input-group input-group-outline">
                    <select class="form-control" name="" id="product_recycle_file">
                      <option value="no">Um arquivo para cada compra</option>
                      <option value="yes">Mesmo arquivo para varias compras</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Link de download</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" disabled placeholder="https://drive.google.com" name=""
                      id="product_link_download" value="">
                  </div>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Imagem do produto</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="URL imagem" name="" id="product_image"
                      value="">
                  </div>
                  <small><a href="https://uploaddeimagens.com.br/" target="_blank">Utilize
                      uploaddeimagens.com.br</a></small>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Preço do produto</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="0,00" name="" id="product_price" value="">
                  </div>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Status</label>
                  <div class="input-group input-group-outline">
                    <select class="form-control" name="" id="product_status">
                      <option value="1">Ativo</option>
                      <option value="0">Inativo</option>
                    </select>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <label for="">Referência</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="Referência" name="" id="product_identifier" value="">
                  </div>
                </div>

                <div class="col-md-12 mb-2">
                  <label for="">Descrição</label>
                  <div class="input-group input-group-outline">
                    <textarea name="product_description" placeholder="Uma breve descrição" id="product_description"
                      class="form-control" rows="8" cols="80"></textarea>
                  </div>
                </div>

                <div class="col-md-12 mb-2">
                  <p id="response_add"></p>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default"
                onclick="$('#modalAddProduct').modal('toggle');">Fechar</button>
              <button type="button" class="btn btn-primary" id="btnAddProd">Adicionar</button>
            </div>
          </div>
        </div>
      </div>


      <!-- Modal edit product -->
      <div class="modal fade" id="moedalEditProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Editar Produto</h5>
              <a style="cursor:pointer;" onclick="$('#moedalEditProduct').modal('toggle');" data-dismiss="modal"
                aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </a>
            </div>
            <div class="modal-body">
              <div class="row">

                <input type="hidden" id="edit_product_id" name="" value="">
                
                <div class="col-md-12 mb-2">
                  <label for="">Selecionar conta Marketplace</label>
                  <div class="input-group input-group-outline">
                    <select class="form-control" name="" id="edit_account_mkt">
                        <option value="" >Selecionar conta</option>
                           <?php if($accounts){ foreach($accounts as $k => $account){ $data_account = json_decode($account->data_account); ?>
                              <option value="<?= $account->id; ?>" ><?= $data_account->nickname; ?> - <?= $data_account->first_name; ?> (<?= $data_account->email; ?>)</option>
                           <?php } }else{ ?>
                             <option value="" >Nenhuma conta cadastrada</option>
                           <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Título do produto</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="Titulo" name="" id="edit_product_name"
                      value="">
                  </div>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Download do produto</label>
                  <div class="input-group input-group-outline">
                    <select class="form-control" name="" id="edit_product_type_download">
                      <option value="upload">Upload</option>
                      <option value="link">Link</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Armazenamento do upload</label>
                  <div class="input-group input-group-outline">
                    <select class="form-control" name="" id="edit_product_recycle_file">
                      <option value="no">Um arquivo para cada compra</option>
                      <option value="yes">Mesmo arquivo para varias compras</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Link de download</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control"  placeholder="https://drive.google.com" name=""
                      id="edit_product_link_download" value="">
                  </div>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Imagem do produto</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="URL imagem" name="" id="edit_product_image"
                      value="">
                  </div>
                  <small><a href="https://uploaddeimagens.com.br/" target="_blank">Utilize
                      uploaddeimagens.com.br</a></small>
                </div>

                <div class="col-md-6">
                  <label for="">Preço do produto</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="0,00" name="" id="edit_product_price" value="">
                  </div>
                </div>

                <div class="col-md-6 mb-2">
                  <label for="">Status</label>
                  <div class="input-group input-group-outline">
                    <select class="form-control" name="" id="edit_product_status">
                      <option value="1">Ativo</option>
                      <option value="0">Inativo</option>
                    </select>
                  </div>
                </div>

                 <div class="col-md-6">
                  <label for="">Referência</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" placeholder="Referência" name="" id="edit_product_identifier" value="">
                  </div>
                </div>
                

                <div class="col-md-12 mb-2">
                  <label for="">Descrição</label>
                  <div class="input-group input-group-outline">
                    <textarea placeholder="Uma breve descrição" name="edit_product_description"
                      id="edit_product_description" class="form-control" rows="8" cols="80"></textarea>
                  </div>
                </div>

                <div class="col-md-12">
                  <p id="response_edit"></p>
                </div>

              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default"
                onclick="$('#moedalEditProduct').modal('toggle');">Fechar</button>
              <button type="button" class="btn btn-primary" id="btnEditProd">Salvar</button>
            </div>
          </div>
        </div>
      </div>

      <script type="text/javascript" src="assets/js/product.js?v=1.3"></script>

      <?php include_once 'inc/header.php'; ?>