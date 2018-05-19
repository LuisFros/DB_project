CREATE OR REPLACE FUNCTION qty_cash_total()
RETURNS TABLE(transactions_quantity integer, total_cash real) AS
$$
DECLARE
	qty integer;
	cash real;
BEGIN
	qty := 0;
	cash := 0;

	FOR mes IN 1..12 LOOP
		qty := qty + qty_monthly(mes);
		cash := cash + cash_monthly(mes);
	END LOOP;

	RETURN QUERY SELECT qty, cash;
END
$$ LANGUAGE plpgsql;
