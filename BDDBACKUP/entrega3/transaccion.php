<!DOCTYPE html>
<html>

  <!-- Es simplemente el head -->
  <head>
  <title>Zorzal Empire</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="stylesheet.css">
  </head>

  <body class="container">
      <!-- Hacemos inicio de seccion -->
      <?php
    session_start();
    ?>
    <?php

        // Se crea un PDO del grupo 1
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

        // Seleccionando los usuarios grupo 2
        $query_users30= 'SELECT * FROM usuarios;';
        $query_users1= 'SELECT uid, nombre, apellido FROM Usuario;';
        $result_users30 = $db30 -> prepare($query_users30);
        $result_users30 -> execute();
        $usuarios30 = $result_users30 -> fetchAll();

        // Selecionando los usuarios grupo 1
        $query_function='SELECT * FROM usuario;';
        $result_users1 = $db1 -> prepare($query_function);
        $result_users1 -> execute();
        $usuarios1 = $result_users1 -> fetchAll();
        $espacio = " ";
        // Haciendo el string para select
        $options_usuarios = "";


        // Ya no dejamos ver los que solo estan el el grupo 1
        // foreach ($usuarios1 as $usuario) {
        //     $options_usuarios = $options_usuarios . "<option value=" . $usuario[0] . "," . $usuario[1] . "," . $usuario[2] . ">" . $usuario[0] . " " . $usuario[1] . " " . $usuario[2] . "</option>";
            
        // }
        foreach ($usuarios30 as $usuario) {
          if (floatval($usuario[0]) != 0 and floatval($usuario[0]) != floatval($_SESSION['uid'])){
            $options_usuarios = $options_usuarios . "<option value=" . $usuario[0] . "," . $usuario[1] . "," . $usuario[2] . ">" . $usuario[0] . " " . $usuario[1] . " " . $usuario[2] . "</option>";
        }}
        // $nuevatransaccion = "INSERT INTO transaccion VALUES 
        // (12, 3, 2018-05-01 20:52:50, hola)";
        // $db1->exec($nuevatransaccion);


    ?>

    <!-- Aquí empieza el HTML, lo anterior es llamada a la base de datos -->



    <!-- Mensaje de saludos -->
    <div class="page-header">
        <h1>Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    </div>
    
    <!-- Opciones para las transacciones -->
        
        <h2>Transacion</h2>

    <!-- Aquí comienza el form -->
        <form action=<?php echo $_SERVER['PHP_SELF']; ?> method="post">

          <div >Usuario:
            <select name="datos">
              <?php echo $options_usuarios; ?>
            </select>
          </div>
            
          
          <div >Cantidad
              <input type="number" name="cantidad" min="0" step="any">
          </div>
        
        <div ><input type="submit" value="Realizar transacción"></div>
      </form>   
  
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST["datos"] and $_POST["cantidad"]){
    $cantidad = $_POST["cantidad"];
    $datos = explode(",",$_POST["datos"]);
    echo $datos[0];
    echo " ";
    echo $datos[1];
    echo " ";
    echo $datos[2];
    echo " ";
    echo $cantidad;
    // $query = "INSERT INTO transaccion(from_id, to_id, fecha, mensaje, cantidad)
    // VALUES ('931',
    //  '$datos[0]', '$time[0]', 'hola', $cantidad)";
    $query_time= 'SELECT CURRENT_TIMESTAMP;';
    $result = $db1 -> prepare($query_time);
    $result -> execute();
    $time = $result -> fetch();

    // variable $date
    $query_time= 'SELECT CURRENT_DATE;';
    $result = $db1 -> prepare($query_time);
    $result -> execute();
    $date = $result -> fetch();


    $name = intval($_SESSION['uid']);
    echo 'aqui';
    echo gettype($name);
    echo $_SESSION['uid'];

    // foreach ($_SESSION as $key => $value)
    //   echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
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

        // Si tiene la plata suficiente para transferir
        if(floatval($quantity)> floatval($cantidad)){
  
          $idquery = "SELECT MAX(tid) FROM transaccion";
          $searchresult = $db30->prepare($idquery);
          $searchresult -> execute();
          $assigned_id_list = $searchresult->fetch();
          $assigned_id = intval($assigned_id_list[0]) + 1;
          
          $query = "INSERT INTO transaccion(tid, date, id_out, id_in, quantity)
          VALUES ('$assigned_id',
           '$date[0]', '$id', '$datos[0]', $cantidad)";
          $result = $db30 -> prepare($query);
          $result -> execute();
          echo 'cooompadre';
        }
        else{
          echo 'No tiene suficiente zorzales lo siento';
        }
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


       if(floatval($quantity)> floatval($cantidad)){
  
        $idquery = "SELECT MAX(tid) FROM transaccion";
        $searchresult = $db30->prepare($idquery);
        $searchresult -> execute();
        $assigned_id_list = $searchresult->fetch();
        $assigned_id = intval($assigned_id_list[0]) + 1;
        
        $query = "INSERT INTO transaccion(tid, date, id_out, id_in, quantity)
        VALUES ('$assigned_id',
         '$date[0]', '$id', '$datos[0]', $cantidad)";
        $result = $db30 -> prepare($query);
        $result -> execute();
        echo 'cooompadre';
       }
       else{
        echo 'No tiene suficiente zorzales lo siento';
      }






    }
    }


    ?>
    
  
  </body>
</html>
