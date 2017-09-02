Readme
Lizenz: GPLv3
soerenj-os@web.de


Das Web-Programm listet die Filmliste vom MediathekView Projekt.


Vorraussetzungen (Stand 8/2017):
-PHP5
-RAM*: sollten >350MB (notfalls 256MB)
-Speicherplatz*: Sollten 570MB (notfalls 240MB)
* Speicherbedarf könnte in Zukunft steigen

Muss installiere sein auf den Server:
-wget xz-utils
Außerdem für Auto-Update der Filmliste (empfohlen):
-curl
-Server muss das ausführen von Befehlen erlauben ("exec")


====== Hardware-Vorraussetzungen im Detail ======

Die Filmlisten-Datei wird vom MediathekView Projekt herruntergeladen.Größe der Datei (Stand 8/2017 ): ~110MB
Nach dieser Datei richtet sich der RAM/Speicher-Bedarf vom Server.
(Das Programm ließe wahrscheinlich auch umschreiben auf wenig RAM-Bedarf)

 RAM:
        Mindestens: 256MB (ohne Cache) (2x Filmliste + X)
        Mindestens: 350MB (mit Cache)

 Speicher auf der Festplatte (stand 8/2017):
        Mindestens: 240MB (ohne Cache)
        Mindestens: 570MB (mit Cache)
        Speicherbedarf besteht aus:
          - kompremierte Filmliste 20MB
          - Filmlisten-Datei 110MB
          - alte Kopie der Filmlisten-Datei 110MB
          - Cache Filmliste je Sender (abschaltbar) 110MB
          - Cache Filmliste je Thema (abschaltbar) 110MB
          - Cache Filmliste je Thema alle Sender (abschaltbar) 110MB

==================================================

Lizenz:  GNU Affero General Public License (APGL)
Außerdem enthaltener Code:
        MIT license http://spin.js.org/#v2.3.2
        Außerdem triviale Code-Auszügen von
                Javascript createCookie()/getCookie(): https://stackoverflow.com/questions/4014935/why-doesnt-this-javascript-focus-work
                Javascript formItemFocus(): https://stackoverflow.com/questions/4825683/how-do-i-create-and-read-a-value-from-cookie

