<?php
 
  
  
  

$filmlisteUrl       = "http://verteiler3.mediathekview.de/Filmliste-akt.xz"; //Filmliste-Download

$impressum_name_adresse = "
Ihr Vor-Name, Ihr Nachname
Ihre Anschrift
Ihre Adresse
Ihre Telefonnummer oder Ihre Emailadresse
"; //eine Homepage muss ein namentliches Impressum haben



/* 

  Allgemein:
    0 = deaktiv
    1 = aktiv
    (evtl. auch 2 = andere Option)
  
*/



//für Filmlisten-Update//
$system_allow_exec_and_have_unxz = 1;    //System muss exec() und unxz erlauben/haben (für das Runterladen+Entpacken der Filmliste)
                                         //Wenn shell_exec() / system() erlaubt ist; evtl. muss noch das Programm xz-utils installiert werden
//$cloud_convert_apikey = ""; //alternativ kümmert sich ein Online-Dienst um's convertierten  (wenn exec()/unxz nicht verfügbar sind auf den Server)(kostenfreier Account für max. 25x/Tag) (für das Runterladen+Entpacken der Filmliste)
$filmlisten_autoUpdate = 1; //automatisch im Hintergrund neue Filmliste laden (von MediathekViewProjekt)
                            //nur wenn $system_allow_exec_and_have_unxz=1; 
$filmliste_manuellUpdate_showLink_timeout = 0;  //Link zum manuellen Update anzeigen (in Minuten nach letzten Download);
                                                //Achtung: lädt evtl. auch die gleiche Filmisten Datei nochmal runter (?!)



