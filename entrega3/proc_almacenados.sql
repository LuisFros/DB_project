CREATE OR REPLACE FUNCTION qty_monthly(month integer, _group integer)
RETURNS integer AS
$$
DECLARE
	ret integer;
BEGIN
	IF _group = 1 THEN
		SELECT INTO ret COUNT(cantidad) AS qty
		FROM Transaccion 
		WHERE EXTRACT(MONTH FROM fecha) = month;
	ELSE
		SELECT INTO ret COUNT(quantity) AS qty
		FROM Transaccion 
		WHERE EXTRACT(MONTH FROM date) = month;
	END IF;

	RETURN ret;
END
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION cash_monthly(month integer, _group integer)
RETURNS real AS
$$
DECLARE
	ret real;
BEGIN
	IF _group = 1 THEN
		SELECT INTO ret COALESCE(SUM(cantidad), 0) AS cash 
		FROM Transaccion 
		WHERE EXTRACT(MONTH FROM fecha) = month;
	ELSE
		SELECT INTO ret COALESCE(SUM(quantity), 0) AS cash 
		FROM Transaccion 
		WHERE EXTRACT(MONTH FROM date) = month;
	END IF;

	RETURN ret;
END
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION qty_cash_monthly(month integer, _group integer)
RETURNS TABLE(transactions_quantity integer, total_cash real) AS
$$
BEGIN
	RETURN QUERY SELECT qty_monthly(month, _group), cash_monthly(month, _group);
	RETURN ;
END
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION qty_cash_by_month(_group integer)
RETURNS TABLE(month integer, transactions_quantity integer, total_cash real) AS
$$
BEGIN
	CREATE TEMP TABLE IF NOT EXISTS aux_table AS
    SELECT 1, qty_monthly(1, _group), cash_monthly(1, _group);

	FOR mes IN 2..12 LOOP
		INSERT INTO aux_table VALUES (mes, qty_monthly(mes, _group), cash_monthly(mes, _group));
	END LOOP;

	RETURN QUERY SELECT * FROM aux_table;
	DROP TABLE aux_table;
	RETURN;	
END
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION qty_cash_total(_group integer)
RETURNS TABLE(transactions_quantity integer, total_cash real) AS
$$
DECLARE
	qty integer;
	cash real;
BEGIN
	qty := 0;
	cash := 0;

	FOR mes IN 1..12 LOOP
		qty := qty + qty_monthly(mes, _group);
		cash := cash + cash_monthly(mes, _group);
	END LOOP;

	RETURN QUERY SELECT qty, cash;
END
$$ LANGUAGE plpgsql;
