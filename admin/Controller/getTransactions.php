<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

 
 require_once '../../config.php';
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simple to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
 
// DB table to use
$table = 'transactions';
 
// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case object
// parameter names

// { "data": "id" },
// { "data": "data" },
// { "data": "valor" },
// { "data": "status" },
// { "data": "qtd_compras" },
// { "data": "comprador" },
// { "data": "opc" }



$columns = array(
    
   array( 'db' => 'id', 'dt' => 'id' , 'formatter' => function ($d, $row) {
       return $row['id'];
   }),
        
        
    array( 'db' => 'created', 'dt' => 'data', 'formatter' => function ($d, $row) {
       return date('d/m/Y ', strtotime($row['created']));
   }),
    
    
    array( 'db' => 'valor', 'dt' => 'valor' , 'formatter' => function ($d, $row) {
        return 'R$ '.$row['valor'];
    }),
        
    array( 'db' => 'status', 'dt' => 'status', 'formatter' => function ($d, $row) {
        if($row['status'] == 'pending'){
            return '<span style="padding: 3px;background-color: gray;color: #fff;border-radius: 4px;" >Pendente</span>';
        }else if($row['status'] == 'approved'){
            return '<span style="padding: 3px;background-color: #03ad7a;color: #fff;border-radius: 4px;" >Aprovado</span>';
        }else{
            return '<span style="padding: 3px;background-color: gray;color: #fff;border-radius: 4px;">Pendente</span>';
        }
    }),
    array( 'db' => 'info_data', 'dt' => 'qtd_compras', 'formatter' => function ($d, $row){
        
         $obj =  json_decode($row['info_data']);
        
         require_once '../../class/Conn.class.php';
         require_once '../../class/Transaction.class.php';
         
         $transactions = new Transaction();
         
         $count_transaction = $transactions->getAllTransactionsByClient($obj->client_id);
        
         return $count_transaction ? $count_transaction : 0; 
        
    }),
    array( 'db' => 'info_data', 'dt' => 'comprador' , 'formatter' => function ($d, $row) {
        
         $obj =  json_decode($row['info_data']);
        
         require_once '../../class/Conn.class.php';
         require_once '../../class/Clients.class.php';
         
         $client = new Clients();
         
         $client = $client->getClientById($obj->client_id);
        
         if($client){
             
             return $client->nome;
             
         }else{
             return "*****";
         }
          
          
    }),
    array( 'db' => 'id', 'dt' => 'opc', 'formatter' => function ($d, $row) {
        
            return '<button class="btn btn-info btn-sm" onclick="getTransactionByDetail(\''.$row['id'].'\');" > <i class="fa fa-eye" ></i> Ver</button>';
        
        
    }),

    
    );
 
// SQL server connection information
$sql_details = array(
    'user' => DB_USER,
    'pass' => DB_PASS,
    'db'   => DB_NAME,
    'host' => DB_HOST
);
 
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
require( '../../lib/datatable/ssp.class.php' );


     $query = null;
     $whereAll = " id!= '0'";


    $return = SSP::complex( $_POST, $sql_details, $table, $primaryKey, $columns, $whereResult= null, $whereAll, $query);

    echo json_encode($return);




