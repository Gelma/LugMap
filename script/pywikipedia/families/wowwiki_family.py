# -*- coding: utf-8  -*-

__version__ = '$Id$'

import config, family, urllib

class Family(family.Family):
    def __init__(self):
        family.Family.__init__(self)
        self.name = 'wowwiki'

        self.langs = {
            'cs': 'cs.wow.wikia.com',
            'da': 'da.wowwiki.com',
            'de': 'de.wow.wikia.com',
            'el': 'el.wow.wikia.com',
            'en': 'www.wowwiki.com',
            'es': 'es.wow.wikia.com',
            'fa': 'fa.wow.wikia.com',
            'fi': 'fi.wow.wikia.com',
            'fr': 'fr.wowwiki.com',
            'he': 'he.wow.wikia.com',
            'hr': 'hr.wow.wikia.com',
            'hu': 'hu.wow.wikia.com',
            'is': 'is.wow.wikia.com',
            'it': 'it.wow.wikia.com',
            'ja': 'ja.wow.wikia.com',
            'ko': 'ko.wow.wikia.com',
            'lt': 'lt.wow.wikia.com',
            'lv': 'lv.wow.wikia.com',
            'nl': 'nl.wow.wikia.com',
            'no': 'no.wow.wikia.com',
            'pl': 'pl.wow.wikia.com',
            'pt': 'pt.wow.wikia.com',
            'pt-br': 'pt-br.wow.wikia.com',
            'ro': 'ro.wow.wikia.com',
            'ru': 'ru.wow.wikia.com',
            'sk': 'sk.wow.wikia.com',
            'sr': 'sr.wow.wikia.com',
            'sv': 'sv.warcraft.wikia.com',
            'tr': 'tr.wow.wikia.com',
            'zh-tw': 'zh-tw.wow.wikia.com',
            'zh': 'zh.wow.wikia.com'
        }

        self.namespaces[4] = {
            'cs': u'WoWWiki',
            'da': u'WoWWiki Danmark',
            'de': u'WoW-Wiki',
            'el': u'WoWWiki Ελληνικός οδηγός',
            'en': u'WoWWiki',
            'es': u'WarcraftWiki',
            'fa': u'دنیای وارکرفت',
            'fi': u'WoWWiki Suomi',
            'fr': u'WikiWoW',
            'he': u'Worldofwiki',
            'hr': u'World of Warcraft Wiki',
            'hu': u'World of Warcraft Wiki',
            'is': u'WoWWiki',
            'it': u'WoWWiki Italia',
            'ja': u'World of Warcraft Wiki',
            'ko': u'World of Warcraft Wiki',
            'lt': u'World of Warcraft Wiki',
            'lv': u'World of Warcraft',
            'nl': u'WoWWiki',
            'no': u'Wowwiki Norge',
            'pl': u'WoWWiki',
            'pt': u'World of Warcraft',
            'pt-br': u'WowWiki Br',
            'ro': u'World of Warcraft Romania',
            'sk': u'WoWwiki',
            'sr': u'Wow wiki',
            'sv': u'WoWWiki Sverige',
            'ru': u'WoWWiki',
            'tr': u'Wow Tr Wikiame',
            'zh': u'World of Warcraft Wiki',
            'zh-tw': u'魔獸世界百科全書'
        }

        self.namespaces[5] = {
            'cs': u'WoWWiki diskuse',
            'da': u'WoWWiki Danmark-diskussion',
            'de': u'WoW-Wiki Diskussion',
            'el': u'WoWWiki Ελληνικός οδηγός συζήτηση',
            'en': u'WoWWiki talk',
            'es': u'WarcraftWiki Discusión',
            'fa': u'بحث دنیای وارکرفت',
            'fi': u'Keskustelu WoWWiki Suomista',
            'fr': u'Discussion WikiWoW',
            'he': u'שיחת Worldofwiki',
            'hr': u'Razgovor World of Warcraft Wiki',
            'hu': u'World of Warcraft Wiki-vita',
            'is': u'WoWWikispjall',
            'it': u'Discussioni WoWWiki Italia',
            'ja': u'World of Warcraft Wiki‐ノート',
            'ko': u'World of Warcraft Wiki토론',
            'lt': u'World of Warcraft Wiki aptarimas',
            'lv': u'World of Warcraft diskusija',
            'nl': u'Overleg WoWWiki',
            'no': u'Wowwiki Norge-diskusjon',
            'pl': u'Dyskusja WoWWiki',
            'pt': u'World of Warcraft Discussão',
            'pt-br': u'WowWiki Br Discussão',
            'ro': u'Discuţie World of Warcraft Romania',
            'ru': u'Обсуждение WoWWiki',
            'sk': u'Diskusia k WoWwiki',
            'sr': u'Разговор о Wow wiki',
            'sv': u'WoWWiki Sverigediskussion',
            'tr': u'Wow Tr Wikiame tartışma',
            'zh': u'World of Warcraft Wiki talk',
            'zh-tw': u'魔獸世界百科全書討論'
        }

        #wikia-wide defaults
        self.namespaces[110] = {
             '_default': 'Forum',
             'es': u'Foro',
             'fa': u'فوروم',
             'fi': u'Foorumi',
             'ru': u'Форум'
        }
        self.namespaces[111] = {
             '_default': 'Forum talk',
             'es': u'Foro Discusión',
             'fa': u'بحث فوروم',
             'fi': u'Keskustelu foorumista',
             'pl': u'Dyskusja forum',
             'ru': u'Обсуждение форума'
        }

        self.namespaces[400] = {
            '_default': u'Video',
            'ru': u'Видео'
        }
        self.namespaces[401] = {
            '_default': u'Video talk',
            'ru': u'Обсуждение видео'
        }
        self.namespaces[500] = {
            '_default': u'User blog',
            'de': u'Benutzer Blog',
            'en': '', #disabled on en
            'ru': u'Блог участника'
        }
        self.namespaces[501] = {
            '_default': u'User blog comment',
            'de': u'Benutzer Blog Kommentare',
            'en': '', #disabled on en
            'ru': u'Комментарий блога участника'
        }
        self.namespaces[502] = {
            '_default': u'Blog',
            'en': '', #disabled on en
            'ru': u'Блог'
        }
        self.namespaces[503] = {
            '_default': u'Blog talk',
            'de': u'Blog Diskussion',
            'en': '', #disabled on en
            'ru': u'Обсуждение блога'
        }

        #a few edge cases:
        self.namespaces[112] = {
            'en': u'Guild', 'ru': u'Портал'
        }
        self.namespaces[113] = {
            'en': u'Guild talk', 'ru': u'Портал talk'
        }
        self.namespaces[114] = {
            'en': u'Server', 'ru': u'Гильдия'
        }
        self.namespaces[115] = {
            'en': u'Server talk', 'ru': u'Гильдия talk'
        }
        self.namespaces[116] = {
             'en': u'Portal', 'ru': u'Сервер'
        }
        self.namespaces[117] = {
            'en': u'Portal talk', 'ru': u'Сервер talk'
        }

        #and a few more
        self.namespaces[120] = { 'no': u'Oppdrag' }
        self.namespaces[121] = { 'no': u'Oppdrag Kommentar' }
        self.namespaces[122] = { 'no': u'Retningslinje' }
        self.namespaces[123] = { 'no': u'Retningslinje Kommentar' }
        self.namespaces[124] = { 'no': u'Portal' }
        self.namespaces[125] = { 'no': u'Portal diskusjon' }
        self.namespaces[126] = { 'no': u'Tinget' }
        self.namespaces[127] = { 'no': u'Tinget Diskusjon' }
        self.namespaces[128] = { 'no': u'Blogg' }
        self.namespaces[129] = { 'no': u'Blogg Kommentar' }

        self.content_id = "article"

        self.disambiguationTemplates['en'] = ['disambig', 'disambig/quest',
                                              'disambig/quest2',
                                              'disambig/achievement2']
        self.disambcatname['en'] = "Disambiguations"

    def protocol(self, code):
        return 'http'

    def scriptpath(self, code):
        return ''

    def apipath(self, code):
        return '%s/api.php' % self.scriptpath(code)

    def version(self, code):
        return '1.16.2'

    def code2encoding(self, code):
        return 'utf-8'
