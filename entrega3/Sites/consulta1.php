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
    $usuario1 = $_POST["usuario_1"];
    $fecha1=$_POST["fecha_1"];

        $query= "SELECT transaccion.* FROM transaccion,usuarios WHERE (usuarios.id=transaccion.id_in OR usuarios.id=transaccion.id_out) AND usuarios.username = '$usuario1' AND transaccion.date='$fecha1';";

        $result = $db -> prepare($query);
        $result -> execute();
        # Ojo, fetch() nos retorna la primer fila, fetchAll()
        # retorna todas.
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

?>

<form action="index.php" method="post">
  <input type="submit" value="Volver">
</form> 
</body>
</html>