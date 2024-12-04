<?php

try {

  if(!isset($explo_url[1])){
    http_response_code(200);
    die;
  }
  
  if($explo_url[1] == ""){
    http_response_code(200);
    die; 
  }
    
   if(!is_numeric(trim($explo_url[1]))){
      http_response_code(200);
      die; 
  }
  
  $account_id = trim($explo_url[1]);
  
  $body = json_decode(file_get_contents("php://input"));

  if (!$body) {
    http_response_code(200);
    die;
  }

  if (!isset($body->topic, $body->resource)) {
    http_response_code(200);
    die;
  }

  if ($body->topic != "payment" || $body->resource == "") {
    http_response_code(200);
    die;
  }

  $resource = $body->resource;
  $path     = parse_url($resource, PHP_URL_PATH);
  $id       = basename($path);
  
  $marketplace = new Marketplace();
  $marketplace->id = $account_id;
  
  $isAccount = $marketplace->getMarketPlaceById();
    
  if(!$isAccount){
    http_response_code(200);
    die;
  }
  
  $settings = new Settings();
  $getSettings = $settings->getSettings();
  $access_token = trim($isAccount[0]->access_token);

  $mercadolivre = new MercadoLivre($access_token);

  $mercadolivre->testConnection();

  if ($mercadolivre->erro) {
    http_response_code(200);
    die;
  }

  $directory = dirname(__FILE__); //DIRECTORY_SEPARATOR
  $process_script_path = str_replace('pages', '', $directory) . 'admin' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . 'Webhook.php';

  http_response_code(200);

  $settings->execInBackground("php $process_script_path", [$id, PATH_BASE, $access_token]);

} catch (\Throwable $th) {
  http_response_code(200);
  die;
}