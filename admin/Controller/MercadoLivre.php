 <?php
 
  if(session_status() !== PHP_SESSION_ACTIVE) session_start();
  
  require_once '../../config.php';

  if(!isset($_SESSION['ADM_LOGGED'])){
    echo json_encode([
      'erro'    => true,
      'message' => 'Faça login'
    ]);
    exit;
  }
 
if (isset($_POST)) {

  if (isset($_POST['type'])) {

    require_once '../../class/Conn.class.php';
    require_once '../../class/MercadoLivre.class.php';
    require_once '../../class/Settings.class.php';
    require_once '../../class/Product.class.php';
    require_once '../../class/Marketplace.class.php';

    $settings = new settings;
    $product = new Product;
    $marketplace = new Marketplace;

    $type = trim($_POST['type']);

    if ($type == "getProducts") {
      try {
          
          if(!isset($_POST['account'])){
               die(json_encode([
                'erro' => true,
                'message' => "Informe qual conta deseja importar os produtos."
              ]));
          }
          
        if($_POST['account'] == ""){
          die(json_encode([
            'erro' => true,
            'message' => "Informe qual conta deseja importar os produtos."
          ]));
        }
          
        $marketplace->id = trim($_POST['account']);  
        $isAccount = $marketplace->getMarketPlaceById();

        if(!$isAccount){
          die(json_encode([
            'erro' => true,
            'message' => "Conta não localizada."
          ]));
        }
        
           $access_token = $isAccount[0]->access_token;

          if ($access_token != "") {

            $mercadolivre = new MercadoLivre($access_token);
            $mercadolivre->testConnection();

            if ($mercadolivre->erro) {
              echo json_encode([
                'erro' => true,
                'message' => $mercadolivre->message_erro
              ]);
            } else {

              $mercadolivre->getProducts();

              if ($mercadolivre->erro) {
                die(json_encode([
                  'erro' => true,
                  'message' => $mercadolivre->message_erro
                ]));
              }

              if ($mercadolivre->products_id == NULL || $mercadolivre->products_id == "") {
                die(json_encode([
                  'erro' => true,
                  'message' => 'Nenhum produto foi localizado'
                ]));
              }

              if (count($mercadolivre->products_id) < 1) {
                die(json_encode([
                  'erro' => true,
                  'message' => 'Nenhum produto foi localizado'
                ]));
              }

              $products_import = array();

              foreach ($mercadolivre->products_id as $key => $id) {
                $product_info = $mercadolivre->getInfoProduct($id);
                if (!$mercadolivre->erro) {
                  $products_import[$key] = $product_info;
                }
              }

              if (count($products_import) < 1) {
                die(json_encode([
                  'erro' => true,
                  'message' => 'Nenhum produto foi localizado'
                ]));
              }


              foreach ($products_import as $key => $prod) {
                $is_created = $product->getProductByIdML($prod->id);
                if (!$is_created) {
                  $add = $product->addProduct((object) [
                    'typeDown' => 'link',
                    'id_mercadolivre' => $prod->id,
                    'name' => $prod->title,
                    'description' => $prod->title,
                    'image' => $prod->thumbnail,
                    'price' => number_format($prod->price, 2, ",", "."),
                    'status' => 1,
                    'link_download' => 'imported',
                    'recycle_file' => 'yes',
                    'permalink_ml' => $prod->permalink,
                    'account_mkt' => $isAccount[0]->id
                  ]);

                  if ($add) {
                    $products_import[$key]->import = array('erro' => false, 'id' => $add);
                  } else {
                    $products_import[$key]->import = array('erro' => true, 'id' => NULL, 'message' => 'Erro ao cadastrar produto no banco de dados');
                  }

                } else {
                  $products_import[$key]->import = array('erro' => true, 'id' => NULL, 'message' => 'Produto já cadastrado no banco de dados.');
                }
              }


              echo json_encode(
                array(
                  'erro' => false,
                  'data' => $products_import,
                  'message' => 'Localizamos (' . count($products_import) . ') produtos em sua conta!'
                )
              );


            }


          } else {
            echo json_encode([
              'erro' => true,
              'message' => 'Access token required'
            ]);
          }

      } catch (\Exception $e) {

        echo json_encode([
          'erro' => true,
          'message' => $e
        ]);

      }
    } else if ($type == "get") {

      $id = trim($_POST['id']);
      $getCategoryById = $categorys->getCategoryById($id);

      if ($getCategoryById) {

        echo json_encode([
          'erro' => false,
          'message' => 'Produto localizado',
          'data' => $getCategoryById
        ]);

      } else {
        echo json_encode([
          'erro' => true,
          'message' => 'Não foi possível buscar o produto'
        ]);
      }


    } else if ($type == "add") {
      try {

        $data = json_decode($_POST['data']);

        if (isset($data->nome, $data->description)) {

          if ($data->nome != "" && $data->description != "") {

            $addCategory = $categorys->addCategory($data);

            if ($addCategory) {

              echo json_encode([
                'erro' => false,
                'message' => 'Categoria adicionado com sucesso.'
              ]);

            } else {
              echo json_encode([
                'erro' => true,
                'message' => 'Não foi possível fazer o cadastro'
              ]);
            }

          } else {
            echo json_encode([
              'erro' => true,
              'message' => 'Preencha todos os campos'
            ]);
          }

        } else {
          echo json_encode([
            'erro' => true,
            'message' => 'Preencha todos os campos'
          ]);
        }

      } catch (\Exception $e) {

        echo json_encode([
          'erro' => true,
          'message' => 'Tente novamente mais tarde'
        ]);

      }
    } else if ($type == "delete") {

      try {

        $id = trim($_POST['id']);
        $delete = $categorys->delete($id);

        if ($delete) {

          echo json_encode([
            'erro' => false,
            'message' => 'Categoria removido com sucesso'
          ]);

        } else {
          echo json_encode([
            'erro' => true,
            'message' => 'Tente novamente mais tarde'
          ]);
        }

      } catch (\Exception $e) {
        echo json_encode([
          'erro' => true,
          'message' => 'Tente novamente mais tarde'
        ]);
      }

    }

  }

} else {
  echo json_encode([
    'erro' => true,
    'message' => 'Request is required'
  ]);
}
