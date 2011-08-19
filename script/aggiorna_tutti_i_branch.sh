#! /bin/bash
# Script stupido per aggiornare tutti i branch del repository LugMap
# Deve essere eseguito in un qualsiasi punto del repo stesso

# controllo di essere nel posto giusto
git remote -v | /bin/grep 'git@github.com:Gelma/LugMap.git (fetch)' -q || exit

dir_backup_prima_di_aggiornamento=/tmp/lugmap_bk
branch_attuale=$(git branch | /bin/grep '^*' | /usr/bin/cut -f2 -d ' ')
repo_locale=$(git rev-parse --show-cdup)

/bin/rm -rf "$dir_backup_prima_di_aggiornamento" # facciamo un backup iniziale per evitare di perdere modifiche non committate
[ "$repo_locale" = "" ] && repo_locale="." # sono nella root del repo
/bin/cp -af "$repo_locale" "$dir_backup_prima_di_aggiornamento"

for branch in $(git branch -r | /bin/grep -v 'HEAD' | /usr/bin/cut -f 2 -d '/') # per ogni branch remoto
do
   git branch | cut -b3- | /bin/grep -q \^$branch\$ || git checkout --track -b $branch origin/$branch # controllo che esista localmente, diversamente lo creo
   git checkout $branch && git pull origin $branch && echo "Aggiornato $branch" # checkout e pull
done

git checkout $branch_attuale
