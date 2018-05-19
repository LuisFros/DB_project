import csv
import random


class Usuario:
    _id = 0
    _usuarios = {}

    def __init__(self, name, phone, country, gender, username, password):
        self.username, self.password, self.gender = username, password, gender
        self.country, self.phone, self.name = country, phone, name
        self.coins = 0
        self.id = Usuario._id
        Usuario._id = Usuario._id + 1
        Usuario._usuarios[self.id] = self

    def __repr__(self):
        return "{},{},{},{},{},{},{}".format(self.id, self.username, self.name,
                                             self.phone,
                                             self.country,
                                             self.gender,
                                             self.password)


class Zorzalcoin:
    def __init__(self, date, value, type):
        self.date, self.value, self.type = date, value, type

    def __repr__(self):
        return self.date + "," + self.type + "," + str(self.value)
        # e_id ----> id_buy, id_sell, value, currency, date


class Exchange:
    _id = 0

    def __init__(self, id_buy, id_sell, quantity, currency_type
                 , date):
        self.id_buy, self.id_sell = id_buy, id_sell
        self.quantity, self.currency_type, self.date = quantity, \
                                                       currency_type, date
        self.e_id = Exchange._id
        Exchange._id += 1

    def __repr__(self):
        return "{},{},{},{},{},{}".format(self.e_id, self.id_buy, self.id_sell,
                                          self.quantity,
                                          self.currency_type,
                                          self.date)


class Transaction:
    _id = 0
    _transactions = {}

    def __init__(self, id_out, id_in, date, quantity):
        self.id_in, self.id_out = id_in, id_out
        self.date, self.quantity = date, quantity

        self.tid = Transaction._id
        Transaction._id += 1
        Transaction._transactions[self.tid] = self

    def __repr__(self):
        return "{},{},{},{},{}".format(self.tid, self.date, self.id_out,
                                       self.id_in,
                                       self.quantity)


def generar_precios():
    precios = {}
    for date in range(1, 31):
        i = date
        if date < 10:
            date = "0" + str(date)
        value_usd = random.randint(8000, 16000)
        aux_1 = Zorzalcoin("2018-03-{}".format(date), value_usd, "USD")
        aux_2 = Zorzalcoin("2018-03-{}".format(date), value_usd * 600, "CLP")
        precios[i * 100] = aux_1
        precios[(i * 100) + 1] = aux_2

    return precios


def generar_exchanges():
    exchanges = {}
    date = "01"
    for user in range(0, 10):  # todos los usuarios
        cuanto = random.randint(100000, 6000000)
        while True:
            a_quien = random.randint(0, 20)
            if a_quien != user:
                break
        clp1 = Exchange(user, a_quien, cuanto, "CLP",
                        "2018-03-{}".format(
                            date
                        ))
        exchanges[user] = clp1
    for user in range(10, 21):
        cuanto = random.randint(100, 60000)
        while True:
            a_quien = random.randint(0, 20)
            if a_quien != user:
                break

        usd1 = Exchange(user, a_quien, cuanto, "USD",
                        "2018-03-{}".format(
                            date
                        ))
        exchanges[user] = usd1
    return exchanges


def generar_transaccion():
    for date in range(1, 31):
        if date < 10:
            date = "0" + str(date)
        zorzales = random.uniform(0, 1)
        while True:
            usuario_in = random.randint(0, 20)
            usuario_out = random.randint(0, 20)
            if usuario_in != usuario_out:
                break
        Transaction(usuario_in, usuario_out, "2018-03-{}".format(date),
                    zorzales)


def escribir_archivo_1(datos, header, nombre):
    with open(nombre + ".csv", "w") as file:
        file.write(header + "\n")
        for key in datos:
            file.write(repr(datos[key]) + "\n")


if __name__ == '__main__':
    u1 = Usuario("Luis Miguel Fros", 942873048, "Chile", "M", "lmfros",
                 "lmfros")

    u2 = Usuario("Mauricio Lopez", 961717169, "Chile", "M", "malopez",
                 "admin")

    u3 = Usuario("Catalina Perez", 969191431, "Estados Unidos", "F",
                 "cperez",
                 "pasteldechoclo")

    u4 = Usuario("Mauricio Valdivia", 910192921, "Chile", "M", "mavaldivia",
                 "69vB")

    u5 = Usuario("Luis Mario Fros", 912312352, "Chile", "M", "lfros",
                 "lfros")

    u6 = Usuario("Mauricio Lopez Carlos", 921727169, "Uruguay", "M",
                 "mcarlos",
                 "mcarlos")

    u7 = Usuario("Beatriz Perez", 969164331, "Bolivia", "F", "bperez",
                 "bspasteles")

    u8 = Usuario("Pedro Valdivia", 911111111, "Chile", "M", "pavaldivia",
                 "aaa444c")
    u9 = Usuario("Miguel Frost", 942873049, "Chile", "M", "mfrost",
                 "mfrost")

    u10 = Usuario("Maria Lopez", 914927928, "Chile", "F", "mlopez",
                  "adminitna")

    u11 = Usuario("Cristian Perez", 969111431, "Peru", "F", "cristperez",
                  "huevofrito")

    u12 = Usuario("Julian Valdivia", 910192929, "Chile", "M", "juvaldivia",
                  "69v99999")
    u13 = Usuario("Luisa Miguela Arroz", 945873048, "Chile", "M", "lmarroz",
                  "lmarroz")

    u14 = Usuario("Mora Cropez", 901717169, "Chile", "f", "mocropez",
                  "admin")

    u15 = Usuario("Cataldo Perez", 911191431, "Paraguay", "M", "catperez",
                  "pateldechoclo")

    u16 = Usuario("Osvaldo Valdivion", 910192900, "Chile", "M",
                  "osvaldivia",
                  "69vB1234")
    u17 = Usuario("Santiago Fros", 952873048, "Chile", "M", "safros",
                  "safros")

    u18 = Usuario("Mauricio Topez", 801717169, "Chile", "M", "malopez",
                  "admin")

    u19 = Usuario("Fabiana Perez", 909101031, "Suecia", "F", "fbperez",
                  "humita")

    u20 = Usuario("Mauricio Paralelepipedo", 999929921, "Chile", "M",
                  "pitagoras",
                  "cateto")
    generar_transaccion()
    escribir_archivo_1(Usuario._usuarios,
                       "id,username,name,phone,country,gender,password",
                       "Usuarios")
    escribir_archivo_1(Transaction._transactions,
                       "tid,date,id_out,id_in,quantity", "Transaccion")
    escribir_archivo_1(generar_exchanges(),
                       "e_id,id_buy,id_sell,quantity,currency_type,date",
                       "Exchange")
    escribir_archivo_1(generar_precios(),
                       "date,currency_type,value",
                       "Zorzalcoin")
    

