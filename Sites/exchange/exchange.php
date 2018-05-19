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
          try {$db1 = new PDO("pgsql:dbname=".DATABASE.";host=".HOST.";port=".PORT.";user=".USER.";password=".PASSWORD);}
          catch(PDOException $e) {echo $e->getMessage();}

          # $fecha = $_POST["fecha_consulta"];

          $query_usuarios1="SELECT uid,nombre FROM Usuario;";
          $result = $db1 -> prepare($query_usuarios1);
          $result -> execute();
          $rows = $result -> fetchAll();
          #print_r($rows);
          echo "<table class='table table-bordered table-hover table-striped'>";
              echo "<tr>";
                echo "<th>Usuario</th>";
                echo "<th>Saldo</th>";
              echo "</tr>";
                foreach ($rows as $uid){
                  $query = "SELECT A.ingresos, B.gastos, C.inicial, A.ingresos + B.gastos + C.inicial AS balance FROM (SELECT COALESCE(Sum(cantidad), 0) AS ingresos FROM Transaccion WHERE to_id = $uid[0]) AS A, (SELECT COALESCE(SUM(-cantidad), 0) AS gastos FROM Transaccion WHERE from_id = $uid[0]) AS B, (SELECT COALESCE(SUM(Exchange.cantidad / PrecioHistorico.cambio), 0) AS inicial FROM Exchange, PrecioHistorico WHERE Exchange.fecha::date = PrecioHistorico.fecha AND Exchange.tipo = PrecioHistorico.tipo AND Exchange.from_id = $uid[0]) AS C;";
                  $result = $db1 -> prepare($query);
                  $result -> execute();
                  $row = $result -> fetchAll();
                  echo "<tr>";
                    echo "<td>$uid[1]</td>";
                    $aux=$row[0]['balance'];
                    echo "<td>$aux</td>";
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