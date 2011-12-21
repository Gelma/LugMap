<?php

require_once ('varie.php');
do_head ('Informazioni su Calendar LugMap');

?>

<div>
	<div class="description-plain">
		<h3>Che è Calendar LugMap?</h3>

		<p>
			<a href="http://calendar.lugmap.it/">Calendar.LugMap.it</a> è un calendario di eventi
			dedicati a Linux ed al software libero, organizzati e/o allestiti dai Linux Users
			Groups italiani.
		</p>

		<h3>Come vengono selezionate le notizie?</h3>

		<p>
			Tristemente a mano. Ogni giorno c'e' chi legge e controlla il feed di
			<a href="http://planet.lugmap.it">Planet LugMap</a>, popolando manualmente il file (in
			formato raw e <a href="https://github.com/Gelma/LugMap/tree/lugmap.it/forge/events">disponibile
			pubblicamente</a>) da cui il resto della pagina viene automaticamente generato.
		</p>

		<p>
			Non vengono applicate moderazioni di sorta, bene o male tutti gli eventi citati sono
			riportati nel calendario.
		</p>

		<h3>Le news del mio LUG non sono visualizzate!</h3>

		<p>
			Poiche' le segnalazioni sono tratte da <a href="http://planet.lugmap.it">Planet LugMap</a>,
			valgono ovviamente le stesse <a href="http://lugmap.it/planet_info.php">regole</a> applicate
			a Planet LugMap: bisogna essere indicizzati sulla LugMap, ed occorre avere un feed valido
			che possa essere aggregato ed ispezionato in cerca di annunci di attivita'.
		</p>

		<p>
			Se il tuo LUG appare su Planet LugMap ed un tuo evento non e' stato citato,
			<a href="partecipa.php">faccelo sapere</a>: essendo la selezione manuale puo' capitare che ogni
			tanto ci scappi qualcosa. Bada comunque che l'aggregazione avviene solo una volta al giorno,
			nella tarda serata: non pretendere che una attivita' di cui viene pubblicato l'annuncio nel
			pomeriggio appaia istantaneamente su queste pagine.
		</p>

		<p>
			Comunque, in nessun caso e per nessun motivo, vengono accettate segnalazioni via mail o per mezzo di
			qualsiasi altro canale.
		</p>
	</div>
</div>

<?php
do_foot ();
?>
