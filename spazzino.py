#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""Per ogni Lug indicato nella LugMap effettuo un insieme di controlli di validità.
   Se qualcosa non torna, avverto chi di dovere"""

if True: # import dei moduli
	try:
		import ConfigParser, csv, glob, os, shelve, shlex, socket, subprocess, sys, syslog, time, urllib
	except:
		import sys
		print "Errore nell'import dei moduli standard. Versione troppo vecchia dell'interprete?"
		sys.exit(-1)

def controllo_dominio_dns(url):
	"""Ricevo un URL, estraggo il dominio, torno True se tutto torna. Diversamente emetto il messaggio da segnalare"""

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
		IP_vecchi.add(IP_nuovo)
		archivio[url_completo]['IP'] = IP_vecchi
		archivio.sync()
		return 'Attenzione: cambiato IP del server (Vecchio: %s, Nuovo: %s)' % (IP_vecchi, IP_nuovo)

def controllo_contenuto():
	"""Leggo lo URL elaborato, estraggo la pagina, ne valuto la differenza rispetto alla lettura precedente. Torno True se tutto a posto."""

	try: # pesco la pagina
		pagina_html = urllib.urlopen(url_completo).read()
	except:
		return 'Errore: impossibile leggere la pagina html.'
	
	Termini_Attuali = set(pagina_html.split()) # Estrapolo subito i termini presenti
	
	try: # Leggo i termini precedenti
		Termini_Precedenti = archivio[url_completo]['TerminiPrecedenti']
	except: # Non esiste la voce, la creo
		archivio[url_completo]['TerminiPrecedenti'] = Termini_Attuali
		return True
	
	valore_magico = float(len(Termini_Precedenti.intersection(Termini_Attuali))*1.0/len(Termini_Precedenti.union(Termini_Attuali)))
	archivio[url_completo] = {'TerminiPrecedenti': Termini_Attuali}

	if valore_magico <= 0.7:
		return 'Errore: troppa differenza di contenuto:'+str(valore_magico)
	else:
		return True
	
def richiedi_controllo(errore):
	"""Ricevo un errore, e invio una mail di richiesta di controllo"""
	
	email_alert = 'andrea.gelmini@gmail.com'
	
	if os.path.exists('/usr/bin/mail'):
		echo_command = shlex.split("echo '"+'\n'.join([errore]+riga)+"'")
		mail_command = shlex.split("mail -s 'LugMap check: %s' %s" % (riga[3], email_alert))
		subprocess.Popen(mail_command, stdin=subprocess.Popen(echo_command, stdout=subprocess.PIPE).stdout, stdout=subprocess.PIPE).wait()

	print errore, riga[3]
	syslog.syslog(syslog.LOG_ERR, 'Spazzino: '+riga[3]+' '+errore)

if __name__ == "__main__":
	#TODO: portare come demone
	#      mettere parser per config	
	# La struttura dell'archivio è:
	# dizionario archivio[url_completo come chiave]: dizionario
	#															['IP'] = Set degli IP
	#															['TerminiPrecedenti'] = Set dei Termini delle pagine HTML

	while True:
		print 'Nuovo giro'
		syslog.syslog(syslog.LOG_ERR, 'Spazzino: Nuovo giro')

		archivio = shelve.open(os.path.join(os.environ["HOME"], '.spazzino.db'), writeback=True) # Apro il db persistente
		
		for filedb in glob.glob( os.path.join('./db/', '*.txt') ): # piglio ogni file db
			for riga in csv.reader(open(filedb, "r"), delimiter='|', quoting=csv.QUOTE_NONE): # e per ogni riga/Lug indicato
				url_completo = riga[3]
				
				responso = controllo_dominio_dns(url_completo) # inizio il ciclo di controlli
				if responso is not True:
					richiedi_controllo(responso)
	
				responso = controllo_contenuto()
				if responso is not True:
					richiedi_controllo(responso)
					
		archivio.sync()
		archivio.close()
		print 'Fine giro'
		time.sleep(60 * 60 * 24)
