<!DOCTYPE html>
<html>

  <head>
  <title>Zorzal Empire</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="stylesheet.css">
  </head>

  <body class="container" style="background-color: #eb7260; font-family: sans-serif; font-weight: lighter;">
  	<?php 
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
        $query_users30= 'SELECT * FROM usuarios;';
        $query_users1= 'SELECT uid, nombre, apellido FROM Usuario;';

        $result_users1 = $db1 -> prepare($query_users1);
        $result_users1 -> execute();
        $usuarios1 = $result_users1 -> fetchAll();
        $result_users30 = $db30 -> prepare($query_users30);
        $result_users30 -> execute();
        $usuarios30 = $result_users30 -> fetchAll();
        


        $options_usuarios = "";
        foreach ($usuarios1 as $usuario) {
            $options_usuarios = $options_usuarios . "<option value=" . $usuario[0] . ">" . $usuario[1] . " " . $usuario[2] . "</option>";
        }

        // $options_usuarios2 = "";
        foreach ($usuarios30 as $usuario) {
            $options_usuarios = $options_usuarios . "<option value=" . $usuario[0] . ">" . $usuario[1] . " " . $usuario[2] . "</option>";
        }
        $query_countries = 'SELECT DISTINCT pais FROM Usuario;';

        $result_countries = $db1 -> prepare($query_countries);
        $result_countries -> execute();
        $countries = $result_countries -> fetchAll();

        $options_countries = "";
        foreach ($countries as $country) {
            $options_countries = $options_countries . "<option>" . $country[0] . "</option>";
        }

        $query_tipo_de_cambio = "SELECT cambio FROM PrecioHistorico ORDER BY Fecha DESC LIMIT 2;";

        $result = $db1 -> prepare($query_tipo_de_cambio);
        $result -> execute();
        $usd = $result -> fetch();
        $clp = $result -> fetch();

        $query_cantidad_transacciones = "SELECT COUNT(*) FROM Transaccion;";
        $query_cantidad_usuarios = "SELECT COUNT(*) FROM Usuario;";
        $query_dias = "SELECT COUNT(*)/2 FROM PrecioHistorico;";

        $result = $db1 -> prepare($query_cantidad_transacciones);
        $result -> execute();
        $cantidad_transacciones = $result -> fetch();

        $result = $db1 -> prepare($query_cantidad_usuarios);
        $result -> execute();
        $cantidad_usuarios = $result -> fetch();

        $result = $db1 -> prepare($query_dias);
        $result -> execute();
        $dias = $result -> fetch();

        $table = "<table class='table'> <tr><th>Valor en Peso Chileno (CLP)</th><td>CLP\$$" . number_format($clp[0], 0, ',', '.') . "</td></tr> <tr><th>Valor en Dolar Americano (USD)</th><td>USD\$" . number_format($usd[0], 2, ',', '.') . "</td></tr> <tr><th>Usuarios totales</th><td>$cantidad_usuarios[0]</td></tr> <tr><th>Transacciones totales</th><td>$cantidad_transacciones[0]</td></tr> <tr><th>Dias desde nacimiento del Zorzal</th><td>$dias[0]</td></tr></table>";
  	?>
    <header>
      <div class="row">
        <div class="col-lg-12">ZORZAL EMPIRE</div>
      </div>
    </header>

    <div class="seccion">

      <div class="row">
        <div class="col-sm-6">
          <div class="row section-head">
            <h2>INFORMACIÓN GENERAL</h2>
          </div>
          <div class="row section-content">
            <p>El Zorzal es la criptomoneda del futuro. En esta plataforma web se tiene acceso a una serie de consultas
            acerca de los usuarios, las transacciones entre ellos, la evolución de la moneda y las compras de esta con dinero real. <br><br> En esta comunidad no discriminamos, tenemos usuarios de múltiples países (incluso del Territorio Británico del Oceano Índico) interesados en nuestra moneda. El mercado abrió el 25 de Marzo de 2018 y asumimos que la fecha actual es 8 de Abril (por simpleza de la cantidad de tuplas dado el enunciado). Los usuarios hacen una primera transacción al Administrador (quien tiene todos los Zorzales del universo) para recibir cantidades de la criptomoneda.</p>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="row section-head">
            <h2>INDICADORES</h2>
          </div>
          <div class="row section-content"> <?php echo $table;?> </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="row section-head">
            <h2>CONSULTAS SQL</h2>
          </div>
        </div>
      </div>

      <!-- Fila de dos consultas -->
      <div class="row section-content" >
        <!-- Primera consulta -->
        <div class="col-sm-6">
          <div class="row"><h4>Quieres hacer un exchange?</h4></div>
          <!-- El dropdown no es necesario cuando el login este implementado -->
          <div class="row" style="padding: 20px;">
            <form action="exchange.php" method="post">
              <div class="row">
                <div class="col-sm-4">Usuario:</div>
                <div class="col-sm-8">
                  <select name="id_usuario">
                    <?php echo $options_usuarios; ?>
                  </select>
                </div>
              </div>
              <!-- <div class="row">
                <div class="col-sm-4">Fecha:</div>
                <div class="col-sm-8"><input type="date" name="fecha_consulta" value="2018-03-25" min="2018-03-25" max="2018-04-08"></div>
              </div> -->
              <br>
              <div class="row" style="padding-top: 7px;"><input class="btn btn-default btn-block" type="submit" value="Mostrar"></div>
            </form> 
          </div>
        </div>
        <!-- Segunda consulta -->
        <div class="col-sm-6">
          <div class="row"><h4>2. Valor de una cantidad de Zorzales en un día determinado</h4></div>
          <div class="row" style="padding: 20px;">
            <form action="consultas/segunda_consulta.php" method="post">
              <div class="row">
                <div class="col-sm-4">Zorsales:</div>
                <div class="col-sm-8"><input type="number" name="zorsales" step="0.01" value="0"></div>
              </div>
              <div class="row">
                <div class="col-sm-4">Fecha:</div>
                <div class="col-sm-8"><input type="date" name="fecha_consulta" value="2018-03-25" min="2018-03-25" max="2018-04-08"></div>
              </div>
              <br>
              <div class="row"><input class="btn btn-default btn-block" type="submit" value="Mostrar"></div>
            </form> 
          </div>
        </div>
      </div>

      <!-- Fila de dos consultas -->
      <div class="row section-content">
        <!-- Primera consulta -->
        <div class="col-sm-6">
          <div class="row"><h4>3. Última transacción de un usuario</h4></div>
          <div class="row" style="padding: 20px;">
            <form action="consultas/tercera_consulta.php" method="post">
              <div class="row">
                <div class="col-sm-4">Usuario:</div>
                <div class="col-sm-8">
                  <select name="id_usuario">
                    <?php echo $options_usuarios; ?>
                  </select>
                </div>
              </div><br>
              <div class="row"><input class="btn btn-default btn-block" type="submit" value="Mostrar"></div>
            </form> 
          </div>
        </div>
        <!-- Segunda consulta -->
        <div class="col-sm-6">
          <div class="row"><h4>4. Usuarios según país de procedencia</h4></div>
          <div class="row" style="padding: 20px;">
            <form action="consultas/cuarta_consulta.php" method="post">
              <div class="row">
                <div class="col-sm-4">País:</div>
                <div class="col-sm-8">
                  <select name="pais">
                    <?php echo $options_countries; ?>
                  </select>
                </div>
              </div>
              <br>
              <div class="row"><input class="btn btn-default btn-block" type="submit" value="Mostrar"></div>
            </form> 
          </div>
        </div>
      </div>

      <!-- Fila de dos consultas -->
      <div class="row section-content">
        <!-- Primera consulta -->
        <div class="col-sm-6">
          <div class="row"><h4>5. Precio promedio de los Zorzales del mes pasado</h4></div>
          <div class="row" style="padding: 20px;">
            <form action="consultas/quinta_consulta.php" method="post"><br>
              <div class="row"><input class="btn btn-default btn-block" type="submit" value="Mostrar"></div>
            </form> 
          </div>
        </div>
        <!-- Segunda consulta -->
        <div class="col-sm-6">
          <div class="row"><h4>6. Días con mayor montos y cantidades de transacción</h4></div>
          <div class="row" style="padding: 20px;">
            <form action="consultas/sexta_consulta.php" method="post">
              <br>
              <div class="row"><input class="btn btn-default btn-block" type="submit" value="Mostrar"></div>
            </form> 
          </div>
        </div>
      </div>

      <!-- Fila de dos consultas -->
      <div class="row section-content">
        <div class="col-sm-3"></div>
        <!-- Primera consulta -->
        <div class="col-sm-6">
          <div class="row"><h4>7. Cuantos Zorzales tiene un determinado usuario hoy</h4></div>
          <div class="row" style="padding: 20px;">
            <form action="consultas/septima_consulta.php" method="post">
              <div class="row">
                <div class="col-sm-4">Usuario:</div>
                <div class="col-sm-8">
                  <select name="id_usuario">
                    <?php echo $options_usuarios; ?>
                  </select>
                </div>
              </div><br>
              <div class="row"><input class="btn btn-default btn-block" type="submit" value="Mostrar"></div>
            </form> 
          </div>
        </div>
        <div class="col-sm-3"></div>
      </div>

    </div>
  </body>
</html>
