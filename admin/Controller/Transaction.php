<?php

@session_start();
if (!isset($_SESSION['ADM_LOGGED'])) {
  echo json_encode([
    'erro' => true,
    'message' => 'Faça login'
  ]);
  exit;
}


if (isset($_POST)) {

  if (isset($_POST['type'])) {

    require_once '../../config.php';
    require_once '../../class/Conn.class.php';
    require_once '../../class/Transaction.class.php';
    require_once '../../class/Clients.class.php';
    require_once '../../class/Product.class.php';

    $transaction = new Transaction;
    $clients = new Clients;
    $product = new Product;

    $type = trim($_POST['type']);

    if ($type == "get_detail") {

      $id = trim($_POST['id']);
      $getTransactionById = $transaction->getTransactionById($id);

      if ($getTransactionById) {

        // get client
        $clientInfo = false;
        $text_zap = false;

        $clientInfo = false;

        if (json_decode($getTransactionById->info_data)) {
          $info_data = json_decode($getTransactionById->info_data);

          if ($getTransactionById->plataform !== "MERCADOLIVRE") {

            $client_id = $info_data->client_id;
            $clientInfo = $clients->getClientById($client_id);

            $text_zap = file_get_contents('../assets/txt/modelo_whats.txt');

            $clientData = get_object_vars($clientInfo);

            $keys = array_keys($clientData);
            $new_keys = array_map(function ($k) {
              return '{{' . $k . '}}';
            }, $keys);

            $text_zap = str_replace($new_keys, array_values($clientData), $text_zap);

          } else {
            $clientInfo = (object) array('nome' => $info_data->buyer->first_name, 'number' => '<i style="font-size:11px;" >(Privado pelo Mercado Livre)</i>', 'email' => '<i style="font-size:11px;" >(Privado pelo Mercado Livre)</i>');
            $text_zap   = false;
          }

        }

        $getprod = $product->getProductById($getTransactionById->product_id);
 
        $getTransactionById->status_read = $getTransactionById->status;
        $getTransactionById->status = $getTransactionById->status == 'approved' ? '<span class="badge bg-success" >Aprovado</span>' : '<span class="badge bg-info" >Pendente</span>';
        $getTransactionById->created = date('d/m/Y H:i', strtotime($getTransactionById->created));

        echo json_encode([
          'erro' => false,
          'message' => 'Transação localizada',
          'transaction' => $getTransactionById,
          'client' => $clientInfo,
          'text_zap' => $text_zap,
          'product' => $getprod
        ]);

      } else {
        echo json_encode([
          'erro' => true,
          'message' => 'Não foi possível buscar a transação'
        ]);
      }


    } else if ($type == "setTag") {

      $id = trim($_POST['id']);
      $getTransactionById = $transaction->getTransactionById($id);

      if ($getTransactionById) {

        if (isset($_POST['tag'])) {

          if (trim($_POST['tag']) == "Concluído" || trim($_POST['tag']) == "Processando") {

            $tag = trim($_POST['tag']);

            if ($transaction->setTag($tag, $id)) {

              echo json_encode([
                'erro' => false,
                'message' => 'Tag editada'
              ]);

            } else {
              echo json_encode([
                'erro' => true,
                'message' => 'Tente novamente'
              ]);
            }


          } else {
            echo json_encode([
              'erro' => true,
              'message' => 'Informe a tag'
            ]);
          }

        } else {
          echo json_encode([
            'erro' => true,
            'message' => 'Informe a tag'
          ]);
        }

      } else {
        echo json_encode([
          'erro' => true,
          'message' => 'Não foi possível buscar a transação'
        ]);
      }

    }else if ($type == "lockDownload") {

      $id = trim($_POST['id']);
      $getTransactionById = $transaction->getTransactionById($id);

      if ($getTransactionById) {
          
            $situ = $getTransactionById->download_active > 0 ? 0 : 1;

            if ($transaction->setStatusDownload($situ, $id)) {
                
              $msg = $situ > 0 ? "desbloqueado" : "bloqueado";

              echo json_encode([
                'erro' => false,
                'message' => 'Download '. $msg
              ]);

            } else {
              echo json_encode([
                'erro' => true,
                'message' => 'Tente novamente'
              ]);
            }



      } else {
        echo json_encode([
          'erro' => true,
          'message' => 'Não foi possível buscar a transação'
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
