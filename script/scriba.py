#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""Esporto la LugMap come tabella in sintonia con la pagina:
   http://it.wikipedia.org/wiki/Lista_dei_LUG_italiani
   Con un banale copia/incolla è possibile quindi aggiornare il
   contenuto della stessa.

   Copyright 2010-2014 - Andrea Gelmini (andrea.gelmini@gelma.net)

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
    import glob, os, csv, sys
except:
    import sys
    sys.exit("Non sono disponibili tutti i moduli necessari.")

regioni = {}
sostituzioni = ('Emilia', 'Friuli', 'Trentino', 'Valle', 'Emilia-Romagna', 'Friuli-Venezia Giulia', 'Trentino-Alto Adige', "Valle d'Aosta")

def parsa():

    global regioni;

    for filedb in glob.glob( os.path.join( sys.path[0]+'/../db/', '*.txt') ): # piglio ogni file db
        regione = filedb.split('/')[-1][:-4] # dell'intero path, prendo il solo nome file
        regione = regione.capitalize()
        for posizione, voce in enumerate(sostituzioni):
            if regione == voce:
                regione = sostituzioni[posizione + 4]
                break
        regioni[regione] = {}
        for riga in csv.reader(open(filedb, "r"), delimiter='|', quoting=csv.QUOTE_NONE): # e per ogni voce
            provincia, denominazione, sito = riga[0], riga[1], riga[3]
            if 'facebook' in sito or sito.endswith('.tk/'): # Wikipedia non accetta link esterni ai gruppi Facebook, così come a servizi di short URL
                sito = 'http://lugmap.linux.it/'+filedb.split('/')[-1][:-4] # quindi puntiamo alla pagina della LugMap della regione in oggetto
            try:
                regioni[regione][provincia][denominazione]=sito
            except:
                regioni[regione][provincia]={}
                regioni[regione][provincia][denominazione]=sito

    return

    # questo lo lasciamo come promemoria futuro della struttura dati
    for regione in sorted(regioni.keys()):
        print regione
        for provincia in sorted(regioni[regione].keys()):
            print 4*' ',provincia
            for lug in sorted(regioni[regione][provincia].keys()):
                print 8*' ',lug,'-', regioni[regione][provincia][lug]

def stampa():
    print '{| border=1\n|-bgcolor="#CCCCCC"\n! Regione\n! Provincia\n! Lug\n\n|-\n'

    regioni_ordinate_stampa = sorted(regioni.keys()) # vogliamo 'Italia' in testa, e poi il resto delle regioni
    regioni_ordinate_stampa.remove('Italia')
    regioni_ordinate_stampa.insert(0,'Italia')

    for regione in regioni_ordinate_stampa:
        print "| rowspan=\"%s\"|'''[[%s]]'''" % (len(regioni[regione].keys()), regione)
        print "| {",
        for provincia in sorted(regioni[regione].keys()):
            print "| %s" % provincia
            print "| ",
            for posizione, lug in enumerate(sorted(regioni[regione][provincia].keys())):
                print '[%s %s]' % (regioni[regione][provincia][lug], lug),
                if posizione+1 != len(regioni[regione][provincia]): print '&mdash;',
            print "\n|-"
    print "|}\n"

if __name__ == "__main__":
    parsa()
    stampa()
