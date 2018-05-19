CREATE OR REPLACE FUNCTION qty_cash_monthly(month integer)
RETURNS TABLE(transactions_quantity integer, total_cash real) AS
$$
BEGIN
	RETURN QUERY SELECT qty_monthly(month), cash_monthly(month);
	RETURN ;
END
$$ LANGUAGE plpgsql;
