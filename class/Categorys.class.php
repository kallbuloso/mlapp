<?php


  /**
   *
   */
  class Categorys extends Conn
  {

    public $conn;
    public $pdo;
    public $category_id;

    function __construct($category_id=false){

      $this->conn        = new Conn();
      $this->pdo         = $this->conn->pdo();
      $this->category_id = $category_id;

    }


    public function delete($id){

      $stmt = $this->pdo->prepare("DELETE FROM `categorias` WHERE id = :id");
      $stmt->bindParam(':id', $id);
      if($stmt->execute()){
        return true;
      }else{
        return false;
      }

    }


    public function list(){

      $query_consult = $this->pdo->query("SELECT * FROM `categorias` ORDER BY id DESC");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }

    }

    public function addCategory($data){

      $query = $this->pdo->prepare("INSERT INTO `categorias` (nome, description) VALUES (:nome, :description) ");
      $query->bindValue(':nome', $data->nome);
      $query->bindValue(':description', $data->description);

      if($query->execute()){
          return true;
      }else{
          return false;
      }

    }


    public function updateCategory($data){
      $query = $this->pdo->prepare("UPDATE `categorias` SET nome= :name, description= :description WHERE id=:id");
      $query->bindValue(':name', $data->name);
      $query->bindValue(':description', $data->description);
      $query->bindValue(':id', $data->id);

      if($query->execute()){
        return true;
      }else{
        return false;
      }
    }

    public function getCategoryById($id){

      $query_consult = $this->pdo->query("SELECT * FROM `categorias` WHERE id='{$id}'");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult[0];
      }else{
        return false;
      }

    }



  }
