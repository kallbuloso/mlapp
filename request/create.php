<?php

if(isset($_POST['nome'], $_POST['email'], $_POST['senha'])){

   $nome  = trim($_POST['nome']);
   $email = trim($_POST['email']);
   $senha = trim($_POST['senha']);

   if($nome == "" || $email == "" || $senha == ""){
     echo json_encode([
       'erro'    => true,
       'message' => 'Preencha todos os campos'
     ]);
     exit;
   }

   $client = new clients();

   $dados_client = new stdClass();
   $dados_client->nome  = $nome.
   $dados_client->email = $email;

   $getClientByEmail = $client->getClientByRecursive($email);

   if($getClientByEmail){

     if($getClientByEmail->password == NULL){
       // cliente existe, define uma senha para ele
       $id_client = $getClientByEmail->id;
     }else{
       echo json_encode([
         'erro'    => true,
         'message' => 'Conta já existe',
         'direct'  => true
       ]);
       exit;
     }

   }else{
     // cliente nao existe cria uma conta
     $id_client = $client->add($dados_client);
   }

   // set password
   if($client->setPasswordClient($id_client, $senha)){
     echo json_encode([
       'erro'    => false,
       'message' => 'Conta criada com sucesso',
       'direct'  => true
     ]);
     exit;
   }else{
     echo json_encode([
       'erro'    => true,
       'message' => 'Desculpe, tente novamente mais tarde'
     ]);
     exit;
   }

   exit;
}
