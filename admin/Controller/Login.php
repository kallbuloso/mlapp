<?php

  if (isset($_POST)) {

   if(isset($_POST['type'])){

      require_once '../../config.php';
      require_once '../../class/Conn.class.php';
      require_once '../../class/Settings.class.php';

      $settings = new Settings;

      $type = trim($_POST['type']);

      if($type == "login"){
        try {

          $data = json_decode($_POST['data']);

          if(isset($data->username, $data->password)){

            if($data->username != "" && $data->password != ""){

              $getSettings = $settings->getSettings();

              if( password_verify($data->password, $getSettings->pwd) && $data->username == $getSettings->username){

                $_SESSION['ADM_LOGGED'] = true;

                echo json_encode([
                  'erro'    => false,
                  'message' => 'Logado com sucesso.'
                ]);

              }else{
                echo json_encode([
                  'erro'    => true,
                  'message' => 'Não foi possível fazer o login, dados incorretos'
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
