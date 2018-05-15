import names, random, pycountry, message_generator, time

def strTimeProp(start, end, format, prop):

    stime = time.mktime(time.strptime(start, format))
    etime = time.mktime(time.strptime(end, format))

    ptime = stime + prop * (etime - stime)

    return time.strftime(format, time.localtime(ptime))


def randomDate(start, end, prop):
    return strTimeProp(start, end, '%Y-%m-%d %H:%M:%S', prop)

def number_gen():
	num = "+569"
	for i in range(8):
		num += str(random.choice(list(range(10))))
	return num

def generate_users():
	with open("data/users.csv", "w", encoding="utf-8") as file:
		# string = "uid;nombre;apellido;email;telefono;pais\n"
		string = ""
		for uid in range(20):
			if uid == 0:
				nombre, apellido = "Administrador", "Zorzal"
				email = "admin@zorzal.cl"
				country = "Chile"
			else:
				nombre, apellido = names.get_full_name().split(" ")
				email = (nombre[0] + apellido).lower() + "@uc.cl"
				country = random.choice(list(pycountry.countries)).name
				if len(country) > 30:
					country = "Chile"
			string += "{};{};{};{};{};{}\n".format(uid, nombre, apellido, email, number_gen(), country)
		file.write(string)

def generate_exchange():
	with open("data/exchange.csv", "w", encoding="utf-8") as file:
		# string = "from_id;to_id;fecha;tipo;cantidad\n"
		string = ""
		for eid in range(20):
			date = randomDate("2018-03-25 01:00:00", "2018-04-08 01:00:00", random.random())
			from_id = random.choice(list(range(1, 20)))
			to_id = 0
			tipo = random.choice(["clp", "usd"])
			if tipo == "usd":
				cantidad = round(random.uniform(100, 300), 2)
			else:
				cantidad = float(random.randint(20000, 200000))
			string += "{};{};{};{};{}\n".format(from_id, to_id, date, tipo, cantidad)
		file.write(string)

def generate_precio_historico():
	with open("data/precio_historico.csv", "w", encoding="utf-8") as file:
		string = ""
		tipos = ["usd", "clp"]
		actual = {"usd": 10.12, "clp": 6233}
		marzo = True
		mes = "03"
		for day in range(0, 15):
			if marzo:
				dia = day + 25
			else:
				dia = day - 6
			fecha = "2018-{}-{}".format(mes, str(dia).zfill(2))
			for tipo in tipos:
				string += "{};{};{}\n".format(fecha, tipo, round(actual[tipo], 2))
			actual["usd"] += random.uniform(-1, 2)
			actual["clp"] += random.randint(-500, 1250)
			if day == 6:
				marzo = False
				mes = "04"
		file.write(string)

def generate_transactions():
	with open("data/exchange.csv") as file:
		users = dict()
		for line in file:
			line = line.strip().split(";")
			if line[0] not in users:
				users[line[0]] = [(float(line[4]), line[3], line[2].split(" ")[0])]
			else:
				users[line[0]].append((float(line[4]), line[3], line[2].split(" ")[0]))
	with open("data/precio_historico.csv") as file:
		saldos = dict()
		tipos_de_cambio = dict()
		for line in file:
			line = line.strip().split(";")
			if line[0] not in tipos_de_cambio:
				tipos_de_cambio[line[0]] = {line[1]: float(line[2])}
			else:
				tipos_de_cambio[line[0]].update({line[1]: float(line[2])})
		for user, exchanges in users.items():
			for exchange in exchanges:
				tipo_cambio_vigente = tipos_de_cambio[exchange[2]][exchange[1]]
				saldos[int(user)] = round(exchange[0] / tipo_cambio_vigente, 2)
	with open("data/transactions.csv", "w", encoding="utf-8") as file:
		string = ""
		for tid in range(20):
			date = randomDate("2018-03-25 01:00:00", "2018-04-08 01:00:00", random.random())
			from_id = -1
			while from_id not in saldos:
				uids = list(range(20))
				random.shuffle(uids)
				from_id, to_id = uids[0:2]
			cantidad = round(random.uniform(0, saldos[from_id]), 2)
			mensaje = message_generator.random_message()
			string += "{};{};{};{};{}\n".format(from_id, to_id, date, mensaje, cantidad)
			saldos[from_id] -= cantidad
			if to_id not in saldos:
				saldos[to_id] = cantidad
			else:
				saldos[to_id] += cantidad
		file.write(string)

generate_users()
generate_exchange()
generate_precio_historico()
generate_transactions()