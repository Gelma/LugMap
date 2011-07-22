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

import editarticle, notifiche, os, pickle, smtplib

class AccessoCronologia(editarticle.ArticleEditor):
	def run(self):
		return self.page.getVersionHistory(self)

def leggo_ultimo_id_inviato():

	try:
		return pickle.load(open(file_con_ultima_modifica, 'r'))
	except:
		return 0

def salva_ultimo_id_inviato(id):
	pickle.dump(id, open(file_con_ultima_modifica, 'w'))

if __name__ == "__main__":

	# Variabili globali
	file_con_ultima_modifica = os.path.join(os.environ["HOME"], '.wimegera_ultimoid')
	righe_della_mail = []

	ultimo_id_inviato = leggo_ultimo_id_inviato()

	oggetto_modifiche = AccessoCronologia('-p','Lista_dei_LUG_italiani')
	elenco_modifiche = [x for x in oggetto_modifiche.run()]#¹
	elenco_modifiche.reverse()

	for modifica in elenco_modifiche:
		id, data, autore, nota = [modifica[x] for x in range(4)]
		if int(id) > ultimo_id_inviato:
			righe_della_mail.append('Nota: '  +nota)
			righe_della_mail.append('Data: '  +data+'   Autore: '+autore+'   ID: '+str(id))
			righe_della_mail.append('\n'+15*'-_'+'\n')

	if righe_della_mail:
		try:
			mail = notifiche.email(mittente		= 'WiMegera <wimegera@gelma.net>',
								   destinatario	= ['WiMegera <wimegera@gelma.net>'],
								   oggetto 		= 'WiMegera: aggiornamenti Lug su Wikipedia',
								   testo		= righe_della_mail,
								   invia_subito	= True) # Se da Aggiornare, vedi Guida Intergalattica alla LugMap §4.1
		except: # se fallisce l'invio stampo la mail, contando sul delivery di cron
			print mail

		salva_ultimo_id_inviato(id)

	#¹ il metodo run() mi restituisce un elenco di tuple, con i dettagli delle modifiche, come questa:
	## (39770501, u'2011-04-08T09:15:08Z', u'Gelma', u'/* Collegamenti esterni */ Aggiunto dominio dedicato', 13101, [])
