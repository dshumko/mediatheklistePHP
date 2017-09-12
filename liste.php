<?php
$program_version = '0.01';
$program_name    = 'Mediathek-liste PHP';
$startTimeRender = microtime(true);ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);

require_once 'config.inc.php';

$minLength = 0;
if(isset($_GET['min_length']) && $_GET['min_length']!='')$minLength = (int)$_GET['min_length']; 
if(isset($_GET['thema']) ) $_GET['thema'] = str_replace('x4sdy0ANDx4sdy0','&',$_GET['thema']); //sonst Probleme mit & im Thema

$addPageTitle = '';
if(isset($_GET['sender']) && $_GET['sender']!='') $addPageTitle .= ' '.$_GET['sender'];
if(isset($_GET['thema']) && $_GET['thema']!='')   $addPageTitle .= ' '.$_GET['thema'];


//Seite im Browser cachen (wenn sich Filmdatei/Programm-Code nicht geändert haben, behalte Browscache)
if(file_exists($file) && $cacheActive){

    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
    $if_modified_since = preg_replace('/;.*$/', '',   $_SERVER['HTTP_IF_MODIFIED_SINCE']);
    } else {
    $if_modified_since = '';
    }

    /* welchen Zeitpunkt nehmen?:
        a)Filmliste
        b)von dieser Skriptdatei
        c)von letzten Cookie-Update von Schnellauswahl
    */
    $mtime1 = filemtime($_SERVER['SCRIPT_FILENAME']);
    $mtime2 = filemtime($file);
    if( !isset($_GET['sender']) || $_GET['sender']=='' ){ //wenn auf Startseite mit Schnellauswahl
      if( isset($_COOKIE['favs']) && isset($_COOKIE['favs_last_update']) && $_COOKIE['favs_last_update']!=''){
            $mtime3 = strtotime($_COOKIE['favs_last_update']);
            $cacheControl = 'private'; //damit der Reverse Proxy nicht zwischenspeichert
       }
    }
    
    $mtime  = ($mtime1>$mtime2)? $mtime1 : $mtime2;
    if(isset($mtime3))$mtime  = ($mtime3>$mtime)? $mtime3 : $mtime; 
    
    $hasAndShowFavs = false;
    if( isset($_COOKIE["favs"]) && strlen($_COOKIE["favs"])<5  ){
         if( (!isset($_GET['sender']) ||$_GET['sender']=='' ) )    $hasAndShowFavs = true;
    }
    $gmdate_mod = date('D, d M Y H:i:s', $mtime) . ' GMT';
    //die($_SERVER['HTTP_IF_MODIFIED_SINCE'].'A'.$gmdate_mod);
    if ($if_modified_since == $gmdate_mod) {
    header("HTTP/1.0 304 Not Modified");
    exit;
    }

    header("Last-Modified: $gmdate_mod");
    if( isset($cacheControl) && $cacheControl!='') header("Cache-Control: max-age=86400, $cacheControl");
    if($only_accessable_for_hbbtv_experimental) header("Content-Type: application/vnd.hbbtv.xhtml+xml;charset=utf-8"); //header("Content-Type: vnd.hbbtv.xhtml+xml; charset=utf-8");
    else header("Content-Type: text/html; charset=utf-8");

    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (60*60*24)) . ' GMT'); //maximal Gültig für ein Tag


}else{
    if($only_accessable_for_hbbtv_experimental) header("Content-Type: application/vnd.hbbtv.xhtml+xml;charset=utf-8");
    else header("Content-Type: text/html;charset=utf-8");    
}




if($only_accessable_for_hbbtv_experimental){
      echo '<!DOCTYPE html PUBLIC "-//HbbTV//1.1.1//EN" "http://www.hbbtv.org/dtd/HbbTV-1.1.1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="de" xml:lang="de">
<head>
<meta charset="UTF-8"/>
<meta http-equiv="Content-Language" content="de"/>
<meta http-equiv="content-type" content="application/vnd.hbbtv.xhtml+xml"/>
';
}else{
      echo '<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8"/>
<meta http-equiv="Content-Language" content="de"/>
';
}


echo '
<title>'.$PageTitle.' '.$addPageTitle.'</title>
';
echo "
<!-- $program_name v$program_version AGPL -->
<!-- stylesheets -->
<link rel=\"stylesheet\" type=\"text/css\" href=\"css/style.css\"/>
<script language=\"javascript\" type=\"text/javascript\">

function  loadNewSite(){ //beim verlassen der Seite (Ladeanimation)
    document.getElementsByTagName('body')[0].style.background = '#c3c3ff';
    document.getElementById('div-sender-select').style.background = '#c3c3ff';
    if(document.getElementById('list_auswahl_links_thema')!=undefined)document.getElementById('list_auswahl_links_thema').style.background  = '#c3c3ff';
    if(document.getElementById('spinner_elem')!=undefined){
      document.getElementById('spinner_elem').style.display='inline';
      window.setTimeout(function(){document.getElementById('spinner_elem').style.display='none'},50);
    }
}

function onload1y(){ //onload 
    var starttime = new Date().getTime();
    //return;
    document.getElementById('please_wait').style.display='none';
    if(document.getElementById('spinner_elem')!=undefined)document.getElementById('spinner_elem').style.display='none';
    var l = document.getElementsByClassName('link_black_before_onload');
    while( l!=undefined && l.length>0){ l[0].classList.remove('link_black_before_onload');}
    updateThemenListeLink_addSchnellauswahl(); //Themenliste-in bezug auf ausgewählte aktualisieren (gespeichert in cookies)
    updateFilmlistenSeite_ListeSchnellauswahl(); //Link hinzufügen zu Schnellauswahl aktualsieren (auf Filmlisten-Einträge-Seite)
    updateListeThemenLink_hideElements_andRepairLinks(); //Themenliste durchgehen, was ggf gelöscht/ausgeblendet werden soll (+anhängen Parameter MindestLänge)
    updateListeSenderLink(); //Senderliste (anhängen Parameter MindestLänge)
    
    setFocusForLastLink();//if(!isHbbTV())setFocusForLastLink(); //funktioniert bei Hbb-TVs nicht??? (noch testen!!)
    possibleHideHbbTVButtons(); //lösche HbbTV-Farb Buttons bei normalen Browsern (fuer Sender/Thema-Wahl)
    //if(isHbbTV()){ //klappt nicht..
          //falls der TV-Browser sich auch als HBBTV ausgibt, dabei ist er auf Maus Steuerung ausgelegt //mousemoveFunctionOnce
         // document.getElementById('link_sender_select').addEventListener('mouseover', function(e){hideHbbTVButtons();}, false);
         // document.getElementById('link_sender_select').addEventListener('focus', hideHbbTVButtons, false);
         // document.getElementById('link_sender_select').addEventListener('focusin', hideHbbTVButtons, false);
    //}
    filmliste_eintrage_event_onclick();
    filmliste_eintrage_event_videolinks_onclick();
    updateVideoMainLink_withQualityLink_andPossibleHideElements(); //Filmliste durchgehen: Update Videolink + was ggf gelöscht/ausgeblendet werden soll 
    updateFilmliste_HideElements( getCookie('hideHoerfassungFilme') ,
                      getCookie('hideAudioDeskriptionFilme') ,
                      getCookie('hideTrailerFilme') ,
                      getCookie('minLength') );
    var h = document.getElementById('fixed_head').offsetHeight;
    if(h>0){
      var elem = document.getElementById('abstand_oben1');      if(elem!=undefined)elem.style.display    = 'none';
      var elem = document.getElementById('abstand_oben2');      if(elem!=undefined)elem.style.display    = 'none';
      //var elem = document.getElementById('div-thema-select');   if(elem!=undefined)elem.style.paddingTop = h+'px';
      //var elem = document.getElementById('div-sender-select');  if(elem!=undefined)elem.style.paddingTop = h+'px';
      //var elem = document.getElementById('schnellauswahl');     if(elem!=undefined)elem.style.paddingTop  = h+'px';
      var elem = document.getElementById('content');              if(elem!=undefined)elem.style.paddingTop  = h+'px';
    }    //alert(a);
    
    //Link: Springe zu Filmen direkt über Schnellauswahl
    var countFavs = 0;       var raw = getCookie('favs');     if(raw=='')raw='{}'; var countFavs = JSON.parse(raw).length;
    if( countFavs> 2 && document.getElementById('schnellauswahl_list__jump_to_filme')!=undefined && document.getElementsByClassName('videolink_row').length>0 ) document.getElementById('schnellauswahl_list__jump_to_filme').style = 'inline-block';
    
    var c = getCookie('showFooter'); if(c>0) document.getElementById('fixed_footer').style.display = 'block';
    //alert( new Date().getTime() - starttime + 'ms');
}



