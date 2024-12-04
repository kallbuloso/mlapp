<?php

  /**
   * Marketplace
   */
  class Marketplace extends Conn
  {

    public $conn;
    public $pdo;
    public $access_token;
    public $data_account;
    public $id;
    
    function __construct(){

      $this->conn = new Conn();
      $this->pdo  = $this->conn->pdo();

    }

    public function getMarketpalces(){

      $query_consult = $this->pdo->query("SELECT * FROM `accounts_marketplace` ORDER BY id DESC");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }

    }
    
    public function getMarketPlaceById(){

      $query_consult = $this->pdo->prepare("SELECT * FROM `accounts_marketplace` WHERE id = :id ORDER BY id DESC");
      $query_consult->bindParam(':id', $this->id);
      
      if($query_consult->execute()){
          
          $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
          if(count($fetch_consult)>0){
            return $fetch_consult;
          }else{
            return false;
          }

      }else{
          return false;
      }

    }

    public function delete(){

      $stmt = $this->pdo->prepare("DELETE FROM `accounts_marketplace` WHERE id = :id");
      $stmt->bindParam(':id', $this->id);
      if($stmt->execute()){
        return true;
      }else{
        return false;
      }

    }



    public function addAccount(){

      $query = $this->pdo->prepare("INSERT INTO `accounts_marketplace` (access_token, data_account) VALUES (:access_token, :data_account) ");
      $query->bindValue(':access_token', $this->access_token);
      $query->bindValue(':data_account', $this->data_account);

      if($query->execute()){
          return $this->pdo->lastInsertId();
      }else{
          return false;
      }

    }
 

    public function saveAccount(){

        $query = $this->pdo->prepare("UPDATE `accounts_marketplace` SET access_token= :access_token, data_account= :data_account WHERE id= :id");
        $query->bindValue(':access_token', $this->access_token);
        $query->bindValue(':data_account', $this->data_account);
        $query->bindValue(':id', $this->id);

        if($query->execute()){
          return true;
        }else{
          return false;
        }
      

    }


  }
