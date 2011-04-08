#!/usr/bin/python
# -*- coding: utf-8 -*-
"""
Scripts to manage categories.

Syntax: python category.py action [-option]

where action can be one of these:
 * add         - mass-add a category to a list of pages
 * remove      - remove category tag from all pages in a category
 * move        - move all pages in a category to another category
 * tidy        - tidy up a category by moving its articles into subcategories
 * tree        - show a tree of subcategories of a given category
 * listify     - make a list of all of the articles that are in a category

and option can be one of these:

Options for "add" action:
 * -person     - sort persons by their last name
 * -create     - If a page doesn't exist, do not skip it, create it instead

If action is "add", the following options are supported:

&params;

Options for "listify" action:
 * -overwrite  - This overwrites the current page with the list even if
                 something is already there.
 * -showimages - This displays images rather than linking them in the list.
 * -talkpages  - This outputs the links to talk pages of the pages to be
                 listified in addition to the pages themselves.

Options for "remove" action:
 * -nodelsum   - This specifies not to use the custom edit summary as the
                 deletion reason.  Instead, it uses the default deletion reason
                 for the language, which is "Category was disbanded" in English.

Options for several actions:
 * -rebuild    - reset the database
 * -from:      - The category to move from (for the move option)
                 Also, the category to remove from in the remove option
                 Also, the category to make a list of in the listify option
 * -to:        - The category to move to (for the move option)
               - Also, the name of the list to make in the listify option
         NOTE: If the category names have spaces in them you may need to use
         a special syntax in your shell so that the names aren't treated as
         separate parameters.  For instance, in BASH, use single quotes,
         e.g. -from:'Polar bears'
 * -batch      - Don't prompt to delete emptied categories (do it
                 automatically).
 * -summary:   - Pick a custom edit summary for the bot.
 * -inplace    - Use this flag to change categories in place rather than
                 rearranging them.
 * -recurse    - Recurse through all subcategories of categories.
 * -match      - Only work on pages whose titles match the given regex (for
                 move and remove actions).

For the actions tidy and tree, the bot will store the category structure
locally in category.dump. This saves time and server load, but if it uses
these data later, they may be outdated; use the -rebuild parameter in this
case.

For example, to create a new category from a list of persons, type:

  python category.py add -person

and follow the on-screen instructions.

Or to do it all from the command-line, use the following syntax:

  python category.py move -from:US -to:'United States'

This will move all pages in the category US to the category United States.

"""

#
# (C) Rob W.W. Hooft, 2004
# (C) Daniel Herding, 2004
# (C) Wikipedian, 2004-2008
# (C) leogregianin, 2004-2008
# (C) Cyde, 2006-2010
# (C) Anreas J Schwab, 2007
# (C) Pywikipedia team, 2008-2009
#
__version__ = '$Id$'
#
# Distributed under the terms of the MIT license.
#

import os, re, pickle, bz2
import wikipedia as pywikibot
import catlib, config, pagegenerators

# This is required for the text that is shown when you run this script
# with the parameter -help.
docuReplacements = {
    '&params;': pagegenerators.parameterHelp
}
# Summary messages
msg_add={
    'af': u'Robot: [[Kategorie:%(newcat)s]] bygevoeg',
    'ar': u'روبوت: إضافة [[تصنيف:%(newcat)s]]',
    'az': u'Robot: [[Kateqoriya:%(newcat)s]] əlavəsı',
    'bar': u'Bot: Aini [[Kategorie:%(newcat)s]]',
    'bat-smg': u'Robots: Pridedama [[Kateguorėjė:%(newcat)s]]',
    'be-tarask': u'Робат: дададзеная [[Category:%(newcat)s]]',
    'be-x-old': u'Робат: дадаваньне [[Катэгорыя:%(newcat)s]]',
    'br': u'Robot : Oc\'h ouzhpennañ [[Rummad:%(newcat)s]]',
    'bs': u'Robot: dodaje [[Category:%(newcat)s]]',
    'ca': u'Robot: Afegint [[Categoria:%(newcat)s]]',
    'cs': u'Robot přidal [[Kategorie:%(newcat)s]]',
    'da': u'Robot: Tilføjer [[Kategori:%(newcat)s]]',
    'de': u'Bot: Ergänze [[Kategorie:%(newcat)s]]',
    'el': u'Ρομπότ: Προσθήκη [[Κατηγορία:%(newcat)s]]',
    'en': u'Robot: Adding [[Category:%(newcat)s]]',
    'eo': u'Robot: Aldonis [[Kategorion:%(newcat)s]]',
    'es': u'Bot: Añadida [[Categoría:%(newcat)s]]',
    'fa': u'ربات: افزودن [[رده:%(newcat)s]]',
    'fi': u'Botti lisäsi luokkaan [[Luokka:%(newcat)s]]',
    'fr': u'Robot : ajoute [[Catégorie:%(newcat)s]]',
    'frp': u'Robot : apond [[Catègorie:%(newcat)s]]',
    'gl': u'Bot: Engado [[Category:%(newcat)s]]',
    'he': u'בוט: מוסיף [[קטגוריה:%(newcat)s]]',
    'hu': u'[[Kategória:%(newcat)s]] hozzáadása bottal',
    'hy': u'Ռոբոտ․ Ավելացվել է [[Կատեգորիա:%(newcat)s]]',
    'ia': u'Robot: Addition de [[Categoria:%(newcat)s]]',
    'id': u'Bot: Menambahkan [[Kategori:%(newcat)s]]',
    'is': u'Vélmenni: Bæti við [[Flokkur:%(newcat)s]]',
    'it': u'Bot: Aggiungo [[Categoria:%(newcat)s]]',
    'ja': u'ロボットによる: カテゴリ追加 [[Category:%(newcat)s]]',
    'kk': u'Бот: [[Санат:%(newcat)s]] үстеді',
    'ko': u'로봇: [[분류:%(newcat)s]] 추가',
    'ksh': u'Bot: [[Saachjropp:%(newcat)s]] erinjedonn',
    'la': u'automaton: addens [[Categoria:%(newcat)s]]',
    'lb': u'Bot: Derbäi setzen [[Kategorie:%(newcat)s]]',
    'lt': u'robotas: Pridedama [[Kategorija:%(newcat)s]]',
    'mk': u'Робот: Додавам [[Категорија:%(newcat)s]]',
    'ms': u'Bot: Menambah [[Kategori:%(newcat)s]]',
    'my': u'ရိုဘော့ - [[ကဏ္ဍ - %(newcat)s]]ကို ပေါင်းထည့်နေသည်',
    'nds': u'Kat-Bot: [[Kategorie:%(newcat)s]] rin',
    'nds-nl': u'bot: [[kattegerie:%(newcat)s]] derbie edaon',
    'ne': u'↓ रोबोट:  [[Category:%(newcat)s]] थप्दै',
    'nl': u'Robot: [[Categorie:%(newcat)s]] toegevoegd',
    'nn': u'robot: la til [[Kategori:%(newcat)s]]',
    'no': u'Robot: Legger til [[Kategori:%(newcat)s]]',
    'pdc': u'Waddefresser: [[Kategorie:%(newcat)s]] dezu geduh',
    'pfl': u'Bot: [[Kategorie:%(newcat)s]] aigfiecht',
    'pl': u'Robot dodaje [[Category:%(newcat)s]]',
    'pt': u'Robô: A adicionar [[Categoria:%(newcat)s]]',
    'ro': u'Robot: Adăugat [[Category:%(newcat)s]]',
    'ru': u'Робот: добавление [[Категория:%(newcat)s]]',
    'rue': u'Робот: додаваня [[Катеґорія:%(newcat)s]]',
    'sk': u'Robot pridal [[Kategória:%(newcat)s]]',
    'sl': u'Robot: Dodajanje [[Kategorija:%(newcat)s]]',
    'sr': u'Робот: додавање [[Category:%(newcat)s]]',
    'sv': u'Robot: Lägger till [[Kategori:%(newcat)s]]',
    'szl': u'Bot dodowo: [[Kategoria:%(newcat)s]]',
    'te': u'బాటు: [[వర్గం:%(newcat)s]] వర్గాన్ని చేర్చింది',
    'tr': u'Robot: Ekleme [[Kategori:%(newcat)s]]',
    'tt-cyrl': u'Робот: өстәү [[Төркем:%(newcat)s]]',
    'uk': u'Робот: додавання [[Категорія:%(newcat)s]]',
    'vi': u'Rôbốt: Thêm [[Thể loại:%(newcat)s]]',
    'vo': u'Bot: läükon: [[Klad:%(newcat)s]]',
    'zh': u'機器人:新增目錄 [[Category:%(newcat)s]]',
    'zh-hans': u'机器人：添加 [[Category:%(newcat)s]]',
    }