function setFocusForLastLink(){
    if(location.hash=='#anker1_film_0') location.hash='#anker1_film_1';
    ";
  
    if( isset($_GET['sender']) && $_GET['sender']!='' && isset($_GET['thema']) && $_GET['thema']!='' ){
            echo "
            var firstlink = document.getElementsByClassName('list_video_mainlink')[0];
            firstlink.focus();
            formItemFocus( document.getElementById(firstlink) ); //alert('a');
            ";
    }
    echo " //--- Themenliste Seitenweise start ---";
    if( isset($_GET['start']) && $_GET['start']!='' ){
            echo "
            // setzt aktuellern Zeiger/Focus
            var prev_c = getCookie('prev_page');
            var next_c = getCookie('next_page');
            var next = document.getElementById('next_page');
            var prev = document.getElementById('prev_page');
            
            if(next!=undefined){ next.focus(); formItemFocus( next ); }
            if(prev!=undefined){ prev.focus(); formItemFocus( prev ); }
            if(next_c > prev_c && next!=undefined){ next.focus(); formItemFocus( next ); } //bleibe bei der gleichen Richtung (bspw. Seite vorwaerts)
            ";
    }    
        
    if( isset($_GET['sender']) && $_GET['sender']!='' && (!isset($_GET['thema']) || $_GET['thema']=='') ){
           echo "
           // setzt/korrigiere URL mit Start/Ende-Parameter
           var c_raw = getCookie('pageination');
           var c = parseInt( c_raw );
           
           var matchS = window.location.href.match(/start=(-?[0-9]*)/);
           var start  = 0;
           if(matchS!=null && matchS.length>1) start = parseInt(matchS[1]);
           
           var matchE = window.location.href.match(/ende=(-?[0-9]*)/);
           var ende   = 0;
           if(matchE!=null && matchE.length>1) ende = parseInt(matchE[1]);
           
           var e = location.href.match('#(.*)');
           var anker = '';
           if(e!=null && e.length>0)anker = '#'+e[1];
           
           var ende_kor = -1; //korrektur am Ende
           //console.log(c_raw+'+'+c+'+'+start+'+'+ ende);
           
           var cleanUrl = window.location.href.replace(anker,'').replace(/start=-?[0-9]*/,'').replace(/ende=-?[0-9]*/,'').replace(/&&/,'&').replace(/&&/,'&');
           if (cleanUrl.substring([ cleanUrl.length -1],1)=='&' ) cleanUrl = cleanUrl.substring(0,cleanUrl.length-2); //& am Ende löschen
           console.log(cleanUrl);
           if( (c_raw=='' || c<=0) && (start>0 || ende>0) ){ //lösche Pageination
             window.location = cleanUrl+anker;
           }else if( c>0 && (start+c+ende_kor)!=ende ){         //korrigiere Pageination
             ende = start+c;
             var preventLoop = 0;
             if(parseInt(getCookie('preventRedirectLoop'))>0) preventLoop+=parseInt(getCookie('preventRedirectLoop'));
             createCookieInSeconds('preventRedirectLoop', preventLoop+1, 5);
             if(start==0) start = 1;
             if( parseInt(getCookie('preventRedirectLoop'))<10) window.location = cleanUrl+'&start='+start+'&ende='+(ende+ende_kor)+anker;
           }
           
           ";
    }
    echo "
    //--- Themenliste Seitenweise ende ---
   
    //if( location.hash=='#thema_select' || location.hash.search('#buchstabe_')!==-1 || location.hash.search('#thema_sel_')!==-1  || location.hash.search('#anker1_thema_sel_')!==-1) show_thema_select();
    if( location.hash=='#sender_select'  || location.hash.search('#thema_sel_')!==-1) document.getElementById('div-sender-select').style.display='block';
  
    if( location.hash=='#settings') toggleShowOptions('show');
    if( location.hash.search('#film_')!==-1  || location.hash.search('#anker1_film_')!==-1){ 
    var mainlinkNumber = location.hash.replace('#','').replace('anker1_film_','');
    var number = Number(mainlinkNumber);//  + 2;
    var mainlink = 'mainlink_' + 'film_' + number; //+2 ist die Korrektur damit der obere Rand nicht überdeckt (vgl. Code beim erstellen des Ankers)
    console.log( 'setzte Fokus auf:'+' id: '+mainlink );
            document.getElementById(mainlink).focus();
    formItemFocus( document.getElementById(mainlink) ); //alert('a');
      }
       if( location.hash.search('#thema_sel_')!==-1 || location.hash.search('#anker1_thema_sel_')!==-1){
            var mainlinkNumber = location.hash.replace('#','').replace('anker1_thema_sel_','');
            var number = Number(mainlinkNumber);// + 5;
            //console.log( ' nr: '+mainlinkNumber );
            var mainlink = 'mainlink_' + 'thema_sel_' + number;//+5 ist die Korrektur damit der obere Rand nicht überdeckt (vgl. Code beim erstellen des Ankers)
            //console.log( 'setzte Fokus auf:'+' id: '+mainlink );
            document.getElementById(mainlink).focus();
            formItemFocus( document.getElementById(mainlink) ); //alert('a');
      }

}//ende function setFocusForLastLink
";
echo "


  
//window.onunload = function(){};

