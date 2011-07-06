#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""Per ogni Lug indicato nella LugMap effettuo un insieme di controlli di validità.
   Se qualcosa non torna, avverto chi di dovere.

   Copyright 2010-2011 - Andrea Gelmini (andrea.gelmini@gelma.net)

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>."""

if True: # import dei moduli
	try:
		import csv, glob, os, socket, sys, smtplib, syslog
	except:
		import sys
		print "Non sono disponibili tutti i moduli standard necessari."
		sys.exit(-1)

	try:
		import ZODB, persistent, transaction
	except:
		print "Installa ZODB3: 'easy_install zodb3' oppyre 'apt-get install python-zodb'"
		sys.exit(-1)
	try:
		import mechanize
	except:
		print "Installa mechanize: 'easy_install mechanize' oppure 'apt-get install python-mechanize'"
		sys.exit(-1)

if True: # attiva DB
	from ZODB.FileStorage import FileStorage
	from ZODB.DB import DB
	storage = FileStorage(os.path.join(os.environ["HOME"], '.spazzino.db'))
	db = DB(storage)
	connection = db.open()
	pdb = connection.root()

	socket.setdefaulttimeout(35.0) # Timeout in secondi del fetching delle pagine (onorato da urllib2, a sua volta usato da Mechanize)

class Lug(persistent.Persistent):

	testo_email_segnalazione = "Ciao,\n   mi chiamo Andrea Gelmini e faccio parte dei Manovali della LugMap.¹\n   Mi permetto di disturbarti per informarti che il sito in oggetto non ci risulta funzionante in questo momento.\n\n   ------\n¹ http://lugmap.linux.it\n http://lugmap.it"

	def __init__(self, url_del_lug):
		self.url = url_del_lug
		self.email_errori = email_report()
		self.dominio = url_del_lug.split('/')[2]
		self.Termini_Precedenti = set()
		self.DNS_noti = set()
		self.numero_controlli = 0
		self.numero_errori = 0

	def controllo_dns(self):
		"""Controllo l'esistenza e la mappatura del dominio"""

		print "Controllo dominio",self.dominio
		self.numero_controlli += 1
		try:
			DNS_attuale = socket.getaddrinfo(self.dominio, 80, 0, 0, socket.SOL_TCP)[0][4][0]
		except:
			self.email_errori.aggiungi("  Errore: problema sul dominio (esistenza/mappatura)")
			self.numero_errori += 1
			return False

		if DNS_attuale not in self.DNS_noti:
			dettaglio = 'Attenzione: nuovo IP %s (%s)' % (DNS_attuale,' '.join(self.DNS_noti))
			self.DNS_noti.add(DNS_attuale)
			self.email_errori.aggiungi(dettaglio)
			self.numero_errori += 1
			return False
		return True

	def controllo_contenuto(self):
		"""Leggo lo URL e faccio una valutazione numerica. True/False di ritorno."""

		print "Controllo contenuto"
		try:
			self.browser = mechanize.Browser()
			self.browser.set_handle_robots(False) # evitiamo di richiedere robots.txt ogni volta
			self.browser.addheaders = [('User-agent', 'Bot: http://lugmap.linux.it - lugmap@linux.it')]
			pagina_web = self.browser.open(self.url, timeout=35.0 )
		except:
			self.email_errori.aggiungi('  Errore: impossibile leggere la pagina html.')
			self.numero_errori += 1
			return False

		Termini_Attuali = set(pagina_web.read().split()) # Estrapolo le parole della pagina HTML
		valore_magico = \
		  float(len(self.Termini_Precedenti.intersection(Termini_Attuali))*1.0/len(self.Termini_Precedenti.union(Termini_Attuali)))
		self.Termini_Precedenti = Termini_Attuali

		if valore_magico <= 0.6:
			self.email_errori.aggiungi('  Errore: troppa differenza di contenuto: '+str(valore_magico))
			self.numero_errori += 1
			return False
		else:
			return True

	def controllo_title_della_pagina(self):
		"""Leggo il title della pagina e controllo che non sia cambiato. True/False di ritorno"""

		print "Controllo title"
		try:
			titolo_attuale = self.browser.title().encode('utf-8')
		except: # se non ho un title, mollo
			return True

		try:
			if self.titolo != titolo_attuale:
				print "Divergenza", self.titolo, titolo_attuale
				self.email_errori.aggiungi('Attenzione: title della home cambiato da\n' +self.titolo+'\na\n'+titolo_attuale)
				self.numero_errori += 1
				self.titolo = titolo_attuale
				return False
			else:
				return True
		except AttributeError: # Se il DB è stato precedentemente creato non ho l'attributo di confronto
			self.titolo = titolo_attuale

	def invia_report(self):
		self.email_errori.subject = 'LugMap: '+self.url
		self.email_errori.invia()
		del self.email_errori
		self.email_errori = email_report()

class email_report():
	"""Prendo in pasto errori e li invio via SMTP"""

	def __init__(self):
		"""Definisco dettagli email"""

		self.mittente = 'lugmapcheck@gelma.net'
		self.destinatario = ['andrea.gelmini@gmail.com'] # Eventualmente da Aggiornare (vedi Guida Intergalattica alla LugMap §4.1)
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

		self.msg = ("From: %s\r\nTo: %s\r\nSubject: %s\r\n\r\n" % (self.mittente, ", ".join(self.destinatario), self.subject))
		self.msg = self.msg + '\n'.join(self.righe) + '\n' + '\n'.join(riga)
		if False: # abilita/disabilita invio della mail, in luogo del report a video
			print 30*'-'+'\n'+self.msg+'\n'+30*'-'
		else:
			try:
				server = smtplib.SMTP('localhost')
				server.sendmail(self.mittente, self.destinatario, self.msg)
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
			if lug.controllo_dns():
				if lug.controllo_contenuto():
					lug.controllo_title_della_pagina()
			lug.invia_report()
			try:
				del lug.browser # Distruggo istanza di mechanize perché non è serializzabile (ZODB)
			except AttributeError: # lug.browser non è detto che esista
				pass
transaction.commit()
db.pack()
db.close()