msg_change={
    'af': u'Robot: wysig %(oldcat)s',
    'als': u'Bot: %(oldcat)s gänderet',
    'ar': u'روبوت: تغيير %(oldcat)s',
    'az': u'Robot: %(oldcat)s dəyişdirildi',
    'bar': u'Bot: %(oldcat)s obàsst',
    'be-tarask': u'Робат: зьмена %(oldcat)s',
    'be-x-old': u'Робат: зьмена %(oldcat)s',
    'br': u'Robot : O kemmañ %(oldcat)s',
    'bs': u'Robot: mijenja %(oldcat)s',
    'ca': u'Robot: Canviant %(oldcat)s',
    'cs': u'Robot změnil [[%(oldcat)s]]→[[%(newcat)s]]',
    'da': u'Robot: Ændrer %(oldcat)s',
    'de': u'Bot: Ändere %(oldcat)s',
    'el': u'Ρομπότ: Αλλαγή %(oldcat)s',
    'en': u'Robot: Changing %(oldcat)s',
    'eo': u'Roboto: ŝanĝo de %(oldcat)s',
    'es': u'Bot: Cambiada %(oldcat)s',
    'fa': u'ربات:تغییر %(oldcat)s',
    'fi': u'Botti muutti luokan %(oldcat)s',
    'fr': u'Robot : modifie [[%(oldcat)s]]',
    'frp': u'Robot : change [[%(oldcat)s]]',
    'gl': u'Bot: Cambio %(oldcat)s',
    'he': u'בוט: משנה %(oldcat)s',
    'hu': u'Módosítás bottal: [[%(oldcat)s]]→[[%(newcat)s]]',
    'hy': u'Ռոբոտ․ Փոփոխվել է %(oldcat)s',
    'ia': u'Robot: Modification de %(oldcat)s',
    'id': u'Bot: Mengganti %(oldcat)s',
    'is': u'Vélmenni: Breyti flokknum [[%(oldcat)s]]',
    'it': u'Bot: Modifico %(oldcat)s',
    'ja': u'ロボットによる: カテゴリ変更 [[%(oldcat)s]]→[[%(newcat)s]]',
    'kk': u'Бот: %(oldcat)s дегенді түзетті',
    'ko': u'로봇: %(oldcat)s 수정',
    'ksh': u'Bot: %(oldcat)s ußjewääßelt',
    'la': u'automaton: mutans %(oldcat)s→[[%(newcat)s]]',
    'lb': u'Bot: Ännere vu(n) %(oldcat)s',
    'lt': u'robotas: Keičiama %(oldcat)s',
    'mk': u'Робот: Ја менувам %(oldcat)s',
    'ms': u'Bot: Menukar %(oldcat)s',
    'my': u'ရိုဘော့ - %(oldcat)sကို ပြောင်းနေသည်',
    'nds': u'Kat-Bot: %(oldcat)s utwesselt',
    'nds-nl': u'bot: wieziging %(oldcat)s',
    'ne': u'↓ रोबोट: ले %(oldcat)s मा परिवर्तन गरेको',
    'nl': u'Robot: wijziging %(oldcat)s',
    'nn': u'robot: endra %(oldcat)s',
    'no': u'Robot: Endrer %(oldcat)s',
    'pdc': u'Waddefresser: Abdeeling vun %(oldcat)s nooch %(newcat)s geennert',
    'pfl': u'Bot: %(oldcat)s gä\'ännat',
    'pl': u'Robot przenosi %(oldcat)s',
    'pt': u'Robô: A modificar %(oldcat)s',
    'ro': u'Robot: Schimbat %(oldcat)s',
    'ru': u'Робот: изменение %(oldcat)s',
    'rue': u'Робот: зміна %(oldcat)s',
    'sk': u'Robot zmenil [[%(oldcat)s]]→[[%(newcat)s]]',
    'sl': u'Robot: Spreminjanje %(oldcat)s',
    'sr': u'Робот: мењање %(oldcat)s',
    'sv': u'Robot: Ändrar %(oldcat)s',
    'te': u'బాటు: %(oldcat)s వర్గాన్ని మార్చింది',
    'tr': u'Robot: %(oldcat)s değiştiriliyor',
    'tt-cyrl': u'Робот: %(oldcat)s үзгәртү',
    'uk': u'Робот: зміна %(oldcat)s',
    'vi': u'Rôbốt: Thay đổi %(oldcat)s',
    'zh': u'機器人:變更目錄 [[%(oldcat)s]]→[[%(newcat)s]]',
    'zh-hans': u'机器人：改换 %(oldcat)s',
    }

msg_replace={
    'af': u'Robot: kategorie %(oldcat)s is vervang met %(newcat)s',
    'als': u'Bot: Kategori %(oldcat)s uustuscht dur %(newcat)s',
    'ar': u'روبوت: استبدال التصنيف %(oldcat)s ب %(newcat)s',
    'az': u'Robot: %(oldcat)s kateqoriyasının %(newcat)s ilə əvəzlənməsi',
    'be-tarask': u'Робат: замена катэгорыі %(oldcat)s на %(newcat)s',
    'br': u'Robot : Oc\'h erlec\'hiañ ar rummad %(oldcat)s gant %(newcat)s',
    'bs': u'Robot: Mijenja kategoriju %(oldcat)s sa %(newcat)s',
    'cs': u'Robot nahradil kategorii %(oldcat)s za %(newcat)s',
    'de': u'Bot: Ersetze Kategorie %(oldcat)s durch %(newcat)s',
    'el': u'Ρομπότ: Αντικατάσταση της κατηγορίας %(oldcat)s με την %(newcat)s',
    'en': u'Robot: Replacing category %(oldcat)s with %(newcat)s',
    'eo': u'Roboto: anstataŭigis %(oldcat)s per $(newcat)s',
    'fa': u'ربات جایگزینی رده %(oldcat)s با %(newcat)s',
    'fi': u'Botti korvasi luokan %(oldcat)s luokalla %(newcat)s',
    'fr': u'Robot : Remplacement de la catégorie %(oldcat)s avec %(newcat)s',
    'frp': u'Robot : remplacement de la catègorie %(oldcat)s avouéc %(newcat)s',
    'gl': u'Bot: Substitución da categoría %(oldcat)s pola %(newcat)s',
    'he': u'בוט מחליף את הקטגוריה %(oldcat)s בקטגוריה %(newcat)s',
    'hu': u'Bot: következő kategória cseréje: %(oldcat)s erre: %(newcat)s',
    'hy': u'Ռոբոտ․ %(oldcat)s կատեգորիան փոխարինվել է %(newcat)s –ով։',
    'ia': u'Robot: Reimplacia categoria %(oldcat)s per %(newcat)s',
    'id': u'Bot: Mengganti kategori %(oldcat)s dengan %(newcat)s',
    'it': u'Bot: Sostituzione di %(oldcat)s con %(newcat)s',
    'ja': u'ロボットによる: カテゴリ変更 [[%(oldcat)s]]→[[%(newcat)s]]',
    'ksh': u'Bot: [[%(oldcat)s]] jääje [[%(newcat)s]] ußjetuusch.',
    'la': u'automaton: mutans categoriam %(oldcat)s→[[%(newcat)s]]',
    'lb': u'Bot: Ersetze vun der Kategorie %(oldcat)s duerch %(newcat)s',
    'mk': u'Робот: Ја заменувам категоријата %(oldcat)s со %(newcat)s',
    'ms': u'Bot: Menggantikan kategori %(oldcat)s dengan %(newcat)s',
    'my': u'ရိုဘော့ - %(oldcat)s ကို %(newcat)s ဖြင့် အစားထိုးနေသည်',
    'ne': u'↓ रोबोट: %(oldcat)s श्रेणी लाइ %(newcat)s संग साट्दै',
    'nl': u'Robot: categorie %(oldcat)s is vervangen door %(newcat)s',
    'no': u'Robot: Erstatter kategorien %(oldcat)s med %(newcat)s',
    'pdc': u'Waddefresser: Abdeeling von %(oldcat)s nooch %(newcat)s geennert',
    'pl': u'Robot zastępuje kategorię %(oldcat)s przez %(newcat)s',
    'pt': u'Robô: A substituir a categoria %(oldcat)s por %(newcat)s',
    'ro': u'Robot: Înlocuit categoria %(oldcat)s cu %(newcat)s',
    'ru': u'Робот: Замена категории %(oldcat)s на %(newcat)s',
    'rue': u'Робот: заміна катеґорії %(oldcat)s на %(newcat)s',
    'sk': u'Robot nahradil kategóriu %(oldcat)s za %(newcat)s',
    'sl': u'Robot: Zamenjava kategorije %(oldcat)s z/s %(newcat)s',
    'sr': u'Робот: мењање категорије %(oldcat)s са %(newcat)s',
    'sv': u'Robot: Ersätter kategorin %(oldcat)s med %(newcat)s',
    'tr': u'Robot: %(oldcat)s, %(newcat)s kategorisiyle değiştirildi.',
    'tt-cyrl': u'Робот: Әлеге төркемне %(oldcat)s  %(newcat)s төркеменә алмаштыру',
    'uk': u'Робот: заміна категорії %(oldcat)s на %(newcat)s',
    'vi': u'Rôbốt: Thay thể loại %(oldcat)s bằng %(newcat)s',
    'zh': u'機器人:變更目錄 [[%(oldcat)s]]→[[%(newcat)s]]',
    'zh-hans': u'机器人：变更目录[[%(oldcat)s]]→[[%(newcat)s]]',
    }

