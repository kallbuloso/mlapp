<?php


  /**
   *
   */
  class Product extends Conn
  {

    
    public $conn;
    public $pdo;
    public $product_id;
    
    function __construct($product_id=false){

      $this->conn       = new Conn();
      $this->pdo        = $this->conn->pdo();
      $this->product_id = $product_id;

    }

    public function init(){

      $lastPagesViews7Days   = self::lastPagesViews7Days();
      $lastPagesViewsGrapich = self::lastPagesViewsGrapich($lastPagesViews7Days);


      return json_encode([
        'lastPagesViews7Days'   => $lastPagesViews7Days,
        'lastPagesViewsGrapich' => $lastPagesViewsGrapich
      ]);



    }

    public function getItemsProduct(){
      $query_consult = $this->pdo->query("SELECT * FROM items WHERE id_product='{$this->product_id}' ORDER BY id DESC");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }
    }

    public function getItemsProductByid($id){
      $query_consult = $this->pdo->query("SELECT * FROM items WHERE id_product='{$id}' ORDER BY id DESC");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }
    }


    public function getItemsProductApproved($product_id, $qtd=1){
      $query_consult = $this->pdo->query("SELECT * FROM items WHERE id_product='{$product_id}' ORDER BY id DESC LIMIT {$qtd}");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }
    }

    public function countItemsProduct($product_id){
      $query_consult = $this->pdo->query("SELECT * FROM items WHERE id_product='{$product_id}' ORDER BY id DESC");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return count($fetch_consult);
      }else{
        return 0;
      }
    }

    public function getLastItem($pId){
      $query_consult = $this->pdo->query("SELECT * FROM items WHERE id_product='{$pId}' ORDER BY id DESC LIMIT 1");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult[0];
      }else{
        return false;
      }
    }


    public function getProductsByCate($cate){
      $query_consult = $this->pdo->query("SELECT * FROM product WHERE categoria_id='{$cate}' AND status='1' ORDER BY id DESC");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }
    }

    public function getCategorysProduct(){
      $query_consult = $this->pdo->query("SELECT * FROM categorias ORDER BY id DESC");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }
    }

    public function getCategoryById($id){

      $query_consult = $this->pdo->query("SELECT * FROM `categorias` WHERE id='{$id}'");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult[0];
      }else{
        return false;
      }

    }

    public function getItemById($id){

      $query_consult = $this->pdo->query("SELECT * FROM `items` WHERE id='{$id}'");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult[0];
      }else{
        return false;
      }

    }


    public function delete($id){

      $stmt = $this->pdo->prepare("DELETE FROM product WHERE id = :id");
      $stmt->bindParam(':id', $id);
      if($stmt->execute()){
        return true;
      }else{
        return false;
      }

    }

    public function deleteItem($id){

      $stmt = $this->pdo->prepare("DELETE FROM items WHERE id = :id");
      $stmt->bindParam(':id', $id);
      if($stmt->execute()){
        return true;
      }else{
        return false;
      }

    }

    public function lastPagesViews7Days(){

      $query_consult = $this->pdo->query("SELECT qtd, data FROM page_views WHERE data >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) ORDER BY data DESC");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }

    }


    public function lastPagesViewsGrapich($page_views){

      if(!$page_views || $page_views == NULL){
        return '';
      }

      setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

      $page_views_daily = array();
       for ($i = 0; $i < 7; $i++) {
           $date = date('Y-m-d', strtotime("- $i days"));
           $page_views_daily[$date] = 0;
       }
       foreach ($page_views as $page_view) {
           $date = $page_view->data;
           @$page_views_daily[$date] += $page_view->qtd;
       }

       $array_return = array();
       $i = 0;
       foreach(array_reverse($page_views_daily) as $key => $vl){
         $array_return[$key] = $vl;
         $i++;
         if($i == 7){
           break;
         }
       }

       $diasemana = array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb');

       $days_name = array();

       foreach ($array_return as $data => $value) {
         $days_name[$data] = $diasemana[date('w', strtotime($data))];
       }

       $daysNameGrapich = array_reverse(array_values($days_name));
       $daysQtdGrapich  = array_reverse(array_values($array_return));

       return array(
         'daysNameGrapich' => $daysNameGrapich,
         'daysQtdGrapich'  => $daysQtdGrapich
       );

    }

    public function list($search = false, $order = false){
        
      $order = $order ? $order : 'ORDER BY id DESC';
      $search = $search ? $search : '';

      $query_consult = $this->pdo->query("SELECT * FROM `product` {$search} {$order}");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult;
      }else{
        return false;
      }

    }

    public function addProduct($data){

      $uniq_link = $data->typeDown == "link" ? $data->link_download : "not";
      $id_mercadolivre = isset($data->id_mercadolivre) ? $data->id_mercadolivre : NULL;
      $permalink_ml = isset($data->id_mercadolivre) ? $data->permalink_ml : NULL;

      $query = $this->pdo->prepare("INSERT INTO `product` (name, description, image, price, status, uniq_link, recycle_file, id_mercadolivre, permalink_ml, account_mkt, identifier) VALUES (:name, :description, :image, :price, :status, :uniq_link, :recycle_file, :id_mercadolivre, :permalink_ml, :account_mkt, :identifier) ");
      $query->bindValue(':name', $data->name);
      $query->bindValue(':description', $data->description);
      $query->bindValue(':image', $data->image);
      $query->bindValue(':price', $data->price);
      $query->bindValue(':status', $data->status);
      $query->bindValue(':uniq_link', $uniq_link);
      $query->bindValue(':recycle_file', $data->recycle_file);
      $query->bindValue(':id_mercadolivre', $id_mercadolivre);
      $query->bindValue(':permalink_ml', $permalink_ml);
      $query->bindValue(':account_mkt', $data->account_mkt);
      $query->bindValue(':identifier', $data->identifier);

      if($query->execute()){
          return $this->pdo->lastInsertId();
      }else{
          return false;
      }

    }

    public function addItem($item){
      $query = $this->pdo->prepare("INSERT INTO `items` (id_product, item) VALUES (:id_product, :item) ");
      $query->bindValue(':id_product', $this->product_id);
      $query->bindValue(':item', $item);

      if($query->execute()){
          return true;
      }else{
          return false;
      }
    }


    public function updateProduct($data){

      $uniq_link = $data->typeDown == "link" ? $data->link_download : "not";
 
      $query = $this->pdo->prepare("UPDATE `product` SET name= :name, description= :description, image= :image, price= :price, status= :status, uniq_link= :uniq_link, recycle_file= :recycle_file, account_mkt= :account_mkt, identifier= :identifier WHERE id=:id");
      $query->bindValue(':name', $data->name);
      $query->bindValue(':description', $data->description);
      $query->bindValue(':image', $data->image);
      $query->bindValue(':price', $data->price);
      $query->bindValue(':status', $data->status);
      $query->bindValue(':uniq_link', $uniq_link);
      $query->bindValue(':recycle_file', $data->recycle_file);
      $query->bindValue(':account_mkt', $data->account_mkt);
      $query->bindValue(':identifier', $data->identifier);

      $query->bindValue(':id', $data->id);

      if($query->execute()){
        return true;
      }else{
        return false;
      }
    }

    public function getProductById($id){

      $query_consult = $this->pdo->query("SELECT * FROM `product` WHERE id='{$id}'");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult[0];
      }else{
        return false;
      }

    }
    
    public function getProductByRef($ref){

      $query_consult = $this->pdo->query("SELECT * FROM `product` WHERE identifier='{$ref}'");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult[0];
      }else{
        return false;
      }

    }
    
    public function getProductByIdML($id){

      $query_consult = $this->pdo->query("SELECT * FROM `product` WHERE id_mercadolivre='{$id}'");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if(count($fetch_consult)>0){
        return $fetch_consult[0];
      }else{
        return false;
      }

    }


  }
