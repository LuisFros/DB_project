<!DOCTYPE html>
<html>

  <head>
  <title></title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="stylesheet.css">
  </head>
  <style type="text/css">
    header {
      background-color: #354458;
      font-size: 30pt;
      font-weight: bolder;
      color: white;
      padding: 15px 15px;
      margin: 0px 300px;
      margin-bottom: 0px;
      text-align: center;
    }

    .seccion {
      background-color: #f1f1f1;
      margin: 0px 300px ;
      padding: 20px;
      min-height: 850px;
    }

    h4 {
      font-weight: bold !important;
    }
  </style>
<body class="container" style="background-color: #eb7260; font-family: sans-serif; font-weight: lighter;">
  <?php
  session_start();
  include_once "psql-config2.php";
  try {
    $db30 = new PDO("pgsql:dbname=".DATABASE2.";host=".HOST2.";port=".PORT2.";user=".USER2.";password=".PASSWORD2);
    $db30->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {
    echo $e->getMessage();
  }
  include_once "psql-config.php";
  try {
    $db1 = new PDO("pgsql:dbname=".DATABASE.";host=".HOST.";port=".PORT.";user=".USER.";password=".PASSWORD);
    $db1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {
    echo $e->getMessage();
  }
  $qid=$_SESSION['uid'];
  $tipo=$_SESSION['currency_type'];
  $cambio=(real)$_SESSION['cantidad_exchange'];
  $id_origen=$_SESSION['id_origen'];
  $username=$_SESSION['username'];
  $date= date('Y-m-d');

  $query_precio='SELECT * FROM Preciohistorico WHERE fecha=:fecha AND tipo=:tipo';
  $result_precio=$db1 -> prepare($query_precio);
  $result_precio->execute(array(':fecha'=>$date,':tipo'=>$tipo));
  $row_precio= $result_precio -> fetchAll();
  $precio=$row_precio[0]['cambio'];
  $cantidad=$cambio/$precio;

  $query_encontrar_db='SELECT * FROM usuarios WHERE username= :user;';
  $result_encontrar=$db30 -> prepare($query_encontrar_db);
  $result_encontrar -> execute(array(':user'=>$username));
  $rows_encontrado= $result_encontrar -> fetchAll();
  if (sizeof($rows_encontrado) <> 0){
    $tipo=strtoupper($tipo);
    
    $max_query='SELECT max(e_id)+1 AS valor FROM exchange;';
    $result= $db30 -> prepare($max_query);
    $result->execute();
    $array_maximo=$result->fetchAll();
    $max=$array_maximo[0]['valor'];

    $insert_query='INSERT INTO exchange(e_id,id_buy, id_sell, quantity, currency_type ,date) VALUES (:max,:id_usuario ,:id_origen, :cambio, :tipo, :fecha);';
    $result_valor = $db30 -> prepare($insert_query);
    $result_valor -> execute(array($max,$qid,$id_origen,$cambio,$tipo,$date));
  } 
  // if in db1
  else {
  $insert_query='INSERT INTO Exchange VALUES (:id_origen , :id_usuario ,current_timestamp::date, :tipo ,:cambio);';
  $result_valor = $db1 -> prepare($insert_query);
  $result_valor -> execute(array(':id_origen'=> $id_origen,':id_usuario'=> $qid ,':tipo' =>$tipo ,':cambio' => $cambio));

  }
  $idquery = "SELECT MAX(tid)+1 FROM transaccion";
  $searchresult = $db30->prepare($idquery);
  $searchresult -> execute();
  $assigned_id_row= $searchresult->fetch();
  $assigned_id=$assigned_id_row[0];
 $query = "INSERT INTO transaccion(tid,date, id_out, id_in, quantity)
    VALUES ($assigned_id,'$date', '$id_origen', '$qid', $cantidad)";
  $result = $db30 -> prepare($query);
  $result -> execute();
  // else in db30
  echo '<div class="alert alert-success"> <strong>Exitoso!</strong> El intercambio se realizo exitosamente.</div>'
    ?>

<form action="/~grupo1/entrega3/welcome.php" method="post">
  <input type="submit" class="btn btn-default btn-block" value="Pagina de Inicio">
</form>
<form action="/~grupo1/entrega3/exchange/exchange.php" method="post">
  <input type="submit" class="btn btn-default btn-block" value="Seguir intercambiando">
</form>
</body>