</script>";
echo '
<script language="javacript"  type="text/javascript"  src="js/schnellauswahl.js"></script>
<script language="javacript"  type="text/javascript"  src="js/base.js"></script>
';
if($loaderAnimation===1) echo "<script language=\"javacript\"  type=\"text/javascript\" src=\"js/spin.min.js\" ></script>";
echo "
<script type=\"text/javascript\">
//kann bereits vor onload passieren
var e = location.href.match('min_length=([0-9]*)');
var minLength = getCookie('minLength');
if(minLength>0){
      if( e==null){ //fuege es hinten an (aber vor den Anker)
        var e = location.href.match('#(.*)');
        var anker = '';
        if(e!=null && e.length>0)anker = '#'+e[1];
        if( location.href.search(/\?/)!=-1 ) location.href = location.href.replace(anker,'') + '&min_length='+ minLength + anker;
        else location.href = location.href.replace(anker,'') + '?min_length='+ minLength + anker;
      } 
      else if(e!=null  && e.length>1 && getCookie('minLength')!=e[1] ) location.href = location.href.replace('&min_length='+e[1], '&min_length='+getCookie('minLength') ); //korrigiere URL
}else if( location.href.search('&min_length=')!=-1){ 
      location.href = location.href.replace(/&min_length=[0-9]*/,''); //lösche es raus
}";
if( isset($_GET['sender']) && (!isset($_GET['thema']) || $_GET['thema']=='') ){
  echo "
  var e = location.href.match('no_table=([0-9]*)');
  var no_table = getCookie('no_table');
  if(no_table>0){
        if( e==null && location.href.search(/\?/)!=-1){ //fuege es hinten an (aber vor den Anker)
          var e = location.href.match('#(.*)');
          var anker = '';
          if(e!=null && e.length>0)anker = '#'+e[1];
          location.href = location.href.replace(anker,'') + '&no_table='+ no_table + anker;
        } 
        else if( e!=null  && e.length>1 && getCookie('no_table')!=e[1] ) location.href = location.href.replace('&no_table='+e[1], '&no_table='+getCookie('no_table') ); //korrigiere URL
  }else if( location.href.search('&no_table=')!=-1){ 
        location.href = location.href.replace(/&no_table=[0-9]*/,''); //lösche es raus
  }";
}
echo "
</script>
</head>
<body onload=\"onload1y()\">


";





/**
 * Update der Filmliste
 */





/***
* Möglichkeiten:
* 1. Automatisch alle X(10) Minuten wird überprüft ob neue Version vorliegt 
*    und dann ggf. auch gleichrunterladen
* 2. Update von Hand anstoßen ?updatelist=1
***/

if(!file_exists('cache'))       mkdir('cache');

//Prüfe ob neue Filmlisten-Datei vorhanden
if( !file_exists('cache/status_newFilmlisteFileVorhanden')
    &&
    (!file_exists("cache/status_lastFilmlistenCheckForUpdate") || filemtime('cache/status_lastFilmlistenCheckForUpdate')+(60*10)<date('U') ))
    { //nur alle 10Minuten möglich
        //if(!file_exists('cache/status_lastFilmlistenFileModified') ) exec('touch cache/status_lastFilmlistenFileModified'); //Timstamp der Datei stimmt zwar nicht, aber besser als kar keinen
        
        require_once('inc/inc.filmlisten_download.php');

        if($filmlisten_autoUpdate){ //sammle Kommandus, für ggf. update
             $comandsDownload = filmlist_download_and_extract_exec_getcommand($filmlisteUrl, $file, 'all');
             $comandsDownload.= ' wget -qO '.$self_host_url .'?list_update__only_aufteilen=1  2>&1 > /dev/null;';//teilt die neue Filmlisten-Datei auf
        }
        //$comandsDownload = '';
        //die($comandsDownload);
        exec("touch cache/status_lastFilmlistenCheckForUpdate");
        exec('(if [ "$(curl -s -v --head '.$filmlisteUrl.'  2>&1  | grep \'^Last-Modified:\')" != "$(cat cache/status_lastFilmlistenFileModified)" ]; then    touch cache/status_newFilmlisteFileVorhanden;    chmod 666 cache/status_newFilmlisteFileVorhanden;'.$comandsDownload.'fi) > /dev/null 2>/dev/null &');
      
}

//falls der Server beim Download/Extract abgebrochen wurde, wird nach 30Minuten erlaubt den Vorgang automatisch neu start zu lassen
if( file_exists('cache/status_startFilmlistenDownload') && filemtime('cache/status_startFilmlistenDownload')+(60*30)<date('U')){
      exec('rm cache/status_startFilmlistenDownload;');
}

if( file_exists('cache/status_startFilmlistenExtract') && filemtime('cache/status_startFilmlistenExtract')+(60*30)<date('U') ){
      exec('rm cache/status_startFilmlistenExtract;');
}

if(isset($_GET['list_update'])){
    sleep(3); //warte, damit oben gezeigt Prüfung durchgeführt wurde;
    if(!$system_allow_exec_and_have_unxz && file_exists($file) && date('U') <= filemtime($file)+($timoutFilmlisteToOld*60))die( "<br><br><br><br><p>Datei konnte nicht aktualisiert werden (geht nur alle ".$timoutFilmlisteToOld." Minuten).</p>".'<a href="liste.php?" style="padding-bottom:15pt">weiter</a>');
    if( file_exists("cache/status_lastFilmlistenFileModified") )$lM = file_get_contents("cache/status_lastFilmlistenFileModified"); else $lM = '';
    if($system_allow_exec_and_have_unxz && !file_exists('cache/status_newFilmlisteFileVorhanden') && file_exists($file)) die( "<br><br><br><br><p>Datei konnte nicht aktualisiert werden <br>(geht nur wenn aktuellere Datei auf den Mediathek-View Server vorliegt; bitte die Seite neu-laden um es ein zeites mal zuversuchen). ".$lM."</p>".'<a href="liste.php?" style="padding-bottom:15pt">weiter</a>');  
}





$options_createCopyEachSender = array('hideArte_fr'=>$hideArte_fr, 'minLengthVorlagenMinuten'=>$minLengthVorlagenMinuten); //,'hideHoerfassung'=>$hideHoerfassung //'hideTrailer'=>$hideTrailer, 
   
