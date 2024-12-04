<?php


  /**
   *
   */
  class Dashboard extends Conn
  {

    public $conn;
    public $pdo;

    function __construct(){

      $this->conn = new Conn();
      $this->pdo  = $this->conn->pdo();

    }

    public function init(){

        $pageViewsToday     = self::pageViewsToday() ? self::pageViewsToday()[0] : (object)array('qtd' => 0);
        $pageViewsYesterday = self::pageViewsYesterday() ? self::pageViewsYesterday()[0] : (object)array('qtd' => 0);



        $diff_pviews   = ($pageViewsToday->qtd - $pageViewsYesterday->qtd);

        if($diff_pviews > 0){
            $diff = '<p class="mb-0"><span class="text-success text-sm font-weight-bolder">+'.$diff_pviews.' </span> do que ontem</p>';
        }else if($diff_pviews < 0) {
            $diff = '<p class="mb-0"><span class="text-danger text-sm font-weight-bolder">'.$diff_pviews.' </span> do que ontem</p>';
        }else {
            $diff = "&nbsp;";
        }

        return json_encode([
          'pageViewsToday'         => $pageViewsToday->qtd,
          'pageViewsYesterday'     => $pageViewsYesterday->qtd,
          'diff_pviews'            => $diff
        ]);

    }

    public function pageViewsToday(){

      $query_consult = $this->pdo->query("SELECT * FROM page_views WHERE DATE(data) = CURDATE();");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }

    }

    public function setPageViewToday(){
      $query_consult = $this->pdo->query("UPDATE `page_views` SET qtd = qtd + 1 WHERE DATE(data) = CURDATE();");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }
    }

    public function insertPageViewToday(){
      $query_consult = $this->pdo->query("INSERT INTO `page_views` (data, qtd) VALUES (now(), 1)");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }
    }


    public function pageViewsYesterday(){

      $query_consult = $this->pdo->query("SELECT * FROM page_views WHERE DATE(data) = DATE_SUB(CURDATE(), INTERVAL 1 DAY);");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }

    }

  }
