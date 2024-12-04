<?php


/**
 *
 */
class Transaction extends Conn
{

  public $conn;
  public $pdo;

  function __construct()
  {

    $this->conn = new Conn();
    $this->pdo = $this->conn->pdo();

  }

  public function init()
  {

    $getTransactions = self::getTransactions();
    $getTransactionsYesterday = self::getTransactionsYesterday();

    $diff_sales = (count($getTransactions) - count($getTransactionsYesterday));

    if ($diff_sales > 0) {
      $diff = '<p class="mb-0"><span class="text-success text-sm font-weight-bolder">+' . $diff_sales . ' </span> do que ontem</p>';
    } else if ($diff_sales < 0) {
      $diff = '<p class="mb-0"><span class="text-danger text-sm font-weight-bolder">-' . $diff_sales . ' </span> do que ontem</p>';
    } else {
      $diff = "&nbsp;";
    }


    $getTransactionsLastMonth = self::getTransactionsLastMonth();
    $getTransactionsMonthCurrent = self::getTransactionsMonthCurrent();
    $getTransactions12Months = self::getTransactions12Months();
    $lastMonths12Name = self::lastMonths12Name();
    $listLast10Dashboard = self::listLast10Dashboard();


    $diffTransactions = self::diffTransactions($getTransactionsMonthCurrent, $getTransactionsLastMonth);
    $formatGraphic12MonthsTransactions = self::formatGraphic12MonthsTransactions($getTransactions12Months);
    $diffTransactionMonth = self::diffTransactionMonth();


    return json_encode([
      'getTransactions' => $getTransactions,
      'getTransactionsYesterday' => $getTransactionsYesterday,
      'diff_sales' => $diff,
      'qtd_transactions' => count($getTransactions),
      'getTransactionsLastMonth' => self::calcTransactions($getTransactionsLastMonth),
      'getTransactionsMonthCurrent' => self::calcTransactions($getTransactionsMonthCurrent),
      'diffTransactions' => $diffTransactions,
      'formatGraphic12MonthsTransactions' => $formatGraphic12MonthsTransactions,
      'lastMonths12Name' => $lastMonths12Name,
      'diffTransactionMonth' => $diffTransactionMonth,
      'listLast10Dashboard' => $listLast10Dashboard
    ]);

  }

  public function sum_transactions_by_client_id()
  {
    $now = date('Y-m-d H:i:s');
    $recurrent_total = 0;
    $new_total = 0;
    $client_ids = array(); // array para guardar os IDs de clientes já encontrados

    // Conexão com o banco de dados
    $conn = $this->pdo;
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta ao banco de dados
    $sql = "SELECT * FROM transactions WHERE created >= DATE_SUB('$now', INTERVAL 24 HOUR) AND status='approved' ";
    $stmt = $conn->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $rowCount = count($rows);

    if ($rowCount > 0) {
      foreach ($rows as $row) {
        $info_data = json_decode($row['info_data'], true); // decodifica o JSON em um array associativo
        $client_id = isset($info_data['client_id']) ? $info_data['client_id'] : $info_data['buyer']['id'];
        $value = str_replace(',', '.', $row['valor']); // converte o valor para um formato numérico válido
        if (!in_array($client_id, $client_ids)) { // se o cliente ainda não foi encontrado
          $client_ids[] = $client_id;
          $recurrent_total += $value;
        } else {
          $new_total += $value;
        }
      }
    }

    // Fecha a conexão com o banco de dados
    $conn = null;

    $response = array(
      'recorrentes' => number_format($recurrent_total, 2, ',', '.'),
      'novas' => number_format($new_total, 2, ',', '.')
    );
    return json_encode($response); // retorna o resultado em formato JSON
  }




  public function getAllTransactionsByClient($client_id)
  {

    $query = $this->pdo->query("SELECT COUNT(*) as num FROM transactions WHERE JSON_UNQUOTE(JSON_EXTRACT(info_data, '$.client_id')) = {$client_id} AND status='approved'");
    $rows = $query->fetch(PDO::FETCH_OBJ);

    return $rows->num;
  }

  public function getAllTransactionsByClientList($client_id)
  {

    $query = $this->pdo->query("SELECT * FROM transactions WHERE JSON_UNQUOTE(JSON_EXTRACT(info_data, '$.client_id')) = {$client_id} ORDER BY id DESC ");
    $rows = $query->fetchAll(PDO::FETCH_OBJ);

    if (count($rows) > 0) {
      return $rows;
    } else {
      return false;
    }
  }


