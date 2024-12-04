<?php

@session_start();
if (!isset($_SESSION['ADM_LOGGED'])) {
    echo json_encode([
        'erro' => true,
        'message' => 'Faça login'
    ]);
    exit;
}


if (isset($_POST)) {


    require_once '../../config.php';
    require_once '../../class/Conn.class.php';
    require_once '../../class/Whatsapp.class.php';

    $whatsapp = new Whatsapp;

    if (isset($_POST['type'])) {

        $type = trim($_POST['type']);

        if ($type == "connect") {
            try {

                $qrcode = $whatsapp->getQrcode();

                if ($qrcode->error) {
                    echo json_encode([
                        'erro' => true,
                        'message' => $qrcode->message_error
                    ]);
                } else {

                    if ($qrcode->is_connected) {
                        echo json_encode([
                            'erro' => false,
                            'is_connected' => true
                        ]);
                    } else {
                        echo json_encode([
                            'erro' => false,
                            'base64' => $qrcode->base64,
                            'is_connected' => false
                        ]);

                    }
                }

            } catch (\Exception $e) {

                echo json_encode([
                    'erro' => true,
                    'message' => 'Tente novamente mais tarde'
                ]);

            }
        } else if ($type == "status") {

            $status = $whatsapp->getStatus();

            if ($status) {
                echo json_encode([
                    'erro' => false,
                    'is_connected' => true
                ]);
            } else {
                echo json_encode([
                    'erro' => false,
                    'is_connected' => false
                ]);
            }

            die;

        } else if ($type == "disconnect") {
            $disconnect = $whatsapp->disconnect();
            if ($disconnect) {
                echo json_encode([
                    'erro' => false,
                    'disconnected' => true
                ]);
            } else {
                echo json_encode([
                    'erro' => true,
                    'disconnected' => false
                ]);
            }

            die;
        }

    }

} else {
    echo json_encode([
        'erro' => true,
        'message' => 'Request is required'
    ]);
}
