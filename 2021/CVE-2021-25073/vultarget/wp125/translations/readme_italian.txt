=== Nome del plugin ===
Sviluppatore: redwall_hp
URI Plugin: http://www.webmaster-source.com/wp125-ad-plugin-wordpress/
URI Autore: http://www.webmaster-source.com
URI Traduttore: http://gidibao.net 
Link donazioni: http://www.webmaster-source.com/donate/?plugin=wp125
Tag: ads, 125x125, management, advertisement
Versione minima WP: 2.3
Compatibile sino alla: 2.7.1
Versione stabile: 1.3.1

Gestione facilitata degli annunci in formato 125x125 per il tuo blog.  Gli annunci potranno essere attivi per un numero specifico di giorni al termine dei quali verranno disattivati. Disponibile anche il conteggio delle impressioni.



== Descrizione ==

Nel caso in cui ti fossi stancato dei network pubblicitari utilizzati dalla gran parte dei blogger e desiderassi vendere direttamente gli annunci, potresti sentirti frustrato per il gran numero di tempo da dovere dedicare per la gestione dei tuoi inserti pubblicitari. Inoltre, sar&agrave; necessario che tu riesca a trovare degli sponsor per il tuo blog, modificare manualmente il template affinché tu possa inserire gli annunci nonché prestare attenzione alla tua applicazione calendario preferita impostando la scadenza degli annunci.

L'esperienza in time consuming ritiene che tutte queste attivit&agrave; da ora appartengono al passato. Il plugin WP125 potr&agrave; aiutarti nella gestione dei tuoi inserti con una maggiore efficienza lasciandoti di fatto il tempo per potere scrivere dei nuovi articoli. Il plugin agginger&agrave; al menu principale di WordPress una nuova sezione a nome "Annunci" contenente dei sotto-menu per la gestione delle impostazioni di agginta e/o rimozione degli annunci.

Funzioni:

* Disposizione per una o due colonne e supporto via template tags affinch&eacute; possa essere implementato il tuo personale design.
* Numero illimitato di spazi pubblicitari da mostrare in ordine manuale o casuale
* Tracciamento delle impressioni per ogni annuncio
* Una volta creato un nuovo annuncio, non sar&agrave; necessario che tu debba calcolare la data di scadenza. Sar&agrave; sufficiente inserire il numero dei giorni che desideri abbia durata la campagna pubblicitaria e la data corretta del suo inizio. L'annuncio avr&agrave; termine automaticamente alla data di scadenza da te indicata.
* Una volta che l'anuncio sar&agrave; scaduto, il record verr&agrave; archiviato sotto la sezione degli Inattivi in modo tale che tu possa verificare il computo totale delle impressioni oppure riattivarlo nuovamente per una nuova campagna.
* Una immagine informativa di tua scelta verr&agrave; mostrata nel caso in cui lo spazio annunci fosse ancora vuoto. Ad esempio, l'immagine "Your Ad Here" collegata ad una pagina contenente le statistiche ed i prezzi oppure un link di affiliazione.
* Possibilit&agrave; di ricevere delle email di notifica alla scadenza degli annunci. Funzione particolarmente utile qualora desiderassi inviare dei messaggi informativi agli inserzionisti oppure restare sempre aggiornato.



== Installazione ==

1. Scaricare e scompattare l'archivio.
2. Caricare via FTP la cartella "wp125" sotto /wp-content/plugins/
3. Attivare il plugin nella sezione "Plugin" del pannello di amministrazione.
4. Utilizzare il widget "WP125: Ads" oppure inserire il template tag `<?php wp125_write_ads(); ?>` laddove desideri che l'annuncio appaia.
5. Vai alla nuova sezione "Annunci" della amministrazione di WordPress per operare sulle impostazioni quali ad esempio il numero complessivo degli annunci da mostrare in un'unica sessione (predefinito a 6) e come desideri che essi vengano mostrati



== Aggiornamento ==

Potrai aggiornare il plugin grazie al sistema automatizzato di WordPress 2.5 o superiore oppure con il "vecchio-stile" scaricando la nuova versione e quindi
1. Disattivare il plugin
2. Caricare i file aggiornati
3. Riattivare il plugin



== FAQ ==

= E se io desiderassi disporre i miei annunci non in una o due colonne? =
Nel caso in cui desiderassi disporre i tuoi annunci in un modo personalizzato, dovrai utilizzare il template tag `<?php wp125_single_ad(num); ?>` (sostituendo "num" con il numero di uno spazio pubblicitario). Il tag attiver&agrave; un annuncio con la formattazione di minima (un semplice <a><img /></a>). Potrai infine utilizzare pi&ugrave; richieste dal tag inserrendolo nel template in modo da impostare la disposizione dei tuoi annunci in un posto di tua preferenza.

