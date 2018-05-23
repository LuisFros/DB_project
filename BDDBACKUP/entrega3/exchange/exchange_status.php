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
  <script type="text/javascript">
    function balance_error() {

    if (confirm("El usuario no tiene tantos zorzales!\nVuelve a intentar con un monto menor")) {
        location.replace(document.referrer);
    } else {
        location.replace(document.referrer);
    }
    document.getElementById("demo").innerHTML = txt;
}
  </script>

  <body class="container" style="background-color: #eb7260; font-family: sans-serif; font-weight: lighter;">
  	<?php 
    session_start();

      include_once "psql-config.php";
      try {
        $db1 = new PDO("pgsql:dbname=".DATABASE.";host=".HOST.";port=".PORT.";user=".USER.";password=".PASSWORD);
        }
        catch(PDOException $e) {
        echo $e->getMessage();
        }
       include_once "psql-config2.php";
      try {
        $db30 = new PDO("pgsql:dbname=".DATABASE2.";host=".HOST2.";port=".PORT2.";user=".USER2.";password=".PASSWORD2);
        }
        catch(PDOException $e) {
        echo $e->getMessage();
        }
      //   foreach ($_POST as $key => $value)
      // echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
        $_SESSION['id_origen']=$_POST['id_origen'];
        $_SESSION['balance_origen']=$_POST['balance_origen'];
        $_SESSION['cantidad_exchange']=$_POST['cantidad_exchange'];
        // a modificar esta linea con el login!
        $_SESSION['id_usuario']=(int)($_SESSION['uid']);
        $_SESSION['currency_type']=strtolower($_POST["currency_type"]);
      //   foreach ($_SESSION as $key => $value)
      // echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
 
      //   //
        $query_valor='SELECT cambio FROM preciohistorico WHERE tipo=:currency_type AND fecha::date=CURRENT_DATE;' ;
        $currency_type=$_SESSION["currency_type"];
        $quantity=$_SESSION['cantidad_exchange'];
        $cantidad_zorzales_origen=$_SESSION['balance_origen'];
        $result_valor = $db1 -> prepare($query_valor);
        $result_valor -> execute(array(':currency_type' => $currency_type));
        $resultado = $result_valor -> fetchAll();
        $valor_dia=$resultado[0]['cambio'];
        $cantidad_zorzales=$quantity/$valor_dia;
        if ($cantidad_zorzales_origen< $cantidad_zorzales) {
          echo '<script>balance_error()</script>';
        }
      ?>
      <div class="row">
       <table class='table table-bordered table-striped'>
        <tr>
          <td >Precio de zorzales en <?php echo $currency_type; ?>:</td>
          <td><?php echo $valor_dia ;?></td>
        </tr>

        <tr>
          <td >Cantidad a transferir:</td>
          <td><?php echo $quantity ;?></td>
        </tr>
         <tr>
          <td >Cantidad de zorzales a recibir:</td>
          <td><?php echo $cantidad_zorzales ;?></td>
        </tr>
     </table>
    </div>
    <div class="row">
      <div class="col-sm-2">
          <form action="<?php echo '/~grupo1/entrega3/exchange/exchange_confirmed.php'; ?>" method="post">
            <!-- <input type="hidden" name="id_usuario_confirmed" value="<?php '$qid'; ?>">
            <input type="hidden" name="tipo_confirmed" value="<?php '$currency_type'; ?>">
            <input type="hidden" name="cambio_confirmed" value="<?php '$valor_dia'; ?>">
            <input type="hidden" name="id_origen_confirmed" value="<?php '$id_origen'; ?>"> -->
            <input class="btn btn-default btn-block" type="submit" value="Confirmar">
          </form>
    </div>
  </body>
</html>
