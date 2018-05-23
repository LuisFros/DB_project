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
<script type="text/javascript">
    function seleccionado(uid,nombre_destinatario, balance){
        // sessionStorage.setItem("id_origen",String(uid));
        var old_tbody=document.getElementById("tablamadre");
        var new_tbody = document.createElement('table');
        new_tbody.setAttribute("id", "tabla_nueva");
        new_tbody.setAttribute("class", "table table-bordered table-hover table-striped");
        var header= "<tr><th>Usuario</th><th>Balance</th>";
        var body= "<tr><td>"+nombre_destinatario+"</td><td>"+balance+"</td></tr>";
        new_tbody_text=new_tbody.innerHTML+header+body;
        new_tbody.innerHTML=new_tbody_text;
        old_tbody.parentNode.replaceChild(new_tbody, old_tbody);
        var quantity=document.getElementById("Quantity");
        quantity.innerHTML='Cantidad: <input type="float" id="input_cantidad" name="cantidad_exchange" required>'
        var radio=document.getElementById("radio_buttons");
        radio.innerHTML='<input type="radio" name="currency_type" value="CLP" required> CLP<br> <input type="radio"' 
        +'name="currency_type" value="USD"> USD<br>'
        var quantity=document.getElementById("exchange_button");
        quantity.innerHTML='<input class="btn btn-default btn-block" type="submit" value="Intercambiar">';
        var origen=document.getElementById("balance_origen");
        origen.innerHTML='<input name="balance_origen" type="hidden" value='+balance+'>';
        var id_origen =document.getElementById("id_origen");
        id_origen.innerHTML='<input name="id_origen" type="hidden" value='+uid+'>';
        var volver_button=document.getElementById('volver_button');
        volver_button.innerHTML='<input class="btn btn-default btn-block" type="submit" value="Volver">'
        window.scrollTo(0,0);

       
    }