//Filmlisten-Datei runterladen (entweder über Adresse liste.php?list_update=1  - oder - automatisch)
if(
      isset($_GET['list_update'])
   ){
           
  require_once('inc/inc.filmlisten_download.php');
  set_time_limit(600); //k.a. ob das hier wirkt
  echo "<br><br><br>";
 
  if($system_allow_exec_and_have_unxz==1){ //lade+entpacke lokal
        echo "Filmliste wird neu geladen....bitte warten... 
        (je nach Internet-Verbindung/Server-Auslastung länger; kann bei 2Mbit auch bis zu 10Minuten dauern)
        ";
        echo "
      <span style=\"float:left;position:relative;left:50%;\">
       <span style=\"float:left;font-size:20pt;position:relative;left:-50%;\">";
       
        if($loaderAnimation!==0) echo     "<span id=\"please_wait\" style=\"font-size:8pt;color:#666666\"> <!--warten--> </span>";
        else             echo     "<span id=\"please_wait\" style=\"font-size:8pt;color:#666666\"> warten </span>";

        if($loaderAnimation===1) echo   "<span id=\"spinner_elem\"></span>";
        if($loaderAnimation===2) echo   "<span id=\"spinner_elem\"><span class=\"rotate\"><span class=\"rotate_correctur\">&#1161;<span></span></span>";

        if($loaderAnimation===1) echo "
        </span>
      </span>
        
    <link rel=\"stylesheet\" type=\"text/css\" href=\"css/style.css\"/>
        <!--<span style=\"color:#cccccc\"></span><br><span id=\"spinner_elem0\"> <--<span class=\"rotate0 rotate_correctur\">&#1161;</span>-->  </span>-->
        <!--
        <style language=\"text/css\">
          .rotate0 {
            position: absolute;
            top: 0%;
            left: 0%;
            width: auto;
            height: auto;
            margin: 0;
            -webkit-animation:spin 4s linear infinite;
            -moz-animation:spin 4s linear infinite;
            animation:spin 4s linear infinite;
          }
          @-moz-keyframes spin { 100% { -moz-transform: rotate(360deg); } }
          @-webkit-keyframes spin { 100% { -webkit-transform: rotate(360deg); } }
          @keyframes spin { 100% { -webkit-transform: rotate(360deg); transform:rotate(360deg); } }
        </style>-->
        <script type=\"text/javascript\" src=\"js/spin.min.js\" async></script>
        <script language=\"javascript\"  type=\"text/javascript\">
          var spinner0e = new Spinner().spin();
          document.getElementById('spinner_elem').appendChild(spinner0e.el)
        </script>"; myFlush();
        echo exec( filmlist_download_and_extract_exec_getcommand($filmlisteUrl, $file, 'download') );
        echo "Filmliste wird entpackt...."; myFlush();
        echo exec( filmlist_download_and_extract_exec_getcommand($filmlisteUrl, $file, 'extract') );
        echo "fertig mit entpacken....<br>"; myFlush();

  }elseif($cloud_convert_apikey!=''){ //lade+entspacke über Cloud-Service
        echo "Filmliste wird neu geladen....bitte warten... 
              (je nach Internet-Verbindung des Server/ dessen Auslastung (>100MB download))<br>"; myFlush();
        if(file_exists($file)) rename($file,$file."_old");
        downloadTheFileAndExtract("https://api.cloudconvert.com/convert", $file, $cloud_convert_apikey, $filmlisteUrl);

        if( (filesize($file)/1024/1024) > 30){ //prüfe Datei auf mind. 30MB
             if(file_exists($file."_old")) unlink($file."_old");
             echo "<p style=\"color:green\">Fertig</p>";
        }else{  //die Datei ist zu klein, wahrscheinlich also fehlerhaft (sollten ~110MB sein)
         if(file_exists($file)) unlink($file); 
             echo "<p><b style=\"color:red\">Abgebrochen</b>";
             echo "  Neue Filmlisten-Datei ist zu klein ".(filesize($file)/1024/1024)."MB und somit evtl. fehlerhaft(?).<br> ";
             echo " (wenn die Liste an einen tag zu oft aktualsiert wurde, kann sein das es erst am nächsten Tag wieder geht???";
             echo " Beschränkung am Download/Datei-Umwandlung vom externen Dienstleister)";
             echo "  Alte Datei wieder hergestellt.";
             echo "</p>";
             rename($file."_old",$file);
        }
  }

  echo "<p>Aufteilen in einzelne Sender/Themenlisten... "; myFlush();
 
  createCopyEachSender($file,$options_createCopyEachSender,$minLengthVorlagenMinuten);

  echo "<u>Fertig</u></p><br>Bitte seite ggf. <a href=\"#\" onClick=\"window.location.reload()\">neuladen</a> <br>Zurück zum Seiten-Beginn <a href=\"liste.php\" >Liste.php</a><br>"; myFlush();
}


if( isset($_GET['list_update__only_aufteilen']) && $_GET['list_update__only_aufteilen']==1 ){ //nur die Aufteilung druchführen
   require_once('inc/inc.filmlisten_download.php');
   createCopyEachSender($file,$options_createCopyEachSender,$minLengthVorlagenMinuten);
   echo "<u>Fertig</u></p><br>Zurück zum Seiten-Beginn <a href=\"liste.php\" >Liste.php</a><br>"; die();
}




/**
 * Hauptprogramm
 */

echo "<div id=\"content\">";

//utf8_encode()
if(!isset($_GET['sender']) && !isset($_GET['themen'])) echo "<span id=\"abstand_oben1\"><br><br><br></span>"; //Leerzeilen am Anfang (werden soweiso überdeckt)
else echo "<span id=\"abstand_oben1\"><br><br><br><br><br></span>"; //Leerzeilen am Anfang (werden soweiso überdeckt)
if(!isset($_GET['sender'])) echo "".$welcomeText."";

echo "<span style=\"float:right;padding-right:3pt;\">";
if(!file_exists($file) && file_exists('cache/status_startFilmlistenDownload')) echo "<p>Derzeit wird eine neue Filmliste runtergeladen (im Hintergrund)</p>";
else if(!file_exists($file) && file_exists('cache/status_startFilmlistenExtract')) echo "<p>Derzeit wird eine neue Filmliste entpackt (im Hintergrund)</p>";
else if(!file_exists($file)) die("<br><br><br>Mediathek Dateiliste fehlt. <a href=\"liste.php?list_update=1\">jetzt Runterladen</a>");
if( file_exists($file)){
    if( $timoutFilmlisteToOld>1 && date('U') > filemtime($file)+($timoutFilmlisteToOld*60) && !$filmlisten_autoUpdate){ echo" <a href=\"liste.php?list_update=1\" class=\"abstandlinks\">Liste aktualisieren</a> &nbsp; "; $fileliste_could_be_updated = true;}
    else if( $system_allow_exec_and_have_unxz && file_exists("cache/status_newFilmlisteFileVorhanden") && !$filmlisten_autoUpdate ){  echo"<span style=\"color:grew\"></span><a href=\"liste.php?list_update=1\" class=\"abstandlinks\">Neue Filmliste laden</a> &nbsp; "; $fileliste_could_be_updated = true;}
}
if( file_exists('cache/status_lastFilmlistenFileModified') ) $d = str_replace(' GMT','', substr(file_get_contents('cache/status_lastFilmlistenFileModified'),19)); else $d = '';
if(!isset($_GET['sender'])) echo " &nbsp; Stand: ".$d.''; //date ("d.m.Y H:i", filemtime($file))
echo "</span><span style=\"clear:both\"></span>\n";

