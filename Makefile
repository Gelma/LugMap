SHELL=/bin/bash
PHPFILES:=$(shell (find -type f -name '*.php'))
HTMLFILES:=$(PHPFILES:.php=.html)
SPHPFILES:=$(shell \
            for x in $(PHPFILES); \
            do \
            grep -q '^// pagina dinamica$$' $$x || \
             echo $$x; \
            done; \
            )

PDEPENDS:=$(shell \
             for x in $(PHPFILES); \
              do \
              grep -q '^//depends_on' $$x && \
               echo $$x; \
              done; \
             )

DDEPENDS:=$(PDEPENDS:.php=.d)

SHTMLFILES:=$(SPHPFILES:.php=.html)


PHP4:=$(shell (which php4 || which php) 2>/dev/null)

all: update files

.PHONY: clean distclean update 
.SUFFIXES: .html .php .css

depends: $(DDEPENDS)

%.d: %.php
	( echo -n $< ":" | sed -e 's/\.php/\.html/g'  && \
          cat $< |grep 'depends_on'|\
          sed -e 's/\/\/depends_on://' ) > $@ 

include $(DDEPENDS)

.php.html: 
	@echo "Genero $@"
	cd $(shell dirname $<) && \
	$(PHP4) -q $(shell basename $<) > $(shell basename $@)
	
update:
	cvs -z9 up -dP

index.html: index.php log_eventi.txt

files: depends $(SHTMLFILES)

$(SHTMLFILES): includes/header.inc includes/footer.inc

clean:
	@rm -f .tmp $(HTMLFILES) $(PDEPENDS:.php=.d)
	@echo Sono stati cancellati tutti i file HTML che avevano \
	un corrispondente php

distclean:
	@rm -vf `find -path './statistiche' -prune -o -type f -name \*.html -print`
	@echo Sono stati cancellati tutti i file HTML 
