#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""Controllo sui dui siti della LugMap, che i dati del DB siano allineati
   Se qualcosa non torna, avverto chi di dovere.

   Copyright 2010-2012 - Andrea Gelmini (andrea.gelmini@gelma.net)

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
    import glob, os, socket, sys, urllib2
    import notifiche
except:
    import sys
    sys.exit("Non sono disponibili tutti i moduli necessari.")
socket.setdefaulttimeout(35) # Timeout in secondi del fetching delle pagine (vedi urllib2)

def email_errori(URL, filedb=''):
    try:
        mail = notifiche.email(mittente     = 'Bigliettaro <bigliettaro@gelma.net>',
                               destinatario = ['Bigliettaro <bigliettaro@gelma.net>'],
                               oggetto      = 'Bigliettaro: '+URL+' '+filedb,
                               testo        = 'Discrepanza: '+URL+' '+filedb,
                               invia_subito = True) # Se da Aggiornare, vedi Guida Intergalattica alla LugMap §4.1
    except: # se fallisce l'invio stampo la mail, contando sul delivery di cron
        print 'Bigliettaro: discrepanza', URL, filedb

if __name__ == "__main__":
    for URL in ('http://lugmap.linux.it/db/', 'http://lugmap.it/db/'):
        for filedb in glob.glob( os.path.join( sys.path[0]+'/../db/', '*.txt') ): # piglio ogni file db
            fileURL = URL + filedb.split('/')[-1] # dell'intero path, prendo il solo nome file
            richiesta_file_db = urllib2.Request(fileURL, None, {"User-Agent":"Bot: http://lugmap.linux.it - lugmap@linux.it"})
            try:
                contenuto_remoto = urllib2.urlopen(richiesta_file_db).read()
            except:
                email_errori(fileURL, ': impossibile leggere il file remoto '+filedb)
                continue
            contenuto_locale = open(filedb, 'r').read()
            if contenuto_locale != contenuto_remoto:
                email_errori(fileURL, filedb)