deletion_reason_move = {
    'af': u'Robot: kategorie is geskuif na [[:Category:%(newcat)s|%(title)s]]',
    'als': u'Bot: Kategori isch no [[:Kategorie:%(newcat)s|%(title)s]] verschobe wore',
    'ang': u'Searuþrǣl: Flocc ƿæs ƿeȝed tō [[:Category:%(newcat)s|%(title)s]]',
    'ar': u'روبوت: التصنيف نقل إلى [[:تصنيف:%(newcat)s|%(title)s]]',
    'az': u'Robot: Kateqoriya köçürüldü: [[:Category:%(newcat)s|%(title)s]]',
    'bat-smg': u'Robots: Kateguorėjė bova parvadėnta i [[:Kateguorėjė:%(newcat)s|%(title)s]]',
    'be-tarask': u'Робат: катэгорыя перанесеная ў [[:Category:%(newcat)s|%(title)s]]',
    'be-x-old': u'Робат: катэгорыя перайменаваная ў [[:Катэгорыя:%(newcat)s|%(title)s]]',
    'bn': u'রোবট: বিষয়শ্রেণী [[:বিষয়শ্রেণী:%(newcat)s|%(title)s]]-এ স্থানান্তরিত হয়েছে',
    'br': u'Robot : Rummad dilec\'hiet da [[:Category:%(newcat)s|%(title)s]]',
    'bs': u'Robot: Kategorija je premještena u [[:Category:%(newcat)s|%(title)s]]',
    'ca': u'Robot: La categoria s\'ha mogut a [[:Categoria:%(newcat)s|%(title)s]]',
    'cs': u'Kategorie přesunuta na [[:Kategorie:%(newcat)s|%(title)s]]',
    'da': u'Robot: Kategori flyttet til [[:Category:%(newcat)s|%(title)s]]',
    'de': u'Bot: Kategorie wurde nach [[:Kategorie:%(newcat)s|%(title)s]] verschoben',
    'el': u'Ρομπότ: Η κατηγορία μετακινήθηκε στην [[:Κατηγορία:%(newcat)s|%(title)s]]',
    'en': u'Robot: Category was moved to [[:Category:%(newcat)s|%(title)s]]',
    'eo': u'Roboto: Kategorio estas movita al [[:Category:%(newcat)s|%(title)s]]',
    'es': u'Robot: La categoría ha sido movida a [[:Category:%(newcat)s|%(title)s]]',
    'fa': u'ربات:رده به رده  [[:رده:%(newcat)s|%(title)s]] منتقل شده‌است',
    'fi': u'Botti siirsi luokan nimelle [[:Luokka:%(newcat)s|%(title)s]]',
    'fr': u'Robot : catégorie déplacée sur [[:Category:%(newcat)s|%(title)s]]',
    'frp': u'Robot : catègorie dèplaciê vers [[:Catègorie:%(newcat)s|%(title)s]]',
    'gl': u'Bot: A categoría trasladouse a [[:Category:%(newcat)s|%(title)s]]',
    'he': u'בוט: הקטגוריה הועברה לשם [[:קטגוריה:%(newcat)s|%(title)s]]',
    'hu': u'A bot áthelyezte a kategória tartalmát ide: [[:Kategória:%(newcat)s|%(title)s]]',
    'hy': u'Ռոբոտ․ Կատեգորիան տեղափոխվեց  [[:Կատեգորիա:%(newcat)s|%(title)s]]',
    'ia': u'Robot: Categoria transferite a [[:Category:%(newcat)s|%(title)s]]',
    'id': u'Bot: Kategori dipindahkan ke [[:Category:%(newcat)s|%(title)s]]',
    'it': u'Bot: La categoria è stata sostituita da [[:Categoria:%(newcat)s|%(title)s]]',
    'ja': u'ロボットによる: カテゴリ [[:Category:%(newcat)s|%(title)s]]へ移動',
    'kk': u'Бот: Санат [[:Санат:%(newcat)s|%(title)s]] дегенге жылжытылды',
    'ko': u'로봇: 분류가 [[:분류:%(newcat)s|%(title)s]]로 옮겨짐',
    'ksh': u'Bot: Saachjropp noh [[:Category:%(newcat)s|%(title)s]] jeschovve',
    'la': u'automaton: categoria mota est ad [[:Category:%(newcat)s|%(title)s]]',
    'lb': u'Bot: Kategorie gouf geréckelt: Nei [[:Kategorie:%(newcat)s|%(title)s]]',
    'lt': u'robotas: Kategorija pervadinta į [[:Category:%(newcat)s|%(title)s]]',
    'mk': u'Робот: Категоријата е преместена во [[:Категорија:%(newcat)s|%(title)s]]',
    'ms': u'Bot: Kategori telah dipindahkan ke [[:Kategori:%(newcat)s|%(title)s]]',
    'my': u'ရိုဘော့ - ကဏ္ဍကို [[- ကဏ္ဍ - %(newcat)s|%(title)s]]သို့ ရွှေ့လိုက်သည်',
    'nds': u'Kat-Bot: Kategorie na [[:Category:%(newcat)s|%(title)s]] schaven',
    'nds-nl': u'Bot: kattegerie is herneumd naor [[:Kattegerie:%(newcat)s|%(title)s]]',
    'ne': u'↓ Robot: श्रेणीलाइ  [[:Category:%(newcat)s|%(title)s]] मा सारियो',
    'nl': u'Robot: categorie is hernoemd naar [[:Category:%(newcat)s|%(title)s]]',
    'nn': u'robot: kategorien blei flytta til [[:Kategori:%(newcat)s|%(title)s]]',
    'no': u'Robot: Kategorien ble flyttet til [[:Category:%(newcat)s|%(title)s]]',
    'pdc': u'Waddefresser: Abdeeling iss gezoge warre nooch [[:Kategorie:%(newcat)s|%(title)s]].',
    'pl': u'Robot przenosi kategorię do [[:Category:%(newcat)s|%(title)s]]',
    'pt': u'Robô: A categoria foi movida para [[:Category:%(newcat)s|%(title)s]]',
    'ro': u'Robot: Categoria a fost mutată la [[:Category:%(newcat)s|%(title)s]]',
    'ru': u'Робот: категория переименована в [[:Категория:%(newcat)s|%(title)s]]',
    'rue': u'Робот: катеґорія переменована на [[:Катеґорія:%(newcat)s|%(title)s]]',
    'sk': u'Kategória bola presunutá na [[:Kategória:%(newcat)s|%(title)s]]',
    'sl': u'Robot: Kategorija je bila prestavljena na [[:Category:%(newcat)s|%(title)s]]',
    'sr': u'Робот: категорија је премештена у [[:Category:%(newcat)s|%(title)s]]',
    'sv': u'Robot: Kategori flyttades till [[:Category:%(newcat)s|%(title)s]]',
    'tr': u'Robot: Kategori şuraya taşındı [[:Category:%(newcat)s|%(title)s]]',
    'tt-cyrl': u'Робот: Төркемнең исеме  [[:Төркем:%(newcat)s|%(title)s]] битенә күчерелде',
    'uk': u'Робот: категорію перейменовано на [[:Категорія:%(newcat)s|%(title)s]]',
    'vi': u'Rôbốt: Di chuyển thể loại qua [[:Thể loại:%(newcat)s|%(title)s]]',
    'vo': u'bot petopätükon kladi lü [[:Klad:%(newcat)s|%(title)s]]',
    'zh': u'機器人:移動目錄至 [[:Category:%(newcat)s|%(title)s]]',
    'zh-hans': u'机器人： 类别移动到 [[:Category:%(newcat)s|%(title)s]]',
    }

cfd_templates = {
    'wikipedia' : {
        'en':[u'cfd', u'cfr', u'cfru', u'cfr-speedy', u'cfm', u'cfdu'],
        'fi':[u'roskaa', u'poistettava', u'korjattava/nimi', u'yhdistettäväLuokka'],
        'he':[u'הצבעת מחיקה', u'למחוק'],
        'nl':[u'categorieweg', u'catweg', u'wegcat', u'weg2']
    },
    'commons' : {
        'commons':[u'cfd', u'move']
    }
}


