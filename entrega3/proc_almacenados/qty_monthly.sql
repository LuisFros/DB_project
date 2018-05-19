CREATE OR REPLACE FUNCTION qty_monthly(month integer)
RETURNS integer AS
$$
DECLARE
	ret integer;
BEGIN
	SELECT INTO ret COUNT(cantidad) AS qty
	FROM Transaccion 
	WHERE EXTRACT(MONTH FROM fecha) = month;

	RETURN ret;
END
$$ LANGUAGE plpgsql;