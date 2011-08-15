<?php

require_once ('varie.php');
do_head ('Informazioni su Planet LugMap');

?>

<div>
	<div class="description-plain">
		<h3>Che è Planet LugMap?</h3>

		<p>
			<a href="http://planet.lugmap.it/">Planet.LugMap.it</a> è l'aggregatore di news dei Linux
			Users Groups indicizzati in LugMap.it: qui vengono raccolte ed esposte le notizie recuperate
			dai diversi siti, per comodità ed in previsione di utilizzi più sofisticati.
		</p>

		<h3>E' disponibile un feed di questo aggregatore?</h3>

		<p>
			Più d'uno! In <a href="<?php echo $main_url ?>/forge/opml-to-planet/rss10.xml">RSS 1.0</a>,
			<a href="<?php echo $main_url ?>/forge/opml-to-planet/rss20.xml">RSS 2.0<a/>, e
			<a href="<?php echo $main_url ?>/forge/opml-to-planet/atom.xml">Atom</a>. E non dimenticare
			<a href="<?php echo $main_url ?>/lugs.opml">il file OPML originale<a/>, qualora tu volessi
			personalizzare la lista dei feeds sul tuo aggregatore.
		</p>

		<h3>Come vengono aggregate le notizie?</h3>

		<p>
			Dunque, a spiegarlo sembra più complesso di quanto non sia...
		</p>

		<p>
			Ogni notte LugMap.it viene sincronizzato automaticamente con
			<a href="https://github.com/Gelma/LugMap">il repository dei dati raw</a>, poi
			<a href="<?php echo $main_url ?>/forge.php#OPML">l'apposito script</a> genera un file OPML
			con i feed RSS di ogni LUG che ne espone uno, da tale file
			<a href="<?php echo $main_url ?>/forge.php#Planet">un altro script</a> genera la
			configurazione per <a href="http://planetplanet.org/">Planet</a>, Planet viene eseguito su
			tale configurazione, e genera un file index.html che viene incluso nell'index.php di questo
			aggregatore (arricchendo la pagina con i menu accessori e quant'altro).
		</p>

		<p>
			... perché fare le cose a mano quando i computer possono automatizzare?!
		</p>

		<h3>Non bastavano gli aggregatori già esistenti?</h3>

		<p>
			L'unico aggregatore di tal fatta a noi noto è <a href="http://lug.linux.it/">quello gestito
			da Italian Linux Society</a>, che purtroppo sembra non essere particolarmente mantenuto:
			include diversi feed non più validi ma non quelli dei Linux Users Groups più recenti, ed in
			generale non è allineato con la realtà linuxara italiana. Codesto aggregatore, in virtù delle
			procedure automatiche sopra esposte, risulta essere sempre in linea con il dinamico e
			scoppiettante mondo dei LUG.
		</p>

		<p>
			Planet.LugMap.it viene inoltre utilizzato come fonte sperimentale per altri progetti più
			sofisticati che prevedono un maggior trattamento delle news raccolte.
		</p>

		<h3>Le news del mio LUG non sono visualizzate!</h3>

		<p>
			Innanzitutto, un LUG per essere preso in considerazione deve essere indicizzato nella LugMap.
			Se non lo è, <a href="<?php echo $main_url ?>/partecipa.php">segnalacelo</a> immediatamente!
		</p>

		<p>
			Dalla lista sono espressamente rimossi i contenuti generati dai wiki (tipo
			<a href="http://www.mediawiki.org/">MediaWiki</a>) in quanto generano un gran rumore di fondo
			inintelligibile, e poi una lista di
			<a href="<?php echo $main_url ?>/forge/opml-generator/eccezioni.txt">altri feeds</a>
			individuati a mano che paiono essere stati compromessi da spam bots o cose del genere.
		</p>

		<p>
			Essendo tutta la procedura gestita in modo automatico, occorre seguire qualche ferrea regola
			deterministica: il feed deve <a href="http://www.rssboard.org/rss-autodiscovery">essere
			identificabile</a> nel codice HTML della homepage del sito del LUG, se ce ne è più di uno
			quello rilevante deve essere il primo a comparire, ed il file XML deve essere leggibile da
			<a href="http://simplepie.org/">SimplePie</a> (classe PHP usata in tutti gli script qui
			utilizzati). Non si fanno eccezioni, per nessuno, di nessun genere, in nessun caso.
		</p>
	</div>
</div>

<?php
do_foot ();
?>
