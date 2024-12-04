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
      require_once '../../class/Settings.class.php';

      $settings = new Settings;

      $type = trim($_POST['type']);

      if($type == "edit"){
        try {

          $data = json_decode($_POST['data']);

          if(isset($data->pwd, $data->username)){

            if($data->username != ""){

              $updateSettings = $settings->updateSettings($data);

              if($updateSettings){

                echo json_encode([
                  'erro'    => false,
                  'message' => 'Configurações editadas com sucesso.'
                ]);

              }else{
                echo json_encode([
                  'erro'    => true,
                  'message' => 'Não foi possível fazer a alteração'
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
      }

    }

  }else{
    echo json_encode([
      'erro'    => true,
      'message' => 'Request is required'
    ]);
  }
