<?php
if(isset($_GET['url'])){

  $explo_url = explode('/',$_GET['url']);

  if(isset($explo_url[1])){

    $token = trim($explo_url[1]);

    if(isset($_GET['admin'])){
      if(isset($_SESSION['ADM_LOGGED'])){
        $file = 'files/' . $token;
        if (file_exists($file)) {
          require_once 'class/Download.class.php';
          $download = new Download();
          $download->output_file($file, basename($file));
        }
      }
      exit;
    }
    
     $redirect = false;

     if(isset($explo_url[2])){
         if($explo_url[2] == "redirect"){
             $redirect = true;
         }
     }

    $transaction           = new Transaction();
    $getTransactionByToken = $transaction->getTransactionByToken($token, $redirect);

    if($getTransactionByToken){

      if($getTransactionByToken->status == "approved" && $getTransactionByToken->download_active){
          
        if(!$redirect){
          $transaction->sumDown($getTransactionByToken->id);
          $file = 'down/' . $token;
          require_once 'class/Download.class.php';
          $download = new Download(); 
          $download->output_file($file, basename($file));
        }else{
            
            require_once 'class/Product.class.php';
            $product = new Product;
            
            $prod = $product->getProductById($getTransactionByToken->product_id);
            
            if($prod){
                if($prod->uniq_link != "not" && $getTransactionByToken->token === null){
                    $transaction->sumDown($getTransactionByToken->id);
                    header('Location: ' . $prod->uniq_link);
                }
            }
            
        }
 
      }else{
          header('Location: https://google.com');
      }

    }

  }

}
