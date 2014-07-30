#!/usr/bin/python
import smbus
import time
import subprocess


# Fonction d'envois d'un texte
def send(text):
	try :
		for ch in text:
			bus.write_byte(address, ord(ch))
		bus.write_byte(address, ord("$"))
	except IOError:
		print "Erreur d'ecriture..."

# Fonction de lecture d'un texte
def read():
	reading = True
	reponse = ""
	# tant qu'on est en lecture la boucle tourne
	while reading:
		try :
			# on lit les caracteres de reponse un par un
			char = chr(bus.read_byte(address))
			# si le caractere $ est lu, c'est la fin de la reponse
			if char == '$':
				reading = False
				break;
			# sinon on continue a lire la reponse
			else:
				reponse += char;

		except IOError:
			reponse = ""
	# Une fois la reponse lu on l'affiche
	return reponse




# Remplacer 0 par 1 si nouveau Raspberry
bus = smbus.SMBus(1)
# Adressse I2C de communication 0x12
address = 0x12



while 1:
	time.sleep(1)
	# envois de la commande ping (demande de badge a verifier) et recuperation du resultat
	send("ping")
	response = read()

	# Traitement du resultat, si un badge est en attente
	if response != '%' and response != '':
		print "check badge..."+response
		# on envois le badge a un script php de yana qui check en base de donnee si il est autorise
		proc = subprocess.Popen(["php domodoor.plugin.php"+" "+response], shell=True, stdout=subprocess.PIPE)
		script_response = proc.stdout.read()
		# on renvois la reponse de php direct a l'arduino 1: ouvre la porte, 0: ne fait rien
		send(script_response)


