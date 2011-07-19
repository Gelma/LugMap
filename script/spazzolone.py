#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""Per ogni Lug indicato nella LugMap effettuo un insieme di controlli di validità.
   Se qualcosa non torna, avverto chi di dovere.
   Nota: il codice è complicato e tortuoso. Ma è stato l'unico modo per risolvere
   problemi di thread appesi, procedure bloccate, ecc. Qualsiasi modifica/miglioria
   ti venga in mente di fare, ti preghiamo di testarla a fondo con tutto l'archivio.

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
	import sys
	if sys.version_info < (2,6):
		sys.exit("Necessito di un interprete Python dalla versione 2.6 in poi")

	try:
		import atexit, csv, datetime, glob, multiprocessing, os, socket, sys, smtplib, syslog, tempfile, time, urllib2
	except:
		sys.exit("Non sono disponibili tutti i moduli standard necessari")

	try:
		import ZODB, persistent, transaction
	except:
		sys.exit("Installa ZODB3: 'easy_install zodb3' oppyre 'apt-get install python-zodb'")

	try:
		import mechanize
	except:
		sys.exit("Installa mechanize: 'easy_install mechanize' oppure 'apt-get install python-mechanize'")

	try:
		import BeautifulSoup
	except:
		sys.exit("Installa beautifulsoup: 'easy_install beautifulsoup' oppure 'apt-get install python-beautifulsoup'")

	try:
		import cPickle as pickle
	except:
		import pickle

def logga(*args):
	"""Ricevo un testo o un array e lo butto nei log di sistema"""

	try:
		linea_log += ' '.join(args)
	except: # desumo che siano presenti argomenti non testuali
		linea_log = ''
		for item in args:
			linea_log += ' %s' % item

	syslog.syslog(syslog.LOG_INFO, linea_log)

logga('avviato')

try: # attiva DB
	from ZODB.FileStorage import FileStorage
	from ZODB.DB import DB
	storage = FileStorage(os.path.join(os.environ["HOME"], '.spazzino.db'))
	db = DB(storage)
	connection = db.open()
	zodb = connection.root()
except:
	logga('Problema sul DB')
	sys.exit('Problema sul DB')

if True: # variabili globali
	elenco_lug						= set() # usato per cancellare Lug rimossi da zodb e per controllare omonimie
	tempo_minimo_per_i_controlli	= 120 # secondi
	elenco_thread					= []
	ritardo_lancio_thread			= 5 # secondi tra un thread e l'altro
	path_coda						= '/tmp/' # posizione dei file temporanei di coda
	report 							= [] # linee del report finale
	socket.setdefaulttimeout(tempo_minimo_per_i_controlli / 2) # Timeout in secondi del fetching delle pagine (onorato da urllib2, a sua volta usato da Mechanize)
	pidfile							= '/tmp/.spazzino.pid' # controllo istanze attive
	orario_partenza					= time.time()

if True: # controllo istanze attive
	if os.path.isfile(pidfile):
		if os.path.isdir('/proc/' + str(file(pidfile,'r').read())):
			logga('Spazzino: forse un\'altra istanza attiva. Eventualmente cancella '+pidfile)
			sys.exit('Spazzino: forse un\'altra istanza attiva. Eventualmente cancella '+pidfile)
		else:
			logga("Spazzino: rimosso "+pidfile)
	file(pidfile,'w').write(str(os.getpid()))

def invia_report(body):
	"""I receive a body, and I send email"""
	return
	header_from   = "Spazzino <spazzino@gelma.net>"
	header_to     = "Gelma <gelma@gelma.net>"
	subject       = 'LugMap: report data (UTC) '+str(datetime.datetime.utcnow())

	msg = ("From: %s\r\nTo: %s\r\nSubject: %s\r\n\r\n" % (header_from, header_to, subject))
	msg = body + '\n\n'
	msg = msg.encode('utf-8')

	try:
		import smtplib
		server = smtplib.SMTP('localhost')
		server.sendmail(header_from, header_to, msg)
		server.quit()
	except:
		logga('Errore: impossibile inviare email')
		print 'Errore: impossibile inviare email'

