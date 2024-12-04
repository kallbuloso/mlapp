<?php

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MpException;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

/**
 *
 */
class Mercadopago extends Conn
{
  public $link_transaction = NULL;
  public $data_payment;
  public $conn;
  public $pdo;
  public $message_erro = NULL;
  public $qrcodepix = NULL;
  public $pixcode = NULL;
  public $erro;
  public $return_gateway = NULL;

  function __construct($data_pay = array())
  {

    $this->data_payment = (object) $data_pay;
    $this->conn = new Conn();
    $this->pdo = $this->conn->pdo();

  }

  public function link()
  {

    try {

      MercadoPagoConfig::setAccessToken($this->data_payment->access_token);
      LOCAL ? MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL) : null;

      $url_notification = LOCAL ? NGROK_LINK . '/callback/' . $this->data_payment->account_id : $this->data_payment->url_notification;

      $client = new PreferenceClient();

      $createRequest = [
        "external_reference" => $this->data_payment->reference,
        "notification_url" => $url_notification,
        "items" => array(
          array(
            "id" => $this->data_payment->product_id,
            "title" => $this->data_payment->product_name,
            "quantity" => $this->data_payment->qtd_product,
            "currency_id" => "BRL",
            "unit_price" => $this->data_payment->product_valor
          )
        )
      ];

      $preference = $client->create($createRequest);

      if (isset($preference->id)) {
        if ($preference->id != NULL) {

          $this->return_gateway = json_encode($preference);

          $this->link_transaction = $preference->init_point;
        } else {
          $this->erro = true;
          $this->message_erro = "Desculpe, tente mais tarde 1";
        }
      } else {
        $this->erro = true;
        $this->message_erro = "Desculpe, tente mais tarde 2";
      }

      return $this;

    } catch (\Exception $th) {
       
      $this->erro = true;
      $this->message_erro = "Desculpe, tente mais tarde 3";
      return $this;
    }

  }

  public function pix()
  {

    MercadoPagoConfig::setAccessToken($this->data_payment->access_token);

    LOCAL ? MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL) : null;

    $url_notification = LOCAL ? NGROK_LINK . '/callback/' . $this->data_payment->account_id : $this->data_payment->url_notification;

    $request = [
      "transaction_amount" => $this->data_payment->valor,
      "description" => $this->data_payment->app_name,
      "external_reference" => $this->data_payment->reference,
      "notification_url" => $url_notification,
      "payment_method_id" => "pix",
      "payer" => [
        "email" => $this->data_payment->email,
        "first_name" => $this->data_payment->nome
      ]
    ];


    try {

      $client = new PaymentClient();
      $payment = $client->create($request);

      if ($payment->point_of_interaction->transaction_data != NULL) {

        $this->return_gateway = json_encode($payment);

        $dados_pix = $payment->point_of_interaction->transaction_data;
        $this->pixcode = $dados_pix->qr_code;
        $this->qrcodepix = "data:image/jpeg;base64,{$dados_pix->qr_code_base64}";
        $this->erro = false;

      } else {
        $this->erro = true;
        $this->message_erro = "Desculpe, tente mais tarde";
      }

      return $this;

    } catch (Throwable $e) {

      $this->erro = true;
      $this->message_erro = $e->getMessage();
      return $this;
    }

  }


}
