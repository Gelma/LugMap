#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""Riporto i commit sull'account twetter della LugMap:
   http://twitter.com/#!/LugMap

   Copyright 2010-2016 - Andrea Gelmini (andrea.gelmini@gelma.net)

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
		import ConfigParser, os, shelve, subprocess, time
	except:
		sys.exit("Non sono disponibili tutti i moduli standard necessari")

	try:
		import tweepy # http://talkfast.org/2010/05/31/twitter-from-the-command-line-in-python-using-oauth
	except:
		sys.exit("Manca il modulo Tweepy: easy_install tweepy non funziona per via dell'aggiornamento delle API di Twitter. Installalo da GitHub: https://github.com/tweepy/tweepy")

if True: # leggo configurazione
	configdiesempio="""
	[db]
	path = /tmp/portavoce.db ; percorso del DB con i commit noti/inviati

	[twitter]
	consumer_key	= xxxxxxxxxxxxxxxxxxxxx
	consumer_secret	= xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	access_key      = xxxxxxxxx-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	access_secret	= xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	"""
	configurazione=ConfigParser.ConfigParser()
	configfile = os.path.join(os.environ["HOME"], '.portavoce.conf')
	try:
		configurazione.read(configfile)
		dbfile = configurazione.get('db', 'path', 1)
	except:
		sys.exit("Necessito del file di configurazione corretto in:\n        "+configfile+"\n\nVedi esempio incluso nel sorgente:"+configdiesempio)

try: # apro il db
	db = shelve.open(dbfile)
except:
	sys.exit("Errore nell'apertura/creazione del DB",dbfile)

class tweta:
	"""Posto su Tweet"""
	def __init__(self):
		# piglio codici
		try:
			self.CONSUMER_KEY    = configurazione.get('twitter','consumer_key',1)
			self.CONSUMER_SECRET = configurazione.get('twitter','consumer_secret',1)
			self.ACCESS_KEY      = configurazione.get('twitter','access_key',1)
			self.ACCESS_SECRET   = configurazione.get('twitter','access_secret',1)
		except:
			sys.exit("Necessito del file di configurazione corretto in:\n        "+configfile+"\n\nVedi esempio incluso nel sorgente:"+configdiesempio)
		# connetto
		self.auth            = tweepy.OAuthHandler(self.CONSUMER_KEY, self.CONSUMER_SECRET)
		self.auth.set_access_token(self.ACCESS_KEY, self.ACCESS_SECRET)
		self.twitter         = tweepy.API(self.auth)

	def tweta(self, testo):
		self.twitter.update_status(status=testo)

if __name__ == "__main__":
	commit_nuovi = []
	os.chdir(sys.path[0])
	for line in subprocess.Popen('git rev-list --pretty=oneline --all --no-merges --abbrev-commit', shell=True, bufsize=1000000, stdout=subprocess.PIPE).stdout:
		commit, log = line[0:7], line[8:-1]
		if not db.has_key(commit): # controllo se il commit non è gia' noto
			db[commit] = None # lo salvo subito, altrimenti risulta nuovo al prossimo controllo
			flag_doppio = False # controllo eventuali commit doppi (cherry-pick, ecc)
			for voce in commit_nuovi:
				if voce['log'] == log:
					flag_doppio = True
					break
			if flag_doppio is False:
				commit_nuovi.append({'commit': commit, 'log': log})

	if commit_nuovi:
		commit_nuovi.reverse() # ribalto l'ordine dei commit
		tweeter = tweta()
		for voce in commit_nuovi:
			testo = voce['log'][0:130]+' ('+voce['commit']+')'
			tweeter.tweta(testo)
			time.sleep(5)
	db.close()
