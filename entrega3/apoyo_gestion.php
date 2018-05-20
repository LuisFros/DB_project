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

	    // transacciones totales grupo1
	   	$totals_g1 = 'SELECT * FROM qty_cash_total(1)';
	    $result = $db_g1 -> prepare($totals_g1);
	    $result -> execute();
	    $totals_g1 = $result -> fetch();

	    // transacciones totales grupo30
	   	$totals_g30 = 'SELECT * FROM qty_cash_total(30)';
	    $result = $db_g30 -> prepare($totals_g30);
	    $result -> execute();
	    $totals_g30 = $result -> fetch();

	    // transacciones por mes grupo1
	   	$by_month_g1 = 'SELECT * FROM qty_cash_by_month(1)';
	    $result = $db_g1 -> prepare($by_month_g1);
	    $result -> execute();
	    $by_month_g1 = $result -> fetchAll();

	    // transacciones por mes grupo30
	   	$by_month_g30 = 'SELECT * FROM qty_cash_by_month(30)';
	    $result = $db_g30 -> prepare($by_month_g30);
	    $result -> execute();
	    $by_month_g30 = $result -> fetchAll();
	?>
    <body>
    	<h1> Apoyo a la gestión </h1>
    	<table>
    		<tr>
    			<th>Q total</th>
    			<th>$ total</th>
    		</tr>
    		<tr>
    			<td><?php echo ($totals_g1[0] + $totals_g30[0]) ?></td>
    			<td><?php echo ($totals_g1[1] + $totals_g30[1]) ?></td>
    		</tr>
    	</table>

    	<table>
    		<tr>
    			<th>Mes</th>
    			<th>Q total</th>
    			<th>$ total</th>
    		</tr>
    		<?php
    			foreach(array_map(null, $by_month_g1, $by_month_g30) as $pair) {
    				$month_g1 = $pair[0];
    				$month_g30 = $pair[1];
    				echo "<tr>";
	    				echo "<td>" . $month_g1[0] . "</td>";
	    				echo "<td>" . ($month_g1[1] + $month_g30[1]) . "</td>";
	    				echo "<td>" . ($month_g1[2] + $month_g30[2]) . "</td>";
    				echo "</tr>";
    			}
    		?>
    	</table>
    </body>
</html>