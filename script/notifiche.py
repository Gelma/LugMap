#!/usr/bin/python
# -*- coding: utf-8 -*-
"""Invio notifiche, al momento solo via email

	Copyright 2010-2019 - Andrea Gelmini (andrea.gelmini@gelma.net)

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

class ErroreGenerico(Exception):
	def __init__(self, testo_errore):
		self.testo_errore = testo_errore
	def __str__(self):
		return repr(self.testo_errore)

class email():
	"""Invio una mail. In entrata accetto:
	mittente: stringa nella forma completa "Nome <email>"
	destinatario: stringa o lista di indirizzi
	oggetto: stringa
	testo: stringa oppure lista di righe
	invia_subito: se True la mail viene fatta partire subito nell'istanziare l'oggetto
	"""

	import smtplib

	def __init__(self, mittente = None, destinatario = None, oggetto = None, testo = None, invia_subito = False):

		self.mittente = self.destinatario = self.oggetto = self.testo = None

		if mittente		!= None: self.mittente		= mittente
		if destinatario	!= None: self.destinatario	= destinatario
		if oggetto		!= None: self.oggetto		= oggetto
		if testo		!= None: self.testo			= testo

		if invia_subito:
			self.invia()

	def invia(self):
		if not self.mittente != None and self.destinatario != None and self.oggetto != None and self.testo != None:
			raise ErroreGenerico('Necessito di tutti i parametri: mittente, destinatario, oggetto e testo')

		if type(self.destinatario) == list:
			self.destinatario = ", ".join(self.destinatario)

		if type(self.testo) == list:
			self.testo = '\n'.join(self.testo)

		try:
			self.testo = self.testo.encode('utf-8')
			self.oggetto = self.oggetto.encode('utf-8')
		except:
			pass

		self.mail_completa = ("From: %s\r\nTo: %s\r\nSubject: %s\r\n\r\n" % (self.mittente, self.destinatario, self.oggetto))
		self.mail_completa = self.mail_completa + self.testo + '\n\n'

		try:
			smtp = self.smtplib.SMTP('localhost')
			smtp.sendmail(self.mittente, self.destinatario, self.mail_completa)
			smtp.quit()
		except:
			print self.mail_completa
			raise ErroreGenerico('Invio della mail fallito')