def termina_thread_appesi():
	"""Uccido tutti i thread ancora attivi"""

	for id in multiprocessing.active_children():
		logga('Lug: <'+id.name+'> ucciso thread')
		id.terminate()

class LUG(persistent.Persistent):
	def __init__(self, id):
		self.id = self.denominazione = id # blocco di informazioni che abbiamo nel db
		self.regione = None
		self.provincia = None
		self.zona = None
		self.url = None
		self.contatto = None

		self.dominio = None # informazioni specifiche per ogni Lug
		self.notifiche = [] # non puoi dichiararla volatile. Il pickling non la porterebbe nella coda_risultati
		self.web_errore_segnalato = False # controllo segnalazioni ripetute
		self.dns_errore_segnalato = False # controllo segnalazioni ripetute
		self.numero_controlli = 0
		self.numero_errori = 0

	def aggiorna_campi(self, riga_csv, filedb):
		"""Se i dati nel CSV sono cambiati, li rifletto nell'oggetto"""

		self.notifiche = [] # ad ogni avvio dei check, azzero
		self.controlli_conclusi = False
		self.aggiorna_dati()

		if self.provincia     != riga_csv[0]:
			self.provincia     = riga_csv[0]
			self.notifica('Atten. provincia aggiornata: '+self.provincia)

		if self.denominazione != riga_csv[1]:
			self.denominazione = riga_csv[1]
			self.notifica('Atten. denominazione aggiornata: '+self.denominazione) # prob. impossibile

		if self.zona          != riga_csv[2]:
			self.zona          = riga_csv[2]
			self.notifica('Atten. zona aggiornata: '+self.zona)

		if self.url           != riga_csv[3]:
			self.url           = riga_csv[3]
			self.dominio		 = self.url.split('/')[2]
			self.notifica('Atten. URL aggiornato: '+self.url)

		if self.contatto      != riga_csv[4]:
			self.contatto	   = riga_csv[4]
			self.notifica('Atten. contatto aggiornato: '+self.contatto)

		if self.regione		  != filedb[5:-4]:
			self.regione	   = filedb[5:-4]
			self.notifica('Atten. regione aggiornata: '+self.regione)

	def notifica(self, testo):
		self.numero_errori += 1
		self.notifiche.append(testo)
		logga('Lug <'+self.id+'>:',testo)
		self.aggiorna_dati()

	def controllo_dns(self):
		"""Controllo l'esistenza e la mappatura del dominio"""

		logga('Lug <'+self.id+'>: controllo DNS per '+self.dominio)
		try:
			self._v_DNS_attuali = set([ IP[4][0] for IP in socket.getaddrinfo(self.dominio, 80, 0, 0, socket.SOL_TCP)])
		except:
			self.notifica("Errore DNS per "+self.dominio)

			if self.dns_errore_segnalato is False:
				self.dns_errore_segnalato = time.time()
			else:
				self.notifiche[-1] = self.notifiche[-1] + ' (noto dal '+ time.strftime('%d/%m/%y') + ')'

			return False

		try: # eccezione nel caso sia il primo lancio e self.DNS_noti non esista
			if self._v_DNS_attuali - self.DNS_noti: # opero sui set per ottenere nuovi DNS
				self.notifica('Atten. DNS: %s' % (' '.join(list(self._v_DNS_attuali - self.DNS_noti))))
				self.DNS_noti = self.DNS_noti | self._v_DNS_attuali # unione dei set
		except:
			self.DNS_noti = self._v_DNS_attuali

		if self.dns_errore_segnalato is not False:
			self.notifica("Precedente errore DNS del " + time.strftime('%d/%m/%y', time.gmtime(self.dns_errore_segnalato)) + ' risolto')
			self.dns_errore_segnalato = False

		return True

	def controllo_homepage(self):
		"""Leggo lo URL e faccio una valutazione numerica. True/False di ritorno."""

		logga('Lug <'+self.id+'>: controllo web per '+self.url)

		self._v_browser = mechanize.Browser() # volatile per zodb
		self._v_browser.set_handle_robots(False) # evitiamo di richiedere robots.txt ogni volta
		self._v_browser.addheaders = [('User-agent', 'Bot: http://lugmap.linux.it - lugmap@linux.it')]

		try:
			if self.id == 'Blug':
				# Questo terribile hack/eccezione si è reseo necessario perché Mechanize
				# resta appesa nel tentativo di connessione al sito del Blug.
				# Mechanize è l'unico package del genere che segue automaticamente anche
				# i refresh nei tag (roba non standard, ma pesantemente usata da quasi tutti
				# i wiki. -- Il problema è gia' stato segnalato all'autore.)

				self._v_richiesta = urllib2.Request(self.url,None, {"User-Agent":"Bot: http://lugmap.linux.it - lugmap@linux.it"})
				self._v_pagina_html = urllib2.urlopen(self._v_richiesta).read()
				self._v_Termini_Attuali = set(self._v_pagina_html.split())

				# estraiamo subito anche il title
				try:
					self._v_soup = BeautifulSoup.BeautifulSoup(self._v_pagina_html)
					self._v_titolo_attuale = self._v_soup.html.head.title.string.strip()
				except: # se non ho un title, lo setto vuoto
					self._v_titolo_attuale = ''
			else:
				self._v_Termini_Attuali = set(self._v_browser.open(self.url).read().split())
		except:
			self.notifica('Errore web: impossibile leggere homepage')

			if self.web_errore_segnalato is False:
				self.web_errore_segnalato = time.time()
			else:
				self.notifiche[-1] = self.notifiche[-1] + ' (noto dal '+ time.strftime('%d/%m/%y') + ')'

			return False

		try: # per evitare segnalazione su un Lug nuovo, se self.Termini_Precedenti non esiste
			valore_magico = \
			  float(len(self.Termini_Precedenti.intersection(self._v_Termini_Attuali))*1.0/len(self.Termini_Precedenti.union(self._v_Termini_Attuali)))
		except:
			# si solleva l'eccezione e setto opportunamente il tutto
			valore_magico = 1.0

		self.Termini_Precedenti = self._v_Termini_Attuali

		if valore_magico <= 0.8:
			self.notifica('Atten.: differenze contenuto homepage ('+str(valore_magico)+')')
		else:
			logga('Lug <'+self.id+'>: valore_magico a', valore_magico)

		if self.web_errore_segnalato is not False:
			self.notifica("Precedente errore WEB del " + time.strftime('%d/%m/%y', time.gmtime(self.web_errore_segnalato)) + ' risolto')
			self.web_errore_segnalato = False

		return True

	def controllo_title(self):
		"""Leggo il title della pagina e controllo che non sia cambiato. True/False di ritorno"""

		logga('Lug <'+self.id+'>: controllo title per '+self.url)

		if not hasattr(self, '_v_titolo_attuale'): # se non è stato gia' settato dall'eccezione Blug (vedi sopra)
			try: # estrapolo il titolo della pagina nella maniera usuale
				self._v_titolo_attuale = self._v_browser.title().encode('utf-8')
			except: # se non ho un title, mollo
				logga('Lug <'+self.id+'>: nessuno title homepage')
				return True

		try:
			if self.title_homepage != self._v_titolo_attuale:
				self.notifica('Atten.: title homepage cambiato da <'+self.title_homepage+'>   a   <'+self._v_titolo_attuale+'>')
		except: # se fallisce è perché non esiste title_homepage (prima esecuzione)
			pass
		self.title_homepage = self._v_titolo_attuale # in ogni caso salvo il nuovo valore

	def aggiorna_dati(self):
		self.ultimo_aggiornamento = time.time()

		try:
			fd, fname = tempfile.mkstemp(suffix='.spazzino', dir=path_coda)
			file_pk = os.fdopen(fd, "w")
			pickle.dump(self, file_pk, 0)
		except:
			logga('Lug <'+self.id+'>: errore salvataggio pickling ' + fname)

	def controlli(self):
		logga('Lug <'+self.id+'>: inizio controlli')
		self.numero_controlli += 1
		if self.controllo_dns():
			if self.controllo_homepage():
				self.controllo_title()
		self.controlli_conclusi = True
		self.aggiorna_dati()
		logga('Lug <'+self.id+'>: fine controlli')

