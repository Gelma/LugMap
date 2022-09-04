#!/bin/bash

# questo è lo script che si occupa dell'aggiornamento di http://lugmap.linux.it
# pigliando il relativo branch da GitHub.
# Bada che questo script non viene eseguito in automatico, sicché eventuali
# modifiche vanno segnalate a Fabio Invernizzi <fabulus@linux.it>

PATH_SITO='/var/www/lugmap'

fallito_aggiornamento() {
	# segnalo via mail problemi sull'aggiornamento, se possibile
	[ -e /usr/bin/mail ] && echo "Problema aggiornamento git-pull lugmap.linux.it" | /usr/bin/mail -s "LugMap.linux.it: errore git-pull" lugmap@lists.linux.it
	# sputo qualcosa anche in output, contando che venga intercettato da cron.
	echo "LugMap.linux.it: errore git-pull"
	exit
}

cd $PATH_SITO

su -c "/usr/bin/git pull -q https://github.com/Gelma/LugMap.git lugmap.linux.it" www-data || fallito_aggiornamento
su -c "/bin/date -d @$(git log -n 1 --pretty='%at')  > .ultimo_commit" www-data
