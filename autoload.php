<?php


try {

  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Conn.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Dashboard.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Pixel.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Clients.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Mercadopago.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Product.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Marketplace.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Settings.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Transaction.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'View.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Email.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Categorys.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Zip.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'Whatsapp.class.php';
  require_once PATH_BASE . DIRECTORY_SEPARATOR . 'class'. DIRECTORY_SEPARATOR . 'MercadoLivre.class.php';

} catch (\Throwable $th) {
   file_put_contents("error_log.txt", $th->getMessage());
}