//Texte//
$PageTitle       = 'MediathekListePHP';
$welcomeText     = '<span style=\"color:#999999\">Mediathek-liste in PHP, &nbsp;geschrieben für TV-Browser & Hbb-TV. Daten von MediathekView.de</span>Danke an MediathekView :-)';
$notice_14_09_2017 = '<div style="border-top: 3pt solid #999999;margin:10pt;padding:10pt;" id="rechtshinweis_14_09_2017"><b>Hier werden Daten der öffentliche rechtlichen Fernsehprogramme dargestellt.</b></br>
Für diese Inhalte kann ich keine Verantwortung übernehmen und würde Sie bitte mich hinzuweisen, sollten Sie rechtliche Probleme mit den dargestellten Daten haben / bzw. grundsätzlich. Dieser Hinweis ist nur nach der Programm-Erstveröffentlichung im Monat September bis 14.10.2017 sichtbar.<p align="right"><a href="#" onClick="createCookie(\'rechtshinweis_14_09_2017\',1,60);document.getElementById(\'rechtshinweis_14_09_2017\').style.display=\'none\';">dauerhaft ausblenden [x]</a></p>
<script type="text/javascript" >if(getCookie(\'rechtshinweis_14_09_2017\')>0)document.getElementById(\'rechtshinweis_14_09_2017\').style.display=\'none\';</script>
</div>';
if(time()< 1505418085 + 60*60*24*30) $welcomeText.= $notice_14_09_2017;

$footerText      = 'Datenquelle <a href="http://mediathekview.de">MediathekView.de</a>';
$impressumText   = '
<h4>Datenherkunft:</h4>
<div style="padding-left:10pt">
Die Daten stammen von den Projekt <a href="http://www.mediathekview.de">http://www.mediathekview.de</a><br>
Diesen Gründer, Entwickler und gesamt Projekt gebührt der Dank für das auslesen der Mediatheken, sonst wäre die Liste leer :-<<br>
Den Quellcode für diese Web-Anwendung finden Sie bei <a href="https://github.com/soerenj/mediatheklistePHP">github</a>.
</div><br>

<h4>Verantwortlich für diesen Server ist:</h4>
<div style="padding-left:10pt">
'.$impressum_name_adresse.'
</div><br>

<br><h4>Datenschutz:</h4>
<div style="padding-left:10pt">
Bei der Nutzung dieser Seite fallen Verkehrsdaten an.<br>
Diese werden als Server-Logs gespeichert (Inhalt: wann; welche Seite; welcher IP-adresse)<br>
-------------------- Bitte ergänzen Sie hier ihren Hinweis -------------------- 
</div><br>


';



$clientBrowserCacheActive             = 1; //0|1 Client/Browser/Proxy-Cache aktvieren;  Beim testen besser ausschalten


//Filmliste
$showSize        = 0;   //0|1 Film-Dateigröße anzeigen (nur bei "Normal" Filmgröße)
$showMinDSLSpeed = 0;   //0|1 Mindest notwendige DSL-Verbindung anzeigen (nur bei "Normal" Filmgröße)
$maxJeSeite    =  100;  //wie viele Listeneinträge( = Filme) auf einer Seite maximal anzeigen (wird dannach einfach abgebrochen);
                        //Grund: Wenn die Liste zu lang ist sind älter TV-Browser vlt. überfordert???
                        //(Problem: die Datumsortierung geht nur auf diese angezeigten; somit stimmt die Sortierung nach Datum dann nicht mehr)
$maxRender     = 5000;  //Es werden nur so viele Filme einer Liste intern verarbeitet, dannach wird abgebrochen
                        //(ist wichtig, damit eine Liste richtig nach Datum sortiert werden kann)
$minLengthVorlagenMinuten = array(1,2,3,5,10,20,25,55,85); //Minuten Auswahl möglichkeiten (für Mindest-Film länge)
$debugTestMaxLineRead    = 0;    //(sollte 0=deaktiviert sein); Ist nur zum Testen: nur die ersten X-Element eingelesen aus der Datei






//Cache auf den Server (braucht viel Speicherplatz und einmalig viel RAM) //
$use_cache_filmlist_sender = true; //true|false Beschleuigt das auslesen, indem die Liste nach Sendern geteilt wird
                         //(Funktion aktiviert oder funktioniert nicht?: Dann den Cache aktualisieren über diese Adresse:liste.php?list_update__only_aufteilen=1)
                         //(Festplatten-Speicherbedarf: braucht 1x Filmgröße-Datei; Bspw. 110MB)     
$use_cache_filmlist_thema  = true; //true|false Beschleuigt das auslesen, indem die Liste nach Thema geteilt wird 
                         //(Funktion aktiviert oder funktioniert nicht?: Dann den Cache aktualisieren über diese Adresse: liste.php?list_update__only_aufteilen=1)
                         //(Festplatten-Speicherbedarf: braucht 2x Filmgröße-Datei; Bspw. 220MB)



$loaderAnimation              = 2;    //0|1|2 ladeKreis Animation (muss der Benutzer zusätzlich unter Einstellungen aktivieren)
                                      //0=aus,
                                      //1=spinner.js (muss noch von Hand runtergeladen werden und in den Ordner js packen), 
                                      //2=drehendes utf8-Zeichen)
$remove_https_at_video_links  = 1;    //0|1 es gab bei sr-online mal probleme mit https://
$orf_filcheck_legal__filesize = 934445; //Prüfe ORF-Film ob auch in deutschand erreichbar (deaktvieren=0 oder =false)
                            //Bei gespeerten Filmen wird stattdessen ein Hinweißclip abgespielt.
                            //diese Prüfung prüft auf diesen Hinweißclip;
                            //Die Größe des Hinweisklips fest zum Vergleich, wird *1.5 genommen;
                            // Wert in Byptes, bspw. 934445Bytes, entspricht 912,54 KB)
$file = "./Filmliste-akt";  //Filmlisten-Datei


$extra_audiodeskription = 1; //erstelle einen extra "Sender" nur für alle AudioDeskription Einträge (aktivierbar für den Benutzer)
$extra_gebaerdensprache = 1; //erstelle einen extra "Sender" nur für alle Gebärdensprache Einträge  (aktivierbar für den Benutzer)



$hideArte_fr     = 2; //Arte.fr ausblenden (0=deaktiviert, 1=komplettArte.fr Ausblenden, 2=Arte.fr Kann vom Benutzer aktiviert werden)
                      // (der Cache muss nach Wertänderung neu erstellt werden: liste.php?list_update__only_aufteilen=1)
$fullscreen_play = 0; //Videoplayer
                      // (0=deaktiviert (nur direkter Datei-Link), 1=Videoplayer (EMPFOHLEN!), 2=Videoplayer Kann vom Benutzer deaktiviert werden)
                      // Öffnet alle Video auf einer Player Seite (Video auf Bildschirmgröße, startet sofort);
                      // Der Datei-dirktlink wird nicht als volle Größe (also nur zu klein) dargestellt
                      // Bei aktivierten download_proxy.php muss der fullscreen_play deaktivert ("0") sein.
$letzterListeneintragOben = 1; //beim Zurückspringen zur Liste, ist der letzte Eintrag ganz oben (Hilfreich wenn der TV-Browser jedesmal die Maus-Position auf oben Links zurücksetzt)
$sortByDate = 1; //sortiert Filmliste nach Datum (aktuelles Oben)

$dereff = ''; //bspw.:   http://nullrefer.com/?//geht nur bei den Direktlinks (nicht für die videos in Vollbild abgespielt werden) (dadurch wird die eigene Homepage-Adresse versteckt)
             //bspw.: download_proxy.php kann man hier auch eintragen (Bspw. wenn man keine deutsch IP-Adresse hat; Dann werden alle Filmdateien darüber rungerladen; sihe download_proxy.php); Als Extra Download Link

$only_accessable_for_hbbtv_experimental = false; //true|false Nur HBB-TV-Gerät zugelasssen (Nut Testweise; Normale Browser sollten nicht mehr richtig mit der Seite arbeiten können)

$search_allow    = true; //Suchfunktion braucht viel System-Ressourcen

//eigene vollständige URL
//Die Adresse ist notwenig für die automatische verarbeiten der Filmliste nach den Download (konkret: liste.php?list_update__only_aufteilen=1 )
//automatisch
if( isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])=='on')$self_host_url = 'https://';
else $self_host_url = 'http://';
$self_host_url .= 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
//manuell
//$self_host_url = 'http://localhost/Mediathek/liste.php';



setlocale(LC_ALL, 'de_DE.utf8');//klappt nicht
date_default_timezone_set("Europe/Berlin");  
setlocale (LC_ALL, null);
$loc_de = setlocale(LC_ALL, 'de_DE@euro', 'de_DE.utf8', 'deu_deu');

?>