  public function listLast10Dashboard()
  {

    $query_consult = $this->pdo->query("SELECT * FROM `transactions` WHERE status='approved' ORDER BY id DESC LIMIT 10");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult;
    } else {
      return false;
    }

  }

  public function getTransactions12Months()
  {
    $query_consult = $this->pdo->query("SELECT SUM(REPLACE(valor, ',', '.')) as total, MONTH(created) as mes, YEAR(created) as ano
                                          FROM transactions
                                          WHERE status='approved' AND created >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                                          GROUP BY MONTH(created), YEAR(created)
                                          ORDER BY created DESC;
                                          ");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult;
    } else {
      return array();
    }
  }


  public function formatGraphic12MonthsTransactions($transactions)
  {

    $current_month = (int) date('n');
    $transactions_monthly = array();
    for ($i = 0; $i < 12; $i++) {
      $month = ($current_month - $i + 12) % 12;
      if ($month == 0)
        $month = 12;
      $transactions_monthly[$month] = 0;
    }

    foreach ($transactions as $transaction) {
      $month = (int) $transaction->mes;
      @$transactions_monthly[$month] += $transaction->total;
    }

    return array_values(array_reverse($transactions_monthly));

  }


  public function getAllTransactionsDayWhere()
  {
    $hoje = date('Y-m-d');
    $stmt = $this->pdo->prepare("SELECT SUM(REPLACE(valor, ',', '.')) as total,
                                        COUNT(DISTINCT CASE WHEN info->>'$.client_id' IS NULL THEN id END) as semClientID,
                                        COUNT(DISTINCT CASE WHEN info->>'$.client_id' IS NOT NULL THEN info->>'$.client_id' END) as comClientID
                                 FROM transactions
                                 WHERE DATE(created) = :hoje");
    $stmt->bindParam(':hoje', $hoje);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }


  public function diffTransactionMonth()
  {

    $query_consult = $this->pdo->query("SELECT
                                              CONCAT(
                                                  CASE
                                                      WHEN (SUM(CASE WHEN MONTH(created) = MONTH(CURDATE()) THEN REPLACE(valor, ',', '.') ELSE 0 END) -
                                                      SUM(CASE WHEN MONTH(created) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN REPLACE(valor, ',', '.') ELSE 0 END)) > 0 THEN '+'
                                                      ELSE '-'
                                                  END,
                                              (SUM(CASE WHEN MONTH(created) = MONTH(CURDATE()) THEN REPLACE(valor, ',', '.') ELSE 0 END) -
                                              SUM(CASE WHEN MONTH(created) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN REPLACE(valor, ',', '.') ELSE 0 END))
                                              / SUM(CASE WHEN MONTH(created) = MONTH(CURDATE() - INTERVAL 1 MONTH) THEN REPLACE(valor, ',', '.') ELSE 0 END) * 100)
                                              AS percentage_diff
                                          FROM transactions
                                          WHERE status = 'approved'
                                          ");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {

      $percentage_diff = $fetch_consult[0]->percentage_diff;
      if ($percentage_diff !== NULL) {
        if (substr($percentage_diff, 0, 1) == "+") {
          return '<p class="text-sm "> (<span class="font-weight-bolder text-success ">' . substr($percentage_diff, 0, 1) . number_format(floatval(str_replace('+', '', $percentage_diff)), 2) . '%</span>) aumento nas vendas desse mês. </p>';

        } else {
          return '<p class="text-sm "> (<span class="font-weight-bolder text-danger">' . substr($percentage_diff, 0, 1) . number_format(floatval(str_replace('-', '', $percentage_diff)), 2) . '%</span>) baixa nas vendas desse mês. </p>';

        }
      } else {
        return '';
      }
    } else {
      return '';
    }

  }

  public function lastMonths12Name()
  {

    $mesesPt = array(
      'Jan' => 'Janeiro',
      'Feb' => 'Fevereiro',
      'Mar' => 'Marco',
      'Apr' => 'Abril',
      'May' => 'Maio',
      'Jun' => 'Junho',
      'Jul' => 'Julho',
      'Aug' => 'Agosto',
      'Nov' => 'Novembro',
      'Sep' => 'Setembro',
      'Oct' => 'Outubro',
      'Dec' => 'Dezembro'
    );

    $current_month = (int) date('n');
    $months_list = array();
    $current_month_name = ucfirst((new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('F'));
    $months_list[] = $current_month_name;

    for ($i = 1; $i <= 11; $i++) {
      $month = ($current_month - $i + 12) % 12;
      if ($month == 0)
        $month = 12;

      // Ajuste para adicionar o ano e utilizar o formato 'Y-m-d'
      $date = new DateTime(date('Y') . '-' . sprintf('%02d', $month) . '-01', new DateTimeZone('America/Sao_Paulo'));
      $months_list[] = ucfirst($mesesPt[$date->format('M')]);
    }

    return array_values(array_reverse($months_list));

  }


  public function diffTransactions($t1, $t2)
  {

    $t1 = json_decode(self::calcTransactions($t1))->v1;
    $t2 = json_decode(self::calcTransactions($t2))->v1;

    $diff_transactions = ($t1 - $t2);

    if ($diff_transactions > 0) {
      $diff = '<p class="mb-0"><span class="text-success text-sm font-weight-bolder"> R$ +' . self::convertMoney(2, $diff_transactions) . ' </span> do que último mes</p>';
    } else if ($diff_transactions < 0) {
      $diff = '<p class="mb-0"><span class="text-danger text-sm font-weight-bolder"> R$ ' . self::convertMoney(2, $diff_transactions) . ' </span> do que último mes</p>';
    } else {
      $diff = "&nbsp;";
    }

    return $diff;

  }
  
  
  public function getTransactionsByClient($id, $status="approved"){
      
    $query_consult = $this->pdo->query("SELECT t.*, DATE_FORMAT(t.created, '%d/%m/%y %H:%i') as data_buy, p.name as product_name, p.uniq_link as uniq_link, p.id as product_id FROM `transactions` as t LEFT JOIN `product` as p ON t.product_id = p.id WHERE JSON_UNQUOTE(JSON_EXTRACT(t.info_data, '$.client_id')) = {$id} AND t.status = '{$status}'");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult;
    } else {
      return false;
    }

  }

  public function getTransactions()
  {

    $query_consult = $this->pdo->query("SELECT * FROM transactions WHERE status='approved' AND DATE(created) = CURDATE()");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult;
    } else {
      return array();
    }

  }

  public function getRecoverCar()
  {

    $configMinutes = explode('-', MINUTE_RECOVER_CAR);
    $min_a = (int) $configMinutes[0];
    $min_b = (int) $configMinutes[1];

    $query_consult = $this->pdo->query("SELECT * FROM transactions WHERE status = 'pending' AND sended_recover = '0' AND created BETWEEN DATE_SUB(NOW(), INTERVAL {$min_b} MINUTE) AND DATE_SUB(NOW(), INTERVAL {$min_a} MINUTE);");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      $this->setSendedMessage($fetch_consult[0]->id);
      return $fetch_consult[0];
    } else {
      return false;
    }

  }

  public function setSendedMessage($id)
  {
    $query_consult = $this->pdo->query("UPDATE transactions SET sended_recover='1' WHERE id='{$id}' ");
    if ($query_consult) {
      return true;
    } else {
      return false;
    }
  }
 
  public function setStatus($status, $id)
  {
    $query_consult = $this->pdo->query("UPDATE transactions SET status='{$status}' WHERE id='{$id}' ");
    if ($query_consult) {
      return true;
    } else {
      return false;
    }
  }

  public function setReturnGateway($data, $reference)
  {
    $query_consult = $this->pdo->query("UPDATE transactions SET return_gateway='{$data}' WHERE reference='{$reference}' ");
    if ($query_consult) {
      return true;
    } else {
      return false;
    }
  }

  public function setToken($token, $id)
  {
    $query_consult = $this->pdo->query("UPDATE transactions SET token='{$token}' WHERE id='{$id}' ");
    if ($query_consult) {
      return true;
    } else {
      return false;
    }
  }


  public function setStatusDownload($status, $id)
  {
    $query_consult = $this->pdo->query("UPDATE transactions SET download_active='{$status}' WHERE id='{$id}' ");
    if ($query_consult) {
      return true;
    } else {
      return false;
    }
  }
  
  public function setErrorMessageML($id, $error_send_message)
  {
    $query_consult = $this->pdo->query("UPDATE transactions SET error_send_message='{$error_send_message}' WHERE id='{$id}' ");
    if ($query_consult) {
      return true;
    } else {
      return false;
    }

  }
  
  public function sumDown($id)
  {
    $query_consult = $this->pdo->query("UPDATE transactions SET count_down= 1+count_down WHERE id='{$id}' ");
    if ($query_consult) {
      return true;
    } else {
      return false;
    }
  }
  
  public function setTag($tag, $id)
  {
    $query_consult = $this->pdo->query("UPDATE transactions SET tag='{$tag}' WHERE id='{$id}' ");
    if ($query_consult) {
      return true;
    } else {
      return false;
    }
  }

  public function getTransactionsYesterday()
  {

    $query_consult = $this->pdo->query("SELECT * FROM transactions WHERE status='approved' AND DATE(created) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult;
    } else {
      return array();
    }

  }

  public function getTransactionById($id)
  {

    $query_consult = $this->pdo->query("SELECT * FROM `transactions` WHERE id='{$id}'");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult[0];
    } else {
      return false;
    }

  }


  public function getTransactionByRef($reference)
  {

    $query_consult = $this->pdo->query("SELECT * FROM `transactions` WHERE reference='{$reference}'");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult[0];
    } else {
      return false;
    }

  }

  public function getTransactionByToken($token, $redirect=false)
  {
    
    if($redirect){
        $query_consult = $this->pdo->query("SELECT * FROM `transactions` WHERE reference='{$token}'");
    }else{
        $query_consult = $this->pdo->query("SELECT * FROM `transactions` WHERE token='{$token}'");
    }
    
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult[0];
    } else {
      return false;
    }

  }

  public function getTransactionsLastMonth()
  {

    $query_consult = $this->pdo->query("SELECT * FROM transactions WHERE status='approved' AND created >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND created < CURDATE();");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult;
    } else {
      return array();
    }

  }

  public function getTransactionsMonthCurrent()
  {

    $query_consult = $this->pdo->query("SELECT * FROM transactions WHERE status='approved' AND MONTH(created) = MONTH(CURDATE()) AND YEAR(created) = YEAR(CURDATE());");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult;
    } else {
      return array();
    }

  }


  public function add($data)
  {

    try {

      $plataform = isset($data->plataform) ? $data->plataform : "MERCADOPAGO";

      $query = $this->pdo->prepare("INSERT INTO `transactions` (product_id, valor, info_data, status, reference, qtd, origem, plataform) VALUES (:product_id, :valor, :info_data, :status, :reference, :qtd, :origem, :plataform) ");
      $query->bindValue(':product_id', $data->product_id);
      $query->bindValue(':valor', $data->valor);
      $query->bindValue(':info_data', $data->info_data);
      $query->bindValue(':status', $data->status);
      $query->bindValue(':reference', $data->reference);
      $query->bindValue(':qtd', $data->qtd);
      $query->bindValue(':origem', $data->origem);
      $query->bindValue(':plataform', $plataform);

      if ($query->execute()) {
        return true;
      } else {
        return false;
      }

    } catch (\Throwable $th) {
      file_put_contents('err.txt', $th->getMessage());
      return false;
    }

  }




  public function listAll($status_get)
  {

    if ($status_get) {

      $status = trim($status_get);
      $query_consult = $this->pdo->query("SELECT * FROM `transactions` WHERE status='{$status}' ORDER BY id DESC");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if (count($fetch_consult) > 0) {
        return count($fetch_consult);
      } else {
        return 0;
      }

    } else {
      $query_consult = $this->pdo->query("SELECT * FROM `transactions` ORDER BY id DESC");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if (count($fetch_consult) > 0) {
        return count($fetch_consult);
      } else {
        return 0;
      }
    }
  }

  public function list($inicio, $itens_por_pagina, $status_get = false)
  {

    if ($status_get) {

      $status = trim($status_get);
      $query_consult = $this->pdo->query("SELECT * FROM `transactions` WHERE status='{$status}' ORDER BY id DESC LIMIT {$inicio}, {$itens_por_pagina}");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if (count($fetch_consult) > 0) {
        return $fetch_consult;
      } else {
        return false;
      }

    } else {
      $query_consult = $this->pdo->query("SELECT * FROM `transactions` ORDER BY id DESC LIMIT {$inicio}, {$itens_por_pagina}");
      $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
      if (count($fetch_consult) > 0) {
        return $fetch_consult;
      } else {
        return false;
      }
    }

  }
  
    public function deleteTransaction($where){

      $stmt = $this->pdo->prepare("DELETE FROM items WHERE ".$where[0]." = :".$where[0]);
      $stmt->bindParam(':'.$where[0], $where[1]);
      if($stmt->execute()){
        return true;
      }else{
        return false;
      }

    }

  public function convertMoney($type, $valor)
  {
    if ($type == 1) {
      $a = str_replace(',', '.', str_replace('.', '', $valor));
      return $a;
    } else if ($type == 2) {
      return number_format($valor, 2, ",", ".");
    }

  }

  public function calcTransactions($transactions)
  {

    $valor = 0;

    foreach ($transactions as $key => $value) {
      $vl1 = self::convertMoney(1, $value->valor);
      $valor = $valor + $vl1;
    }

    return json_encode([
      'v1' => $valor,
      'v2' => self::convertMoney(2, $valor)
    ]);

  }

}
