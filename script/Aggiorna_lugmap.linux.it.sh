#!/bin/bash

PATH_SITO='/var/www/lugmap'

fallito_aggiornamento() {
	# segnalo via mail problemi sull'aggiornamento, se possibile
	[ -e /usr/bin/mail ] && echo "Problema aggiornamento git-pull lugmap.linux.it" | /usr/bin/mail -s "LugMap.linux.it: errore git-pull" lugmap@lists.linux.it
	# sputo qualcosa anche in output, contando che venga intercettato da cron.
	echo "LugMap.linux.it: errore git-pull"
	exit
}

su -c "cd $PATH_SITO && /usr/bin/git pull -q git://github.com/Gelma/LugMap.git lugmap.linux.it" www-data || fallito_aggiornamento
