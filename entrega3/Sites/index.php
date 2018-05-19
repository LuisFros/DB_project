<!DOCTYPE html>
<html>
<style>
table, th, td {
    border: 1px solid black;
}
</style>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="zorzalcoin.css">
</head>
<body>

<h1>Zorzalcoin</h1>

<br>
Todas las transacciones de una usuario en una fecha
<br>
<form action="consulta1.php" method="post">
  <br/>
  Usuario:<input type="text" name="usuario_1">
  Fecha:<input type="text" name="fecha_1">
  <br/>
  <input type="submit" value="Buscar">
</form>

<br>
Equivalencia en CLP y USD de Zorzales por fecha:
<br>
<form action="consulta2.php" method="post">
  <br/>
  Cantidad:<input type="text" name="cantidad_2">
  Fecha:<input type="text" name="fecha_2">
  <br/>
  <input type="submit" value="Buscar">
</form>

<br>
Ultima transaccion de un usuario:
<br>
<form action="consulta3.php" method="post">
  <br/>
  Usuario:<input type="text" name="usuario_3">
  <br/>
  <input type="submit" value="Buscar">
</form>


<br>
Todos los usuarios de un cierto pais de procedencia:
<br>
<form action="consulta4.php" method="post">
  <br/>
  Pais:<input type="text" name="pais_3">
  <br/>
  <input type="submit" value="Buscar">
</form>
<br>
<br>

Precio promedio de los zorzales el mes anterior:
<br>
<form action="consulta5.php" method="post">
  <br/><br/>
  <input type="submit" value="Buscar">
</form>

<br>
<br>


Dia del mes anterior con mayor cantidad de transacciones, y dia en que se transo la  mayor cantidad de zorzales:
<br>
<form action="consulta6.php" method="post">
  <br/><br/>
  <input type="submit" value="Buscar">
</form>
<br>
<br>

Cantidad de zorzales que posee un usuario:
<br>
<form action="consulta7.php" method="post">
    <br/>
  Usuario:<input type="text" name="usuario_7">
  <br/>
  <br/><br/>
  <input type="submit" value="Buscar">
</form>
</body>
</html>
