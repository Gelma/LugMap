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
	"""Ricevo un URL, estraggo il dominio, torno True/False a seconda della mappatura o meno dello stesso nel DNS"""
	
	# estraggo solo il nome di dominio
	# TODO: una regexp sarebbe più elegante
	url = url.split('/')[2]

	# TODO: si potrebbe utilizzare una libreria specifica come DNS client.
	# 		Ma in questo modo ci basta una riga
	#		Tenere traccia dei cambiamenti degli IP può avere senso? E con i servizi in round robin?
	try:
		risultati=socket.getaddrinfo(url, 80, 0, 0, socket.SOL_TCP)
	except:
		return False
	return True

def controllo_whois(url):
	"""Ricevo un URL, estraggo il dominio, torno True/False in caso di cambiamento del whois"""

	pass

if __name__ == "__main__":
	#TODO: portare come demone
	#      mettere segnalazioni
	#      mettere parser per config
	#      scrivere test whois
	#      scrivere test contenuto
	for filedb in glob.glob( os.path.join('./db/', '*.txt') ): # piglio ogni file db
		for riga in csv.reader(open(filedb, "r"), delimiter='|', quoting=csv.QUOTE_NONE): # e per ogni riga/Lug indicato
			url = riga[3]
			
			if not controllo_dominio_dns(url): # inizio il ciclo di controlli
				print 'Non ci siamo -------->', url
				continue
			else:
				print 'Ok:', url