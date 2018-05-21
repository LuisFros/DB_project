import names
import random
import pycountry
import message_generator
import time
import datetime


def strTimeProp(start, end, format, prop):
    stime = time.mktime(time.strptime(start, format))
    etime = time.mktime(time.strptime(end, format))
    ptime = stime + prop * (etime - stime)
    return time.strftime(format, time.localtime(ptime))


def daterange(start_date, end_date):
    for n in range(int((end_date - start_date).days)):
        yield start_date + datetime.timedelta(n)


def randomDate(start, end, prop):
    return strTimeProp(start, end, '%Y-%m-%d %H:%M:%S', prop)


def number_gen():
    num = "+569"
    for i in range(8):
        num += str(random.choice(list(range(10))))
    return num


def id_gen():
    _id = 0
    while True:
        yield _id
        _id += 1


class User:

    _id = id_gen()

    def __init__(self, nombre, apellido, email, pais):
        self.uid = next(self._id)
        self.nombre = nombre
        self.apellido = apellido
        self.email = email
        self.pais = pais
        self.telefono = number_gen()

    def header(self):
        return "uid,nombre,apellido,email,telefono,pais\n"

    def __repr__(self):
        return "{};{};{};{};{};{}\n".format(self.uid, self.nombre, self.apellido,
                                            self.email, self.telefono, self.country)


class Exchange:
    def __init__(self, from_id, to_id, date, tipo, cantidad):
        self.from_id = int(from_id)
        self.to_id = int(to_id)
        self.fecha = date
        self.tipo = tipo
        self.cantidad = float(cantidad)

    def header(self):
        return "from_id,to_id,fecha,tipo,cantidad\n"

    def __repr__(self):
        return "{};{};{};{};{}\n".format(self.from_id, self.to_id, self.fecha,
                                         self.tipo, self.cantidad)


class PrecioHistorico:
    def __init__(self, fecha, tipo, cantidad):
        self.fecha = fecha
        self.tipo = tipo
        self.cantidad = float(cantidad)

    def header(self):
        return "fecha,tipo,cantidad\n"

    def __repr__(self):
        return "{};{};{}\n".format(self.fecha, self.tipo, self.cantidad)


class Simulation:
    def __init__(self, start_date, end_date):
        self.start = start_date
        self.end = end_date
        self.users = list()
        self.transactions = list()
        self.exchanges = list()
        self.precios_historicos = list()

        self.run(e_qty=200)

    def run(self, u_qty=30, e_qty=30, t_qty=30):
    	self.generate_precios_historicos()
    	self.generate_users(u_qty)
    	self.generate_exchanges(e_qty)
    	#self.generate_transactions(t_qty)

    def generate_users(self, u_qty):
        for uid in range(u_qty):
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
            self.users.append(User(nombre, apellido, email, country))

    def generate_exchanges(self, e_qty):
        for eid in range(e_qty):
            date = randomDate("{} 00:00:00".format(self.start),
                              "{} 00:00:00".format(self.end), random.random())
            from_id = random.choice(
                list(map(lambda usr: usr.uid, self.users))[1:])
            to_id = 0
            tipo = random.choice(["clp", "usd"])
            if tipo == "usd":
                cantidad = round(random.uniform(100, 300), 2)
            else:
                cantidad = float(random.randint(20000, 200000))
            self.exchanges.append(
                Exchange(from_id, to_id, date, tipo, cantidad))

    def generate_precios_historicos(self):
        tipos = ["usd", "clp"]
        actual = {"usd": 10.12, "clp": 6233}
        for fecha in daterange(self.start, self.end):
            for tipo in tipos:
            	self.precios_historicos.append(PrecioHistorico(fecha, tipo, round(actual[tipo], 2)))
            actual["usd"] += random.uniform(-1, 2)
            actual["clp"] += random.randint(-500, 1250)
            

    def generate_transactions(self, t_qty):
        with open("data/exchange.csv") as file:
            users = dict()
            for line in file:
                line = line.strip().split(";")
                if line[0] not in users:
                    users[line[0]] = [
                        (float(line[4]), line[3], line[2].split(" ")[0])]
                else:
                    users[line[0]].append(
                        (float(line[4]), line[3], line[2].split(" ")[0]))
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
                    tipo_cambio_vigente = tipos_de_cambio[exchange[2]
                                                          ][exchange[1]]
                    saldos[int(user)] = round(
                        exchange[0] / tipo_cambio_vigente, 2)
        with open("data/transactions.csv", "w", encoding="utf-8") as file:
            string = ""
            for tid in range(20):
                date = randomDate("2018-03-25 01:00:00",
                                  "2018-04-08 01:00:00", random.random())
                from_id = -1
                while from_id not in saldos:
                    uids = list(range(20))
                    random.shuffle(uids)
                    from_id, to_id = uids[0:2]
                cantidad = round(random.uniform(0, saldos[from_id]), 2)
                mensaje = message_generator.random_message()
                string += "{};{};{};{};{}\n".format(
                    from_id, to_id, date, mensaje, cantidad)
                saldos[from_id] -= cantidad
                if to_id not in saldos:
                    saldos[to_id] = cantidad
                else:
                    saldos[to_id] += cantidad
            file.write(string)


sim = Simulation(datetime.date(2018, 4, 1), datetime.date(2018, 8, 1))