= Uno dei miei annunci &eacute; in data di scadenza. Dove finir&agrave;? =
Quando il tempo di un annuncio viene a scadere, l'inserto sparir&agrave; dal tuo sito e verr&agrave; rimosso dalla pagina degli Annunci attivi presente nella amministrazione di WordPress. Per potere accedere al record, sar&agrave; sufficiente cliccare sul link "Inattivo" nella pagina di gestione del plugin. La pagina dovrebbe mostrare tutti i tuoi annunci inattivi.

= E se io desiderassi che un annuncio non abbia una scadenza in automatico? =
Seleziona la voce "verr&agrave; rimosso manualmente" durante l'impostazione per la data di scadenza quando crei un nuovo annuncio.

= Come posso impostare lo stile degli annunci differente da quello predefinito? =
Per prima cosa, togli il segno di spunta dalla casella Stile predefinito nella pagina delle Impostazioni in modo tale che questa operazione rimuova lo stile predefinito quindi, utilizza tutta la tua fantasia per lavorare sul CSS degli annunci. Ecco il foglio di stile predefinito:

`/* Styles for one-column display */
#wp125adwrap_1c { width:100%; }
#wp125adwrap_1c .wp125ad { margin-bottom:10px; }

/* Styles for two-column display */
#wp125adwrap_2c { width:100%; }
#wp125adwrap_2c .wp125ad { width:125px; float:left; padding:10px; }`

= Come posso fare affinch&eacute; l'annuncio sia aperto in una nuova pagina? =
Nel caso in cui fosse estremamente *necessario* che gli annunci siano visualizzabili in una nuova pagina, apri il file wp125.php file, trova `define(...` in cima e rimuovi il primo `//` posizionato al suo inizio.

= Sebbene io abbia impostato l'opzione, i miei annunci non appaiono su due colonne! Cosa posso fare? =
Dipende probabilmente dal fatto che il DIV principale che contiene il codice dell'annuncio non &eacute; abbastanza esteso. Ti ricordo che &eacute; necessario almeno un *minimo* di 300px di spazio orizzontale per potere avere gli annunci disposti su due colonne e pi&ugrave; ancora nel caso utilizzassi il CSS predefinito. Prova a ridurre il padding CSS intorno agli annunci passando dai 10px ad un valore pi&ugrave; basso.

`#wp125adwrap_2c .wp125ad { padding:4px; }`



== Screenshots ==
1. Alcuni annunci 125x125 nel formato due-colonne.
2. La sezione Aggiungi/Modifica annunci.



== Problematiche ==
* Nel caso in cui tu avessi installato WP Super Cache nel tuo blog, potrebbe verificarsi un conflitto con la funzione di WP125 preposta al tracciamento delle impressioni. Affinch&eacute; il problema possa essere risolto, aggiungi "index.php" in una nuova linea nel campo "Rejected URLs" nella pagina delle opzioni di WP Super Cache. Questa operazione far&agrave; s&igrave; che venga disabilitata la cache per miosito.com/index.php. Ai visitatori di miosito.com verr&agrave; in ogni caso proposta la versione cache mentre sino a quando l'URL del tracciamento delle impressioni di WP125 sar&agrave; simile a "/index.php?adclick=1," la cache non sar&agrave; operativa.



== Supporto ==
Per ogni problema riscontrato con il plugin, ti invito a partecipare al forum ufficiale di WordPress all'indirizzo http://wordpress.org/support/ (assicurati di usare il tag "WP125"!). Io stesso oppure un altro utente che utilizza il plugin proveremo a dare una risposta alle tue domande. In alternativa, inviami una email tramite il modulo di contatto del sito Webmaster-Source.com.



== Storico versione ==
* 1.0.0 - Versione iniziale
* 1.1.x - Riparati alcuni errori in ambito di sicurezza e prestazioni, aggiunta di alcune nuove opzioni per la personalizzazione ed alcune funzioni importanti.
* 1.2.x - Nuove funzioni aggiunte: possibilit&agrave; di ricevere una email di notifica prima della scadenza degli annunci email, classi CSS per gli annunci e possibilit&agrave; di fare aprire gli annunci in una nuova finestra se *necessario*.
* 1.3.x - Supporto localizzazione (francese, italiano e spagnolo), funzione iCalendar per gli annunci in scadenza e WP Dashboard widget. Risolto il problema "ADLINK_EXTRA" per gli annunci singoli, risolto il bug "niente annunci, niente immagini indicative", etc..