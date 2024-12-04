<?php

/**
 * Whatsapp
 */
class Whatsapp extends Conn
{
    public $conn;
    public $pdo;
    public $endpoint;
    public $token_admin = WPP_TOKEN_ADMIN;
    public $instance;
    public $error;
    public $message_error;
    public $base64 = null;
    public $is_connected;

    function __construct()
    {

        $this->conn = new Conn();
        $this->pdo = $this->conn->pdo();
        $this->endpoint = rtrim(WPP_ENDPOINT, '/');
        $this->instance = $this->isCreateInstance();

    }

    public function getInstance()
    {

        $query_consult = $this->pdo->query("SELECT * FROM `instance_wpp` ORDER BY id DESC");
        $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
        if (count($fetch_consult) > 0) {
            return $fetch_consult[0];
        } else {
            return false;
        }

    }

    public function isCreateInstance()
    {

        $instance = $this->getInstance();
        $create = false;
        $update = false;

        if (!$instance) {
            $create = true;
        } else {
            if ($instance->instance == "deleted") {
                $create = true;
                $update = true;
            }
        }

        if ($create) {
            $instance_id = uniqid() . '_WPP';
            if ($update) {
                $q = $this->pdo->query("UPDATE `instance_wpp` SET instance= '{$instance_id}'");
            } else {
                $q = $this->pdo->query("INSERT INTO `instance_wpp` (instance) VALUES ('{$instance_id}')");
            }

            return $q ? $instance_id : false;
        } else {
            return $instance->instance;
        }

    }


    public function isCreated()
    {
        $instance = $this->instance;

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->endpoint . '/devices/list',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Token: ' . trim($this->token_admin)
                ),
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);

        try {
            $data = json_decode($response, true);
            if (isset($data['code']) && $data['code'] == 200) {
                $column2 = array_column($data['data']['Data'], 2);

                if (in_array($instance, $column2)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

    }

    public function startWhatsapp()
    {

        $instance = $this->instance;

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->endpoint . '/session/connect',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{"Subscribe":["Message"],"Immediate":false}',
                CURLOPT_HTTPHEADER => array(
                    'Token: ' . trim($instance),
                    'Content-Type: application/json'
                ),
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);

        try {

            $json = json_decode($response);

            if (isset($json->success)) {

                return true;

            } else {
                return false;
            }


        } catch (\Exception $e) {
            return false;
        }


    }

    public function createInstance()
    {

        $instance = $this->instance;

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->endpoint . '/devices/create?name=' . uniqid() . '&token=' . trim($instance),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'Token: ' . trim($this->token_admin),
                    'Content-Type: application/json'
                ),
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);

        try {

            $json = json_decode($response);

            if (isset($json->success)) {
                if ($json->success == true) {

                    if (isset($json->data->name)) {
                        if ($json->data->name != "") {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        return false;
                    }

                } else {
                    return false;
                }
            } else {
                return false;
            }


        } catch (\Exception $e) {
            return false;
        }

    }

    public function getStatus()
    {

        $instance = $this->instance;

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->endpoint . '/session/status',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Token: ' . trim($instance)
                ),
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);

        try {

            $json = json_decode($response);

            if (isset($json->success)) {
                if ($json->success == true) {

                    if ($json->data->Connected == false && $json->data->LoggedIn == false) {
                        return false;
                    } else if ($json->data->Connected == true && $json->data->LoggedIn == false) {
                        return false;
                    } else if ($json->data->Connected == false && $json->data->LoggedIn == true) {
                        return false;
                    } else if ($json->data->Connected == true && $json->data->LoggedIn == true) {
                        return true;
                    } else {
                        return false;
                    }

                } else {
                    return false;
                }
            } else {
                return false;
            }


        } catch (\Exception $e) {
            return false;
        }

    }

    public function sendMessage($message, $phone)
    {

        $this->startWhatsapp();
    
        $instance = $this->instance;

        $data = array(
            "Phone" => $phone,
            "Body" => $message
        );

         $postdata = json_encode($data);


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->endpoint . '/chat/send/text',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 1,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postdata,
            CURLOPT_HTTPHEADER => array(
                'Token: ' . trim($instance),
                'Content-Type: application/json'
            ),
        )
        );

        $response = curl_exec($curl);
        curl_close($curl);

      

        try {

            $json = json_decode($response);

            if (isset($json->success)) {
                if ($json->success) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }

        } catch (\Exception $e) {
            return false;
        }
    }

    public function isWhatsapp($num)
    {

        $instance = $this->instance;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://whatsapp.gestorlite.com/user/check',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"Phone":["{' . $num . '}"]}',
            CURLOPT_HTTPHEADER => array(
                'Token: ' . trim($instance),
                'Content-Type: application/json'
            ),
        )
        );

        $response = curl_exec($curl);
        curl_close($curl);


        try {

            $json = json_decode($response);

            if (isset($json->success)) {
                if ($json->success == true) {
                    return $json->data;
                } else {
                    return false;
                }
            } else {
                return false;
            }


        } catch (\Exception $e) {
            return false;
        }

    }

    public function disconnect()
    {

        $instance = $this->instance;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->endpoint . '/session/logout',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Token: ' . trim($instance)
            ),
        )
        );

        $response = curl_exec($curl);
        curl_close($curl);

        try {

            $json = json_decode($response);

            if (isset($json->success)) {
                if ($json->success == true) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }


        } catch (\Exception $e) {
            return false;
        }

    }

    public function getQrcode()
    {

        $status = $this->getStatus();

        if ($status) {
            $this->error = false;
            $this->is_connected = true;
            return $this;
        } else {

            $isCreated = $this->isCreated();
            $createIsApi = true;

            if (!$isCreated) {
                $createIsApi = $this->createInstance();
                sleep(4);
            }

            if (!$createIsApi) {
                $this->error = true;
                $this->message_error = 'Error Api Connected';
                return $this;
            }
        }

        $this->startWhatsapp();
        sleep(2);

        $instance = $this->instance;

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => $this->endpoint . '/session/qr',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Token: ' . trim($instance)
                ),
            )
        );

        $response = curl_exec($curl);
        curl_close($curl);

        try {

            $json = json_decode($response);

            if (isset($json->success)) {


                if ($json->success == false) {

                    if (isset($json->error)) {

                        if ($json->error == "Already Loggedin") {

                            $this->error = false;
                            $this->is_connected = true;
                        } else {
                            $this->error = true;
                            $this->message_error = $json->error;
                        }
                        return $this;

                    } else {
                        $this->error = true;
                        $this->message_error = $response;
                        return $this;
                    }

                } else if ($json->success == true) {

                    if (isset($json->data->QRCode)) {
                        $this->error = false;
                        $this->base64 = $json->data->QRCode;

                    } else {
                        $this->error = true;
                        $this->message_error = 'Qrcode not found';
                    }

                    return $this;

                }

                $this->error = true;
                $this->message_error = 'Error Api Connected';
                return $this;

            }

            $this->error = true;
            $this->message_error = 'Error Api Connected';
            return $this;


        } catch (\Exception $e) {
            $this->error = false;
            $this->message_error = $e;
            return $this;
        }

    }


}
