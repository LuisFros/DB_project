CREATE OR REPLACE FUNCTION balance(uid integer)
RETURNS real AS
$$
DECLARE
	cursor_income REFCURSOR;
	cursor_outcome REFCURSOR;
	cursor_exchange REFCURSOR;
	tupla RECORD;
	balance real;
BEGIN
	OPEN cursor_income FOR 
		SELECT cantidad AS income
		FROM Transaccion 
		WHERE to_id = uid;

	OPEN cursor_outcome FOR
		SELECT cantidad AS outcome
		FROM Transaccion 
		WHERE from_id = uid;

	OPEN cursor_exchange FOR 
		SELECT Exchange.cantidad / PrecioHistorico.cambio AS initial 
		FROM Exchange, PrecioHistorico 
		WHERE Exchange.fecha::date = PrecioHistorico.fecha 
		AND Exchange.tipo = PrecioHistorico.tipo AND Exchange.from_id = uid;

	balance := 0;

	LOOP
		FETCH cursor_income INTO tupla;
		EXIT WHEN NOT FOUND;
		balance := balance + tupla.income;
	END LOOP;

	LOOP
		FETCH cursor_outcome INTO tupla;
		EXIT WHEN NOT FOUND;
		balance := balance - tupla.outcome;
	END LOOP;

	LOOP
		FETCH cursor_exchange INTO tupla;
		EXIT WHEN NOT FOUND;
		balance := balance + tupla.initial;
	END LOOP;

	RETURN balance;
END
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION all_balances()
RETURNS TABLE(id integer, balance real) AS
$$
BEGIN
	RETURN QUERY SELECT uid, balance(uid) FROM Usuario ORDER BY balance(uid);
	RETURN;
END
$$ LANGUAGE plpgsql;