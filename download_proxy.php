<?php

//Diese Datei ist nur optional

/*
Eine Weiterleitung (Proxy) zum Download/ansehen vom Filmdateien.
Das ist bspw. sinnvoll wenn man keine Deutsche IP-Adresse hat.

Dieser Download Proxy ist eine mögliche SICHERHEITS-LÜCKE.
Daher empfehle ich diese Funktion nur zu aktivieren bei ::::: Private+Geheimen ::::: Installationen.
Auch sollte mal hiebei das Urheber-Recht beachten; Daher nochmal: Nur für Private und Geheime installation (keine öffentliche Aufführung; Man könnte die Installtion am besten auch Passwort schüztzen )
Außerdem entsteht bei der Verwendung dieses Proxy's viel Daten-Traffic (Bitte eigenen WebServer-Tarif beachten um kosten zu vermeiden)
Die Sicherheits-Überprüfung der Adresse ist sehr !!!!!!unsicher!!!!! Es besteht die Gefahr das Hacker diese ausnutzen um dei Seiten der öffentlich Rechlichen zu attaktieren. 

Aktivieren:
1) Diese Zeile löschen:
   die('deaktiviert');
2) Config.inc.php bearbeiten:
   $dereff = 'download_proxy.php?url='; 
3) Config.inc.php bearbeiten:
   $fullscreen_play = 0;
*/

die('deaktiviert! ACHTUNG: Verwendung des Proxys unsicher!!!! Nur bei Privaten und Passwort-geschützen installationen (mögliche Gefahr: das Hacker dieses verwenden um die Seiten der Medienanstalten anzugreifen)');

if( !isset($_GET['url']) ) die("keine ?url=...");
if( count($_GET['url'])>512 ) die("url zu lang");

$_GET['url'] = str_replace('http://','', $_GET['url']); $_GET['url'] = 'http://'.$_GET['url']; //muss http:// drin haben

$url_cuted = str_replace( basename($_GET['url']) , '', $_GET['url']); //lösche in URL: filmdatei.mp4 (o.ä.)


//Prüfe ob URL in Filmliste vorhanden ist
if( !stristr(file_get_contents('Filmliste-akt'), $url_cuted) ){
	//prüfe nochmal auf https:// URL
	if( !stristr(file_get_contents('Filmliste-akt'), str_replace('http://','https://',$url_cuted)) )
	{
		die("url (".strip_tags($url_cuted).")  nicht gefunden in Filmliste.<br>Mögliche Gründe für den Fehler:<br>-Fehler im Programm (versuche einen anderen Sender)<br>-Film ist inwzischen offline<br>-du bist ein Hacker");
	}
}
$url_secure = explode('?',$_GET['url'])[0];
$url = $url_secure;


exec("curl -s -X HEAD -I --connect-timeout 10 -m 10 ".escapeshellarg($url)." 2>&1  | grep '^Content-Length: '",$return);
$filesize = str_replace('Content-Length: ','',$return[0]);
if($filesize==0) die("Datei nicht gefunden");

//if($filesize<10485760) die("Da Stimmt was nicht.<br>Die Datei sehr klein (<10MB); Vermutlich ist es daher kein Video und der Zugriff über Proxy wurde gesperrt.<br>Direkter Zugriff ohne Proxy über diese Adresse:<br>".strip_tags($_GET[''])); //Das funktioniert nicht als Sicherheits-maßname. Grund: da nach einen HTTP HEAD-Anfrage wahrscheinlich schon die Anfrage/bzw. Hackangriff ausgeführt wurden (?)
                                 
//header("Content-Disposition: attachment; filename=\"".basename($_GET['url'])."\";" ); //Datei direkt runterladen
if( substr($url,-4)=='.mp4')header("Content-Type: video/mp4" );
else header("Content-Disposition: attachment; filename=\"".basename($_GET['url'])."\";" ); //Datei direkt runterladen
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".$filesize);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($curl, $data) {
    echo $data;
    ob_flush();
    flush();
    return strlen($data);
});
curl_exec($ch);
curl_close($ch);

/* fehler: 
$fp = fsockopen($url_secure, 80, $errno, $errstr, 30);
if (!$fp) {
    echo strip_tags($url_secure)." $errstr ($errno)<br />\n";
} else {
    $out = "GET / HTTP/1.1\r\n";
    $out .= "Host: $url_secure\r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $out);
    while (!feof($fp)) {
        echo fgets($fp, 128);
    }
    fclose($fp);
}*/