class CategoryDatabase:
    '''This is a temporary knowledge base saving for each category the contained
    subcategories and articles, so that category pages do not need to be loaded
    over and over again

    '''
    def __init__(self, rebuild = False, filename = 'category.dump.bz2'):
        if rebuild:
            self.rebuild()
        else:
            try:
                if not os.path.isabs(filename):
                    filename = pywikibot.config.datafilepath(filename)
                f = bz2.BZ2File(filename, 'r')
                pywikibot.output(u'Reading dump from %s'
                                 % pywikibot.config.shortpath(filename))
                databases = pickle.load(f)
                f.close()
                # keys are categories, values are 2-tuples with lists as entries.
                self.catContentDB = databases['catContentDB']
                # like the above, but for supercategories
                self.superclassDB = databases['superclassDB']
                del databases
            except:
                # If something goes wrong, just rebuild the database
                self.rebuild()

    def rebuild(self):
        self.catContentDB={}
        self.superclassDB={}

    def getSubcats(self, supercat):
        '''For a given supercategory, return a list of Categorys for all its
        subcategories. Saves this list in a temporary database so that it won't
        be loaded from the server next time it's required.

        '''
        # if we already know which subcategories exist here
        if supercat in self.catContentDB:
            return self.catContentDB[supercat][0]
        else:
            subcatlist = supercat.subcategoriesList()
            articlelist = supercat.articlesList()
            # add to dictionary
            self.catContentDB[supercat] = (subcatlist, articlelist)
            return subcatlist

    def getArticles(self, cat):
        '''For a given category, return a list of Pages for all its articles.
        Saves this list in a temporary database so that it won't be loaded from the
        server next time it's required.

        '''
        # if we already know which articles exist here
        if cat in self.catContentDB:
            return self.catContentDB[cat][1]
        else:
            subcatlist = cat.subcategoriesList()
            articlelist = cat.articlesList()
            # add to dictionary
            self.catContentDB[cat] = (subcatlist, articlelist)
            return articlelist

    def getSupercats(self, subcat):
        # if we already know which subcategories exist here
        if subcat in self.superclassDB:
            return self.superclassDB[subcat]
        else:
            supercatlist = subcat.supercategoriesList()
            # add to dictionary
            self.superclassDB[subcat] = supercatlist
            return supercatlist

    def dump(self, filename = 'category.dump.bz2'):
        '''Saves the contents of the dictionaries superclassDB and catContentDB
        to disk.

        '''
        if not os.path.isabs(filename):
            filename = pywikibot.config.datafilepath(filename)
        if self.catContentDB or self.superclassDB:
            pywikibot.output(u'Dumping to %s, please wait...'
                             % pywikibot.config.shortpath(filename))
            f = bz2.BZ2File(filename, 'w')
            databases = {
                'catContentDB': self.catContentDB,
                'superclassDB': self.superclassDB
            }
            # store dump to disk in binary format
            try:
                pickle.dump(databases, f, protocol=pickle.HIGHEST_PROTOCOL)
            except pickle.PicklingError:
                pass
            f.close()
        else:
            try:
                os.remove(filename)
            except EnvironmentError:
                pass
            else:
                pywikibot.output(u'Database is empty. %s removed'
                                 % pywikibot.config.shortpath(filename))


class AddCategory:
    '''A robot to mass-add a category to a list of pages.'''

    def __init__(self, generator, sort_by_last_name=False, create=False,
                 editSummary='', dry=False):
        self.generator = generator
        self.sort = sort_by_last_name
        self.create = create
        self.site = pywikibot.getSite()
        self.always = False
        self.dry = dry
        self.newcatTitle = None
        self.editSummary = editSummary

    def sorted_by_last_name(self, catlink, pagelink):
        '''Return a Category with key that sorts persons by their last name.

        Parameters: catlink - The Category to be linked
                    pagelink - the Page to be placed in the category

        Trailing words in brackets will be removed. Example: If
        category_name is 'Author' and pl is a Page to [[Alexandre Dumas
        (senior)]], this function will return this Category:
        [[Category:Author|Dumas, Alexandre]]

        '''
        page_name = pagelink.title()
        site = pagelink.site()
        # regular expression that matches a name followed by a space and
        # disambiguation brackets. Group 1 is the name without the rest.
        bracketsR = re.compile('(.*) \(.+?\)')
        match_object = bracketsR.match(page_name)
        if match_object:
            page_name = match_object.group(1)
        split_string = page_name.split(' ')
        if len(split_string) > 1:
            # pull last part of the name to the beginning, and append the
            # rest after a comma; e.g., "John von Neumann" becomes
            # "Neumann, John von"
            sorted_key = split_string[-1] + ', ' + \
                         ' '.join(split_string[:-1])
            # give explicit sort key
            return pywikibot.Page(site, catlink.title() + '|' + sorted_key)
        else:
            return pywikibot.Page(site, catlink.title())

    def run(self):
        self.newcatTitle = pywikibot.input(
            u'Category to add (do not give namespace):')
        if not self.site.nocapitalize:
            self.newcatTitle = self.newcatTitle[:1].upper() + \
                               self.newcatTitle[1:]
        if not self.editSummary:
            self.editSummary = pywikibot.translate(self.site, msg_add) \
                               % {'newcat' : self.newcatTitle}
        counter = 0
        for page in self.generator:
            self.treat(page)
            counter += 1
        pywikibot.output(u"%d page(s) processed." % counter)

    def load(self, page):
        """
        Loads the given page, does some changes, and saves it.
        """
        try:
            # Load the page
            text = page.get()
        except pywikibot.NoPage:
            if self.create:
                pywikibot.output(u"Page %s doesn't exist yet; creating."
                                 % (page.title(asLink=True)))
                text = ''
            else:
                pywikibot.output(u"Page %s does not exist; skipping."
                                 % page.title(asLink=True))
        except pywikibot.IsRedirectPage, arg:
            redirTarget = pywikibot.Page(self.site, arg.args[0])
            pywikibot.output(u"WARNING: Page %s is a redirect to %s; skipping."
                             % (page.title(asLink=True),
                                redirTarget.title(asLink=True)))
        else:
            return text
        return None

    def save(self, text, page, comment, minorEdit=True, botflag=True):
        # only save if something was changed
        if text != page.get():
            # show what was changed
            pywikibot.showDiff(page.get(), text)
            pywikibot.output(u'Comment: %s' %comment)
            if not self.dry:
                if not self.always:
                    confirm = 'y'
                    while True:
                        choice = pywikibot.inputChoice(
                            u'Do you want to accept these changes?',
                            ['Yes', 'No', 'Always'], ['y', 'N', 'a'], 'N')
                        if choice == 'a':
                            confirm = pywikibot.inputChoice(u"""\
This should be used if and only if you are sure that your links are correct!
Are you sure?""", ['Yes', 'No'], ['y', 'n'], 'n')
                            if confirm == 'y':
                                self.always = True
                                break
                        else: break
                if self.always or choice == 'y':
                    try:
                        # Save the page
                        page.put(text, comment=comment,
                                 minorEdit=minorEdit, botflag=botflag)
                    except pywikibot.LockedPage:
                        pywikibot.output(u"Page %s is locked; skipping."
                                         % page.title(asLink=True))
                    except pywikibot.EditConflict:
                        pywikibot.output(
                            u'Skipping %s because of edit conflict'
                            % (page.title()))
                    except pywikibot.SpamfilterError, error:
                        pywikibot.output(
u'Cannot change %s because of spam blacklist entry %s'
                            % (page.title(), error.url))
                    else:
                        return True
        return False

    def treat(self, page):
        text = self.load(page)
        if text is None:
            return
        cats = page.categories()
        # Show the title of the page we're working on.
        # Highlight the title in purple.
        pywikibot.output(
            u"\n\n>>> \03{lightpurple}%s\03{default} <<<"
            % page.title())
        pywikibot.output(u"Current categories:")
        for cat in cats:
            pywikibot.output(u"* %s" % cat.title())
        catpl = pywikibot.Page(self.site, self.newcatTitle, defaultNamespace=14)
        if catpl in cats:
            pywikibot.output(u"%s is already in %s."
                             % (page.title(), catpl.title()))
        else:
            if self.sort:
                catpl = self.sorted_by_last_name(catpl, page)
            pywikibot.output(u'Adding %s' % catpl.title(asLink=True))
            cats.append(catpl)
            text = pywikibot.replaceCategoryLinks(text, cats)
            if not self.save(text, page, self.editSummary):
                pywikibot.output(u'Page %s not saved.'
                                 % page.title(asLink=True))