/* Suchen/Filtern aus (geht auch nur in aktueller Filmliste) (veralteter Programmcode; Suche wurde gelöscht, da viel Ressourcen braucht)
if( isset($_GET['sender']) && $_GET['sender']!='' ){
      $n = 'display:none';
      if( isset($_GET['filter_minFilmLength']) && $_GET['filter_minFilmLength']!='' ) $n = '';
          if( isset($_GET['filter_maxFilmLength']) && $_GET['filter_maxFilmLength']!='' ) $n = '';
      if($n!='')echo "<br><span style=\"text-align:right;display:block;\" ><a href=\"#\" onclick=\"document.getElementById('filmliste_filter_zeit').style.display='inline';this.style.display='none';\" class=\"abstandlinks\" >Filtern in aktueller Liste nach Filmlänge</a>  (nur in aktueller Liste) &nbsp; </span> ";
      echo "<form style=\"display:block;text-align:right;$n\" id=\"filmliste_filter_zeit\" method=\"GET\" >Liste Filtern nach Filmlänge: &nbsp; ";
      echo "<input type=\"hidden\" name=\"sender\" value=\"".$_GET['sender']."\" />";
      echo "<input type=\"hidden\" name=\"search\" value=\"".(isset($_GET['search'])?$_GET['search']:'')."\" />";
      echo "<input type=\"hidden\" name=\"search_fulltext\" value=\"".(isset($_GET['search_fulltext'])?$_GET['search_fulltext']:'')."\" />";
      echo "<input type=\"hidden\" name=\"thema\" value=\"".(isset($_GET['thema'])?$_GET['thema']:'')."\" />";
      //echo "<input type=\"hidden\" name=\"quality\" value=\"".(isset($_GET['filter_quality'])?$_GET['quality']:'')."\" />"; //verschoben in Cookie/Javascript
      echo "Minimum<input type=\"number\" pattern=\"[0-9]*\" length=\"3\" size=\"3\" name=\"filter_minFilmLength\"  id=\"filmliste_filter_zeit_input_minFilmLength\" value=\"".(isset($_GET['filter_minFilmLength'])?$_GET['filter_minFilmLength']:'')."\" />Min. ";
      echo "&nbsp; Maximal<input type=\"number\" pattern=\"[0-9]*\" length=\"3\" size=\"3\" name=\"filter_maxFilmLength\" id=\"filmliste_filter_zeit_input_maxFilmLength\" value=\"".(isset($_GET['filter_maxFilmLength'])?$_GET['filter_maxFilmLength']:'')."\" />Min. ";
      //foreach($allLengths as $l=>$count){} //bisher nicht benutzt
      echo "<input type=\"submit\" value=\"Filtern\" /> <input type=\"reset\" onClick=\"document.getElementById('filmliste_filter_zeit_input_minFilmLength').value='';document.getElementById('filmliste_filter_zeit_input_maxFilmLength').value='';this.form.submit();\" form=\"filmliste_filter_zeit\" value=\"⌫ Löschen\" />";
      echo "</form>\n";
}


if( isset($_GET['sender']) && $_GET['sender']!='' ){
      $n='';
      if( !isset($_GET['search']) || $_GET['search']=='' ) $n = 'display:none';
      if($n!='')echo "<span style=\"text-align:right;display:block;\" ><a href=\"#\" onclick=\"document.getElementById('filmliste_search').style.display='inline';this.style.display='none';\" class=\"abstandlinks\" >Suchwort</a> (nur in aktueller Liste) &nbsp; </span> ";
      echo "<form style=\"display:block;text-align:right;$n\" id=\"filmliste_search\" method=\"GET\" >Suchwort in aktueller Liste: &nbsp; ";
       echo "<input type=\"hidden\" name=\"sender\" value=\"".$_GET['sender']."\" />";
       echo "<input type=\"hidden\" name=\"filter_minFilmLength\" value=\"".(isset($_GET['filter_minFilmLength'])?$_GET['filter_minFilmLength']:'')."\" />";
       echo "<input type=\"hidden\" name=\"filter_maxFilmLength\" value=\"".(isset($_GET['filter_maxFilmLength'])?$_GET['filter_maxFilmLength']:'')."\" />";
       echo "<input type=\"hidden\" name=\"thema\" value=\"".(isset($_GET['thema'])?$_GET['thema']:'')."\" />";
       //echo "<input type=\"hidden\" name=\"quality\" value=\"".(isset($_GET['filter_quality'])?$_GET['quality']:'')."\" />"; //verschoben in Cookie/Javascript
       echo "<input type=\"text\" name=\"search\" placeholder=\"Einzelnes Wort\"  size=\"10\" id=\"filmliste_search_input_search\" value=\"".(isset($_GET['search'])?$_GET['search']:'')."\" /> ";
      echo "&nbsp;<input type=\"checkbox\" name=\"search_fulltext\" id=\"search_fulltext\" value=\"1\" ".(isset($_GET['search_fulltext'])?'checked':'')." /><label for=\"search_fulltext\">Auch in Beschreibung suchen </label>";
      //foreach($allLengths as $l=>$count){} //bisher nicht benutzt
       echo "<input type=\"submit\" value=\"Suchen\" /> <input type=\"reset\" onClick=\"document.getElementById('filmliste_search_input_search').value='';document.getElementById('filmliste_search_input_search').checked=false;this.form.submit();\" form=\"filmliste_search\" value=\"⌫ Löschen\" />";
      echo "</form>\n";
}
*/



if( isset($_GET['thema']) && $_GET['thema']!=''){
      echo "<br/ ><br />";
      require_once 'inc/inc.filmliste_eintraege.php';
      onFilmlisteSeite_linkAddToSchnellauswahl();
}








if( isset($_COOKIE['favs']) && !isset($_GET['sender']) && !isset($_GET['thema'])){
    if(isset($_GET['sender'])) $d =' display:none';else $d = '';
    $jumpToFilmsLink =  "<a href=\"#anker1_film_0\" id=\"schnellauswahl_list__jump_to_filme\" style=\"display:none\" onClick=\"var e = document.getElementsByCallName('videolink_row'); if(e.length>0)formItemFocus( e[0] )\"> Springe zu den Filmen<br></a>";
    echo "<div id=\"schnellauswahl\" style=\"$d;padding-top:5pt;\">";
    echo "<span>$jumpToFilmsLink Schnellauswahl:</br></span>";
    echo "</div>";
    echo "<script language=\"javascript\" type=\"text/javascript\">getSchnellauswahl()</script>";
}

echo "<span id=\"start_list_beginn\"></span>";

echo "<a name=\"top\"></a>\n";


