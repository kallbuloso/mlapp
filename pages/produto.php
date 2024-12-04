<?php

  $view          = new View;
  $view->app_url = APP_URL;

 if(isset($_GET['url'])){

   $uri = explode('/',$_GET['url']);

   if(isset($uri[1])){

     $id = trim($uri[1]);

     if($id != ""){

       if(is_numeric($id)){

         $product   = new Product;
         $dashboard = new Dashboard;
         
         $produto = $product->getProductByRef($id);

         $produto        = !$produto ? $product->getProductById($id) : $produto;
         $pageViewsToday = $dashboard->pageViewsToday();

         if(!$pageViewsToday){
           // insert
           $dashboard->insertPageViewToday();
         }else{
           //update
           $dashboard->setPageViewToday();
         }

         if($produto){

           $view->page           = "produto";

           $view->headerElements = [
             'title'        => $produto->name,
             'description'  => $produto->description,
             'canonical'    => APP_URL.'/produto/'.$produto->id
           ];

           $view->view([
             'product_name'        => $produto->name,
             'product_description' => $produto->description,
             'product_price'       => $produto->price,
             'product_image'       => $produto->background,
             'product_id'          => $produto->id,
           ]);

         }else{
           $view->notfound();
         }

       }else{
         $view->notfound();
       }

     }else{
       $view->notfound();
     }

   }else{
     $view->notfound();
   }

 }else{
   $view->notfound();
 }
