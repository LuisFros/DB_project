-- Crear tablas con primaries
CREATE TABLE Usuario(uid int PRIMARY KEY, nombre varchar(30), apellido varchar(30), email varchar(30), telefono varchar(30), pais varchar(30));
CREATE TABLE PrecioHistorico(fecha date, tipo varchar(10), cambio float, PRIMARY KEY(fecha, tipo));
CREATE TABLE Transaccion(from_id int, to_id int, fecha timestamp, mensaje varchar(255), cantidad float, PRIMARY KEY(from_id, to_id, fecha));
CREATE TABLE Exchange(from_id int, to_id int, fecha timestamp, tipo varchar(10), cantidad float, PRIMARY KEY(from_id, to_id, fecha));
-- agregar foreigns
ALTER TABLE Exchange ADD CONSTRAINT foreign_from FOREIGN KEY (from_id) REFERENCES Usuario(uid);
ALTER TABLE Exchange ADD CONSTRAINT foreign_to FOREIGN KEY (to_id) REFERENCES Usuario(uid);
ALTER TABLE Transaccion ADD CONSTRAINT foreign_from FOREIGN KEY (from_id) REFERENCES Usuario(uid);
ALTER TABLE Transaccion ADD CONSTRAINT foreign_to FOREIGN KEY (to_id) REFERENCES Usuario(uid);
-- >cd al directorio con los csvs y copiar datos
\copy Usuario FROM 'usuario.csv' DELIMITER ';';
\copy PrecioHistorico FROM 'preciohistorico.csv' DELIMITER ';';
\copy Exchange FROM 'exchange.csv' DELIMITER ';';
\copy Transaccion FROM 'transaccion.csv' DELIMITER ';';
