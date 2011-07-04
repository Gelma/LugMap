#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""Controllo sui dui siti della LugMap, che i dati del DB siano allineati
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
		import csv, glob, os, socket, sys, smtplib, syslog, urllib2
	except:
		import sys
		print "Non sono disponibili tutti i moduli standard necessari."
		sys.exit(-1)
	socket.setdefaulttimeout(35) # Timeout in secondi del fetching delle pagine (vedi urllib2)

def email_errori(URL, filedb=''):
	msg = "From: %s\r\nTo: %s\r\nSubject: %s\r\n\r\nControlla\n" % ('lugmapcheck@gelma.net', 'andrae.gelmini@gmail.com', 'Lugmap: dati non sincronizzati: '+URL+filedb+'\n') # Eventualmente da Aggiornare (vedi Guida Intergalattica alla LugMap §4.1)
	try:
		server = smtplib.SMTP('localhost')
		server.sendmail('lugmapcheck@gelma.net', 'andrea.gelmini@gmail.com', msg) # Eventualmente da Aggiornare (vedi Guida Intergalattica alla LugMap §4.1)
		server.quit()
	except:
		print "Non è stato possibile inviare la mail"
		syslog.syslog(syslog.LOG_ERR, 'Lugmap: dati non sincronizzati '+URL+filedb)

if __name__ == "__main__":
	for URL in ('http://lugmap.linux.it/db/', 'http://lugmap.it/db/'):
		for filedb in glob.glob( os.path.join('./db/', '*.txt') ): # piglio ogni file db
			fileURL = URL+filedb[5:]
			print 'Controllo',fileURL
			richiesta_file_db = urllib2.Request(fileURL, None, {"User-Agent":"LugMap.it checker - lugmap@linux.it"})
			try:
				contenuto_remoto = urllib2.urlopen(richiesta_file_db).read()
			except:
				email_errori(fileURL,': impossibile leggere il file remoto')
				continue
			contenuto_locale = open(filedb, 'r').read()
			if contenuto_locale != contenuto_remoto:
				print "   * Differenza di contenuto"
				email_errori(fileURL)
