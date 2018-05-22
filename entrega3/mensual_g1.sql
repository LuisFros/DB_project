CREATE OR REPLACE FUNCTION month_transactions(month integer)
RETURNS TABLE(qty integer, cash real) AS
$$
DECLARE
	cursor REFCURSOR;
	tupla RECORD;
	cash real;
	qty integer;
BEGIN
	OPEN cursor FOR SELECT *
					FROM Transaccion
					WHERE EXTRACT(MONTH FROM fecha) = month;
	cash := 0;
	qty := 0;

	LOOP
		FETCH cursor INTO tupla;
		EXIT WHEN NOT FOUND;
		cash := cash + tupla.cantidad;
		qty := qty + 1;
	END LOOP;

	RETURN QUERY SELECT qty, cash;
	RETURN;
END
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION total_transactions()
RETURNS TABLE(qty integer, cash real) AS
$$
DECLARE
	cursor REFCURSOR;
	tupla RECORD;
	qty integer;
	cash real;
BEGIN
	OPEN cursor FOR SELECT * FROM Transaccion;
	qty := 0;
	cash := 0;

	LOOP
		FETCH cursor INTO tupla;
		EXIT WHEN NOT FOUND;
		cash := cash + tupla.cantidad;
		qty := qty + 1;
	END LOOP;

	RETURN QUERY SELECT qty, cash;
	RETURN;
END
$$ LANGUAGE plpgsql;
