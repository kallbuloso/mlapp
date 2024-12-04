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

   if(isset($_POST['type'], $_POST['product'])){

      require_once '../../config.php';
      require_once '../../class/Conn.class.php';
      require_once '../../class/Product.class.php';

      $type       = trim($_POST['type']);
      $product_id = trim($_POST['product']);

      $product     = new Product($product_id);

      if($type == "add"){
          try {

            $file_names     = $_FILES['files']['name'];
            $file_tmp_names = $_FILES['files']['tmp_name'];


            // Execute um loop para cada arquivo enviado
             for ($i = 0; $i < count($file_names); $i++) {
               // Obtenha o nome original do arquivo
               $file_name = $file_names[$i];

               $ext = pathinfo($file_name, PATHINFO_EXTENSION);

               // Defina o novo nome do arquivo
               $new_file_name = md5(time() . '_' . rand(1000,99999)) . '.' .$ext;
               // Defina o caminho de destino para o arquivo
               $destination = '../../files/' . $new_file_name;

  
               // Verifique se o arquivo foi movido com sucesso
               if (move_uploaded_file($file_tmp_names[$i], $destination)) {
                 // Prepare uma consulta SQL para inserir informações sobre o arquivo no banco de dados
                  $addItem = $product->addItem($new_file_name);
               }
             }

              header('Location: ../items?product_id=' . $product_id . '&success');

          } catch (\Exception $e) {

             header('Location: ../items?product_id=' . $product_id . '&error');

          }
        }else if($type == "delete"){

          try {

              $id     = trim($_POST['id']);
              $item   = $product->getItemById($id);

              if(is_file('../../files/' . $item->item)){
                unlink('../../files/' . $item->item);
              }

              $delete = $product->deleteItem($id);

              if($delete){

                echo json_encode([
                  'erro'    => false,
                  'message' => 'Item removido com sucesso'
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
