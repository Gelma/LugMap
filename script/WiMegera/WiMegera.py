#!/usr/bin/python
# -*- coding: utf-8 -*-
"""Leggo la cronologia di questa pagina Wikipedia:
	http://it.wikipedia.org/wiki/Lista_dei_LUG_italiani
	e notifico eventuali variazioni alla LugMap

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

try:
	import editarticle, notifiche, os, pickle, shelve
except:
	import sys
	sys.exit('Errore: non sono disponibili tutti i moduli necessari')

class AccessoCronologia(editarticle.ArticleEditor):
	def run(self):
		return self.page.getVersionHistory(self)

if __name__ == "__main__":
	db = shelve.open(os.path.join(os.environ["HOME"], '.wimegera.db'), writeback=True) # Apro DB

	# Variabili globali
	righe_della_mail = []
	pagine_wikipedia_da_controllare = ('Lista_dei_LUG_italiani', 'Hacklab') # eventuali altre pagine da monitorare vanno aggiunte qui

	for pagina_wiki in pagine_wikipedia_da_controllare: # prelevo la lista di modifiche
		cronologia = AccessoCronologia('-p', pagina_wiki)
		elenco_modifiche = [x for x in cronologia.run()]#¹
		elenco_modifiche.reverse()

		for modifica in elenco_modifiche: # controllo ogni modifica
			id, data, autore, nota = [modifica[x] for x in range(4)]
			try: # provo il confronto
				if int(id) > db[pagina_wiki]:
					db[pagina_wiki] = int(id)
					righe_della_mail.append('Nota: '  +nota)
					righe_della_mail.append('Data: '  +data+'   Autore: '+autore+'   ID: '+str(id))
					righe_della_mail.append('\n'+15*'-_'+'\n')
			except KeyError: # se non trovo la pagina nel db, significa che è nuova
				db[pagina_wiki] = 0
				righe_della_mail.append('Nota: pagina nuova')

		if righe_della_mail:
			try:
				mail = notifiche.email(mittente		= 'WiMegera <wimegera@gelma.net>',
									   destinatario	= ['WiMegera <wimegera@gelma.net>'],
									   oggetto 		= 'WiMegera: aggiornamento su '+pagina_wiki,
									   testo		= righe_della_mail,
									   invia_subito	= True) # Se da Aggiornare, vedi Guida Intergalattica alla LugMap §4.1
			except: # se fallisce l'invio stampo la mail, contando sul delivery di cron
				print '\n'.join(righe_della_mail)

	db.close()
	#¹ il metodo run() mi restituisce un elenco di tuple, con i dettagli delle modifiche, come questa:
	## (39770501, u'2011-04-08T09:15:08Z', u'Gelma', u'/* Collegamenti esterni */ Aggiunto dominio dedicato', 13101, [])
