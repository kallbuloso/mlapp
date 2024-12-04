<?php

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MpException;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

/**
 *
 */
class MercadoLivre extends Conn
{

  public $access_token = NULL;
  public $conn;
  public $pdo;
  public $message_erro = NULL;
  public $erro;
  public $info_account = NULL;
  public $products_id = array();
  public $collection = NULL;
  public $is_marketplace = false;
  public $order_ml = NULL;
  public $transaction_id = false;

  function __construct($access_token = NULL)
  {

    $this->access_token = $access_token;
    $this->conn = new Conn();
    $this->pdo = $this->conn->pdo();

  }

  public function testConnection()
  {

    $curl = curl_init();

    curl_setopt_array(
      $curl,
      array(
        CURLOPT_URL => 'https://api.mercadolibre.com/users/me',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer ' . $this->access_token
        ),
      )
    );

    $response = curl_exec($curl);
    
    if (curl_errno($curl)) {
      $this->erro = true;
      $this->message_erro = '' . curl_error($curl);
      return $this;
    }

    curl_close($curl);

    try {

      $response = json_decode($response);

      if (isset($response->id)) {
        if (is_numeric($response->id)) {
          $this->erro = false;
          $this->info_account = $response;
          return $this;
        }
      }

      $this->erro = true;
      $this->message_erro = "Access Token Not valid";

      return $this;

    } catch (\Throwable $th) {
      $this->erro = false;
      return $this;
    }

  }

  public function getCollection($id)
  {

    $curl = curl_init();

    curl_setopt_array(
      $curl,
      array(
        CURLOPT_URL => 'https://api.mercadolibre.com/collections/' . $id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer ' . $this->access_token
        ),
      )
    );

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->erro = true;
      $this->message_erro = '' . curl_error($curl);
      return $this;
    }

    curl_close($curl);

    try {

      $response = json_decode($response);

      if (isset($response->id)) {
        if (!empty($response->id)) {
          $this->erro = false;
          $this->collection = $response;
          return $this;
        }
      }

      $this->erro = true;
      $this->message_erro = "Error get collection";

      return $this->erro;

    } catch (\Throwable $th) {
      $this->erro = false;
      return $this;
    }



  }

  public function sendMessageToPayer($message)
  {
    $transaction = new Transaction;

    $message = substr($message, 0, 350);
    $pack_id = $this->order_ml->pack_id === null ? $this->order_ml->id : $this->order_ml->pack_id;
    $seller_id = $this->order_ml->seller->id;
    $from = $this->order_ml->seller->id;
    $to = $this->order_ml->buyer->id;

    $curl = curl_init();

    $data = array(
      'from' => array('user_id' => $from),
      'to' => array('user_id' => $to),
      'text' => $message
    );

    $data_post = json_encode($data);

    curl_setopt_array(
      $curl,
      array(
        CURLOPT_URL => "https://api.mercadolibre.com/messages/packs/{$pack_id}/sellers/{$seller_id}?tag=post_sale",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => $data_post,
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer ' . $this->access_token,
          'Content-Type: application/json'
        ),
      )
    );

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->erro = true;
      $this->message_erro = '' . curl_error($curl);
      return $this;
    }

    curl_close($curl);

    try {

      $response = json_decode($response);

      if (isset($response->id)) {
        if ($response->status != "rejected") {
          $this->erro = false;
          return $this;
        }
      }

      $this->erro = true;
      $this->message_erro = "Error send message to chat payer";

      $this->transaction_id ? $transaction->setErrorMessageML($this->transaction_id, json_encode($response)) : null;

      return $this;

    } catch (\Throwable $th) {
      $this->erro = false;
      $this->transaction_id ? $transaction->setErrorMessageML($this->transaction_id, $th->getMessage()) : null;
      return $this;
    }

  }

  public function isMarketPlace()
  {

    if ($this->collection->marketplace == "MELI") {

      try {

        $this->is_marketplace = true;
        $this->getOrder();

        if ($this->order_ml === NULL) {
          $this->erro = true;
          $this->message_erro = "Order ID not found";
          return false;
        }

        $transaction = new Transaction;
        $product = new Product;

        // id external product
        $idMercadoLivre = isset($this->order_ml->order_items[0]->item->id) ? $this->order_ml->order_items[0]->item->id : NULL;

        if ($idMercadoLivre === NULL) {
          $this->erro = true;
          $this->message_erro = "Order empty item";
          return false;
        }

        // get product
        $prod = $product->getProductByIdML($idMercadoLivre);

        if (!$prod) {
          $this->erro = true;
          $this->message_erro = "Product no linked item ML";
          return false;
        }

        $transaction_data = $transaction->getTransactionByRef($this->collection->external_reference);

        if (!$transaction_data) {

          $transaction_id = $transaction->add((object) [
            'product_id' => $prod->id,
            'valor' => number_format(($this->order_ml->order_items[0]->unit_price - $this->order_ml->order_items[0]->sale_fee), 2, ",", "."),
            'info_data' => json_encode($this->order_ml),
            'status' => 'pending',
            'reference' => $this->collection->external_reference,
            'qtd' => $this->order_ml->order_items[0]->quantity,
            'origem' => 'https://mercadolibre.com',
            'plataform' => 'MERCADOLIVRE'
          ]);

          if (!$transaction_id) {
            $this->erro = true;
            $this->message_erro = "Product no linked item ML";
            return false;
          }

          $this->erro = false;
          return true;

        }

        $this->erro = false;
        return true;

      } catch (\Throwable $th) {
        $this->erro = true;
        $this->message_erro = $th->getMessage();

        return false;
      }

    }

    return false;

  }

  public function getOrder()
  {

    $curl = curl_init();

    curl_setopt_array(
      $curl,
      array(
        CURLOPT_URL => 'https://api.mercadolibre.com/orders/' . $this->collection->order_id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer ' . $this->access_token
        ),
      )
    );

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->erro = true;
      $this->message_erro = '' . curl_error($curl);
      return $this;
    }

    curl_close($curl);

    try {

      $response = json_decode($response);

      if (isset($response->id)) {
        if (!empty($response->id)) {
          $this->erro = false;
          $this->order_ml = $response;
          return $this->erro;
        }
      }

      $this->erro = true;
      $this->message_erro = "Erro get order";

      return $this->erro;

    } catch (\Throwable $th) {
      $this->erro = false;
      return $this->erro;
    }

  }


  public function getProducts()
  {

    $curl = curl_init();

    curl_setopt_array(
      $curl,
      array(
        CURLOPT_URL => 'https://api.mercadolibre.com/users/' . $this->info_account->id . '/items/search?access_token=' . $this->access_token,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => array(
          'Authorization: Bearer ' . $this->access_token
        ),
      )
    );

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->erro = true;
      $this->message_erro = '' . curl_error($curl);
      return $this;
    }

    curl_close($curl);

    try {

      $response = json_decode($response);

      if (isset($response->seller_id)) {
        if (!empty($response->seller_id)) {
          $this->erro = false;
          $this->products_id = $response->results;
          return $this->erro;
        }
      }

      $this->erro = true;
      $this->message_erro = "Erro list products";

      return $this->erro;

    } catch (\Throwable $th) {
      $this->erro = false;
      return $this->erro;
    }

  }

  public function confirmSendedProduct()
  {

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.mercadolibre.com/orders/'.$this->order_ml->id.'/feedback',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_POSTFIELDS => '{
        "fulfilled": true,
        "rating": "positive",
        "message": "'.MESSAGE_FEEDBACK_PAYER.'"
      }',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $this->access_token,
        'Content-Type: application/json'
      ),
    )
    );

    $response = curl_exec($curl);
    if (curl_errno($curl)) {
      $this->erro = true;
      $this->message_erro = '' . curl_error($curl);
      return $this;
    }

    curl_close($curl);

    
    try {

      $response = json_decode($response);

      if (isset($response->id)) {
        if (!empty($response->id)) {
          $this->erro = false;
          $this->products_id = $response->results;
          return $this;
        }
      }

      $this->erro = true;
      $this->message_erro = "Erro set order delivered";

      return $this;

    } catch (\Throwable $th) {
      $this->erro = false;
      return $this;
    }
    
  }

  public function getInfoProduct($product_id)
  {


    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api.mercadolibre.com/items/' . $product_id . '?access_token=' . $this->access_token);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      $this->erro = true;
      $this->message_erro = '' . curl_error($curl);
      return $this;
    }

    curl_close($curl);

    try {

      $response = json_decode($response);

      if (isset($response->id)) {
        if (!empty($response->id)) {
          $this->erro = false;
          return $response;
        }
      }

      $this->erro = true;
      $this->message_erro = "Erro get product";

      return $this->erro;

    } catch (\Throwable $th) {
      $this->erro = false;
      return $this->erro;
    }

  }


}