</script>
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  </head>


  <body style="background-color: #eb7260; font-family: sans-serif;">


    <header>
      <div class="row">
        <div class="col-lg-12">Bases de Datos - Entrega 3</div>
      </div>
    </header>

    <div class="seccion">
      <div class='row' style="margin: 0px 5px;"><h4>Selecciona a quien le quieres intercambiar</h4></div>
      <div class="row" style="margin: 0px 5px;">
        <?php
          session_start();
          include_once "psql-config.php";
          try {$db1 = new PDO("pgsql:dbname=".DATABASE.";host=".HOST.";port=".PORT.";user=".USER.";password=".PASSWORD);}
          catch(PDOException $e) {echo $e->getMessage();}
          include_once "psql-config2.php";
          try {$db30 = new PDO("pgsql:dbname=".DATABASE2.";host=".HOST2.";port=".PORT2.";user=".USER2.";password=".PASSWORD2);}
          catch(PDOException $e) {echo $e->getMessage();}
          $qid = $_SESSION["uid"];
          $username = $_SESSION["username"];
          $username=substr($username,0, strpos($username, '_'));

          $query_usuarios1="SELECT uid,email FROM Usuario;";
          $result = $db1 -> prepare($query_usuarios1);
          $result -> execute();
          $rows = $result -> fetchAll();

          $query_usuarios30="SELECT id,username FROM usuarios;";
          $result30 = $db30 -> prepare($query_usuarios30);
          $result30 -> execute();
          $rows30 = $result30 -> fetchAll();
          echo "<table id='tablamadre' class='table table-bordered table-hover table-striped'>";
              echo "<tr>";
                echo "<th>ID</th>";
                echo "<th>Usuario</th>";
                echo "<th>Saldo</th>";
              echo "</tr>";
                foreach ($rows as $uid){
                  # Condicion para que no se chequee a si mismo
                  $aux2 = substr($uid[1],0, strpos($uid[1], '@'));

                  if ($aux2 != $username){
                  $query = "SELECT A.ingresos, B.gastos, A.ingresos + B.gastos AS balance FROM (SELECT COALESCE(Sum(cantidad), 0) AS ingresos FROM Transaccion WHERE to_id = $uid[0]) AS A, (SELECT COALESCE(SUM(-cantidad), 0) AS gastos FROM Transaccion WHERE from_id = $uid[0]) AS B;";
                  $result = $db1 -> prepare($query);
                  $result -> execute();
                  $row = $result -> fetchAll();
                  $aux=$row[0]['balance'];
                  $aux2 = substr($uid[1],0, strpos($uid[1], '@'));
                  // Variable para el manejo de balances en multiples bd
                  $extra=0;
                   foreach ($rows30 as $user_tabla_grupo_30){   
                    $id_extra=$uid[0];
                    $concatenado=$aux2."_".$id_extra;
                    $usuario_en_tabla_30=$user_tabla_grupo_30['username'];
                    
                    // si usuario_id = usuario_id esta en la tabla de registrados,buscar su saldo
                    if ($usuario_en_tabla_30==$concatenado){
                        // print("|".$concatenado.$usuario_en_tabla_30."|");
               
                         $query = "SELECT (t3.inicial +t1.p1-t2.p2) AS Cantidad
                            FROM (SELECT COALESCE(SUM(quantity),0) AS p1
                            FROM transaccion
                            WHERE transaccion.id_in=$id_extra) AS t1
                            ,(SELECT COALESCE(SUM(quantity),0) AS p2
                            FROM transaccion
                            WHERE transaccion.id_out=$id_extra ) AS t2
                            ,(SELECT COALESCE(SUM(exchange.quantity / zorzalcoin.value), 0) AS inicial 
                            FROM exchange, zorzalcoin
                            WHERE exchange.date= zorzalcoin.date
                            AND exchange.currency_type = zorzalcoin.currency_type AND exchange.id_buy = $id_extra) AS t3;";
                        $result = $db30 -> prepare($query);
                        $result -> execute();
                        $row = $result -> fetchAll();
                        $extra=$row[0]['cantidad'];
                        $balance_total=$aux + $extra;
                        echo "<tr onclick='seleccionado($uid[0],\"".str_replace('"', '\"', $aux2)."\",$balance_total)'>";
                          echo "<td>$uid[0]</td>";
                          echo "<td>$aux2</td>"; 
                          echo "<td>$balance_total</td>";
                        echo "</tr>";
                        break;
                  }
                 }
                  // Parseo de un string para que js lo pueda leer dentro de la
                
                
              }
            }
                foreach ($rows30 as $uid){
                  # Condicion para que no se chequee a si mismo
                  if (($uid[0] <> $qid) and ($uid[0] <> 0)){
                  $query = "SELECT (t1.p1-t2.p2) AS Cantidad
                            FROM (SELECT COALESCE(SUM(quantity),0) AS p1
                            FROM transaccion
                            WHERE transaccion.id_in=$uid[0]) AS t1
                            ,(SELECT COALESCE(SUM(quantity),0) AS p2
                            FROM transaccion
                            WHERE transaccion.id_out=$uid[0] ) AS t2;";
                  $result = $db30 -> prepare($query);
                  $result -> execute();
                  $row = $result -> fetchAll();
                  $aux=$row[0]['cantidad'];
                  $aux2 =$uid[1];
                  if (strpos($aux2, '_') ==false){
                  // Parseo de un string para que js lo pueda leer dentro de la
                  echo "<tr onclick='seleccionado($uid[0],\"".str_replace('"', '\"', $aux2)."\",$aux)'>";
                    echo "<td>$uid[0]</td>";

                    echo "<td>$aux2</td>";
                    echo "<td>$aux</td>";
                  echo "</tr>";
                  }
                }
                }
          echo "</table>";
        ?>
      </div>
      <div class="row">
        <div class="col-sm-9">
          <!-- Form dinamico que se muestra solo al seleccionar un usuario -->
          <form action="/~grupo1/entrega3/exchange/exchange_status.php " method="post">
              <p id="Quantity">
            
              </p>
            <!-- name="cantidad_exchange" -->
              <p id="radio_buttons">
            <!-- name="currenct_type" -->
             </p>
            <p id="exchange_button">
              
            </p>
            <p id="balance_origen">
              
            </p>
            <p id="id_origen">
              
            </p>
            <input type="hidden" name="confirmation" value="0">
          </form>
        </div>
        <div class="col-sm-3">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <!-- <input type="hidden" name="id_usuario" value="<?php '$qid'; ?>"> -->
            <p id="volver_button">
              
            </p>
          </form>
        </div>
    </div>
    <div class="row">
      <form action="/~grupo1/entrega3/welcome.php" method="post">
            <input type="submit" class="btn btn-default btn-block" value="Pagina de Inicio">
      </form>
    </div>

</body>
</html>