<?php

  /**
   * Settings
   */
  class Settings extends Conn
  {

    public $conn;
    public $pdo;
    
    function __construct(){

      $this->conn = new Conn();
      $this->pdo  = $this->conn->pdo();

    }

    public function getSettings(){

      $query_consult = $this->pdo->query("SELECT * FROM `settings` WHERE id='1'");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult[0];
      }else{
        return false;
      }

    }
    public function execInBackground($cmd, $params){

      $params = implode(" ", $params);      

      if (substr(php_uname(), 0, 7) == "Windows"){
          return pclose(popen("start /B ". $cmd .' '. $params, "r")); 
      }
      else {
        return exec($cmd .' '. $params . " > /dev/null &");  
      }
  } 
    public function convertMoney($type,$valor){
        if($type == 1){
          $a = str_replace(',','.',str_replace('.','',$valor));
          return $a;
        }else if($type == 2){
          return number_format($valor,2,",",".");
        }
      }


    public function updateSettings($data){

      if($data->pwd != ""){

        $hash = password_hash($data->pwd, PASSWORD_DEFAULT);

        $query = $this->pdo->prepare("UPDATE `settings` SET pwd= :pwd, username= :username, template_ml_message= :template_ml_message, template_ml_email= :template_ml_email, title_template_email= :title_template_email");
        $query->bindValue(':pwd', $hash);
        $query->bindValue(':username', $data->username);
        $query->bindValue(':template_ml_message', $data->template_ml_message);
        $query->bindValue(':template_ml_email', $data->template_ml_email);
        $query->bindValue(':title_template_email', $data->title_template_email);

        if($query->execute()){
          return true;
        }else{
          return false;
        }

      }else{
        $query = $this->pdo->prepare("UPDATE `settings` SET username= :username, template_ml_message= :template_ml_message, template_ml_email= :template_ml_email, title_template_email= :title_template_email");
        $query->bindValue(':username', $data->username);
        $query->bindValue(':template_ml_message', $data->template_ml_message);
        $query->bindValue(':template_ml_email', $data->template_ml_email);
        $query->bindValue(':title_template_email', $data->title_template_email);


        if($query->execute()){
          return true;
        }else{
          return false;
        }
      }




    }


  }
