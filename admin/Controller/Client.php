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
      require_once '../../class/Clients.class.php';
      require_once '../../class/Transaction.class.php';

      $client = new Clients;
      $transaction = new Transaction;

      $type = trim($_POST['type']);

     if($type == "delete"){

        try {

            $id     = trim($_POST['id']);
            $delete = $client->delete($id);

            if($delete){

              echo json_encode([
                'erro'    => false,
                'message' => 'Cliente removido com sucesso'
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

      }else if($type == "viewBuys" && isset($_POST["id"])){

        $id = trim($_POST['id']);
        $status = "approved";

        $getTransactions = $transaction->getTransactionsByClient($id, $status);
    
        if($getTransactions){
              echo json_encode([
              'erro'    =>  false,
              'message' => 'Localizamos ( ' . count($getTransactions) . ' ) para este cliente.',
              'data'    => $getTransactions
            ]); 
        }else{
             echo json_encode([
              'erro'    => true,
              'message' => 'Nenhuma compra localizada para este cliente'
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
