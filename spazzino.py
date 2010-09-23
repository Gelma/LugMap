#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""Per ogni Lug indicato nella LugMap effettuo un insieme di controlli di validità.
   Se qualcosa non torna, avverto chi di dovere"""

if True: # import dei moduli
	try: # quelli builtin
		import ConfigParser
		import csv
		import datetime
		import glob
		import multiprocessing
		import os
		import random
		import re
		import shelve
		import shlex
		import subprocess
		import socket
		import sys
		import time
	except:
		import sys
		print "Errore nell'import dei moduli standard. Versione troppo vecchia dell'interprete?"
		sys.exit(-1)

def controllo_dominio_dns(url):
	"""Ricevo un URL, estraggo il dominio, torno True se tutto torna. Diversamente emetto il messaggio da segnalare"""
	
	global archivio, url_completo

	url = url.split('/')[2] # estraggo solo il nome di dominio	# TODO: una regexp sarebbe più elegante

	try:
		info_url = socket.getaddrinfo(url, 80, 0, 0, socket.SOL_TCP)
	except:
		return "Errore: problema sul dominio (esistenza/mappatura)"

	IP_nuovo = info_url[0][4][0] # Inizio controllo IP cambiato. Possono esserci dei falsi positivi in caso di round robin.
	
	try: # Pesco il vecchio iP
		IP_vecchi = archivio[url_completo]['IP']
	except: # Non esiste ancora una voce relativa, la creiamo
		archivio[url_completo] = {'IP': set([IP_nuovo])}
		return True

	if IP_nuovo in IP_vecchi: # se è gia' presente, me ne fotto
		return True
	else: # diversamente segnalo e aggiungo
		archivio[url_completo]['IP'].add(IP_nuovo)
		return 'Errore: cambiato IP del server (Vecchio: %s, Nuovo: %s)' % (IP_vecchi, IP_nuovo)

def controllo_whois(url):
	"""Ricevo un URL, estraggo il dominio, torno True/False in caso di cambiamento del whois"""
	pass

def richiedi_controllo(errore):
	"""Ricevo un errore, e invio una mail di richiesta di controllo"""
	
	global riga
	
	print errore, riga

if __name__ == "__main__":
	#TODO: portare come demone
	#      mettere segnalazioni
	#      mettere parser per config
	#      scrivere test whois
	#      scrivere test contenuto
	
	# La struttura dell'archivio è:
	# dizionario archivio[url_completo come chiave]:
	#												dizionario ['IP'] = Set degli IP
	archivio = shelve.open(os.path.join(os.environ["HOME"], '.spazzino.db'), writeback=True) # Apro il db persistente
	
	for filedb in glob.glob( os.path.join('./db/', '*.txt') ): # piglio ogni file db
		for riga in csv.reader(open(filedb, "r"), delimiter='|', quoting=csv.QUOTE_NONE): # e per ogni riga/Lug indicato
			url_completo = riga[3]
			
			risultato = controllo_dominio_dns(url_completo) # inizio il ciclo di controlli
			if risultato is not True:
				richiedi_controllo(risultato)

	archivio.sync()
	archivio.close