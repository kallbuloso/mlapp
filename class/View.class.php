<?php

 /**
  * View
  */
 class View{

   public $page;

   public $headerElements = array();

   public $app_url;

   public $address_ip;

   public $user_agent;

   public $conn;
   public $pdo;
   public function __construct(){
     $this->getIp();
     $this->getUserAgent();
   }

   public function view($params = array()){

    $params['app_url']    = $this->app_url;

    $array_values = array_values($params);
    function addBrace($value) {
         return '{{' . $value . '}}';
     }
     $array_keys = array_map('addBrace', array_keys($params));
     $content1   = self::getContent();
     $content    = str_replace($array_keys, $array_values, $content1);
     echo self::getHeader().$content.self::getFooter();

   }

   private function getUserAgent(){
     $this->address_ip = $_SERVER['REMOTE_ADDR'];
   }

   private function getIp(){
     $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
   }

   public function getHeader(){

     if(is_file('pages/views/elements/header.html')){

       $content1          = file_get_contents('pages/views/elements/header.html');
       $params            = $this->headerElements;

       $params['APP_URL']    = $this->app_url;
       $params['address_ip'] = $this->address_ip;
       $params['user_agent'] = $this->user_agent;

       $array_values = array_values($params);
       function addBraceHeader($value) {
            return '{{' . $value . '}}';
        }
        $array_keys = array_map('addBraceHeader', array_keys($params));
        $content    = str_replace($array_keys, $array_values, $content1);

        return $content;

     }else{
       return '';
     }

   }


      public function getFooter(){

        if(is_file('pages/views/elements/footer.html')){

          $content1          = file_get_contents('pages/views/elements/footer.html');
          $params            = $this->headerElements;

          $params['APP_URL'] = $this->app_url;

          $array_values = array_values($params);
          function addBraceFooter($value) {
               return '{{' . $value . '}}';
           }
           $array_keys = array_map('addBraceFooter', array_keys($params));
           $content    = str_replace($array_keys, $array_values, $content1);

           return $content;

        }else{
          return '';
        }

      }

   public function notfound(){
     $this->page = "404";
     $this->headerElements = [
       'title'    => 'Não localizado'
     ];
     $this->view();
   }

   public function getContent(){
     if(is_file('pages/views/'. $this->page . '.html')){
       $content = file_get_contents('pages/views/'. $this->page . '.html');
       return $content;
     }else{
       return false;
     }
   }

 }