class CategoryMoveRobot:
    """Robot to move pages from one category to another."""
    def __init__(self, oldCatTitle, newCatTitle, batchMode=False,
                 editSummary='', inPlace=False, moveCatPage=True,
                 deleteEmptySourceCat=True, titleRegex=None,
                 useSummaryForDeletion=True):
        site = pywikibot.getSite()
        self.editSummary = editSummary
        self.oldCat = catlib.Category(site, oldCatTitle)
        self.newCatTitle = newCatTitle
        self.inPlace = inPlace
        self.moveCatPage = moveCatPage
        self.batchMode = batchMode
        self.deleteEmptySourceCat = deleteEmptySourceCat
        self.titleRegex = titleRegex
        self.useSummaryForDeletion = useSummaryForDeletion

    def run(self):
        site = pywikibot.getSite()
        newCat = catlib.Category(site, self.newCatTitle)
        # set edit summary message
        if not self.editSummary:
            self.editSummary = pywikibot.translate(site, msg_change) \
                               % {'oldcat':self.oldCat.title(),
                                  'newcat':newCat.title()}

        if self.useSummaryForDeletion and self.editSummary:
            reason = self.editSummary
        else:
            reason = pywikibot.translate(site, deletion_reason_move) \
                     % {'newcat': self.newCatTitle, 'title': self.newCatTitle}


        # Copy the category contents to the new category page
        copied = False
        oldMovedTalk = None
        if self.oldCat.exists() and self.moveCatPage:
            copied = self.oldCat.copyAndKeep(
                            self.newCatTitle,
                            pywikibot.translate(site, cfd_templates))
            # Also move the talk page
            if copied:
                oldTalk = self.oldCat.toggleTalkPage()
                if oldTalk.exists():
                    newTalkTitle = newCat.toggleTalkPage().title()
                    try:
                        talkMoved = oldTalk.move(newTalkTitle, reason)
                    except (pywikibot.NoPage, pywikibot.PageNotSaved), e:
                        #in order :
                        #Source talk does not exist, or
                        #Target talk already exists
                        pywikibot.output(e.message)
                    else:
                        if talkMoved:
                            oldMovedTalk = oldTalk

        # Move articles
        gen = pagegenerators.CategorizedPageGenerator(self.oldCat,
                                                      recurse=False)
        preloadingGen = pagegenerators.PreloadingGenerator(gen)
        for article in preloadingGen:
            if not self.titleRegex or re.search(self.titleRegex,
                                                article.title()):
                catlib.change_category(article, self.oldCat, newCat,
                                       comment=self.editSummary,
                                       inPlace=self.inPlace)

        # Move subcategories
        gen = pagegenerators.SubCategoriesPageGenerator(self.oldCat,
                                                        recurse=False)
        preloadingGen = pagegenerators.PreloadingGenerator(gen)
        for subcategory in preloadingGen:
            if not self.titleRegex or re.search(self.titleRegex,
                                                subcategory.title()):
                catlib.change_category(subcategory, self.oldCat, newCat,
                                       comment=self.editSummary,
                                       inPlace=self.inPlace)

        # Delete the old category and its moved talk page
        if copied and self.deleteEmptySourceCat == True:
            if self.oldCat.isEmptyCategory():
                confirm = not self.batchMode
                self.oldCat.delete(reason, confirm, mark = True)
                if oldMovedTalk is not None:
                    oldMovedTalk.delete(reason, confirm, mark = True)
            else:
                pywikibot.output('Couldn\'t delete %s - not empty.'
                                 % self.oldCat.title())


class CategoryListifyRobot:
    '''Creates a list containing all of the members in a category.'''
    listify_msg={
        'af': u'Robot: lys van %(fromcat)s (%(num)d bladsye)',
        'als': u'Bot: Lischt us %(fromcat)s (%(num)d Yytreg)',
        'ar': u'بوت: عرض من %(fromcat)s (%(num)d مدخلة)',
        'be-tarask': u'Робат: ствараецца сьпіс з %(fromcat)s (%(num)d элемэнтаў)',
        'br': u'Robot : Roll eus %(fromcat)s (%(num)d pajenn)',
        'bs': u'Bot: ispisuje iz %(fromcat)s (%(num)d stavki)',
        'ca': u'Robot: Llistant de %(fromcat)s (%(num)d entrades)',
        'cs': u'Robot: vytvoření soupisu obsahu kategorie %(fromcat)s (%(num)d položek)',
        'de': u'Bot: Liste aus %(fromcat)s (%(num)d Einträge)',
        'el': u'Ρομπότ: Καταλογοποίηση από %(fromcat)s (%(num)d καταχωρίσεις)',
        'en': u'Bot: Listifying from %(fromcat)s (%(num)d entries)',
        'eo': u'Roboto: listo de %(fromcat)s (%(num)d da objektoj)',
        'fa': u'ربات:فهرست کردن اعضای رده %(fromcat)(%(num)d عضو)',
        'fi': u'Botti listasi luokan %(fromcat)s (%(num)d jäsentä)',
        'fr': u'Bot : liste de %(fromcat)s (%(num)d entées)',
        'frp': u'Bot : lista de %(fromcat)s (%(num)d entrâs)',
        'gl': u'Bot: Listando a partir de %(fromcat)s (%(num)d entradas)',
        'he': u'בוט: יוצר רשימה מהקטגוריה %(fromcat)s (%(num)d דפים)',
        'hu': u'%(fromcat)s listázása bottal (%(num)d lap)',
        'hy': u'Ռոբոտ․ Կազմում է ցանկը %(fromcat)s -ից (%(num)d տարր)',
        'ia': u'Robot: Face lista de %(fromcat)s (%(num)d entratas)',
        'id': u'Bot: Membuat daftar dari %(fromcat)s (%(num)d entri)',
        'it': u'Bot: Lista del contento dalla %(fromcat)s (%(num)d pagine)',
        'ja': u'ボット： %(fromcat)sからリスト化（%(num)d件）',
        'kk': u'Бот: %(fromcat)s дегеннен (%(num)d буын) тізімдеді',
        'ksh': u'Bot: Leß vun dä %(fromcat)s (%(num)d Enndrääsch)',
        'lb': u'Bot: Lëscht vun der %(fromcat)s (%(num)d Memberen)',
        'mk': u'Бот: Попишувам од %(fromcat)s (%(num)d ставки)',
        'ms': u'Bot: Menyenaraikan daripada %(fromcat)s (%(num)d entri)',
        'my': u'ဘော့ - %(fromcat)s (စာရင်းသွင်းမှု %(num)d ခု) မှ စာရင်းများကို ပြုစုနေသည်',
        'nds-nl': u'Bot: lieste van %(fromcat)s (%(num)d pagina\'s)',
        'ne': u'↓ बोट: %(fromcat)s बाट (%(num)d entries) मा श्रृङ्खलाबद्ध गर्दै',
        'nl': u'Robot: lijst van %(fromcat)s (%(num)d pagina\'s)',
        'no': u'Robot: Gjør om kategorien %(fromcat)s til liste (%(num)d elementer)',
        'pdc': u'Waddefresser: Lischt vun %(fromcat)s (%(num)d Eitraeg)',
        'pl': u'Robot listuje kategorię %(fromcat)s (%(num)d pozycji)',
        'pt': u'Robô: A listar a partir de %(fromcat)s (%(num)d entradas)',
        'ru': u'Робот: Составляется список из (%(num)d элементов) %(fromcat)s',
        'rue': u'Робот: складаня списку %(fromcat)s з (%(num)d записів)',
        'sl': u'Bot: Poseznamljanje iz %(fromcat)s (%(num)d vnosov)',
        'sr': u'Робот: сврставање из %(fromcat)s (%(num)d entries)',
        'sv': u'Robot: Skapar en lista från %(fromcat)s (%(num)d)',
        'tr': u'Bot: Şuradan itibaren listeleniyor %(fromcat)s (%(num)d girdi)',
        'tt-cyrl': u'Робот:  (%(num)d Элементтан исемлек төзелә) %(fromcat)s',
        'uk': u'Робот: складання списку %(fromcat)s з (%(num)d записів)',
        'vi': u'Rôbốt: Danh sách hóa từ %(fromcat)s (%(num)d trang)',
        'zh': u'機器人: 從%(fromcat)s提取列表(%(num)d個項目)',
        'zh-hans': u'机器人: 从 %(fromcat)s 提取列表(%(num)d 个项目)',
    }

    def __init__(self, catTitle, listTitle, editSummary, overwrite = False, showImages = False, subCats = False, talkPages = False, recurse = False):
        self.editSummary = editSummary
        self.overwrite = overwrite
        self.showImages = showImages
        self.site = pywikibot.getSite()
        self.cat = catlib.Category(self.site, 'Category:' + catTitle)
        self.list = pywikibot.Page(self.site, listTitle)
        self.subCats = subCats
        self.talkPages = talkPages
        self.recurse = recurse

    def run(self):
        listOfArticles = self.cat.articlesList(recurse = self.recurse)
        if self.subCats:
            listOfArticles += self.cat.subcategoriesList()
        if not self.editSummary:
            self.editSummary = pywikibot.translate(self.site, self.listify_msg) \
                               % {'fromcat': self.cat.title(),
                                  'num': len(listOfArticles)}

        listString = ""
        for article in listOfArticles:
            if (not article.isImage() or self.showImages) and not article.isCategory():
                if self.talkPages and not article.isTalkPage():
                    listString = listString + "*[[%s]] -- [[%s|talk]]\n" % (article.title(), article.toggleTalkPage().title())
                else:
                    listString = listString + "*[[%s]]\n" % article.title()
            else:
                if self.talkPages and not article.isTalkPage():
                    listString = listString + "*[[:%s]] -- [[%s|talk]]\n" % (article.title(), article.toggleTalkPage().title())
                else:
                    listString = listString + "*[[:%s]]\n" % article.title()
        if self.list.exists() and not self.overwrite:
            pywikibot.output(u'Page %s already exists, aborting.' % self.list.title())
        else:
            self.list.put(listString, comment=self.editSummary)


