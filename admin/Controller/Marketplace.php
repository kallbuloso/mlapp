<?php

require_once '../../config.php';

if (!isset($_SESSION['ADM_LOGGED'])) {
  echo json_encode([
    'erro' => true,
    'message' => 'Faça login'
  ]);
  exit;
}

 
if (isset($_POST)) {

  if (isset($_POST['type'])) {

    require_once '../../class/Conn.class.php';
    require_once '../../class/MercadoLivre.class.php';
    require_once '../../class/Marketplace.class.php';

    $marketplace = new Marketplace;

    $type = trim($_POST['type']);

     if ($type == "add") {
      try {

        $access_token = trim($_POST['access_token']);

          if ($access_token != ""){
              
            $mercadolivre = new MercadoLivre($access_token);
            $mercadolivre->testConnection();
            
            if ($mercadolivre->erro) {
              die(json_encode([
                'erro' => true,
                'message' => "Access Token inválido"
              ]));
            }
            
            $marketplace->access_token = $access_token;
            $marketplace->data_account = json_encode($mercadolivre->info_account);
             
            $addAccount = $marketplace->addAccount();

            if ($addAccount) {

              echo json_encode([
                'erro'    => false,
                'id'      => $addAccount,
                'message' => 'Conta adicionado com sucesso.'
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

      } catch (\Exception $e) {

        echo json_encode([
          'erro' => true,
          'message' => 'Tente novamente mais tarde'
        ]);

      }
    } else if ($type == "delete") {

      try {

        $id = trim($_POST['id']);
        $marketplace->id = $id;
        $delete = $marketplace->delete();
    
        if ($delete) {

          echo json_encode([
            'erro' => false,
            'message' => 'Conta removida com sucesso'
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

    }else if ($type == "save") {
      try {

        $access_token = trim($_POST['access_token']);
        $id = $_POST['id'];

          if ($access_token != "" && $id != ""){
              
            $marketplace->id = $id;  
            $isAccount = $marketplace->getMarketPlaceById();
            
            if(!$isAccount){
                die(json_encode([
                    'erro' => true,
                    'message' => "Não localizamos este registro"
                 ]));
            }
            
            if($isAccount[0]->access_token === $access_token){
                 die(json_encode([
                    'erro' => true,
                    'message' => "Não há alterações para serem salvas."
                 ]));
            }
              
            $mercadolivre = new MercadoLivre($access_token);
            $mercadolivre->testConnection();
            
            if ($mercadolivre->erro) {
              die(json_encode([
                'erro' => true,
                'message' => "Access Token inválido"
              ]));
            }
            
            $marketplace->access_token = $access_token;
            $marketplace->data_account = json_encode($mercadolivre->info_account);

            $saveAccount = $marketplace->saveAccount();

            if ($saveAccount) {

              echo json_encode([
                'erro'    => false,
                'message' => 'Conta editada com sucesso.'
              ]);

            } else {
              echo json_encode([
                'erro' => true,
                'message' => 'Não foi possível fazer a alteração'
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
    }

  }

} else {
  echo json_encode([
    'erro' => true,
    'message' => 'Request is required'
  ]);
}
