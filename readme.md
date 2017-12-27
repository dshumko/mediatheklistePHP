Mediathek-Liste PHP
---------------------------------------------------------------------------------------
**Archiv**; No longer developed  
List of Movies from german Television/Broadcast Mediathek (Crawled by MediathekView.de)

Mediathek-Liste PHP
---------------------------------------------------------------------------------------
**Archiv**; Keine Weiterentwicklung  
Server-Programm für Web-Server mit PHP.
Das Web-Programm listet die Filmliste vom MediathekView Projekt. Die Ansicht ist optimiert für SmartTV / TV-Browser.

### Server
* PHP, ausreichend Ram, "exec" erlaubt (details: siehe unten)

### Client
 * normaler Browser (nicht alle Filme abspielbar)
 * (neuere) TVs mit Internet-Browser (Mausbedienung ist umständlich!)
 * (Früher:) War auch mal als TV-App möglich (über die Pfeiltasten/Farbtasten der Fernbedienung)



### Demo
DemoServer abgeschaltet  
[DemoVideo youtube](https://youtu.be/DLFDfNCTSYQ)  
[Screenshots](https://github.com/soerenj/mediatheklistePHP/wiki)    

![Screenshot mit Themenliste](img/screenshots/Bildschirmfoto_themenliste.png)





### Bedienbarkeit:

  * (Früher:) Als Smart-TV-App (für u.g. Modell)mit der Fernbedienung: Teilweise noch nicht ganz rund, aber im großen und ganzen gut.
  * Im TV-Browser (d.h. die Fernbedienung-Pfeiltasten werden als Maus verwendet. Besser: man schließt eine ComputerMaus an den TV an): Das steuern mit der Fernbedienung ist recht umständlich.


### Funktion:
  * Themen je Sender
  * Alle Themen, aller Sender 
  * Extra-Liste für Gebärdensprache / Hörfassung / AudioDeskription (aktivierbar)
  * Themen als Schnellauswahl speichern (Favouriten; Programmstart bei langer Schnellauswahl z.T. mit Wartezeit verbunden)
  * Startseite [mit individueller Filmliste](https://raw.githubusercontent.com/soerenj/mediatheklistePHP/master/img/screenshots/Bildschirmfoto_schnellauswahl.png)
  * lange Scroll-Liste oder [Seitenweise](https://raw.githubusercontent.com/soerenj/mediatheklistePHP/master/img/screenshots/Bildschirmfoto_themenliste_seitenweise.png) navigieren
  * Sortiert nach Datum
  * verschiedene Video-Qualitäten einstellbar
  * Such-Funktion (verbraucht viel Ressourcen, dauert 10Sekunden; auch deaktivierbar)
  
### Filter:
  * Themen ausblenden
  * Filme ausblenden (freitext)
  * arte_fr deaktivierbar
  * orf-Filme prüfen ob in dtl. erreichbar 
  * Trailer/Vorschau/Hörfassungen/AudioDeskription ausblenden
  * filtern nach Mindest-Filmlänge  

### Geschwindigkeit
  * Grundsätzlich in Ordnung / brauchbar.
  * Beim nutzen der Funktion "[*Eigene Startseite mit individueller Filmliste*](https://raw.githubusercontent.com/soerenj/mediatheklistePHP/master/img/screenshots/Bildschirmfoto_schnellauswahl.png) " jedoch z.T. Wartezeiten von bis zu 10 Sekunden möglich.
  
### Installation auf TV:

  * (Früher:) Wäre nur via DNS-Hack möglich. Siehe Erkläsung im wiki: Installieren--&-DNS-Hack-auf-Smart-TV: https://github.com/soerenj/mediatheklistePHP/wiki/Installieren--&-DNS-Hack-auf-Smart-TV
  

### Server-Vorraussetzungen
Stand 8/2017  
- PHP 5.6 *(?)*
- RAM: sollten > 450MB (notfalls 256MB)
- Speicherplatz: Sollten 570MB (notfalls 240MB)
- Speicher-Vorraussetzungen im Detail: https://github.com/soerenj/mediatheklistePHP/wiki/Speicher-Vorraussetzungen-im-Detail

Muss installiert sein auf den Server:  
Mindestens:  
- PHP mit cURL  
- wenn nur mindestens vorliegt: der Filmlisten-Download läuft über externen WebSerice (muss man sich anmelden)  

Empfohlen:  
- ausführen von Befehlen erlauben ("exec")
- installierte Programme: wget xz-utils curl (sollte bei etwas besseren Webhosting standart sein(?))

### Fehlerbehebung
- Findet keine neue Filmliste? Prüfen ob Filmlisten-URL noch richtig ist (https://res.mediathekview.de/akt.xml)

==================================================

Lizenz:  GNU Affero General Public License (APGL)  
Außerdem enthaltener Code: 
        triviale Code-Auszügen von  
                Javascript createCookie()/getCookie(): https://stackoverflow.com/questions/4014935/why-doesnt-this-javascript-focus-work  
                Javascript formItemFocus(): https://stackoverflow.com/questions/4825683/how-do-i-create-and-read-a-value-from-cookie  

