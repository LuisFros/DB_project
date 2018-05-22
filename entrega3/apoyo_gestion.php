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
	    $result = $db_g1 -> prepare('SELECT * FROM total_transactions()');
	    $result -> execute();
	    $totals_g1 = $result -> fetch();

	    // transacciones totales grupo30
	    $result = $db_g30 -> prepare('SELECT * FROM total_transactions');
	    $result -> execute();
	    $totals_g30 = $result -> fetch();

	    $by_month_data = array();

	    // transacciones por mes
	    for ($mes=1; $mes < 13; $mes++) { 
	    	$g1 = $db_g1 -> prepare("SELECT * FROM month_transactions(" . $mes . ")");
	    	$g30 = $db_g30 -> prepare("SELECT * FROM month_transactions(" . $mes . ")");

	    	$g1 -> execute();
	    	$g30 -> execute();

	    	$by_month_g1 = $g1 -> fetch();
	    	$by_month_g30 = $g30 -> fetch();

	    	array_push($by_month_data, array($mes, $by_month_g1[0] + $by_month_g30[0], $by_month_g1[1] + $by_month_g30[1]));
	    }

	    // estadisticas
	    $balances_g1 = $db_g1 -> query('SELECT * FROM all_balances()');
	    $balances_g30 = $db_g30 -> query('SELECT * FROM all_balances()');

	    $query_subroutine = array('CREATE TABLE aux_table(id integer, balance real);');
	    $len = 0;

	    while ($row = $balances_g1 -> fetch(PDO::FETCH_ASSOC)) {
	    	array_push($query_subroutine, 'INSERT INTO aux_table VALUES (' . $row["id"] . ', ' . $row["balance"] . ');');
	    	$len += 1;
	    }

	  	while($row = $balances_g30 -> fetch(PDO::FETCH_ASSOC)) {
	    	array_push($query_subroutine, 'INSERT INTO aux_table VALUES (' . $row["id"] . ', ' . $row["balance"] . ');');
	    	$len += 1;
	    }

	    foreach($query_subroutine as $query) {
	    	$result = $db_g1 -> prepare($query);
	    	$result -> execute();
	    }

	    // mediana
	    $median_calc = $db_g1 -> query('SELECT * FROM aux_table ORDER BY balance;');

	    if ($len % 2 != 0) {
	    	$target = array(floor($len / 2) + 1);
	    } else {
	    	$target = array(floor($len / 2), floor($len / 2) + 1);
	    }

	    $cont = 0;
	    $median = 0;
	    while ($row = $median_calc -> fetch(PDO::FETCH_ASSOC)) {
	    	$cont += 1;
	    	if ($cont == $target[0]) {
	    		$median = $row["balance"];
	    		if (sizeof($target) == 2) {
	    			$row = $median_calc -> fetch(PDO::FETCH_ASSOC);
	    			$median += $row["balance"];
	    			$median /= 2;
	    		}
	    	}
	    }

	    // resto de resultados
	    $result = $db_g1 -> prepare('SELECT MIN(balance), MAX(balance), AVG(balance), stddev(balance) FROM aux_table;');
	    $result -> execute();
	    $stats = $result -> fetch();

	    $result = $db_g1 -> prepare('DROP TABLE aux_table;');
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
    			foreach($by_month_data as $data) {
    				echo "<tr>";
	    				echo "<td>" . $data[0] . "</td>";
	    				echo "<td>" . $data[1] . "</td>";
	    				echo "<td>" . $data[2] . "</td>";
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

	    			echo "<td>" . number_format($median, 2) . "</td>";
	    		?>
	    	</tr>
    	</table>
    </body>
</html>