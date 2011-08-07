#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""Controllo l'output del widget di lugmap.it
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

try:
    import itertools, os, shelve, socket, StringIO, sys, urllib2
except:
    import sys
    sys.exit("Non sono disponibili tutti i moduli di base.")
try:
    import Image, notifiche
except:
    sys.exit('Fallito import moduli specifici: python-imaging e/o notifiche.py')

socket.setdefaulttimeout(35) # Timeout in secondi del fetching delle pagine (vedi urllib2)
elenco_regioni = ['abruzzo', 'basilicata', 'calabria', 'campania', 'emilia', 'friuli', 'Italia', 'lazio', 'liguria', 'lombardia', 'marche', 'molise', 'piemonte', 'puglia', 'sardegna', 'sicilia', 'toscana', 'trentino', 'umbria', 'valle', 'veneto']

def email_errori(URL, testo):
    try:
        mail = notifiche.email(mittente     = 'Visionario <visionario@gelma.net>',
                               destinatario = ['Visionario <visionario@gelma.net>'],
                               oggetto      = 'Visionario: '+URL,
                               testo        = testo,
                               invia_subito = True) # Se da Aggiornare, vedi Guida Intergalattica alla LugMap §4.1
    except: # se fallisce l'invio stampo la mail, contando sul delivery di cron
        print 'Visionario: controlla', URL

if __name__ == "__main__":
    try:
        db = shelve.open(os.path.join(os.environ["HOME"]+'/.visionario.db'))
    except:
        sys.exit('Problemi sul DB')

    for regione in elenco_regioni:
        url = 'http://lugmap.it/forge/lug-o-matic/widget.php?region=%s&format=image' % regione
        try:
            img_new = Image.open(StringIO.StringIO(urllib2.urlopen(urllib2.Request(url, None, {"User-Agent":"Bot: http://lugmap.linux.it - lugmap@linux.it"})).read()))
        except:
            email_errori(url, 'Fallito fetch immagine')
            continue

        try:
            img_old = db[regione] # inizio confronto
        except:
            db[regione] = img_new
            continue

        if img_old.size != img_new.size:
            email_errori(url, 'Dimensione diversa')
            continue

        db[regione] = img_new # per evitare noie con il pickling, salvo subito l'immagine nel db

        pairs = itertools.izip(img_old.getdata(), img_new.getdata()) # pescato da Stack Overflow
        if len(img_old.getbands()) == 1:
            dif = sum(abs(p1-p2) for p1, p2 in pairs)
        else:
            dif = sum(abs(c1-c2) for p1, p2 in pairs for c1, c2 in zip(p1, p2))
        ncomponents = img_new.size[0] * img_new.size[1] * 3
        differenza = (dif / 255.0 * 100) / ncomponents

        if differenza != 0:
            email_errori(url, 'Immagine cambiata: '+str(differenza))
