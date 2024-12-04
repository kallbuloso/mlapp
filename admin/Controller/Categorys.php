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
      require_once '../../class/Categorys.class.php';

      $categorys = new Categorys;

      $type = trim($_POST['type']);

      if($type == "edit"){
        try {

          $data = json_decode($_POST['data']);

          if(isset($data->id, $data->name, $data->description)){

            if($data->id != "" && $data->name != "" && $data->description != ""){

              $updateCategory = $categorys->updateCategory($data);

              if($updateCategory){

                echo json_encode([
                  'erro'    => false,
                  'message' => 'Categoria editado com sucesso.'
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
            'message' => $e
          ]);

        }
      }else if($type == "get"){

        $id   = trim($_POST['id']);
        $getCategoryById = $categorys->getCategoryById($id);

        if($getCategoryById){

          echo json_encode([
            'erro'    => false,
            'message' => 'Produto localizado',
            'data'    => $getCategoryById
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

          if(isset($data->nome, $data->description)){

            if($data->nome != "" && $data->description != "" ){

              $addCategory = $categorys->addCategory($data);

              if($addCategory){

                echo json_encode([
                  'erro'    => false,
                  'message' => 'Categoria adicionado com sucesso.'
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
            $delete = $categorys->delete($id);

            if($delete){

              echo json_encode([
                'erro'    => false,
                'message' => 'Categoria removido com sucesso'
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
