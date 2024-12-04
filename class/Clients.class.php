<?php


/**
 *
 */
class Clients extends Conn
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

    $getUsers = self::getUsers();
    $getUsersYesterday = self::getUsersYesterday();
    $getClients12Months = self::getClients12Months();
    $getUsersLastWeek = self::getUsersLastWeek();

    $diff_users = ($getUsersLastWeek - count($getUsersYesterday));

    $formatGraphic12MonthsClients = self::formatGraphic12MonthsClients($getClients12Months);
    $lastMonths12Name = self::lastMonths12Name();

    if ($diff_users > 0) {
      $diff = '<p class="mb-0"><span class="text-success text-sm font-weight-bolder">+' . $diff_users . ' </span> do que a ultima semana</p>';
    } else if ($diff_users < 0) {
      $diff = '<p class="mb-0"><span class="text-danger text-sm font-weight-bolder">' . $diff_users . ' </span> do que a ultima semana</p>';
    } else {
      $diff = "&nbsp;";
    }



    return json_encode([
      'getUsers' => count($getUsers),
      'getUsersYesterday' => count($getUsersYesterday),
      'diff_users' => $diff,
      'getClients12Months' => $getClients12Months,
      'formatGraphic12MonthsClients' => $formatGraphic12MonthsClients,
      'lastMonths12Name' => $lastMonths12Name,
      'getUsersLastWeek' => $getUsersLastWeek
    ]);

  }



  public function lastMonths12Name()
  {

    $mesesPt = array(
      'Jan' => 'Jan',
      'Feb' => 'Fev',
      'Mar' => 'Mar',
      'Apr' => 'Abr',
      'May' => 'Mai',
      'Jun' => 'Jun',
      'Jul' => 'Jul',
      'Aug' => 'Ago',
      'Nov' => 'Nov',
      'Sep' => 'Set',
      'Oct' => 'Out',
      'Dec' => 'Dez'
    );

    $current_month = (int) date('n');
    $months_list = array();
    $current_month_name = ucfirst((new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('M'));
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

  public function formatGraphic12MonthsClients($clients)
  {

    $current_month = (int) date('n');
    $clients_monthly = array();
    for ($i = 0; $i < 12; $i++) {
      $month = ($current_month - $i + 12) % 12;
      if ($month == 0)
        $month = 12;
      $clients_monthly[$month] = 0;
    }

    foreach ($clients as $client) {
      $month = (int) $client->mes;
      @$clients_monthly[$month] += $client->total;
    }

    return array_values(array_reverse($clients_monthly));

  }

  public function getClients12Months()
  {
    $query_consult = $this->pdo->query("SELECT COUNT(*) as total, MONTH(created) as mes FROM clients
                                          WHERE created >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
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



  public function getUsers()
  {

    $query_consult = $this->pdo->query("SELECT * FROM clients ORDER BY id DESC");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult;
    } else {
      return array();
    }

  }

  public function getUsersYesterday()
  {

    $query_consult = $this->pdo->query("SELECT * FROM clients WHERE created >= DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND created < DATE_SUB(CURDATE(), INTERVAL 7 DAY);");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult;
    } else {
      return array();
    }

  }

  public function getUsersLastWeek()
  {
    $query_consult = $this->pdo->query("SELECT COUNT(*) as total FROM clients WHERE created >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult[0]->total;
    } else {
      return false;
    }
  }

  public function getClientById($id)
  {

    $query_consult = $this->pdo->query("SELECT * FROM `clients` WHERE id='{$id}'");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult[0];
    } else {
      return false;
    }

  }


  public function getClientByRecursive($email)
  {

    $query_consult = $this->pdo->query("SELECT * FROM `clients` WHERE email='{$email}'");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return $fetch_consult[0];
    } else {
      return false;
    }

  }

  public function listAll()
  {

    $query_consult = $this->pdo->query("SELECT * FROM `clients` ORDER BY id DESC");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return count($fetch_consult);
    } else {
      return 0;
    }

  }

  public function setPasswordClient($id, $senha)
  {
    $password = password_hash($senha, PASSWORD_DEFAULT);
    if ($this->pdo->query("UPDATE `clients` SET password='{$password}' WHERE id='{$id}' ")) {
      return true;
    } else {
      return false;
    }
  }

  public function disconnectMp($id)
  {
    if ($this->pdo->query("UPDATE `clients` SET mercadopago=NULL WHERE id='{$id}' ")) {
      return true;
    } else {
      return false;
    }
  }


  public function setMercadoPagoClient($id, $mercadopago)
  {
    if ($this->pdo->query("UPDATE `clients` SET mercadopago='{$mercadopago}' WHERE id='{$id}' ")) {
      return true;
    } else {
      return false;
    }
  }

  public function add($data)
  {

    $query = $this->pdo->prepare("INSERT INTO `clients` (nome, email, number) VALUES (:nome, :email, :number) ");
    $query->bindValue(':nome', $data->nome);
    $query->bindValue(':email', $data->email);
    $query->bindValue(':number', $data->number);

    if ($query->execute()) {
      $lastId = $this->pdo->lastInsertId();
      return $lastId;
    } else {
      return false;
    }

  }

  public function delete($id)
  {

    $stmt = $this->pdo->prepare("DELETE FROM `clients` WHERE id = :id");
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
      return true;
    } else {
      return false;
    }

  }


  public function searchClients($term, $inicio, $itens_por_pagina)
  {

    $query_consult = $this->pdo->query("SELECT * FROM `clients` WHERE email LIKE '%{$term}%' OR nome LIKE '%{$term}%' OR number LIKE '%{$term}%'  LIMIT {$inicio}, {$itens_por_pagina}");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);

    if (count($fetch_consult) > 0) {
      return $fetch_consult;
    } else {
      return false;
    }
  }

  public function listAllSearch($term)
  {
    $query_consult = $this->pdo->query("SELECT * FROM `clients` WHERE email LIKE '%{$term}%' OR nome LIKE '%{$term}%' OR number LIKE '%{$term}%'");
    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);
    if (count($fetch_consult) > 0) {
      return count($fetch_consult);
    } else {
      return 0;
    }
  }

  public function list($inicio, $itens_por_pagina, $ordem_desc = true)
  {
    $order_by = $ordem_desc ? "DESC" : "ASC";

    $query_consult = $this->pdo->query("
            SELECT clients.*, COUNT(transactions.id) AS num_transactions
            FROM clients
            LEFT JOIN transactions ON clients.id = JSON_EXTRACT(transactions.info_data, '$.client_id')
            GROUP BY clients.id
            ORDER BY num_transactions {$order_by}
            LIMIT {$inicio}, {$itens_por_pagina}
        ");

    $fetch_consult = $query_consult->fetchAll(PDO::FETCH_OBJ);

    if (count($fetch_consult) > 0) {
      return $fetch_consult;
    } else {
      return false;
    }
  }




}
