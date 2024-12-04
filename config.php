<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$base_project = dirname(__FILE__);

define("PATH_BASE", $base_project);

define("DB_HOST", "databases_mysql-db");
define("DB_USER", "mysql");
define("DB_PASS", "b020d42f7b130a361d958d6a3");
define("DB_NAME", "melidigital");

// https://www.tiny.cloud/powered-by-tiny
define("TINY_TOKEN", "z5np1g6ggjva2s24hfup4y998ivs8eswehuz7vyqwac0mmdd");

define("MESSAGE_FEEDBACK_PAYER", "Obrigado por comprar conosco. Espetamos te ver novamente");

define("LOCAL", false); // false for production
define("NGROK_LINK", "https://812b-45-2371-140-202.ngrok-free.app/workspace/jobs/mercadolivre");

/*API WHATSAPP DATA*/
define("ENABLE_API_WPP", false);
define("WPP_ENDPOINT", "http://sua-api.com");
define("WPP_TOKEN_ADMIN", "TOKEN_ADMIN");

/*DDI CODE COUNTRY*/
define("DDI", "55");

/*MINUTES SEND MESSAGE RECOVER*/
define("MINUTE_RECOVER_CAR", "5-10"); // between 5 and 10 minutes

define("APP_NAME", "Meli Produtos Digitais"); // Title website
define("APP_URL", "https://meli.keira.top"); // URL website

define("GATEWAY_PAYMENT", "mercadopago"); // Set gateway payment used
define("METHODS_GATEWAY", ['pix', 'link']);

/*Info user agent for pixel*/
define("ADDRESS_IP", ' ');
define("USER_AGENT", ' ');
define("ORIGEM", 'Desconhecida');


/*PIXEL FACEBOOK*/
define("ENABLE_PIXEL", false);

// urls return for gateway
define("URL_SUCCESS", APP_URL . "/payment");
define("URL_PENDING", APP_URL . "/payment");
define("URL_CANCEL", APP_URL . "/payment");

define("SMTP_ENABLE", true);
define("SMTP_HOST", "");
define("SMTP_PORT", 0);
define("SMTP_USER", "");
define("SMTP_PASS", "");
define("SMTP_MAIL", "");
define("SMTP_SECURE", "TLS");