/* 
array(20) { [0]=> string(6) "Sender" [1]=> string(5) "Thema" [2]=> string(5)
"Titel" [3]=> string(5) "Datum" [4]=> string(4) "Zeit"
[5]=> string(5) "Dauer" [6]=> string(12) "GrÃ¶ÃŸe [MB]" [7]=> string(12) "Beschreibung" 
[8]=> string(3) "Url" [9]=> string(7) "Website" [10]=> string(14) "Url Untertitel"
[11]=> string(8) "Url RTMP" [12]=> string(9) "Url Klein" [13]=> string(14) "Url RTMP Klein"
[14]=> string(6) "Url HD" [15]=> string(11) "Url RTMP HD" [16]=> string(6) "DatumL"
[17]=> string(11) "Url History" [18]=> string(3) "Geo" [19]=> string(3) "neu" }
*/


$sender = array();
$senderThema = array();
$allLengths = array();



echo "
  <span style=\"float:left;position:relative;left:50%;\">
   <span style=\"float:left;font-size:20pt;position:relative;left:-50%;\">";
   
if($loaderAnimation!==0) echo     "<span id=\"please_wait\" style=\"font-size:8pt;color:#666666\"> <!--warten--> </span>";
else             echo     "<span id=\"please_wait\" style=\"font-size:8pt;color:#666666\"> warten </span>";

if($loaderAnimation===1) echo   "<span id=\"spinner_elem\"></span>";
if($loaderAnimation===2) echo   "<span id=\"spinner_elem\"><span class=\"rotate\"><span class=\"rotate_correctur\">&#1161;</span></span></span>";

if($loaderAnimation===1) echo "
<script language=\"javascript\"  type=\"text/javascript\">
var target = document.getElementById('spinner_elem');
var opts = {
  lines: 7 // The number of lines to draw
, length: 0 // The length of each line
, width: 14 // The line thickness
, radius: 22 // The radius of the inner circle
, scale: 0.5 // Scales overall size of the spinner
, corners: 1 // Corner roundness (0..1)
, color: '#fff' // #rgb or #rrggbb or array of colors
, opacity: 0.15 // Opacity of the lines
, rotate: 0 // The rotation offset
, direction: 1 // 1: clockwise, -1: counterclockwise
, speed: 0.21 // Rounds per second
, trail: 60 // Afterglow percentage
, fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
, zIndex: 2e9 // The z-index (defaults to 2000000000)
, className: 'spinner' // The CSS class to assign to the spinner
, top: '50%' // Top position relative to parent
, left: '50%' // Left position relative to parent
, shadow: true // Whether to render a shadow
, hwaccel: false // Whether to use hardware acceleration
, position: 'absolute' // Element positioning
}
var spinner = new Spinner(opts).spin()
target.appendChild(spinner.el)

  </script>";
echo "
    </span>
  </span>
  <span style=\"clear:both\"></span>
";


echo "
<div id=\"fixed_head\" style=\"width:100%;min-width:100%;top:0px;display:block;position:fixed;margin:0pt;padding:0pt;padding-left:8pt;margin-left:-8pt;padding-right:20pt;z-index:9;\">\n";

echo"\n<div id=\"start\">";




if( isset($_GET['sender']) && $_GET['sender']!='' ){
echo "
  <span style=\"float:left;position:relative;left:50%;margin-left: -16pt;\">
   <span style=\"float:left;position:relative;left:-50%;\">
  <!--<a href=\"liste.php?\" style=\"font-size:20pt;\" title=\"haus home startseite von Liste\" id=\"homeicon\" tabindex=\"3\">&#x2302;</a>-->";
      
echo "
   </span>
  </span>
  <span style=\"clear:both\"></span>
";
}



//document.getElementById('div-sender-select').style.display='block';document.body.insertBefore(document.getElementById('div-sender-select'),document.getElementById('schnellauswahl'));
//doen not work (probleme bei HBBTV): formItemFocus(document.getElementById('sender_alle_link') )

echo "<a href='#sender_select' onClick=\"document.getElementById('div-sender-select').style.display='block';formItemFocus( document.getElementById('senderliste_2') );window.setTimeout(function(){ formItemFocus( document.getElementById('senderliste_2') ); },50);\"  id=\"link_sender_select\" class=\"link_black_before_onload\" tabindex=\"1\"><span style=\"background:yellow\"  class=\"hbbtv_button\">&nbsp;&nbsp;&nbsp;</span> Sender wählen&nbsp;&nbsp;&nbsp;";
if($minLength>0)echo "<small align=\"right\" style=\"float:right; padding-right:6pt;color:#777777\">kürzer als ".$minLength." Min. werden ausgeblendet.</small>";

if( isset($_GET['sender']))$s=$_GET['sender'];else $s='';
echo "<span style=\"color:black\">$s <span id=\"sender_waehlen_append\"></span> </span>";
echo "</a>";





echo "<div style=\"float:right;text-align:right\">
  <a href=\"#settings\" onclick=\"toggleShowOptions('');\" style=\"margin-left:0px;padding-left:3pt;padding-right:10pt;text-decoration:none\">⚙ Einstellungen</a> <!-- <a href=\"#\" onclick=\"document.getElementById('filmliste_search').style.display='inline';this.style.display='none';\" class=\"abstandlinks\" >&#x2315; Suchen</a> -->
  <a href=\"#bottom\" title=\"nach unten scrollen\" style=\"margin-left:0px;padding-left:5pt;padding-right: 5pt;text-decoration:none\" tabindex=\"0\">↧</a>
  <a href=\"#top\" title=\"nach oben scrollen\" style=\"margin-left:0px;padding-left:5pt;padding-right: 5pt;text-decoration:none\" tabindex=\"0\">↥</a>";


echo "</div>";
echo "
<script language=\"javascript\"  type=\"text/javascript\">
/*
function show_thema_select(){
      document.getElementById('thema_sel_buchstaben').style.display='inline-block';
      document.getElementById('list_auswahl_links_thema').style.display='block';
      document.getElementById('link_thema_select').style.background='';  
      document.getElementById('spinner_elem').style.display='none';
}*/
</script>";
echo "<br>\n";

if(isset($_GET['sender']))echo "<a href='liste.php?sender=".$_GET['sender']."' id=\"link_thema_select\" onclick=\"document.getElementById('link_thema_select').style.background='#f3f3f3'; document.getElementById('spinner_elem').style.display='inline'; window.setTimeout(show_thema_select,10);\"  class=\"link_black_before_onload\" tabindex=\"2\"><span style=\"background:green\" class=\"hbbtv_button\">&nbsp;&nbsp;&nbsp;</span> Thema wählen&nbsp;&nbsp;
<script type=\"text/javascript\">
var c = getCookie('minLength');
if(c>0) document.getElementById('link_thema_select').href = document.getElementById('link_thema_select').href+'&min_length='+c+'';
</script>
";
if( isset($_GET['thema']))$t=$_GET['thema'];else $t='';
echo "<span style=\"color:black\">$t</span>";
echo "</a>";



  

echo "<div style=\"float:right;padding-right:6pt;\">
      <small style=\"color:#777777\">".$footerText. " </small>";
      echo "<small style=\"\"><a href=\"#impressum\" onclick=\"if(document.getElementById('impressum').style.display=='none'){document.getElementById('impressum').style.display = 'block';}else{document.getElementById('impressum').style.display = 'none';}\">Impressum</a></small></div>";






