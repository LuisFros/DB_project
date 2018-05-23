CREATE OR REPLACE FUNCTION get_mybalance (uid integer,db1 boolean)
RETURNS TABLE(balance REAL)AS $$
BEGIN
IF db1  THEN
	RETURN QUERY SELECT A.ingresos + B.gastos + C.inicial AS balance 
	FROM (SELECT COALESCE(Sum(cantidad), 0) AS ingresos 
		FROM Transaccion 
		WHERE to_id = uid) AS A, 
	(SELECT COALESCE(SUM(-cantidad), 0) AS gastos 
		FROM Transaccion 
		WHERE from_id = uid) AS B, 
	(SELECT COALESCE(SUM(Exchange.cantidad / PrecioHistorico.cambio), 0) AS inicial 
		FROM Exchange, PrecioHistorico 
		WHERE Exchange.fecha::date = PrecioHistorico.fecha 
		AND Exchange.tipo = PrecioHistorico.tipo AND Exchange.from_id = uid) AS C;
ELSE
	RETURN QUERY SELECT (t3.inicial +t1.p1-t2.p2) AS Cantidad
            FROM (SELECT COALESCE(SUM(quantity),0) AS p1
            FROM transaccion
            WHERE transaccion.id_in=uid) AS t1
            ,(SELECT COALESCE(SUM(quantity),0) AS p2
            FROM transaccion
            WHERE transaccion.id_out=uid ) AS t2
            ,(SELECT COALESCE(SUM(exchange.quantity / zorzalcoin.value), 0) AS inicial 
		FROM exchange, zorzalcoin
		WHERE exchange.date= zorzalcoin.date
		AND exchange.currency_type = zorzalcoin.currency_type AND exchange.id_buy = uid) AS t3;
END IF;
END $$
language plpgsql
