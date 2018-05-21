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
	    $result = $db_g1 -> prepare('SELECT * FROM qty_cash_total(1)');
	    $result -> execute();
	    $totals_g1 = $result -> fetch();

	    // transacciones totales grupo30
	    $result = $db_g30 -> prepare('SELECT * FROM qty_cash_total(30)');
	    $result -> execute();
	    $totals_g30 = $result -> fetch();

	    // transacciones por mes grupo1
	    $result = $db_g1 -> prepare('SELECT * FROM qty_cash_by_month(1)');
	    $result -> execute();
	    $by_month_g1 = $result -> fetchAll();

	    // transacciones por mes grupo30
	    $result = $db_g30 -> prepare('SELECT * FROM qty_cash_by_month(30)');
	    $result -> execute();
	    $by_month_g30 = $result -> fetchAll();

	    // estadisticas
	    $result = $db_g1 -> prepare('SELECT * FROM all_balances(1)');
	    $result -> execute();
	    $balances_g1 = $result -> fetchAll();

	    $result = $db_g30 -> prepare('SELECT * FROM all_balances(30)');
	    $result -> execute();
	    $balances_g30 = $result -> fetchAll();


	    $query_subroutine = array('CREATE TABLE aux_table(id integer, balance real);');
	    foreach($balances_g1 as $balance) {
	    	array_push($query_subroutine, 'INSERT INTO aux_table VALUES (' . $balance[0] . ', ' . $balance[1] . ');');
	    }
	  	foreach($balances_g30 as $balance) {
	    	array_push($query_subroutine, 'INSERT INTO aux_table VALUES (' . $balance[0] . ', ' . $balance[1] . ');');
	    }

	    foreach($query_subroutine as $query) {
	    	$result = $db_g1 -> prepare($query);
	    	$result -> execute();
	    }

	    $result = $db_g1 -> prepare('SELECT * FROM aux_table ORDER BY balance;');
	    $result -> execute();
	    $balances_global = $result -> fetchAll();

	    $result = $db_g1 -> prepare('SELECT MIN(balance), MAX(balance), AVG(balance), stddev(balance) FROM aux_table;');
	    $result -> execute();
	    $stats = $result -> fetch();

	    $len = sizeof($balances_global);
	    if ($len % 2 == 0) {
	    	$median = ($balances_global[floor($len/2) - 1][1] + $balances_global[floor($len/2)][1]) / 2;
	    } else {
	    	$median = $balances_global[$len/2][1];
	    }

	    array_push($stats, $median);

		$result = $db_g1 -> prepare('DROP TABLE aux_table');
	    $result -> execute();

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

    	<table>
    		<tr>
    			<th>min</th>
    			<th>max</th>
    			<th>avg</th>
    			<th>std</th>
    			<th>med</th>
    		</tr>
    		<tr>
	    		<?php
	    			foreach(array_unique($stats) as $stat) {
	    				echo "<td>" . number_format($stat, 2) . "</td>";
	    			}
	    		?>
	    	</tr>
    	</table>
    </body>
</html>