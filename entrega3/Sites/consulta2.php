<!DOCTYPE html>
<html>
<style>
table, th, td {
    border: 1px solid black;
}
</style>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php
  include_once "psql-config.php";
  try {
    $db = new PDO("pgsql:dbname=".DATABASE.";host=".HOST.";port=".PORT.";user=".USER.";password=".PASSWORD);
    }
    catch(PDOException $e) {
    echo $e->getMessage();
    }
    $cantidad2 = $_POST["cantidad_2"];
    $fecha2=$_POST["fecha_2"];

        $query= "SELECT * FROM (SELECT ($cantidad2*value) AS USD
                FROM zorzalcoin
                WHERE zorzalcoin.date='$fecha2' AND currency_type='USD') AS USD_TABLE,
                (SELECT ($cantidad2*value) AS CLP
                FROM zorzalcoin
                WHERE zorzalcoin.date='$fecha2' AND currency_type='CLP') AS CLP_TABLE ;";
        $result = $db -> prepare($query);
        $result -> execute();
        # Ojo, fetch() nos retorna la primer fila, fetchAll()
        # retorna todas.
        // $tabla = $result -> fetchAll();
        echo "<table>";
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if(True) {
                echo '<tr>';
                foreach($row as $key=>$value) {
                    echo "<th>{$key}</th>";
                }
                echo '</tr>';
                $tableheader = true;
            }
            echo "<tr>";
            foreach($row as $value) {
                echo "<td>{$value}</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        // echo "$columns";
        // echo "<table><tr><th>tid</th><th>date</th><th>id_out</th><th>id_in</th><th>quantity</th><th>id</th><th>username</th><th>name</th><th>phone</th><th>country</th><th>gender</th><th>password</th></tr>";
        // foreach ($tabla as $fila) {
        // echo "<tr><td>$fila[0]</td><td>$fila[1]</td><td>$fila[2]</td><td>$fila[3]</td><td>$fila[4]</td><td>$fila[5]</td><td>$fila[6]</td><td>$fila[7]</td><td>$fila[8]</td><td>$fila[9]</td><td>$fila[10]</td><td>$fila[11]</td></tr>";
        // }
        // echo "</table>";
?>

<form action="index.php" method="post">
  <input type="submit" value="Volver">
</form> 
</body>
</html>