echo "</div></div>"; //schließe fixed header Leiste vom oben





//options
echo "<a name=\"settings\" class=\"anker\" ></a>
<div id=\"options\" style=\"z-index:991;display:none;background:#ffffff;padding:25pt;min-width:500pt;\">
  <span style=\"float:right\"><a href=\"#\" onclick=\"toggleShowOptions('close');return false;\" title=\"close\">x</a></span>
  <h3>⚙ Einstellungen</h3><br>";
  require_once 'inc/inc.einstellungen.php';
  echo "
  <span style=\"float:right\"><a href=\"#\" onclick=\"toggleShowOptions('close');return false;\" title=\"close\">x</a></span>
</div>"; //ende von id=options





/**
 * Liste zur Sender- / Themen-wahl
 */
require_once 'inc/inc.senderliste_themenliste.php';

$options_showSenderAndThemenliste = array('hideArte_fr'=>$hideArte_fr,'minLength'=>$minLength); //, 'hideHoerfassung'=>$hideHoerfassung
$senderListOutArray = getSenderListe($options_showSenderAndThemenliste);
$dp = 'display:none'; $anker = parse_url($_SERVER["REQUEST_URI"],PHP_URL_FRAGMENT);
if( $anker=='#sender_select'  || strstr($anker,'#thema_sel')!==false) $dp = 'display:block';
      
echo "
<div id=\"div-sender-select\"  style=\"$dp\">
      <div id=\"list_auswahl_links_sender\">
            <a name=\"sender_select\"></a>&nbsp;<br>&nbsp;<br>
            <span style=\"float:right\">
                <a href='#sender_select' onclick=\"document.getElementById('div-sender-select').style.display='none'\">X</a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br>";
            
            $i=0;
            foreach($senderListOutArray as $senderTitel => $senderUrl){
                $i++;
                echo "<a href=\"$senderUrl\" name=\"sender_sel$i\" id=\"senderliste_$i\" onClick=\"window.location='#sender_sel$i';loadNewSite()\" style=\"display:block;width:100%;margin-left:-3pt;\" class=\"link_every_same_color\">$senderTitel</a>\n";     
            }
echo "
      </div>
</div>"; //id=div-sender-select





if( isset($_GET['sender']) && $_GET['sender']!='' && (!isset($_GET['thema']) || $_GET['thema']=='') ){

  $return = getThemenliste($options_showSenderAndThemenliste);

  $themen        = $return['themen'];
  $buchstabenLinks = $return['buchstabenLinks'];
  $dp = 'display:none'; $anker = parse_url($_SERVER["REQUEST_URI"],PHP_URL_FRAGMENT);
  if( $anker=='#sender_select'  || strstr($anker,'#thema_sel')!==false) $dp = 'display:block';
  if( isset($_GET['sender']) && $_GET['sender']!='' && (!isset($_GET['thema']) || $_GET['thema']=='')) $dp = 'display:block';
  //$dp = '';



  echo "
  <div id=\"div-thema-select\">";
  
    if(isset($_GET['start'])){
      $ll_von = (int)$_GET['start'];        
      $ll_bis = (int)$_GET['ende'];
      $ll_diff = $ll_bis - $ll_von +1;
      
      $total_count = 0; foreach($themen as $b => $themen0) $total_count+=count($themen0);

      $h = $_SERVER['REQUEST_URI'];
      $h = preg_replace('/&start=-?[0-9]*/', '', $h);
      $h = preg_replace('/&ende=-?[0-9]*/', '', $h);
      
      if($ll_von-1 > 0)            $h_prev = $h.'&start='.($ll_von-$ll_diff).'&ende='.($ll_bis-$ll_diff);
      if($ll_bis+1 < $total_count) $h_next = $h.'&start='.($ll_von+$ll_diff).'&ende='.($ll_bis+$ll_diff);
      if( isset($h_prev) ){
        echo "<p style=\"text-align:center;width:50%;display:block;float:left\">";
        echo "<a id=\"prev_page\" style=\"width:100%;min-width:100%;display:inline-block;\" onClick=\"createCookieInSeconds('prev_page',Date.now(),5)\" href=\"$h_prev\" ><</a>";
        echo "</p>";
      }
      if( !isset($h_prev) )echo "<p style=\"text-align:center;width:100%;display:block;float:left\">";
      else echo "<p style=\"text-align:center;width:50%;display:block;float:left\">";
      if( isset($h_next) )echo "<a id=\"next_page\" style=\"width:100%;min-width:100%;display:inline-block;\" onClick=\"createCookieInSeconds('next_page',Date.now(),5)\" href=\"$h_next\">></a>";
      echo "</p>";
    }
  
    echo "
      <!--<a name=\"thema_select\"></a>-->
      <a name=\"anker1_thema_sel_0\"></a><span id=\"abstand_oben2\"><br><br><br></span>
      <div id=\"list_auswahl_links_thema\" style=\"$dp;position:auto;z-index:102;\">
        <!--<span style=\"float:right\"> <a href='#thema_select' onclick=\"document.getElementById('list_auswahl_links_thema').style.display='none'\">x</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>-->\n
        ";
        //else echo "<br><br>";
        $ll = 0;
        
        $tag_table = 'table';
        $tag_tr = 'tr'; $tag_tr_append = ' class="t_row" ';
        $tag_td = 'td';
        if( isset($_GET['no_table']) && $_GET['no_table']==1){
                $tag_table = 'div';
                $tag_tr = 'div';
                $tag_td = 'span';  
        }elseif( isset($_GET['no_table']) && $_GET['no_table']==2){
                $tag_table = 'div';
                $tag_tr = 'div'; $tag_tr_append =  ' class="t_row float_e" ';
                $tag_td = 'span';  
        }
        

        echo "        <$tag_table id=\"table_sel_thema\"  style=\"border-spacing:0 0pt;width:100%;\" >";
        if(is_array($themen))foreach($themen as $b => $themen){
              $showBuchstabenlink = true;
              foreach($themen as $url => $more){
                  $ll++;
                  if( isset($ll_von) && isset($ll_bis) && ($ll<$ll_von || $ll>$ll_bis) )continue;
                  //$aktiv;
                  $anker_ll = $ll;
                  //if($anker_ll<=6)$anker_ll = 0;
                  //else if($anker_ll>6) $anker_ll -=5; //damit der aktuelle Eintrag nicht verdeckt ist von der Oberen Leiste
                  //   <span class=\"link_to_thema_span t_sel\">...</span><span style=\"clear:both\"></span>
                  
                  echo "
                  <$tag_tr $tag_tr_append>
                      <$tag_td>";
                        if($showBuchstabenlink==true){ //Buchstaben link (einmal)
                          echo "<a class=\"anker anker_buchstabe\" name=\"buchstabe_".rawurlencode($b)."\"></a>";
                          $showBuchstabenlink = false;
                        }
                        echo "
                        <a name=\"anker1_thema_sel_$ll\" class=\"anker_thema\"></a>
                        <a href=\"$url\" id=\"mainlink_thema_sel_$ll\" onClick=\"updateHash('#anker1_thema_sel_".($anker_ll)."');loadNewSite();return true;\" class=\"t_sel_a\">".$more['title']."</a>
                      </$tag_td>
                      <$tag_td class=\"t_sel_date\" align=\"right\"><nobr>".$more['date']."</nobr></$tag_td>
                      <$tag_td class=\"td_del\" align=\"center\"></$tag_td>
                      <$tag_td class=\"td_schnell\"></$tag_td>
                  </$tag_tr>\n";
              }
        }         

  echo"
        </$tag_table> <!-- von id=\"table_sel_thema\" -->
      </div>
      $buchstabenLinks
  </div>"; //id=div-thema-select     
}

















