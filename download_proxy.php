<?php

//Diese Datei ist nur optional

/*
Eine Weiterlöeitung (Proxy) zum DOWNLOAD (nur Download, nicht direkt anschauen) vom Filmdateien.
Das ist bspw. sinnvoll wenn man keine Deutsche IP-Adresse hat.

Download Proxy Ist eine mögliche SICHERHEITS-LÜCKE
Die Überprüfung der Adresse ist sehr einfach ausgelegt.
Daher sollte diese Funktion nur aktiviert werden bei Private+Geheimen installationen.
Aktivieren:
1) Diese Zeile löschen:
   die('deaktiviert');
2) Config.inc.php bearbeiten:
   $dereff = 'download_proxy.php?url='; 
3) Config.inc.php
   $fullscreen_play darf nicht auf =1 stehen ! (am besten $fullscreen_play=0;)
*/

die('deaktiviert');

if( !isset($_GET['url']) ) die("keine ?url=...");
if( count($_GET['url'])>512 ) die("url zu lang");
$url_end = end( explode('/',$_GET['url']) );

$url_cuted = str_replace($url_end, '', $_GET['url']);

if( !stristr(file_get_contents('Filmliste-akt'), $url_cuted) ) die("url (".strip_tags($url_cuted).")  nicht gefunden in Filmliste.<br>Mögliche Gründe für den Fehler:<br>-Fehler im Programm (versuche einen anderen Sender)<br>-Film ist inwzischen offline<br>-du bist ein Hacker");

$url_secure = explode('?',$_GET['url'])[0];


$url = $_GET['url'];


exec("curl -s -X HEAD -I --connect-timeout 10 -m 10 ".$url." 2>&1  | grep '^Content-Length: '",$return);
$filesize = str_replace('Content-Length: ','',$return[0]);
                                 

header("Content-Disposition: attachment; filename=\"".basename($_GET['url'])."\";" );
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
