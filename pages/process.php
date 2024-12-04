<?php

// error_reporting(E_ALL & ~E_WARNING);
// ini_set('display_errors', 1);

if (isset($_POST['buy'], $_POST['method'])) {

  if (isset($_POST['dados'])) {

    if ($_POST['dados'] != "") {

      if (json_decode($_POST['dados'])) {

        $dados = json_decode($_POST['dados']);
        $method = $_POST['method'];

        if (!in_array($method, METHODS_GATEWAY)) {
          die(json_encode([
            'erro' => true,
            'message' => 'Method payment not found',
            'err_l' => __LINE__
          ]));
        }

        if (isset($dados->id, $dados->nome, $dados->doc, $dados->email, $dados->qtd, $dados->number)) {

          if ($dados->id != "" && $dados->nome != "" && $dados->doc != "" && $dados->email != "" && $dados->number != "" && $dados->qtd != 0) {

            $product = new Product($dados->id);
            $produto = $product->getProductById($dados->id);

            if ($produto) {

              if ($produto->uniq_link == "not") {

                // get items product
                $getItemsProduct = $product->getItemsProduct();
                $qtdItensProd = $getItemsProduct ? count($getItemsProduct) : 0;

                if (isset($_POST['calc'])) {
                  echo json_encode([
                    'erro' => false,
                    'qtd' => $qtdItensProd,
                    'data' => $produto
                  ]);
                  exit;

                }

              } else {
                $getItemsProduct = true;
              }


              if ($getItemsProduct) {

                if ($produto->uniq_link == "not") {
                  if ($dados->qtd > $qtdItensProd) {
                    echo json_encode([
                      'erro' => true,
                      'message' => 'Quantidade de itens indisponível.',
                      'err_l' => __LINE__
                    ]);
                    exit;
                  }
                }

                $clients = new Clients();
                $transaction = new Transaction();
                $settings = new Settings();
                $marketplace = new Marketplace();
    
                $marketplace->id = $produto->account_mkt;
                $isAccount = $marketplace->getMarketPlaceById();
                
                if(!$isAccount){
                    die(json_encode([
                      'erro' => true,
                      'message' => 'Desculpe, tente novamente mais tarde.',
                      'err_l' => __LINE__
                    ]));
                    
                }
    
                $getSettings = $settings->getSettings();

                $getClientByRecursive = $clients->getClientByRecursive($dados->email);

                if ($getClientByRecursive) {
                  $addClient = $getClientByRecursive->id;
                } else {
                  $addClient = $clients->add($dados);
                }

                if ($addClient) {

                  $vlConvert = $settings->convertMoney(1, $produto->price);
                  $vlCalc = ($dados->qtd * $vlConvert);
                  $vlFinal = $settings->convertMoney(2, $vlCalc);

                  // set cookie
                  setcookie('client_mail', $dados->email, strtotime('1 year'), '/');
                  setcookie('client_nome', $dados->nome, strtotime('1 year'), '/');
                  setcookie('client_number', $dados->number, strtotime('1 year'), '/');

                  $dados->client_id = $addClient;

                  // DATA TRANSACTION
                  $data = new stdClass();
                  $data->product_id = $produto->id;
                  $data->valor = $vlFinal;
                  $data->info_data = json_encode($dados);
                  $data->status = 'pending';
                  $data->reference = uniqid(); 
                  $data->qtd = $dados->qtd;
                  $data->origem = ORIGEM;
                  $data->plataform = GATEWAY_PAYMENT;

                  $addTransaction = $transaction->add($data);

                  if ($addTransaction) {

                        
                        try{
                            
                            require_once 'lib/' . GATEWAY_PAYMENT . '/vendor/autoload.php';
                            $gateway_init = ucfirst(GATEWAY_PAYMENT);
        
                            // generete payment
        
                            $payment = new $gateway_init([
                              'access_token' => $isAccount[0]->access_token,
                              'valor' => (double) $settings->convertMoney(1, $data->valor),
                              'app_name' => APP_NAME,
                              'url_success' => URL_SUCCESS,
                              'url_failure' => URL_CANCEL,
                              'url_pending' => URL_PENDING,
                              'app_url' => APP_URL,
                              'reference' => $data->reference,
                              'url_notification' => APP_URL . '/callback/' . $isAccount[0]->id,
                              'account_id' => $isAccount[0]->id,
                              'nome' => $dados->nome,
                              'email' => $dados->email,
                              'number' => $dados->number,
                              'product_id' => $produto->id,
                              'product_name' => $produto->name,
                              'product_valor' => (double) $settings->convertMoney(1, $produto->price),
                              'qtd_product' => $dados->qtd,
                            ]);
        
                            $methodPayment = $method;
        
                            if (method_exists($payment, $methodPayment)) {
                                $payment->$methodPayment();
                            } else {
                              $transaction->deleteTransaction(['reference', $payment->data_payment->reference]);
                              die(json_encode([
                                'erro'    => true,
                                'message' => 'Method payment not found',
                                'err_l' => __LINE__
                              ]));
                            }
        
                            if ($payment->erro) { 
                              
                              $transaction->deleteTransaction(['reference', $payment->data_payment->reference]);
                             
                              echo json_encode([
                                'erro' => true,
                                'message' => $payment->message_erro,
                                'err_l' => __LINE__
                              ]);
            
                            } else {
        
                             $transaction->setReturnGateway($payment->return_gateway, $data->reference);
        
                              echo json_encode([
                                'erro' => false,
                                'qrcodepix' => $payment->qrcodepix,
                                'reference' => $data->reference,
                                'pixcode' => $payment->pixcode,
                                'link'  => $payment->link_transaction
                              ]);
                            }

                        }catch(\Exception $e){
                            $transaction->setReturnGateway($payment->return_gateway, $data->reference);
                            
                              echo json_encode([
                                'erro' => true,
                                'message' => 'Desculpe, tente novamente.',
                                'err_l' => __LINE__
                              ]);
                              
                        }
                        
            
                  } else {
                     
                    echo json_encode([
                      'erro' => true,
                      'message' => 'Desculpe, tente novamente.',
                      'err_l' => __LINE__
                    ]);
                  }

                } else {
                  echo json_encode([
                    'erro' => true,
                    'message' => 'Desculpe, tente novamente.',
                    'err_l' => __LINE__
                  ]);
                }

              } else {
                echo json_encode([
                  'erro' => true,
                  'message' => 'Produto indisponível no momento',
                  'err_l' => __LINE__
                ]);
              }


            } else {
              echo json_encode([
                'erro' => true,
                'message' => 'Produto não está mais disponível',
                'err_l' => __LINE__
              ]);
            }

          } else {
            echo json_encode([
              'erro' => true,
              'message' => 'Preencha todos os campos',
              'err_l' => __LINE__
            ]);
          }

        } else {
          echo json_encode([
            'erro' => true,
            'message' => 'Preencha todos os campos',
            'err_l' => __LINE__
          ]);
        }

      } else {
        echo json_encode([
          'erro' => true,
          'message' => 'Preencha todos os campos',
          'err_l' => __LINE__
        ]);
      }

    } else {
      echo json_encode([
        'erro' => true,
        'message' => 'Preencha todos os campos',
        'err_l' => __LINE__
      ]);
    }

  } else {
    echo json_encode([
      'erro' => true,
      'message' => 'Preencha todos os campos',
      'err_l' => __LINE__
    ]);
  }

} else {
  echo json_encode([
    'erro' => true,
    'message' => 'Preencha todos os campos',
    'err_l' => __LINE__
  ]);
}
