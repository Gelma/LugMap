#!/bin/bash

fallito_aggiornamento() {
	# segnalo via mail problemi sull'aggiornamento, se possibile
	[ -e /usr/sbin/sendmail ] && echo "Problema aggiornamento git-pull lugmap.linux.it" | /usr/sbin/sendmail -s "LugMap.linux.it: errore git-pull" lugmap@lists.linux.it
	# sputo qualcosa anche in output, contando che venga intercettato da cron.
	echo "LugMap.linux.it: errore git-pull"
	exit
}

cd $PATH || fallito_aggiornamento
/usr/bin/git pull -q git@github.com:Gelma/LugMap.git lugmap.linux.it || fallito_aggiornamento
