Mediathek-Liste PHP
---------------------------------------------------------------------------------------

List of Movies from german Television/Broadcast Mediathek (Crawled by MediathekView.de)

Mediathek-Liste PHP
---------------------------------------------------------------------------------------

Das Web-Programm listet die Filmliste vom MediathekView Projekt.  Optimiert für SmartTV / TV-Browser  


### Zielplattform
Für folgende TVs:

 *  neuere TV mit Internet-Browser (Mausbedienung ist aber umständlich)
 *  TV-Modelle vom Hersteller Vestel / Telefunken / Dual (s.u.)

**Oder** im normalen Browser (nicht alle Filme abspielbar)

### Demo
DemoServer: http://149.202.236.159/liste.php   *(bis vorraussichtlich 31.12.2017)*
[DemoVideo youtube](https://youtu.be/DLFDfNCTSYQ)  
[Screenshots](https://github.com/soerenj/mediatheklistePHP/wiki)    
TV-App für Vestel/Telefunken: [Anleitung](https://github.com/soerenj/mediatheklistePHP/wiki/DNS-Hack-auf-Smart-TV) *(Nachteil: der Start vom Smart-Portal dauert länger)*  *(bis vorraussichtlich 31.12.2017)*

![Screenshot mit Themenliste](img/screenshots/Bildschirmfoto_themenliste.png)





### Bedienbarkeit:

  * Als Smart-TV-App (für u.g. Modell)mit der Fernbedienung: Teilweise noch nicht ganz rund, aber im großen gut.
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

  * Nur für einige Telefunken / Vestel / Dual TV-Geräte (Board MB110); DNS (Am TV muss die App drivecast muss vorhanden sein)
  * o.g. Anleitung lässt sich auf andere TVs übertragen, wenn man die Zeit hat die passende Adresse herauszufinden (braucht technisches Verständnis & Zeit).  
  
Darum habe ich das Programm geschrieben:  
Weil bei meiner Internet-Verbindung die SmartTV ZDF-Mediathek nicht benutzbar ist (zu langsam).  
Und ein anderer Grund: Vielleicht können ältere SmartTV-Modell dadurch doch noch die Mediatheken nutzen.  

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


==================================================

Lizenz:  GNU Affero General Public License (APGL)  
Außerdem enthaltener Code: 
        triviale Code-Auszügen von  
                Javascript createCookie()/getCookie(): https://stackoverflow.com/questions/4014935/why-doesnt-this-javascript-focus-work  
                Javascript formItemFocus(): https://stackoverflow.com/questions/4825683/how-do-i-create-and-read-a-value-from-cookie  

