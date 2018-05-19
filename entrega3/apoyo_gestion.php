<!DOCTYPE HTML>
<html>
    <head>
        <title>Hola</title>
    </head>
	<?php
	    try {
	        $db_g1 = new PDO("pgsql:dbname=grupo1;host=localhost;port=5432;user=grupo1;password=grupo1");}
	        catch(PDOException $e) {
	        echo $e->getMessage();
	    }

	    try {
	        $db_g30 = new PDO("pgsql:dbname=grupo30;host=localhost;port=5432;user=grupo30;password=grupo30");}
	        catch(PDOException $e) {
	        echo $e->getMessage();
	    }

	   	$totals = 'SELECT * FROM qty_cash_total()';
	    $result = $db_g1 -> prepare($totals);
	    $result -> execute();
	    $totals = $result -> fetch();

	   	$by_month = 'SELECT * FROM qty_cash_by_month()';
	    $result = $db_g1 -> prepare($by_month);
	    $result -> execute();
	    $by_month = $result -> fetchAll();
	?>
    <body>
    	<h1> Apoyo a la gestión </h1>
    	<table>
    		<tr>
    			<th>Q total</th>
    			<th>$ total</th>
    		</tr>
    		<tr>
    			<td><?php echo $totals[0] ?></td>
    			<td><?php echo $totals[1] ?></td>
    		</tr>
    	</table>

    	<table>
    		<tr>
    			<th>Mes</th>
    			<th>Q total</th>
    			<th>$ total</th>
    		</tr>
    		<?php 
    			foreach($by_month as $month) {
    				echo "<tr>";
	    				echo "<td>" . $month[0] . "</td>";
	    				echo "<td>" . $month[1] . "</td>";
	    				echo "<td>" . $month[2] . "</td>";
    				echo "</tr>";
    			}
    		?>
    	</table>
    </body>
</html>