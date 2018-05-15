<!DOCTYPE html>
<html>
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

  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  </head>

  <body style="background-color: #eb7260; font-family: sans-serif;">

    <header>
      <div class="row">
        <div class="col-lg-12">Bases de Datos - Entrega 2</div>
      </div>
    </header>

    <div class="seccion">
      <div class='row' style="margin: 0px 5px;"><h4>Resultado de la consulta</h4></div>
      <div class="row" style="margin: 0px 5px;">
        <?php
          include_once "psql-config.php";
          try {$db = new PDO("pgsql:dbname=".DATABASE.";host=".HOST.";port=".PORT.";user=".USER.";password=".PASSWORD);}
          catch(PDOException $e) {echo $e->getMessage();}

         	$query = "SELECT * FROM (SELECT COUNT(cantidad) AS max_qty, Transaccion.fecha::date AS fecha_max_qty FROM transaccion GROUP BY Transaccion.fecha::date ORDER BY COUNT(cantidad) DESC LIMIT 1) AS A, (SELECT SUM(cantidad) AS max_amount, Transaccion.fecha::date AS fecha_max_amount FROM transaccion GROUP BY Transaccion.fecha::date ORDER BY SUM(cantidad) DESC LIMIT 1) AS B;";

        	$result = $db -> prepare($query);
        	$result -> execute();
        	$rows = $result -> fetch();
        	echo "<table class='table table-bordered table-hover table-striped'>"; 
            echo "<tr>";
              echo "<th></th>";
              echo "<th>Fecha</th>";
              echo "<th>Cantidad</th>";
            echo "</tr>";
            echo "<tr>";
              echo "<th>Mayor cantidad de transacciones</th>";
              echo "<td>$rows[1]</td>";
              echo "<td>$rows[0] transacciones</td>";
            echo "</tr>";
            echo "<tr>";
              echo "<th>Mayor monto de transacción</th>";
              echo "<td>$rows[3]</td>";
              echo "<td>ZRZ$" . number_format($rows[2], 2, ",", ".") . "</td>";
            echo "</tr>";
           echo "</table>";
        ?>
      </div>
      <div class="row">
        <div class="col-sm-8"></div>
        <div class="col-sm-4">
          <form action="../index.php" method="post">
            <input class="btn btn-default btn-block" type="submit" value="Volver">
          </form>
        </div>
      </div>
    </div>

</body>
</html>