<?php

try {

    if (!isset($argv[1], $argv[2], $argv[3])) {
        http_response_code(200);
        die;
    }

    $base_project = $argv[2];

    require_once $base_project . DIRECTORY_SEPARATOR . 'config.php';
    require_once $base_project . DIRECTORY_SEPARATOR . 'autoload.php';

    $id = $argv[1];

    $settings = new Settings();
    $pixel = new Pixel();
    $product = new Product();
    $transaction = new Transaction();
    $zip = new Zip();
    $mail = new Email();
    $whatsapp = new Whatsapp();
    
    $getSettings = $settings->getSettings();
    $access_token = trim($argv[3]);

    $mercadolivre = new MercadoLivre($access_token);
    $mercadolivre->testConnection();

    if ($mercadolivre->erro) {
        http_response_code(200);
        die;
    }

    $mercadolivre->testConnection();
    $mercadolivre->getCollection($id);

    if ($mercadolivre->erro) {
        http_response_code(200);
        die;
    }

    if ($mercadolivre->collection == NULL) {
        http_response_code(200);
        die;
    }


    $status = $mercadolivre->collection->status;
    $ref = $mercadolivre->collection->external_reference;
    $marketplace = $mercadolivre->collection->marketplace;

    // verify buy in MercadoLivre
    $mercadolivre->isMarketPlace();

    if ($mercadolivre->erro) {
        http_response_code(200);
        die;
    }

    $transaction_data = $transaction->getTransactionByRef($ref);

    if ($transaction_data) {

        if ($transaction_data->status == 'pending') {

            $produto_data = $product->getProductById($transaction_data->product_id);

            if ($produto_data) {

                if ($status == 'approved') { //approved

                    if ($produto_data->uniq_link == "not") {

                        $namZip = md5(uniqid() . strtotime('now') . rand(1000, 99999)) . '.zip';

                        // get file
                        $getItems = $product->getItemsProductApproved($produto_data->id, $transaction_data->qtd);

                        if ($getItems) {

                            $namesFiles = "";
                            foreach ($getItems as $key => $item) {
                                $namesFiles .= "files/" . $item->item . ",";

                                if ($produto_data->recycle_file == "no") {
                                    $product->deleteItem($item->id);
                                    if (is_file('../files' . $item->item)) {
                                        unlink('../files' . $item->item);
                                    }
                                }

                            }

                            $namesFiles = rtrim($namesFiles, ',');
                            $namesFiles = explode(',', $namesFiles);

                            $new_path = "down/" . $namZip;
                            $ziping = $zip->createZip($new_path, $namesFiles);

                            if ($ziping) {
                                $transaction->setToken($namZip, $transaction_data->id);
                            }

                        }

                        $link_download = APP_URL . '/download/' . $namZip;
                        $setFile = (object) array("file" => "down/{$namZip}", "name" => $produto_data->name, "extension" => "zip");

                    } else {
                        $link_download = APP_URL . '/download/' .$transaction_data->reference . '/redirect';
                        $setFile = false;
                    }

                    // set status
                    $transaction->setStatus('approved', $transaction_data->id);

                    if (ENABLE_PIXEL) {
                        // send pixel
                        $pixel->setCredentials();
                        $pixel->sendPurchase(json_decode($transaction_data->info_data), $transaction_data->valor);
                    }


                    if (SMTP_ENABLE && $mercadolivre->is_marketplace === FALSE) {

                        require_once 'lib/mailer/vendor/autoload.php';

                        ############ SEND EMAIL ###################################
                        $data_info_transaction = json_decode($transaction_data->info_data);

                        $template_email = $getSettings->template_ml_email;
                        
                        $array_text = array(
                                '{username}' => '',
                                '{first_name}' => $data_info_transaction->nome,
                                '{last_name}' => '',
                                '{link_download}' => $link_download,
                                '{order_id}' => $mercadolivre->collection->order_id,
                                '{payment_id}' => $mercadolivre->collection->id,
                                '{external_reference}' => $mercadolivre->collection->external_reference,
                                '{date}' => date('d/m/Y H:i:s'),
                                '{product_name}' => $produto_data->name
                        );


                        $texto_final_email = str_replace(
                            array_keys($array_text),
                            array_values($array_text),
                            $template_email
                        );
                        
                        $title_template_email = str_replace(
                            array_keys($array_text),
                            array_values($array_text),
                            $getSettings->title_template_email
                        );


                        $mail->sendMail($data_info_transaction, $title_template_email, $texto_final_email, APP_NAME, $setFile);
                        ############ SEND EMAIL ###################################

                    }


                    // SEND MESSAGE CHAT MERCADO LIVRE TO PAYER
                    if ($mercadolivre->collection->marketplace == "MELI") {
                        $template_message_ml = $getSettings->template_ml_message;
                        if ($template_message_ml !== "") {

                            $array_text = array(
                                '{username}' => $mercadolivre->order_ml->buyer->nickname,
                                '{first_name}' => $mercadolivre->order_ml->buyer->first_name,
                                '{last_name}' => $mercadolivre->order_ml->buyer->last_name,
                                '{link_download}' => $link_download,
                                '{order_id}' => $mercadolivre->order_ml->id,
                                '{payment_id}' => $mercadolivre->collection->id,
                                '{external_reference}' => $mercadolivre->collection->external_reference,
                                '{date}' => date('d/m/Y H:i:s'),
                                '{product_name}' => $produto_data->name
                            );


                            $text_message = str_replace(
                                array_keys($array_text),
                                array_values($array_text),
                                $template_message_ml
                            );

                            $mercadolivre->sendMessageToPayer($text_message);
                            $mercadolivre->confirmSendedProduct();


                        }
                    }

                    // SEND MESSAGE WHATSAPP
                    if ($mercadolivre->collection->marketplace != 'MELI' && ENABLE_API_WPP) {

                        $is_connected = $whatsapp->getStatus();

                        if ($is_connected) {

                            $phone = DDI . str_replace(['-', '(', ')', '+', ' '], '', $data_info_transaction->number);

                            $isPhoneValid = $whatsapp->isWhatsapp($phone);

                            if ($isPhoneValid) {

                                $phone = @explode('@',$isPhoneValid->Users[0]->JID)[0];

                                $template_message_ml = $getSettings->template_ml_message;
                                if ($template_message_ml !== "") {

                                    $array_text = array(
                                        '{username}' => '',
                                        '{first_name}' => $data_info_transaction->nome,
                                        '{last_name}' => '',
                                        '{link_download}' => $link_download,
                                        '{order_id}' => $mercadolivre->collection->order_id,
                                        '{payment_id}' => $mercadolivre->collection->id,
                                        '{external_reference}' => $mercadolivre->collection->external_reference,
                                        '{product_name}' => $produto_data->name
                                    );


                                    $text_message = str_replace(
                                        array_keys($array_text),
                                        array_values($array_text),
                                        $template_message_ml
                                    );

                                    $sendMessage = $whatsapp->sendMessage($text_message, $phone);

                                }

                            }



                        }

                    }
                }

            }

        }


    }


} catch (\Throwable $th) {
    file_put_contents("error_log.txt", $th->getMessage());
}