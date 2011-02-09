#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""Per ogni Lug indicato nella LugMap effettuo un insieme di controlli di validità.
   Se qualcosa non torna, avverto chi di dovere"""

if True: # import dei moduli
	try:
		import copy, csv, glob, os, shelve, socket, sys, smtplib, syslog, urllib
	except:
		import sys
		print "Non sono disponibili tutti i moduli necessari."
		sys.exit(-1)

def controllo_dominio_dns(url):
	"""Ricevo un URL, estraggo il dominio, torno True se tutto torna, diversamente False"""

	url = url.split('/')[2] # estraggo solo il nome di dominio	# TODO: una regexp sarebbe più elegante
	if Debug: print "\t--->",url

	try:
		info_url = socket.getaddrinfo(url, 80, 0, 0, socket.SOL_TCP)
	except:
		if Debug: print "controllo_dominio_dns: socket.getaddrinfo fallito"
		email_errori.aggiungi("Errore: problema sul dominio (esistenza/mappatura)")
		return False

	IP_nuovo = info_url[0][4][0] # Inizio controllo IP cambiato. Possono esserci dei falsi positivi in caso di round robin.
	if Debug: print "controllo_dominio_dns: IP_nuovo", IP_nuovo

	try: # Pesco i vecchi iP
		IP_vecchi = copy.deepcopy(archivio[url_completo]['IP'])
		if Debug: print "controllo_dominio_dns: IP_vecchi",IP_vecchi
	except: # Non esiste ancora una voce relativa, la creiamo
		archivio[url_completo] = {'IP': set([IP_nuovo])}
		if Debug: print "controllo_dominio_dns: IP_vecchi inesistenti"
		return True

	if IP_nuovo in IP_vecchi: # se è gia' presente, me ne fotto
		if Debug: print "controllo_dominio_dns: IP noto", IP_nuovo
		return True
	else: # diversamente segnalo e aggiungo
		elenco_ip_dns = IP_vecchi
		if Debug: print "controllo_dominio_dns: elenco vecchi IP", elenco_ip_dns
		elenco_ip_dns.add(IP_nuovo)
		archivio[url_completo]['IP'] = elenco_ip_dns
		if Debug: print "controllo_dominio_dns: elenco IP storati", archivio[url_completo]['IP']
		archivio.sync()
		dettaglio = 'Attenzione: cambiato IP del server (Vecchio: %s, Nuovo: %s, Storato: %s)' % (' - '.join(IP_vecchi), IP_nuovo, ' - '.join(elenco_ip_dns))
		email_errori.aggiungi(dettaglio)
		return False

def controllo_contenuto():
	"""Leggo lo URL elaborato, estraggo la pagina, ne valuto la differenza rispetto alla lettura precedente. Torno True se tutto a posto."""

	try: # pesco la pagina
		pagina_html = urllib.urlopen(url_completo).read()
	except:
		email_errori.aggiungi('Errore: impossibile leggere la pagina html.')
		return False

	Termini_Attuali = set(pagina_html.split()) # Estrapolo subito i termini presenti

	try: # Leggo i termini precedenti
		Termini_Precedenti = archivio[url_completo]['TerminiPrecedenti']
	except: # Non esiste la voce, la creo
		archivio[url_completo]['TerminiPrecedenti'] = Termini_Attuali
		return True

	valore_magico = float(len(Termini_Precedenti.intersection(Termini_Attuali))*1.0/len(Termini_Precedenti.union(Termini_Attuali)))
	archivio[url_completo] = {'TerminiPrecedenti': Termini_Attuali}

	if Debug: print "controllo_contenuto: valore_magico = ",str(valore_magico)
	if valore_magico <= 0.7:
		email_errori.aggiungi('Errore: troppa differenza di contenuto:'+str(valore_magico))
		return False
	else:
		return True

class email_report():
	"""Prendo in pasto errori e li invio via SMTP"""

	def __init__(self):
		"""Definisco dettagli email"""

		self.mittente = 'lugmapcheck@gelma.net'
		self.destinatario = ['andrea.gelmini@gmail.com']
		self.righe = []
		self.subject = 'Lugmap:'

	def aggiungi(self,testo):
		"""ACcetto un argomento che metto nel corpo email"""

		if type(testo) == tuple or type(testo) == list:
			[self.righe.append(riga) for riga in testo]
		elif type(testo) == str:
			self.righe.append(testo)
		else:
			try:
				self.righe.append(str(testo))
			except:
				print "Il cast non ha funzionato"
				# Raise exception

	def invia(self):
		"""Effettuo l'invio vero e proprio"""

		if not self.righe: return # Se non ho alcun testo di errore, non proseguo

		if Debug:
			print "invio questo testo",self.righe

		msg = ("From: %s\r\nTo: %s\r\nSubject: %s\r\n\r\n" % (self.mittente, ", ".join(self.destinatario), self.subject))
		msg = msg + '\n'.join(self.righe) + '\n' + '\n'.join(riga)
		try:
			server = smtplib.SMTP('localhost')
			server.sendmail(self.mittente, self.destinatario, msg)
			server.quit()
		except:
			print "Non è stato possibile inviare la mail"

		if Debug: print self.subject,self.righe
		syslog.syslog(syslog.LOG_ERR, 'Spazzino: '+self.subject+' '+'  '.join(self.righe))

if __name__ == "__main__":
	# La struttura dell'archivio è:
	# dizionario archivio[url_completo come chiave]: dizionario
	#		['IP'] = Set degli IP
	#		['TerminiPrecedenti'] = Set dei Termini delle pagine HTML

	Debug = True

	archivio = shelve.open(os.path.join(os.environ["HOME"], '.spazzino.db'), writeback=True) # Apro il db persistente

	for filedb in glob.glob( os.path.join('./db/', '*.txt') ): # piglio ogni file db
		for riga in csv.reader(open(filedb, "r"), delimiter='|', quoting=csv.QUOTE_NONE): # e per ogni riga/Lug indicato
			url_completo = riga[3]

			email_errori = email_report()
			email_errori.subject = 'Lugmap: '+url_completo

			if controllo_dominio_dns(url_completo): # controllo mappature
				responso = controllo_contenuto() # se ok, controllo anche il contenuto
			email_errori.invia()
	archivio.sync()
	archivio.close()
