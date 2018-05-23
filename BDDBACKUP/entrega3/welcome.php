

<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: login.php");
  exit;
}
echo "userid: $_SESSION[uid]";

// CODIGO DE SALDO ACTUAL DE ZORZALES  ############################################
include_once "psql-config.php";
try {
$db1 = new PDO("pgsql:dbname=".DATABASE.";host=".HOST.";port=".PORT.";user=".USER.";password=".PASSWORD);
}
catch(PDOException $e) {
echo $e->getMessage();
}

// Se crea un PDO del grupo 30
include_once "psql-config2.php";
try {
$db30 = new PDO("pgsql:dbname=".DATABASE2.";host=".HOST2.";port=".PORT2.";user=".USER2.";password=".PASSWORD2);
}
catch(PDOException $e) {
echo $e->getMessage();
}

$username = explode("_", $_SESSION['username']);
$id = $_SESSION['uid'];

  // Si el usuario pertenece al grupo 30
  if(count($username) == 1){
    $query_suma = "Select Sum(quantity) from transaccion where id_in = $id";
    $result_suma = $db30 -> prepare($query_suma);
    $result_suma -> execute();
    $quantity_suma = $result_suma -> fetch();

    $query_resta = "Select Sum(quantity) from transaccion where id_out = $id";
    $result_resta = $db30 -> prepare($query_resta);
    $result_resta -> execute();
    $quantity_resta = $result_resta -> fetch();

    $quantity = floatval($quantity_suma[0]) - floatval($quantity_resta[0]);
  }
else {
  $id_grupo1 = $username[1];
  $id_grupo30 = $_SESSION['uid'];

  //Buscando la cantidad en grupo 30
  $query30_suma = "Select Sum(quantity) from transaccion where id_in = $id_grupo30";
  $result30_suma = $db30 -> prepare($query30_suma);
  $result30_suma -> execute();
  $quantity30_suma = $result30_suma -> fetch();

  $query30_resta = "Select Sum(quantity) from transaccion where id_out = $id_grupo30";
  $result30_resta = $db30 -> prepare($query30_resta);
  $result30_resta -> execute();
  $quantity30_resta = $result30_resta -> fetch();

  //Buscando la cantidad en grupo 1
  $query1_suma = "Select Sum(cantidad) from transaccion where to_id = $id_grupo1";
  $result1_suma = $db1 -> prepare($query1_suma);
  $result1_suma -> execute();
  $quantity1_suma = $result1_suma -> fetch();

  $query1_resta = "Select Sum(cantidad) from transaccion where from_id = $id_grupo1";
  $result1_resta = $db1 -> prepare($query1_resta);
  $result1_resta -> execute();
  $quantity1_resta = $result1_resta -> fetch();

  $quantity = floatval($quantity1_suma[0]) - floatval($quantity1_resta[0]) + floatval($quantity30_suma[0])
   - floatval($quantity30_resta[0]);


}
echo "\n";
echo "    Tu saldo actual es ".$quantity;

// CODIGO DE SALDO ACTUAL DE ZORZALES ############################################
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>. Welcome to our site.</h1>
    </div>
    <p><a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a></p>
</body>
</html>
