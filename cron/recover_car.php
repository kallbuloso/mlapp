<?php 

    require_once '../config.php';
    require_once '../autoload.php';

    $transaction = new Transaction();
    $clients = new Clients();
    $whatsapp = new Whatsapp();
    $product = new Product();

    $getTransactionRecover = $transaction->getRecoverCar();

    if($getTransactionRecover){

        $transaction_data = json_decode($getTransactionRecover->info_data);

        $return_gateway = json_decode($getTransactionRecover->return_gateway);

        // get client
        $client = $clients->getClientById($transaction_data->client_id);

        if($client){

            // check whatsapp is conected 
            $is_connected = $whatsapp->getStatus();

            if($is_connected){

                // check phone client
                $phone = DDI . str_replace(['-','(',')','+',' '],'',$client->number);
 
                $isPhoneValid = $whatsapp->isWhatsapp($phone);

             
                if($isPhoneValid){
                    // is whatsapp valid

                    // get is product
                    $product_data = $product->getProductById($getTransactionRecover->product_id);

                    if($product_data){

                        // whatsapp client
                        $phone = @explode('@',$isPhoneValid->Users[0]->JID)[0];        

                        // pix payload
                        $pix_payload = $return_gateway->point_of_interaction->transaction_data->qr_code;

                        // get content message
                        $content = file_get_contents('../template/whatsapp/recover_car.txt');
    
                        $pix_tow_message = false;

                        // verify send two message
                        if (strpos($content, '%{pix_payload}%') !== false) {
                            $pix_tow_message = true;
                        }

                        // set variables
                        $variables = array(
                            '{first_name}'     => explode(' ', $client->nome)[0],
                            '{product_name}'   => $product_data->name,
                            '%{pix_payload}%'  => ''
                        );

                        if(!$pix_tow_message){
                            $variables['{pix_payload}'] = $pix_payload;
                        }

                        // message send
                        $message = str_replace(array_keys($variables), array_values($variables), $content);

                        $sendMessage = $whatsapp->sendMessage($message, $phone);

                        var_dump($sendMessage);

                   
                        if($pix_tow_message && $sendMessage){
                            sleep(2);
                            $whatsapp->sendMessage($pix_payload, $phone);
                        }

                    }else{
                        echo 'not prod';
                    }

                }else{
                    echo 'not phone valid';
                }

            }else{
                echo 'not connected';
            }

        }else{
            echo 'not client';
        }

    }