myFlush(); //sofort darstellen


/**
 * Liste mit Filmeinträgen: erstellen
 */
require_once 'inc/inc.filmliste_eintraege.php';
$allOuts = createAllElements();



//Ein zweites Mal prüfen ob die Liste aktualisiert werden kann
if( (!isset($fileliste_could_be_updated) || $fileliste_could_be_updated==false) && file_exists("cache/status_newFilmlisteFileVorhanden") && !file_exists('cache/status_startFilmlistenDownload') && !file_exists('cache/status_startFilmlistenExtract') ){  echo" <br>Neue Filmliste vorhanden: <a href=\"liste.php?list_update=1\" class=\"abstandlinks\">Liste aktualisieren</a> &nbsp; ";}











/**
 * Liste mit Filmeinträgen: ausgeben
 */
echo "<div style=\"\">";

echo $out1; //sammlungs-Ausgabe von oben (ggf. Debug-Infos, bislang ansonsten unwichtig)

//Liste sortieren
if($sortByDate==1) krsort($allOuts);
//Liste ausgeben


echo "
<div id=\"notice_before_filmliste__minLength\" style=\"display:none;text-align:right;padding-right:3pt;\"></div>
";

if( isset($_GET['sender']) && isset($_GET['thema']) && is_array($allOuts) && count($allOuts)==0){
  echo "<p style=\"color:#555555\">Kein Filme vorhanden (vlt. wegen  <a href=\"#settings\" onclick=\"toggleShowOptions('');\" style=\"margin-left:0px;padding-left:3pt;padding-right:10pt;text-decoration:none\">Einstellungen</a>)";

  if($minLength>0)echo "<span align=\"left\" style=\"padding-right:6pt;color:#777777\"><br>Kürzer als ".$minLength." Min. werden ausgeblendet.</span>";
  echo "</p>";
}
        
echo "
    <a name=\"anker1_film_0\" id=\"anker1_film_0\"></a>
    <table style=\"border-collapse: separate;border-spacing:0 20pt;width:100%;\" >\n";
$i = 0;
foreach($allOuts as $outArrayS){ //Timestamp Array
      foreach($outArrayS as $outArray){ //Filme je Timestamp
            $i++; if($i>$maxJeSeite) break;
            $br = ''; if($outArray['titleNotice']!='') $br = '<br/>';
            echo "
            <tr class=\"videolink_row\">
            <td valign=\"top\">
              <a name=\"anker1_film_".$i."\" class=\"anker\"></a>
              <a href=\"".$outArray['mainlink']."\" id=\"mainlink_film_".$i."\"  class=\"list_video_mainlink\" style=\"text-decoration:none;color:black;margin-left:-2pt;padding-left:2pt;display:block;\"  >
            <span class=\"line_headline\" style=\"display: inline-block;width: 80%;min-width: 80%;\">
                ".$outArray['titleNotice']." $br ".$outArray['title']."
          ".$outArray['notice']."
          ".$outArray['possibleThema']."
            </span>
                <span style=\"padding-left:8pt;display:block;\">
                ".$outArray['date_time']."
                ".$outArray['length']."
                ".$outArray['possibleSender']."
                <span >".$outArray['possibleAddationalDataAtNormalQ']." </span>
                 <br>
                <i>".$outArray['desc']."</i>
                </span>
              </a>
            </td>
            <td>";
            //$dpVideoLink='style="display:none"';
            //if(count($outArray['videofiles_links'])>1) echo "<a href=\"#\" class=\"abstandrechts link_every_same_color show_videolinks\" $dpShowLink>VideoLinks</a><br />";            
           // echo "<span class=\"videofiles_links\" $dpVideoLink>\n";
              
      foreach ($outArray['videofiles_links'] as $videoTitle => $videoUrl ){
          $class = strtolower( substr(strip_tags($videoTitle), 0,2) );
    echo "
        <a class=\"videolink_$class videolink\" href=\"".$videoUrl."\" > $videoTitle</a><br />";
      }
              
      //echo "</span>";
      echo "
              <a href=\"".$outArray['mediathekUrl']."\">".$outArray['mediathekTitle']."</a>
          </td>
        </tr>";
  }
}
echo "
    </table>
</div>\n";









/**
 * Impressum
 */

//echo "<br><br>echo "&nbsp; <br>";
echo "<a name=\"impressum\" ></a>";
echo "<div id=\"impressum\" style=\"display:none;background:white;margin:12pt;padding:12pt\">
<br/><p align=\"right\"><a href=\"#\" onclick=\"document.getElementById('impressum').style.display='none'\">X</a></p>
<h3>Impressum</h3>".$impressumText." <br>
<small><span style=\"color:#5f5f5f;float:right;padding-right:3pt;\">$program_name v$program_version</span></small><br>
</div>";







/**
 * Footer
 */
echo "<a name=\"bottom\" ></a>";
//echo "<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;";//Abstandshalter nach unten;
//<p><a href=\"#top\">↥</a></p>
//echo "<div id=\"footer2\" style=\"position: fixed;z-index: 222;display: block;bottom: 2pt;left: 270pt;\">";
//echo '</div>';


$end = microtime(true);
$creationtime = ($end - $startTimeRender);
//position:fixed;
if($cacheActive==false) printf("<small><span style=\"color:#5f5f5f;float:right;padding-right:3pt;\">page created in %.2f sec.</span></small>", $creationtime);
echo '</div>'; //id="content" ende
echo "<div id=\"fixed_footer\" style=\"width:100%;min-width:100%;bottom:0px;display:none;position:fixed;margin:0pt;padding-left:8pt;margin-left:-8pt;padding-right:20pt;z-index:9;;\">
    <a href=\"#top\" tabindex=\"\" style=\"display:block\" title=\"nach oben scrollen\">
      <span class=\"abstandlinks\">↥</span> <span style=\"float:right;padding-right: 12pt;\">↥</span><span style=\"clear:both\"></span>
    </a>
</div>";

echo "</body></html>";




/**
 *  Functions
 */
function myFlush(){ ob_end_flush();ob_start();flush();ob_flush(); } //Zwischenspeicher der Ausgabe senden
?>