if __name__ == "__main__":
	atexit.register(termina_thread_appesi)
	for filedb in glob.glob( os.path.join('./db/', '*.txt') ): # piglio ogni file db
		for riga in csv.reader(open(filedb, "r"), delimiter='|', quoting=csv.QUOTE_NONE): # e per ogni riga/Lug indicato
			id = riga[1]

			if id in elenco_lug: # controllo univocita' nome lug
				report.append('Errore: omonimia tra i Lug: '+id) # metti in reportistica generale
				logga('Errore: omonimia per i Lug', id)
				continue
			else:
				elenco_lug.add(id)

			if not zodb.has_key(id): # se il Lug non è gia' presente nel DB
				zodb[id] = LUG(id) # lo creo

			zodb[id].aggiorna_campi(riga, filedb) # controllo eventuali cambiamenti nei campi del db

	for voce in set(zodb.keys()) - elenco_lug: # elimino da zodb le voci non piu' presenti
		del zodb[voce]
		report.append('Atten.: <'+voce+'> rimosso')
		logga('rimosso <'+voce+'> da ZODB')

	for id in sorted(zodb.keys()):
		elenco_thread.append(multiprocessing.Process(target=zodb[id].controlli, name=id))
		elenco_thread[-1].start()
		time.sleep(ritardo_lancio_thread)

	for j in elenco_thread:
		j.join(tempo_minimo_per_i_controlli)

	logga('inizio commit dei risultati in zodb')
	for filepk in sorted( glob.glob( os.path.join(path_coda, '*.spazzino') ) ):
		try:
			lug_risultati = pickle.load(open(filepk,'r'))
		except:
			logga('Errore lettura pickle dal file',filepk)
			continue

		if zodb[lug_risultati.id].ultimo_aggiornamento <= lug_risultati.ultimo_aggiornamento:
			zodb[lug_risultati.id] = lug_risultati
			logga('Lug: <'+lug_risultati.id+'> commit dei dati')
		else:
			logga('Lug: <'+lug_risultati.id+'> file pickle '+filepk+' vecchio. Scartato')
		os.remove(filepk)

	logga('fine commit dei risultati in zodb')

	for id in sorted(zodb.keys()): # report dei thread che non hanno concluso
		if zodb[id].controlli_conclusi is False:
			logga('Lug: <'+zodb[id].id+'> non ha concluso il ciclo di controlli')
			report.append('Atten. <'+zodb[id].id+'> non ha concluso il ciclo di controlli')

	logga('inizio invio notifiche')
	for id in sorted(zodb.keys()):
		if zodb[id].notifiche:
			logga('Lug <'+id+'> invio notifiche')
			report.append('\n- - ----> Lug: '+zodb[id].id+' ('+str(zodb[id].numero_controlli)+'/'+str(zodb[id].numero_errori)+') <---- - -\n')
			for rigo in zodb[id].notifiche: report.append(rigo)
			report.append('\n        * Dati DB *')
			report.append('Url : ' + zodb[id].url + '   Email: '+zodb[id].contatto)
			report.append('Dove: ' + zodb[id].regione.capitalize() + ' -> '+zodb[id].provincia + ' -> ' +zodb[id].zona)
	logga('fine invio notifiche')

	if report:
		report.insert(0, 'Spazzino: report del ' +
					  time.strftime('%d/%m/%y', time.gmtime(orario_partenza)) +
					  ' dalle ' +
					  time.strftime('%H:%M', time.gmtime(orario_partenza)) +
					  ' alle ' +
					  time.strftime('%H:%M', time.gmtime(time.time()))
					  )
		#invia_report('\n'.join(report))
		print '\n'.join(report)

transaction.commit()
db.pack()
db.close()
os.remove(pidfile)
logga('concluso')
