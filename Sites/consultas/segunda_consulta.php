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
          
          $zorsales = $_POST["zorsales"];
          $fecha = $_POST["fecha_consulta"];

         	$query = "SELECT tipo, cambio, cambio * $zorsales AS conversion FROM PrecioHistorico WHERE PrecioHistorico.fecha::date = '$fecha';";

        	$result = $db -> prepare($query);
        	$result -> execute();
        	$rows = $result -> fetchAll();
          	echo "<table class='table table-bordered table-hover table-striped'>";
              echo "<tr>";
                echo "<th>Tipo de moneda</th>";
                echo "<th>Tipo de cambio</th>";
                echo "<th>Zorsales</th>";
                echo "<th>Total</th>";
              echo "</tr>";
            	foreach ($rows as $row) {
                  if ($row[0] == "clp") {
                    $type = "Peso Chileno (CLP)";
                    $value = "$" . number_format($row[1], 0, ",", ".");
                    $total = "$" . number_format($row[2], 0, ",", ".");
                  } elseif ($row[0] == "usd") {
                    $type = "Dolar Americano (USD)";
                    $value = "$" . number_format($row[1], 2, ",", ".");
                    $total = "$" . number_format($row[2], 2, ",", ".");
                  }
                  echo "<tr>";
                    echo "<td>$type</td>";
                    echo "<td>$value</td>";
                		echo "<td>$zorsales</td>";
                    echo "<td>$total</td>";
                  echo "</tr>";
            	}
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