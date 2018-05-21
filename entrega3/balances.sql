CREATE OR REPLACE FUNCTION balance(uid integer, _group integer)
RETURNS real AS
$$
DECLARE
	ret real;
BEGIN
	IF _group = 1 THEN
		SELECT INTO ret CAST(A.ingresos + B.gastos + C.inicial AS real) AS balance 
		FROM (
 			SELECT COALESCE(Sum(cantidad), 0) AS ingresos 
			FROM Transaccion 
			WHERE to_id = uid
		) AS A, (
			SELECT COALESCE(SUM(-cantidad), 0) AS gastos 
			FROM Transaccion 
			WHERE from_id = uid
		) AS B, (
			SELECT COALESCE(SUM(Exchange.cantidad / PrecioHistorico.cambio), 0) AS inicial 
			FROM Exchange, PrecioHistorico 
			WHERE Exchange.fecha::date = PrecioHistorico.fecha 
			AND Exchange.tipo = PrecioHistorico.tipo AND Exchange.from_id = uid
		) AS C;
	ELSE 
		SELECT INTO ret CAST(t3.inicial + t1.p1 - t2.p2 AS real) AS Cantidad
		FROM ( 
			SELECT COALESCE(SUM(quantity),0) AS p1
			FROM transaccion
			WHERE transaccion.id_in = uid
		) AS t1, (
			SELECT COALESCE(SUM(quantity),0) AS p2
			FROM transaccion
			WHERE transaccion.id_out = uid
		) AS t2, (
			SELECT COALESCE(SUM(exchange.quantity / zorzalcoin.value), 0) AS inicial 
			FROM exchange, zorzalcoin
			WHERE exchange.date = zorzalcoin.date
			AND exchange.currency_type = zorzalcoin.currency_type AND exchange.id_buy = uid
		) AS t3;
	END IF;

	RETURN ret;
END
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION all_balances(_group integer)
RETURNS TABLE(id integer, balance real) AS
$$
BEGIN
	IF _group = 1 THEN
		RETURN QUERY SELECT uid, balance(uid, _group) FROM Usuario;
	ELSE
		RETURN QUERY SELECT Usuarios.id, balance(Usuarios.id, _group) FROM Usuarios;
	END IF;

	RETURN;
END
$$ LANGUAGE plpgsql;

-- CREATE OR REPLACE FUNCTION sorted_balances(_group integer)
-- RETURNS TABLE(cnt integer, id integer, balance real) AS
-- $$
-- DECLARE
-- 	tupla RECORD;
-- 	cnt integer;
-- BEGIN
-- 	cnt := 1;
-- 	CREATE TEMP TABLE IF NOT EXISTS aux_table AS SELECT 1 AS cnt, 1 AS id, CAST(1 AS real) AS balance;
-- 	DELETE FROM aux_table;

-- 	FOR tupla in SELECT * FROM all_balances(_group) ORDER BY balance LOOP
-- 		INSERT INTO aux_table VALUES (cnt, tupla.id, tupla.balance);
-- 		cnt := cnt + 1;
-- 	END LOOP; 

-- 	RETURN QUERY SELECT * FROM aux_table;
-- 	DROP TABLE aux_table;
-- 	RETURN;
-- END
-- $$ LANGUAGE plpgsql;

-- CREATE OR REPLACE FUNCTION median(_group integer)
-- RETURNS real AS
-- $$
-- DECLARE
-- 	len integer;
-- 	ret real;
-- BEGIN
-- 	SELECT INTO len COUNT(*) 
-- 	FROM sorted_balances(_group);

-- 	IF len % 2 = 0 THEN
-- 		SELECT INTO ret AVG(balance)
-- 		FROM sorted_balances(_group) 
-- 		WHERE cnt = div(len, 2) OR cnt = div(len, 2) + 1;
-- 	ELSE
-- 		SELECT INTO ret balance 
-- 		FROM sorted_balances(_group)
-- 		WHERE cnt = div(len, 2) + 1;
-- 	END IF;

-- 	RETURN ret;
-- END
-- $$ LANGUAGE plpgsql;

-- CREATE OR REPLACE FUNCTION statistics(_group integer)
-- RETURNS TABLE(avg real, median real, min real, max real, std real) AS
-- $$
-- BEGIN
-- 	RETURN QUERY SELECT AVG(balance), median(_group), MIN(balance), MAX(balance), stddev(balance)
-- 				 FROM all_balances(_group);

-- END
-- $$ LANGUAGE plpgsql;