=== Yasr - Yet Another Stars Rating ===
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8S963KJ3UW5XG
Tags: Review, Star, Snippet
Requires at least: 3.5
Tested up to: 3.9.1
Stable tag: 0.0.3
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Yet Another Stars Rating is a simple plugin which allows you and / or your visitor to rate a post or element. Ideal for review's website

== Description ==
Yet Another Stars Rating (YASR) is a new system review based on jquery plugin RateIT. 
With YASR you can make your own review or let your visitors vote, and you can even create multiple sets (a set of stars for each aspect to
rate). Review scores will be indexed by search engines through snippets .
Most important, if you are a gd star rating user, with YASR you can import all the data from gd star rating (which isn't maintained anymore).

= How To use =

= Reviewer Vote =
Una volta installato YASR, quando si crea o modifica una pagina o un post, si avrà a disposizione in alto a destra un riquadro (metabox)
dove sarà possibile inserire il voto complessivo. Questo voto, chiamato overall rating, può essere sia inserito automaticamente,
all'inizio o alla fine di ogni pagina o post, (lo si può impostare in "Settings" -> "Yet Another Stars Rating: Settings") o dovunque si 
vuole usando il short tag [yasr_overall_rating] (può essere inserito facilmente dall'editor visuale).

= Visitor Votes = 
Se vuoi dare la possibilità ai tuoi utenti di votare, lo puoi fare incollando dove si vogliono fare apparire le stelle lo shortag [yasr_visitor_votes] . 
Come prima, anche questo può essere inserito automaticamente all'inizio o alla fine di ogni articolo andando in "Settings" -> Yet Another Stars Rating: Settings.

= Multi Set =
Tramite i multi set è possibile creare varie sottovoci per una singola recensione: per esempio, se si sta recensendo un videogioco, si possono creare le sottovoci
"Grafica", "Gameplay", "Storia", etc

= Importing data from gd star rating =
Se utilizzi gd-star-rating, questo è il plugin che fa per te! Puoi importare da gd star rating gli "overall_rating", i "visitor_votes" e tutti i multi set che hai creato
con i rispettivi voti! Una volta installato YASR non devi fare altro che andare su "Settings" -> "Yet Another Stars Rating: Settings" ed avviare l'import (ultimo box in basso). 
Questa operazione può richiedere del tempo, non interromperla! Una volta finita l'importazione non dovrai fare altro che rimpiazzare i shorttag di gd star rating con quelli di YASR.
Inoltre, se su gd star rating usi un numero di stelle diverso da 5, YASR convertirà in automatico tutti i voti su un range di voti da 1 a 5 


== Installation ==
1. Install Yet Another Stars Rating either via the WordPress.org plugin directory, or by uploading the files to your server
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Yet Another Star Rating menu in Settings and set your options.


== Screenshots ==
1. Example of yasr in a videogame review
2. Another example of a restaurant review

== Changelog ==

= 0.0.3 =
* Fixed bug describe if 
* Code Cleanup

= 0.0.2 =
* Using input type radio when select a multi set instead select / option 
* Added css style for table showing multiset
* Added yasr.css file
* Some code cleanup