class CategoryRemoveRobot:
    '''Removes the category tag from all pages in a given category
    and from the category pages of all subcategories, without prompting.
    Does not remove category tags pointing at subcategories.

    '''
    deletion_reason_remove = {
        'af': u'Bot: Kategorie is opgehef',
        'als': u'Bot: Kategori isch ufglest wore',
        'ar': u'بوت: التصنيف تم الاستغناء عنه',
        'az': u'Bot: Kateqoriya ləğv edildi',
        'be-tarask': u'Робат: катэгорыя была расфарміраваная',
        'be-x-old': u'Робат: катэгорыя расфармаваная',
        'br': u'Robot : Dilamet eo bet ar rummad',
        'bs': u'Bot: Kategorija je raspuštena',
        'ca': u'Robot: La categoria s\'ha eliminat',
        'cs': u'Robot: kategorie byla vyprázdněna',
        'da': u'Robot: Kategorien blev opløst',
        'de': u'Bot: Kategorie wurde aufgelöst',
        'el': u'Ρομπότ: Η κατηγορία διαγράφηκε',
        'en': u'Bot: Category was disbanded',
        'eo': u'Roboto: kategorio estas nuligita',
        'es': u'Robot: La categoría ha sido eliminada',
        'fa': u'ربات: رده خالی',
        'fi': u'Botti tyhjensi luokan',
        'fr': u'Bot : la catégorie a été supprimée',
        'frp': u'Bot : la catègorie at étâ suprimâ',
        'gl': u'Bot: A categoría foi eliminada',
        'he': u'בוט: הקטגוריה פורקה',
        'hu': u'A bot kiürítette a kategóriát',
        'hy': u'Ռոբոտ․ Կատեգորիան լուծարված էր',
        'ia': u'Robot: Categoria esseva dissolvite',
        'id': u'Bot: Kategori dipecah',
        'it': u'Bot: La categoria è stata eliminata',
        'ja': u'ボット：カテゴリが廃止されています',
        'kk': u'Бот: Санат тарқатылды',
        'ksh': u'Bot: de Saachjropp is nu opjelööß',
        'la': u'automaton: categoria dissoluta est',
        'lb': u'Bot: Kategorie gouf opgeléist',
        'mk': u'Бот: Категоријата е распуштена',
        'ms': u'Bot: Kategori telah dibubarkan',
        'my': u'ဘော့ - ကဏ္ဍကို ပယ်ဖျက်လိုက်သည်',
        'nds': u'Kat-Bot: Kategorie is nu oplööst',
        'nds-nl': u'Bot: kattegerie besteet neet meer',
        'ne': u'↓ बोट: श्रेणी लाइ छोडियो',
        'nl': u'Robot: categorie is opgeheven',
        'nn': u'robot: kategorien blei løyst opp',
        'no': u'Robot: Kategorien ble oppløst',
        'pdc': u'Waddefresser: Abdeeling iss glescht warre',
        'pl': u'Robot usuwa kategorię',
        'pt': u'Robô: A categoria foi eliminada',
        'pt-br': u'Robô: A categoria foi separada',
        'ro': u'Robot: Categoria a fost desființată',
        'ru': u'Робот: категория расформирована',
        'rue': u'Робот: катеґорія розформована',
        'sl': u'Bot: Kategorija je bila razpuščena',
        'sr': u'Робот: категорија је распуштена',
        'sr-ec': u'Бот: категорија је распуштена',
        'sr-el': u'Bot: kategorija je raspuštena',
        'sv': u'Robot: Kategorin upplöstes',
        'tr': u'Bot: Kategori dağıtıldı',
        'tt-cyrl': u'Робот: төркем яңадан ясалган',
        'uk': u'Робот: категорія розформована',
        'vi': u'Rôbốt: Thể loại bị giải tán',
        'zh': u'機器人:本目錄已解散',
        'zh-hans': u'机器人：分类已被解散',
    }

    msg_remove={
        'af': u'Robot: verwyder uit %(oldcat)s',
        'als': u'Bot: us %(oldcat)s  uusegnuu',
        'ar': u'بوت: إزالة من %(oldcat)s',
        'bat-smg': u'Robots: Trėnama ėš %(oldcat)s',
        'be-tarask': u'Робат: выдаленьне з %(oldcat)s',
        'be-x-old': u'Робат: выключэньне з [[%(oldcat)s]]',
        'br': u'Robot : Tennet diwar %(oldcat)s',
        'bs': u'Bot: uklanja iz %(oldcat)s',
        'ca': u'Robot: Eliminant de %(oldcat)s',
        'cs': u'Robot: odstranění kategorie %(oldcat)s',
        'da': u'Robot: Fjerner fra %(oldcat)s',
        'de': u'Bot: Entferne aus %(oldcat)s',
        'el': u'Ρομπότ: Αφαίρεση από την %(oldcat)s',
        'en': u'Bot: Removing from %(oldcat)s',
        'eo': u'Roboto: Forigis el %(oldcat)s',
        'es': u'Bot: Eliminada de la %(oldcat)s',
        'fa': u'ربات:حذف از %(oldcat)s',
        'fi': u'Botti poisti luokasta %(oldcat)s',
        'fr': u'Robot : Retiré depuis %(oldcat)s',
        'frp': u'Bot : enlevâ dês %(oldcat)s',
        'gl': u'Bot: Elimino desde %(oldcat)s',
        'he': u'בוט: מסיר את הדף מהקטגוריה %(oldcat)s',
        'hu': u'[[%(oldcat)s]] eltávolítása bottal',
        'hy': u'Ռոբոտ․ հեռացվել է %(oldcat)s -ից',
        'ia': u'Robot: Eliminate de %(oldcat)s',
        'id': u'Bot: Menghapus dari %(oldcat)s',
        'is': u'Vélmenni: Fjarlægi [[%(oldcat)s]]',
        'it': u'Bot: Rimozione da %(oldcat)s',
        'ja': u'ロボットによる:[[%(oldcat)s]]を除去',
        'kk': u'Бот: %(oldcat)s дегеннен аластатты',
        'ksh': u'Bot: uß de %(oldcat)s ußjedraare',
        'la': u'automaton abdit %(oldcat)s',
        'lb': u'Bot: Ewech huele vun %(oldcat)s',
        'mk': u'Бот: Отстранувам од %(oldcat)s',
        'ms': u'Bot: Mengeluarkan daripada %(oldcat)s',
        'nds': u'Kat-Bot: rut ut %(oldcat)s',
        'nds-nl': u'Bot: vort-ehaold uut %(oldcat)s',
        'ne': u'↓ रोबोट: %(oldcat)s बाट हटाउँदै',
        'nl': u'Robot: verwijderd uit %(oldcat)s',
        'nn': u'robot: fjerna ifrå %(oldcat)s',
        'no': u'Robot: Fjerner ifra %(oldcat)s',
        'pdc': u'Waddefresser: Aus %(oldcat)s raus gnumme',
        'pfl': u'Bot: Aus %(oldcat)s rausgenumme',
        'pl': u'Robot usuwa z kategorii %(oldcat)s',
        'pt': u'Robô: A remover de [[%(oldcat)s]]',
        'pt-br': u'Robô: Removendo [[%(oldcat)s]]',
        'ro': u'Robot: Înlăturat din %(oldcat)s',
        'ru': u'Робот: исключение из %(oldcat)s',
        'rue': u'Робот: одстранїня з %(oldcat)s',
        'sl': u'Bot: Odstranjevanje iz %(oldcat)s',
        'sr': u'Робот: уклањање из %(oldcat)s',
        'sv': u'Robot: Tar bort från %(oldcat)s',
        'tr': u'Bot: %(oldcat)s ögesi kaldırıldı',
        'tt-cyrl': u'Робот:  %(oldcat)s арасыннан аеру',
        'uk': u'Робот: видалення з %(oldcat)s',
        'vi': u'Rôbốt: Dời khỏi %(oldcat)s',
        'vo': u'bot moükon se %(oldcat)s',
        'zh': u'機器人:移除目錄 [[%(oldcat)s]]',
        'zh-hans': u'机器人:移除目录 [[%(oldcat)s]]',
    }

    def __init__(self, catTitle, batchMode = False, editSummary = '', useSummaryForDeletion = True, titleRegex = None, inPlace = False):
        self.editSummary = editSummary
        self.site = pywikibot.getSite()
        self.cat = catlib.Category(self.site, catTitle)
        # get edit summary message
        self.useSummaryForDeletion = useSummaryForDeletion
        self.batchMode = batchMode
        self.titleRegex = titleRegex
        self.inPlace = inPlace
        if not self.editSummary:
            self.editSummary = pywikibot.translate(self.site, self.msg_remove) \
                               % {'oldcat': self.cat.title()}

    def run(self):
        articles = self.cat.articlesList(recurse = 0)
        if len(articles) == 0:
            pywikibot.output(u'There are no articles in category %s' % self.cat.title())
        else:
            for article in articles:
                if not self.titleRegex or re.search(self.titleRegex,article.title()):
                    catlib.change_category(article, self.cat, None, comment = self.editSummary, inPlace = self.inPlace)
        # Also removes the category tag from subcategories' pages
        subcategories = self.cat.subcategoriesList(recurse = 0)
        if len(subcategories) == 0:
            pywikibot.output(u'There are no subcategories in category %s' % self.cat.title())
        else:
            for subcategory in subcategories:
                catlib.change_category(subcategory, self.cat, None, comment = self.editSummary, inPlace = self.inPlace)
        # Deletes the category page
        if self.cat.exists() and self.cat.isEmptyCategory():
            if self.useSummaryForDeletion and self.editSummary:
                reason = self.editSummary
            else:
                reason = pywikibot.translate(self.site, self.deletion_reason_remove)
            talkPage = self.cat.toggleTalkPage()
            try:
                self.cat.delete(reason, not self.batchMode)
            except pywikibot.NoUsername:
                pywikibot.output(u'You\'re not setup sysop info, category will not delete.' % self.cat.site())
                return
            if (talkPage.exists()):
                talkPage.delete(reason=reason, prompt=not self.batchMode)


