CREATE OR REPLACE FUNCTION cash_monthly(month integer)
RETURNS real AS
$$
DECLARE
	ret real;
BEGIN
	SELECT INTO ret COALESCE(SUM(cantidad), 0) AS cash 
	FROM Transaccion 
	WHERE EXTRACT(MONTH FROM fecha) = month;

	RETURN ret;
END
$$ LANGUAGE plpgsql;