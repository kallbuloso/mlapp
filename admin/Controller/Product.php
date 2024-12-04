<?php

    @session_start();
    if(!isset($_SESSION['ADM_LOGGED'])){
      echo json_encode([
        'erro'    => true,
        'message' => 'Faça login'
      ]);
      exit;
    }
 

  if (isset($_POST)) {

   if(isset($_POST['type'])){

      require_once '../../config.php';
      require_once '../../class/Conn.class.php';
      require_once '../../class/Product.class.php';

      $product = new Product;

      $type = trim($_POST['type']);

      if($type == "edit"){
        try {

          $data = json_decode($_POST['data']);

          if(isset($data->account_mkt, $data->id, $data->recycle_file, $data->name, $data->typeDown, $data->price, $data->status, $data->description)){

            if($data->account_mkt != "" && $data->id != "" && $data->recycle_file !== "" && $data->name != "" && $data->typeDown != "" && $data->price != "" && $data->status != "" && $data->description){

              if($data->typeDown == "link"){
                if($data->link_download == ""){
                  die(json_encode([
                    'erro'    => true,
                    'message' => 'Informe um link para download'
                  ]));
                }
              }else if($data->typeDown == "upload"){
                if($data->recycle_file == ""){
                  die(json_encode([
                    'erro'    => true,
                    'message' => 'Informe o tipo de armazenamento do upload'
                  ]));
                }
              }

              $data->price = trim(str_replace('R$ ','',$data->price));

              if($data->image == ""){
                $data->image = "https://dummyimage.com/450x300/dee2e6/6c757d.jpg";
              }

              $updateProduct = $product->updateProduct($data);

              if($updateProduct){

                echo json_encode([
                  'erro'    => false,
                  'message' => 'Produto editado com sucesso.'
                ]);

              }else{
                echo json_encode([
                  'erro'    => true,
                  'message' => 'Não foi possível fazer o cadastro'
                ]);
              }

            }else{
              echo json_encode([
                'erro'    => true,
                'message' => 'Preencha todos os campos'
              ]);
            }

          }else{
            echo json_encode([
              'erro'    => true,
              'message' => 'Preencha todos os campos'
            ]);
          }

        } catch (\Exception $e) {

          echo json_encode([
            'erro'    => true,
            'message' => 'Tente novamente mais tarde'
          ]);

        }
      }else if($type == "get"){

        $id   = trim($_POST['id']);
        $getProductById = $product->getProductById($id);

        if($getProductById){

          echo json_encode([
            'erro'    => false,
            'message' => 'Produto localizado',
            'data'    => $getProductById
          ]);

        }else{
          echo json_encode([
            'erro'    => true,
            'message' => 'Não foi possível buscar o produto'
          ]);
        }


      }else if($type == "add"){
        try {

          $data = json_decode($_POST['data']);

          if(isset($data->recycle_file, $data->name, $data->typeDown, $data->price, $data->status, $data->description)){

            if($data->recycle_file !== "" && $data->name != "" && $data->typeDown != "" && $data->price != "" && $data->status != "" && $data->description){

              if($data->typeDown == "link"){
                if($data->link_download == ""){
                  die(json_encode([
                    'erro'    => true,
                    'message' => 'Informe um link para download'
                  ]));
                }
              }else if($data->typeDown == "upload"){
                if($data->recycle_file == ""){
                  die(json_encode([
                    'erro'    => true,
                    'message' => 'Informe o tipo de armazenamento do upload'
                  ]));
                }
              }

              $data->price = trim(str_replace('R$ ','',$data->price));

              if($data->image == ""){
                $data->image = "https://dummyimage.com/450x300/dee2e6/6c757d.jpg";
              }

              $addProduct = $product->addProduct($data);

              if($addProduct){

                echo json_encode([
                  'erro'    => false,
                  'message' => 'Produto adicionado com sucesso.',
                  'id'      => $addProduct
                ]);

              }else{
                echo json_encode([
                  'erro'    => true,
                  'message' => 'Não foi possível fazer o cadastro'
                ]);
              }

            }else{
              echo json_encode([
                'erro'    => true,
                'message' => 'Preencha todos os campos'
              ]);
            }

          }else{
            echo json_encode([
              'erro'    => true,
              'message' => 'Preencha todos os campos'
            ]);
          }

        } catch (\Exception $e) {

          echo json_encode([
            'erro'    => true,
            'message' => 'Tente novamente mais tarde'
          ]);

        }
      }else if($type == "delete"){

        try {

            $id     = trim($_POST['id']);
            $delete = $product->delete($id);

            if($delete){

              echo json_encode([
                'erro'    => false,
                'message' => 'Produto removido com sucesso'
              ]);

            }else{
              echo json_encode([
                'erro'    => true,
                'message' => 'Tente novamente mais tarde'
              ]);
            }

        } catch (\Exception $e) {
          echo json_encode([
            'erro'    => true,
            'message' => 'Tente novamente mais tarde'
          ]);
        }

      }

    }

  }else{
    echo json_encode([
      'erro'    => true,
      'message' => 'Request is required'
    ]);
  }
