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
		SELECT quantity AS income
		FROM Transaccion 
		WHERE id_in = uid;

	OPEN cursor_outcome FOR
		SELECT quantity AS outcome
		FROM Transaccion 
		WHERE id_out = uid;

	OPEN cursor_exchange FOR 
		SELECT Exchange.quantity / Zorzalcoin.value AS initial 
		FROM Exchange, Zorzalcoin 
		WHERE Exchange.date = Zorzalcoin.date 
		AND Exchange.currency_type = Zorzalcoin.currency_type AND Exchange.id_buy = uid;

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
	RETURN QUERY SELECT Usuarios.id, balance(Usuarios.id) FROM Usuarios ORDER BY balance(Usuarios.id);
	RETURN;
END
$$ LANGUAGE plpgsql;