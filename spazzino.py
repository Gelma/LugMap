#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""Per ogni Lug indicato nella LugMap effettuo un insieme di controlli di validità.
   Se qualcosa non torna, avverto chi di dovere"""

if True: # import dei moduli
	try:
		import csv, glob, os, socket, sys, smtplib, syslog, urllib2
	except:
		import sys
		print "Non sono disponibili tutti i moduli standard necessari."
		sys.exit(-1)
	try:
		import ZODB, persistent, transaction
	except:
		print "Non sono disponibili uno o più di questi moduli: ZODB, persistent, transaction"

if True: # attiva DB
	from ZODB.FileStorage import FileStorage
	from ZODB.DB import DB
	storage = FileStorage(os.path.join(os.environ["HOME"], '.spazzino.db'))
	db = DB(storage)
	connection = db.open()
	pdb = connection.root()

class Lug(persistent.Persistent):
	def __init__(self, url_del_lug):
		self.url = url_del_lug
		self.reset_iniziale()
		self.dominio = url_del_lug.split('/')[2]
		self.Termini_Precedenti = set()
		self.DNS_noti = set()

	def reset_iniziale(self):
		self.email_errori = email_report()

	def controllo_dns(self):
		"""Controllo l'esistenza e la mappatura del dominio"""

		print "             ->Controllo dominio",self.dominio
		try:
			DNS_attuale = socket.getaddrinfo(self.dominio, 80, 0, 0, socket.SOL_TCP)[0][4][0]
		except:
			print "socket.getaddrinfo fallito"
			self.email_errori.aggiungi("Errore: problema sul dominio (esistenza/mappatura)")
			return False

		if DNS_attuale not in self.DNS_noti:
			dettaglio = 'Attenzione: nuovo IP %s (%s)' % (DNS_attuale,' '.join(self.DNS_noti))
			print dettaglio
			self.DNS_noti.add(DNS_attuale)
			self.email_errori.aggiungi(dettaglio)
			return False

	def controllo_contenuto(self):
		"""Leggo lo URL e faccio una valutazione numerica. True/False di ritorno."""

		try: # pesco la pagina
			pagina_html = urllib2.urlopen(self.url).read()
		except:
			self.email_errori.aggiungi('Errore: impossibile leggere la pagina html.')
			return False

		Termini_Attuali = set(pagina_html.split()) # Estrapolo i termini presenti
		valore_magico = \
		  float(len(self.Termini_Precedenti.intersection(Termini_Attuali))*1.0/len(self.Termini_Precedenti.union(Termini_Attuali)))
		self.Termini_Precedenti = Termini_Attuali

		print "controllo_contenuto: valore_magico = ",str(valore_magico)
		if valore_magico <= 0.7:
			self.email_errori.aggiungi('Errore: troppa differenza di contenuto:'+str(valore_magico))
			return False

	def invia_report(self):
		self.email_errori.subject = 'LugMap: '+self.url
		self.email_errori.invia()


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

		msg = ("From: %s\r\nTo: %s\r\nSubject: %s\r\n\r\n" % (self.mittente, ", ".join(self.destinatario), self.subject))
		msg = msg + '\n'.join(self.righe) + '\n' + '\n'.join(riga)
		try:
			server = smtplib.SMTP('localhost')
			server.sendmail(self.mittente, self.destinatario, msg)
			server.quit()
		except:
			print "Non è stato possibile inviare la mail"

		syslog.syslog(syslog.LOG_ERR, 'Spazzino: '+self.subject+' '+'  '.join(self.righe))


if __name__ == "__main__":
	for filedb in glob.glob( os.path.join('./db/', '*.txt') ): # piglio ogni file db
		for riga in csv.reader(open(filedb, "r"), delimiter='|', quoting=csv.QUOTE_NONE): # e per ogni riga/Lug indicato
			url = riga[3]
			if pdb.has_key(url): # se è gia' presente nel DB
				lug = pdb[url] # la recupero
			else:
				lug = Lug(url) # diversamente creo la classe
				pdb[url] = lug # e la lego al DB
			lug.controllo_dns()
			lug.controllo_contenuto()
			transaction.commit()
			lug.invia_report()
db.close()
