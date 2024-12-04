<?php

error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 1);

 if(isset($_POST['request'])){

     $clients = new Clients;

     if(isset($_SESSION['logado'])){

       $client_logged = $_SESSION['logado']['id'];

       if($clients->disconnectMp($client_logged)){

         echo json_encode([
           'erro' => false,
           'message' => 'Conta desconectada'
         ]);

       }else{
         echo json_encode([
           'erro' => true,
           'message' => 'Desculpe, tente novamente mais tarde'
         ]);
       }

     }else{
       echo json_encode([
         'erro' => true,
         'message' => 'Faça login em sua conta'
       ]);
     }


 }else{
   echo json_encode([
     'erro' => true,
     'message' => 'Method not allow'
   ]);
 }
