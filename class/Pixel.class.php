<?php

  /**
   *
   */
  class Pixel extends Conn
  {
    public $pixeltoken;
    public $pixelid;
    public $conn;
    public $pdo;

    function __construct(){

      $this->conn = new Conn();
      $this->pdo  = $this->conn->pdo();
      
    }

    public function setCredentials(){
      $pixel_get = self::getPixelFacebook();
      
      if($pixel_get){
          $this->pixeltoken = $pixel_get->token_pixel;
          $this->pixelid    = $pixel_get->id_pixel;
      }

      return false;

    }

    public function getPixelFacebook(){

      $query_consult = $this->pdo->query("SELECT * FROM `pixel_facebook` WHERE id='1'");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult[0];
      }else{
        return false;
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

    public function sendPurchase($data, $valor){

      $email_hash   = hash("sha256", strtolower( $data->email) );
      $phone_hash   = hash("sha256", strtolower( $data->telefone) );
      $nome_hash    = hash("sha256", strtolower( explode(' ', $data->nome)[0]) );
      $sobreN_hash  = hash("sha256", strtolower( explode(' ', $data->nome)[1]) );
      $data_nasc    = hash("sha256", strtolower( str_replace('-', '', $data->data_nasc)) );
      $cidade       = hash("sha256", strtolower( trim(explode(',', $data->cidade_uf)[0])) );
      $estado       = hash("sha256", strtolower( trim(explode(',', $data->cidade_uf)[1])) );
      $country      = hash("sha256", strtolower( 'BR' ));

       $data = array(
        "data" => array(
          array(
            "event_name"  => "Purchase",
            "event_time"  => time(),
            "user_data"   => array(
              "client_ip_address" => $data->address_ip,
              "client_user_agent" => $data->user_agent,
              "em" => $email_hash,
              "ph" => $phone_hash,
              "fn" => $nome_hash,
              'ln' => $sobreN_hash,
              'db' => $data_nasc,
              'ct' => $cidade,
              'st' => $estado,
              'country' => $country,
              "fbc" =>'',
              "fbp" =>''
            ),
            "contents" => array(
              array(
                "id" => '1',
                "quantity" => 1,
                "delivery_category"=> "home_delivery"
              )
            ),
            "custom_data" => array(
              "currency" => "BRL",
              "value"    => self::convertMoney(1, $valor),
            ),
            "action_source" => "website",
          )
        ),
        "access_token" => $this->pixeltoken
      );

      $dataString = json_encode($data);
      $ch = curl_init('https://graph.facebook.com/v11.0/'.$this->pixelid.'/events');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($dataString)
      ));
      $response = curl_exec($ch);

     file_put_contents('data.json',$response);

    }

    public function updatePixel($data){
      $query = $this->pdo->prepare("UPDATE `pixel_facebook` SET id_pixel= :id_pixel, token_pixel= :token_pixel");
      $query->bindValue(':id_pixel', $data->id_pixel);
      $query->bindValue(':token_pixel', $data->token_pixel);

      if($query->execute()){
        return true;
      }else{
        return false;
      }
    }


  }
