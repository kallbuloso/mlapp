<?php

  if(isset($_POST['email'], $_POST['password'])){

    $senha = trim($_POST['password']);
    $email = trim($_POST['email']);

    if($email == "" || $senha == ""){
      echo json_encode([
        'erro' => true,
        'message' => "Preencha todos os campos"
      ]);
      exit;
    }

    $client = new Clients();

    // get account by email
    $getClientByEmail = $client->getClientByRecursive($email);

    if($getClientByEmail){

      if(password_verify($senha, $getClientByEmail->password)){

        $logado = (array)$getClientByEmail;

        @session_start();
        $_SESSION['logado'] = $logado;

        echo json_encode([
          'erro' => false,
          'message' => "Logado com sucesso"
        ]);
        exit;

      }else{
        if($getClientByEmail->password == NULL){
          echo json_encode([
            'erro' => true,
            'message' => "Você preciso definir uma senha em Criar conta"
          ]);
          exit;
        }else{
          echo json_encode([
            'erro' => true,
            'message' => "Senha incorreta"
          ]);
          exit;
        }
      }

    }else{
      echo json_encode([
        'erro' => true,
        'message' => "Conta não localizada"
      ]);
      exit;
    }

  }