class CategoryTidyRobot:
    """Script to help a human to tidy up a category by moving its articles into
    subcategories

    Specify the category name on the command line. The program will pick up the
    page, and look for all subcategories and supercategories, and show them with
    a number adjacent to them. It will then automatically loop over all pages
    in the category. It will ask you to type the number of the appropriate
    replacement, and perform the change robotically.

    If you don't want to move the article to a subcategory or supercategory, but to
    another category, you can use the 'j' (jump) command.

    Typing 's' will leave the complete page unchanged.

    Typing '?' will show you the first few bytes of the current page, helping
    you to find out what the article is about and in which other categories it
    currently is.

    Important:
     * this bot is written to work with the MonoBook skin, so make sure your bot
       account uses this skin

    """
    def __init__(self, catTitle, catDB):
        self.catTitle = catTitle
        self.catDB = catDB
        self.site = pywikibot.getSite()
        self.editSummary = pywikibot.translate(self.site, msg_change)\
                           % {'oldcat':self.catTitle, 'newcat':u''}

    def move_to_category(self, article, original_cat, current_cat):
        '''
        Given an article which is in category original_cat, ask the user if
        it should be moved to one of original_cat's subcategories.
        Recursively run through subcategories' subcategories.
        NOTE: current_cat is only used for internal recursion. You should
        always use current_cat = original_cat.
        '''
        pywikibot.output(u'')
        # Show the title of the page where the link was found.
        # Highlight the title in purple.
        pywikibot.output(u'Treating page \03{lightpurple}%s\03{default}, currently in \03{lightpurple}%s\03{default}' % (article.title(), current_cat.title()))

        # Determine a reasonable amount of context to print
        try:
            full_text = article.get(get_redirect = True)
        except pywikibot.NoPage:
            pywikibot.output(u'Page %s not found.' % article.title())
            return
        try:
            contextLength = full_text.index('\n\n')
        except ValueError: # substring not found
            contextLength = 500
        if full_text.startswith(u'[['): # probably an image
            # Add extra paragraph.
            contextLength = full_text.find('\n\n', contextLength+2)
        if contextLength > 1000 or contextLength < 0:
            contextLength = 500
        print
        pywikibot.output(full_text[:contextLength])
        print

        subcatlist = self.catDB.getSubcats(current_cat)
        supercatlist = self.catDB.getSupercats(current_cat)
        alternatives = u'\n'
        if len(subcatlist) == 0:
            alternatives += u'This category has no subcategories.\n\n'
        if len(supercatlist) == 0:
            alternatives += u'This category has no supercategories.\n\n'
        # show subcategories as possible choices (with numbers)
        for i in range(len(supercatlist)):
            # layout: we don't expect a cat to have more than 10 supercats
            alternatives += (u"u%d - Move up to %s\n" % (i, supercatlist[i].title()))
        for i in range(len(subcatlist)):
            # layout: we don't expect a cat to have more than 100 subcats
            alternatives += (u"%2d - Move down to %s\n" % (i, subcatlist[i].title()))
        alternatives += u" j - Jump to another category\n"
        alternatives += u" s - Skip this article\n"
        alternatives += u" r - Remove this category tag\n"
        alternatives += u" l - list these options again\n"
        alternatives += u" m - more context\n"
        alternatives += (u"Enter - Save category as %s\n" % current_cat.title())
        flag = False
        longchoice = True
        while not flag:
            if longchoice:
                longchoice = False
                pywikibot.output(alternatives)
                choice = pywikibot.input(u"Option:")
            else:
                choice = pywikibot.input(u"Option (#, [j]ump, [s]kip, [r]emove, [l]ist, [m]ore context, [RETURN]):")
            if choice in ['s', 'S']:
                flag = True
            elif choice == '':
                pywikibot.output(u'Saving category as %s' % current_cat.title())
                if current_cat == original_cat:
                    print 'No changes necessary.'
                else:
                    newcat = u'[[:%s|%s]]' % (current_cat.title(savetitle=True, decode=True), current_cat.titleWithoutNamespace())
                    editsum = pywikibot.translate(pywikibot.getSite(), msg_replace) % {'oldcat': original_cat.titleWithoutNamespace(), 'newcat': newcat}
                    catlib.change_category(article, original_cat, current_cat, comment = editsum)
                flag = True
            elif choice in ['j', 'J']:
                newCatTitle = pywikibot.input(u'Please enter the category the article should be moved to:')
                newCat = catlib.Category(pywikibot.getSite(), 'Category:' + newCatTitle)
                # recurse into chosen category
                self.move_to_category(article, original_cat, newCat)
                flag = True
            elif choice in ['r', 'R']:
                # remove the category tag
                catlib.change_category(article, original_cat, None, comment = self.editSummary)
                flag = True
            elif choice in ['l', 'L']:
                longchoice = True
            elif choice in ['m', 'M', '?']:
                contextLength += 500
                print
                pywikibot.output(full_text[:contextLength])
                print

                # if categories possibly weren't visible, show them additionally
                # (maybe this should always be shown?)
                if len(full_text) > contextLength:
                    print ''
                    print 'Original categories: '
                    for cat in article.categories():
                        pywikibot.output(u'* %s' % cat.title())
            elif choice[0] == 'u':
                try:
                    choice=int(choice[1:])
                except ValueError:
                    # user pressed an unknown command. Prompt him again.
                    continue
                self.move_to_category(article, original_cat, supercatlist[choice])
                flag = True
            else:
                try:
                    choice=int(choice)
                except ValueError:
                    # user pressed an unknown command. Prompt him again.
                    continue
                # recurse into subcategory
                self.move_to_category(article, original_cat, subcatlist[choice])
                flag = True

    def run(self):
        cat = catlib.Category(self.site, 'Category:' + self.catTitle)

        articles = cat.articlesList(recurse = False)
        if len(articles) == 0:
            pywikibot.output(u'There are no articles in category ' + catTitle)
        else:
            preloadingGen = pagegenerators.PreloadingGenerator(iter(articles))
            for article in preloadingGen:
                pywikibot.output('')
                pywikibot.output(u'=' * 67)
                self.move_to_category(article, cat, cat)


