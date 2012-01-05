#! /bin/bash
# Script stupido per aggiornare l'anno del copyright
# Gli anni vanno aggiornati manualmente...

# controllo di essere nel posto giusto
git remote -v | /bin/grep 'git@github.com:Gelma/LugMap.git (fetch)' -q || exit

dir_backup_prima_di_aggiornamento=/tmp/lugmap_bk
branch_attuale=$(git branch | /bin/grep '^*' | /usr/bin/cut -f2 -d ' ')
repo_locale=$(git rev-parse --show-cdup)

/bin/rm -rf "$dir_backup_prima_di_aggiornamento" # facciamo un backup iniziale per evitare di perdere modifiche non committate
[ "$repo_locale" = "" ] && repo_locale="." # sono nella root del repo
/bin/cp -af "$repo_locale" "$dir_backup_prima_di_aggiornamento"

/usr/bin/find -type f | /bin/egrep '(.py|.html|.txt|.php)$' | /usr/bin/xargs /bin/grep -il 'Copyright 2010-2011' | /usr/bin/xargs /bin/sed -i 's/Copyright 2010-2011/Copyright 2010-2012/g'
