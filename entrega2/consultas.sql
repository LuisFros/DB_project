-- 1
SELECT Remitentes.nombre, Remitentes.apellido, Destinatarios.nombre, Destinatarios.apellido, cantidad, mensaje FROM Transaccion, Usuario AS Remitentes, Usuario AS Destinatarios WHERE Remitentes.uid = Transaccion.from_id AND Destinatarios.uid = Transaccion.to_id AND fecha::date = '$fecha' AND from_id = $uid;
-- 2
SELECT tipo, cambio, cambio * $zorsales AS conversion FROM PrecioHistorico WHERE PrecioHistorico.fecha::date = '$fecha';
-- 3
SELECT Remitentes.nombre, Remitentes.apellido, Destinatarios.nombre, Destinatarios.apellido, fecha, cantidad, mensaje FROM Transaccion, Usuario AS Remitentes, Usuario AS Destinatarios WHERE Remitentes.uid = Transaccion.from_id AND Destinatarios.uid = Transaccion.to_id AND from_id = $uid ORDER BY fecha DESC LIMIT 1;
-- 4
SELECT * FROM Usuario WHERE pais LIKE '%$pais%';
-- 5
SELECT tipo, AVG(cambio) FROM PrecioHistorico WHERE (EXTRACT(MONTH FROM fecha) + 1 = EXTRACT(MONTH FROM CURRENT_TIMESTAMP)) GROUP BY tipo;
-- 6
SELECT * FROM (SELECT COUNT(cantidad) AS max_qty, Transaccion.fecha::date AS fecha_max_qty FROM transaccion GROUP BY Transaccion.fecha::date ORDER BY COUNT(cantidad) DESC LIMIT 1) AS A, (SELECT SUM(cantidad) AS max_amount, Transaccion.fecha::date AS fecha_max_amount FROM transaccion GROUP BY Transaccion.fecha::date ORDER BY SUM(cantidad) DESC LIMIT 1) AS B;
-- 7
SELECT A.ingresos, B.gastos, C.inicial, A.ingresos + B.gastos + C.inicial AS balance FROM (SELECT COALESCE(Sum(cantidad), 0) AS ingresos FROM Transaccion WHERE to_id = $uid) AS A, (SELECT COALESCE(SUM(-cantidad), 0) AS gastos FROM Transaccion WHERE from_id = $uid) AS B, (SELECT COALESCE(SUM(Exchange.cantidad / PrecioHistorico.cambio), 0) AS inicial FROM Exchange, PrecioHistorico WHERE Exchange.fecha::date = PrecioHistorico.fecha AND Exchange.tipo = PrecioHistorico.tipo AND Exchange.from_id = $uid) AS C;