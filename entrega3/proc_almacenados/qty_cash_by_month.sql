CREATE OR REPLACE FUNCTION qty_cash_by_month()
RETURNS TABLE(month integer, transactions_quantity integer, total_cash real) AS
$$
BEGIN
	CREATE TEMP TABLE IF NOT EXISTS aux_table AS
    SELECT 1, qty_monthly(1), cash_monthly(1);

	FOR mes IN 2..12 LOOP
		INSERT INTO aux_table VALUES (mes, qty_monthly(mes), cash_monthly(mes));
	END LOOP;

	RETURN QUERY SELECT * FROM aux_table;
	DROP TABLE aux_table;
END
$$ LANGUAGE plpgsql;
