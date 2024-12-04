<?php

 class Conn{

   private $host;
   private $user;
   private $senha;
   private $bd;

  public static function pdo(){

    $host   = DB_HOST;
    $user   = DB_USER;
    $senha  = DB_PASS;
    $bd     = DB_NAME;

    try{
      $pdo = new \PDO("mysql:host=$host;dbname=$bd", $user, $senha, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8MB4"));
      return $pdo;
      $pdo = null;
    }catch(PDOException $e){
      return false;
    }
  }

 }

 ?>