class CategoryTreeRobot:
    '''
    Robot to create tree overviews of the category structure.

    Parameters:
        * catTitle - The category which will be the tree's root.
        * catDB    - A CategoryDatabase object
        * maxDepth - The limit beyond which no subcategories will be listed.
                     This also guarantees that loops in the category structure
                     won't be a problem.
        * filename - The textfile where the tree should be saved; None to print
                     the tree to stdout.
    '''

    def __init__(self, catTitle, catDB, filename = None, maxDepth = 10):
        self.catTitle = catTitle
        self.catDB = catDB
        if filename and not os.path.isabs(filename):
            filename = pywikibot.config.datafilepath(filename)
        self.filename = filename
        # TODO: make maxDepth changeable with a parameter or config file entry
        self.maxDepth = maxDepth
        self.site = pywikibot.getSite()

    def treeview(self, cat, currentDepth = 0, parent = None):
        '''
        Returns a multi-line string which contains a tree view of all subcategories
        of cat, up to level maxDepth. Recursively calls itself.

        Parameters:
            * cat - the Category of the node we're currently opening
            * currentDepth - the current level in the tree (for recursion)
            * parent - the Category of the category we're coming from
        '''

        # Translations to say that the current category is in more categories than
        # the one we're coming from
        also_in_cats = {
            'af': u'(ook in %s)',
            'als': u'(au in %s)',
            'ar': u'(أيضا في %s)',
            'be-tarask': u'(таксама ў %s)',
            'be-x-old': u'(таксама ў %s)',
            'br': u'(ivez e %s)',
            'bs': u'(također u %s)',
            'ca': u'(també a %s)',
            'cs': u'(také v %s)',
            'da': u'(også i %s)',
            'de': u'(auch in %s)',
            'el': u'(επίσης στη %s)',
            'en': u'(also in %s)',
            'eo': u'(ankaŭ en  %s)',
            'es': u'(también en %s)',
            'fa': u'(ﻪﻣچﻥیﻥ ﺩﺭ %s)',
            'fi': u'(myös luokassa %s)',
            'fr': u'(également dans %s)',
            'frp': u'(tot pariér dens %s)',
            'gl': u'(tamén en %s)',
            'he': u'(גם בקטגוריות %s)',
            'hu': u'(a következőkben is: %s)',
            'hy': u'(այդ թվում %s –ում)',
            'ia': u'(equalmente in %s)',
            'id': u'(juga dalam %s)',
            'is': u'(einnig í %s)',
            'it': u'(anche in %s)',
            'ja': u'（%sにも入っています）',
            'kk': u'(тағы да %s дегенде)',
            'ksh': u'(och en dä %s)',
            'la': u'(etiam in %s)',
            'lb': u'(och a(n) %s)',
            'mk': u'(и во %s)',
            'ms': u'(juga dalam %s)',
            'my': u'(%s တွင်လည်း)',
            'nds-nl': u'(oek in %s)',
            'ne': u'↓ (%s मा पनि)',
            'nl': u'(ook in %s)',
            'nn': u'(òg i %s)',
            'no': u'(også i %s)',
            'pdc': u'(aach in %s)',
            'pl': u'(również w %s)',
            'pt': u'(também em %s)',
            'ro': u'(de asemenea în %s)',
            'ru': u'(также в %s)',
            'rue': u'(тыж у %s)',
            'sl': u'(tudi v %s)',
            'sr': u'(такође у %s)',
            'sv': u'(också i %s)',
            'te': u'(%s లలో కూడా ఉంది)',
            'tr': u'(ayrıca %s)',
            'tt-cyrl': u'(шулай ук %s)',
            'uk': u'(також у %s)',
            'vi': u'(cũng trong %s)',
            'zh': u'(也在 %s)',
            'zh-hans': u'(也在 %s)',
            }

        result = u'#' * currentDepth
        result += '[[:%s|%s]]' % (cat.title(), cat.title().split(':', 1)[1])
        result += ' (%d)' % len(self.catDB.getArticles(cat))
        # We will remove an element of this array, but will need the original array
        # later, so we create a shallow copy with [:]
        supercats = self.catDB.getSupercats(cat)[:]
        # Find out which other cats are supercats of the current cat
        try:
            supercats.remove(parent)
        except:
            pass
        if supercats != []:
            supercat_names = []
            for i in range(len(supercats)):
                # create a list of wiki links to the supercategories
                supercat_names.append('[[:%s|%s]]' % (supercats[i].title(), supercats[i].title().split(':', 1)[1]))
                # print this list, separated with commas, using translations given in also_in_cats
            result += ' ' + pywikibot.translate(self.site, also_in_cats) % ', '.join(supercat_names)
        result += '\n'
        if currentDepth < self.maxDepth:
            for subcat in self.catDB.getSubcats(cat):
                # recurse into subdirectories
                result += self.treeview(subcat, currentDepth + 1, parent = cat)
        else:
            if self.catDB.getSubcats(cat) != []:
                # show that there are more categories beyond the depth limit
                result += '#' * (currentDepth + 1) + '[...]\n'
        return result

    def run(self):
        """Prints the multi-line string generated by treeview or saves it to a
        file.

        Parameters:
            * catTitle - the title of the category which will be the tree's root
            * maxDepth - the limit beyond which no subcategories will be listed

        """
        cat = catlib.Category(self.site, 'Category:' + self.catTitle)
        tree = self.treeview(cat)
        if self.filename:
            pywikibot.output(u'Saving results in %s' % self.filename)
            import codecs
            f = codecs.open(self.filename, 'a', 'utf-8')
            f.write(tree)
            f.close()
        else:
            pywikibot.output(tree, toStdout = True)


def main(*args):
    global catDB

    fromGiven = False
    toGiven = False
    batchMode = False
    editSummary = ''
    inPlace = False
    overwrite = False
    showImages = False
    talkPages = False
    recurse = False
    titleRegex = None

    # This factory is responsible for processing command line arguments
    # that are also used by other scripts and that determine on which pages
    # to work on.
    genFactory = pagegenerators.GeneratorFactory()
    # The generator gives the pages that should be worked upon.
    gen = None

    # If this is set to true then the custom edit summary given for removing
    # categories from articles will also be used as the deletion reason.
    useSummaryForDeletion = True
    catDB = CategoryDatabase()
    action = None
    sort_by_last_name = False
    restore = False
    create_pages = False
    for arg in pywikibot.handleArgs(*args):
        if arg == 'add':
            action = 'add'
        elif arg == 'remove':
            action = 'remove'
        elif arg == 'move':
            action = 'move'
        elif arg == 'tidy':
            action = 'tidy'
        elif arg == 'tree':
            action = 'tree'
        elif arg == 'listify':
            action = 'listify'
        elif arg == '-person':
            sort_by_last_name = True
        elif arg == '-rebuild':
            catDB.rebuild()
        elif arg.startswith('-from:'):
            oldCatTitle = arg[len('-from:'):].replace('_', ' ')
            fromGiven = True
        elif arg.startswith('-to:'):
            newCatTitle = arg[len('-to:'):].replace('_', ' ')
            toGiven = True
        elif arg == '-batch':
            batchMode = True
        elif arg == '-inplace':
            inPlace = True
        elif arg == '-delsum':
            # This parameter is kept for historical reasons,
            # as it was previously not the default option.
            pass
        elif arg == '-nodelsum':
            useSummaryForDeletion = False
        elif arg == '-overwrite':
            overwrite = True
        elif arg == '-showimages':
            showImages = True
        elif arg.startswith('-summary:'):
            editSummary = arg[len('-summary:'):]
        elif arg.startswith('-match'):
            if len(arg) == len('-match'):
                titleRegex = pywikibot.input(
                    u'Which regular expression should affected objects match?')
            else:
                titleRegex = arg[len('-match:'):]
        elif arg == '-talkpages':
            talkPages = True
        elif arg == '-recurse':
            recurse = True
        elif arg == '-create':
            create_pages = True
        else:
            genFactory.handleArg(arg)

    if action == 'add':
        # Note that the add functionality is the only bot that actually
        # uses the the generator factory.  Every other bot creates its own
        # generator exclusively from the command-line arguments that
        # category.py understands.
        if not gen:
            gen = genFactory.getCombinedGenerator()
        if not gen:
            #default for backwords compatibility
            genFactory.handleArg('-links')
        # The preloading generator is responsible for downloading multiple
        # pages from the wiki simultaneously.
        gen = pagegenerators.PreloadingGenerator(
            genFactory.getCombinedGenerator())
        bot = AddCategory(gen, sort_by_last_name, create_pages, editSummary)
        bot.run()
    elif action == 'remove':
        if (fromGiven == False):
            oldCatTitle = pywikibot.input(
u'Please enter the name of the category that should be removed:')
        bot = CategoryRemoveRobot(oldCatTitle, batchMode, editSummary,
                                  useSummaryForDeletion, inPlace=inPlace)
        bot.run()
    elif action == 'move':
        if (fromGiven == False):
            oldCatTitle = pywikibot.input(
                u'Please enter the old name of the category:')
        if (toGiven == False):
            newCatTitle = pywikibot.input(
                u'Please enter the new name of the category:')
        bot = CategoryMoveRobot(oldCatTitle, newCatTitle, batchMode,
                                editSummary, inPlace, titleRegex=titleRegex)
        bot.run()
    elif action == 'tidy':
        catTitle = pywikibot.input(u'Which category do you want to tidy up?')
        bot = CategoryTidyRobot(catTitle, catDB)
        bot.run()
    elif action == 'tree':
        catTitle = pywikibot.input(
            u'For which category do you want to create a tree view?')
        filename = pywikibot.input(
            u'Please enter the name of the file where the tree should be saved,\n'
            u'or press enter to simply show the tree:')
        bot = CategoryTreeRobot(catTitle, catDB, filename)
        bot.run()
    elif action == 'listify':
        if (fromGiven == False):
            oldCatTitle = pywikibot.input(
                u'Please enter the name of the category to listify:')
        if (toGiven == False):
            newCatTitle = pywikibot.input(
                u'Please enter the name of the list to create:')
        bot = CategoryListifyRobot(oldCatTitle, newCatTitle, editSummary,
                                   overwrite, showImages, subCats=True,
                                   talkPages=talkPages, recurse=recurse)
        bot.run()
    else:
        pywikibot.showHelp('category')


if __name__ == "__main__":
    try:
        main()
    finally:
        catDB.dump()
        pywikibot.stopme()
