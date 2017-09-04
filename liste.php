<?php
$startTimeRender = microtime(true);ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);

require_once 'config.inc.php';



if(isset($_GET['thema']) ) $_GET['thema'] = str_replace('x4sdy0ANDx4sdy0','&',$_GET['thema']); //sonst Probleme mit & im Thema

//Programmcode
/* nun per Javascript
if( isset($_GET['hideHoerfassung']) ){
      if((int)$_GET['hideHoerfassung']==$hideHoerfassung)setCookie('hideHoerfassung','',0);
      else{
            $hideHoerfassung = (int)$_GET['hideHoerfassung'];
            setCookie('hideHoerfassung',$hideHoerfassung,time()+60*60*24*30*12*3);
      }
}else if( isset($_COOKIE['hideHoerfassung']) ) $hideHoerfassung = (int)$_COOKIE['hideHoerfassung'];
*/

$addPageTitle = '';
if(isset($_GET['sender']) && $_GET['sender']!='') $addPageTitle .= ' '.$_GET['sender'];
if(isset($_GET['thema']) && $_GET['thema']!='')   $addPageTitle .= ' '.$_GET['thema'];


//if( isset($_GET['hideHoerfassung']) && $_GET['hideHoerfassung']!='') $hideHoerfassung = $_GET['hideHoerfassung'];   


//Seite im Browser cachen (wenn sich Filmdatei/Programm-Code nicht geändert haben, behalte Browscache)
$siteAllowCache = true;
/* die Bedinung wurde später in den Code verschoben
if(isset($_GET['sender']) && $_GET['sender']!=''){ $siteAllowCache = true;} //alle Sender- oder Themen-Unterseiten
else if( (!isset($_GET['sender']) || $_GET['sender']=='') && (!isset($_COOKIE['favs']) || strlen($_COOKIE['favs'])<10) ) $siteAllowCache = true; //=entspricht Startseite ohne Cookies
*/


if(file_exists($file) && $cacheActive && $siteAllowCache){

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
<!-- stylesheets -->
<link rel=\"stylesheet\" type=\"text/css\" href=\"css/style.css\"/>
<script language=\"javascript\" type=\"text/javascript\">

function  loadNewSite(){ //beim verlassen der Seite (Ladeanimation)
    document.getElementsByTagName('body')[0].style.background = '#c3c3ff';
    document.getElementById('list_auswahl_links_sender').style.background = '#c3c3ff';
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
	updateListeThemenLink_hideElements_andRepairLinks(); //Themenliste durchgehen, was ggf gelsöscht werden soll (+anhängen Parameter MindestLänge)
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
	updateVideoMainLink_withQualityLink();
	updateFilmliste_HideElements( getCookie('hideHoerfassungFilme') ,
	                    getCookie('hideAudioDeskriptionFilme') ,
	                    getCookie('hideTrailerFilme') ,
	                    getCookie('hideShorterThen') );
        //alert( new Date().getTime() - starttime + 'ms');
}



function setFocusForLastLink(){
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
           var c = parseInt( getCookie('pageination') );
           
           var matchS = window.location.href.match(/start=(-?[0-9]*)/);
           var start = 1;
           if(matchS!=null && matchS.length>1) start = parseInt(matchS[1]);
           
           var matchE = window.location.href.match(/ende=(-?[0-9]*)/);
           var ende = 0;
           if(matchE!=null && matchE.length>1) ende = parseInt(matchE[1]);
           
           var e = location.href.match('#(.*)');
           var anker = '';
           if(e!=null && e.length>0)anker = '#'+e[1];
           
           var ende_kor = -1; //korrektur am Ende
           
           var cleanUrl = window.location.href.replace(anker,'').replace(/start=-?[0-9]*/,'').replace(/ende=-?[0-9]*/,'');
           if( c<=0 && (start>0 || ende>0) ){ //lösche Pageination
             window.location = cleanUrl+'anker';
           }else if( c>0 && (start+c+ende_kor)!=ende ){         //korrigiere Pageination
             ende = start+c;
             var preventLoop = 0;
             if(parseInt(getCookie('preventRedirectLoop'))>0) preventLoop+=parseInt(getCookie('preventRedirectLoop'));
             createCookieInSeconds('preventRedirectLoop', preventLoop+1, 5);
             if( parseInt(getCookie('preventRedirectLoop'))<5) window.location = cleanUrl+'&start='+start+'&ende='+(ende+ende_kor)+anker;
           }
           
           ";
   }
   echo "
   //--- Themenliste Seitenweise ende ---
   
	 //if( location.hash=='#thema_select' || location.hash.search('#buchstabe_')!==-1 || location.hash.search('#thema_sel_')!==-1  || location.hash.search('#anker1_thema_sel_')!==-1) show_thema_select();
	 if( location.hash=='#sender_select'  || location.hash.search('#thema_sel_')!==-1) document.getElementById('list_auswahl_links_sender').style.display='block';
	
	if( location.hash=='#settings') toggleShowOptions('show');
	if( location.hash.search('#film_')!==-1  || location.hash.search('#anker1_film_')!==-1){ 
		var mainlinkNumber = location.hash.replace('#','').replace('anker1_film_','');
		var number = Number(mainlinkNumber) + 2;
		var mainlink = 'mainlink_' + 'film_' + number; //+2 ist die Korrektur damit der obere Rand nicht überdeckt (vgl. Code beim erstellen des Ankers)
		console.log( 'setzte Fokus auf:'+' id: '+mainlink );
            document.getElementById(mainlink).focus();
		formItemFocus( document.getElementById(mainlink) ); //alert('a');
      }
     	if( location.hash.search('#thema_sel_')!==-1 || location.hash.search('#anker1_thema_sel_')!==-1){
            var mainlinkNumber = location.hash.replace('#','').replace('anker1_thema_sel_','');
            var number = Number(mainlinkNumber) + 5;
            //console.log( ' nr: '+mainlinkNumber );
            var mainlink = 'mainlink_' + 'thema_sel_' + number;//+5 ist die Korrektur damit der obere Rand nicht überdeckt (vgl. Code beim erstellen des Ankers)
		console.log( 'setzte Fokus auf:'+' id: '+mainlink );
            document.getElementById(mainlink).focus();
		formItemFocus( document.getElementById(mainlink) ); //alert('a');
      }
      

}";
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
var e = location.href.match('hide_shorter_then=([0-9]*)');
var hideShorterThen = getCookie('hideShorterThen');
if(hideShorterThen>0){
      if( e==null && location.href.search(/\?/)!=-1){ //fuege es hinten an (aber vor den Anker)
        var e = location.href.match('#(.*)');
        var anker = '';
        if(e!=null && e.length>0)anker = '#'+e[1];
        location.href = location.href.replace(anker,'') + '&hide_shorter_then='+ hideShorterThen + anker;
      } 
      else if(e!=null  && e.length>1 && getCookie('hideShorterThen')!=e[1] ) location.href = location.href.replace('&hide_shorter_then='+e[1], '&hide_shorter_then='+getCookie('hideShorterThen') ); //korrigiere URL
}else if( location.href.search('&hide_shorter_then=')!=-1){ 
      location.href = location.href.replace(/&hide_shorter_then=[0-9]*/,''); //lösche es raus
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





$options_createCopyEachSender = array('hideArte_fr'=>$hideArte_fr, 'hideShorterThen'=>$hideShorterThen); //,'hideHoerfassung'=>$hideHoerfassung //'hideTrailer'=>$hideTrailer, 
   
//Filmlisten-Datei runterladen (entweder über Adresse liste.php?list_update=1  - oder - automatisch)
if(
      isset($_GET['list_update'])
   ){
         
         /* || !file_exists($file) ||
      ( 
	  isset($filmlisteAutoReload_ifOlderThen) && $filmlisteAutoReload_ifOlderThen>0 && 
	  ( !file_exists($file) || ( file_exists($file) && date('U') > filemtime($file)+($filmlisteAutoReload_ifOlderThen*60) ) )
      )
      */
  
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

if($loaderAnimation===1) echo 	"<span id=\"spinner_elem\"></span>";
if($loaderAnimation===2) echo 	"<span id=\"spinner_elem\"><span class=\"rotate rotate_correctur\">&#1161;</span></span>";

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
     echo "  Neue Filmlisten-Datei ist zu klein ".(filesize($file)/1024/1024)."MB und somit evtl. fehlerhaft(?).<br> (wenn die Liste an einen tag zu oft aktualsiert wurde, kann sein das es erst am nächsten Tag wieder geht??? Beschränkung am Download/Datei-Umwandlung vom externen Dienstleister)";
     echo "  Alte Datei wieder hergestellt.";
     echo "</p>";
     rename($file."_old",$file);
    }
  }

   echo "<p>Aufteilen in einzelne Sender/Themenlisten... "; myFlush();
   
   createCopyEachSender($file,$options_createCopyEachSender,$hideShorterThenList);

   echo "<u>Fertig</u></p><br>Bitte seite ggf. <a href=\"#\" onClick=\"window.location.reload()\">neuladen</a><br>"; myFlush();
}


if( isset($_GET['list_update__only_aufteilen']) && $_GET['list_update__only_aufteilen']==1 ){ //nur die Aufteilung druchführen
   require_once('inc/inc.filmlisten_download.php');
   createCopyEachSender($file,$options_createCopyEachSender,$hideShorterThenList);
   echo "<u>Fertig</u></p><br>Zurück zum Seiten-Beginn <a href=\"liste.php\" >Liste.php</a><br>"; die();
}




/**
 * Hauptprogramm
 */



//utf8_encode()
echo "<br><br>"; //Leerzeilen am Anfang (werden soweiso überdeckt)
echo "<br><br><br>"; //Leerzeilen am Anfang (werden soweiso überdeckt)
if(!isset($_GET['sender'])) echo " ".$welcomeText."";

echo "<span style=\"float:right\">";
if(!file_exists($file) && file_exists('cache/status_startFilmlistenDownload')) echo "<p>Derzeit wird eine neue Filmliste runtergeladen (im Hintergrund)</p>";
else if(!file_exists($file) && file_exists('cache/status_startFilmlistenExtract')) echo "<p>Derzeit wird eine neue Filmliste entpackt (im Hintergrund)</p>";
else if(!file_exists($file)) die("<br><br><br>Mediathek Dateiliste fehlt. <a href=\"liste.php?list_update=1\">jetzt Runterladen</a>");
if( file_exists($file)){
 if( $timoutFilmlisteToOld>1 && date('U') > filemtime($file)+($timoutFilmlisteToOld*60) && !$filmlisten_autoUpdate){ echo" <a href=\"liste.php?list_update=1\" class=\"abstandlinks\">Liste aktualisieren</a> &nbsp; "; $fileliste_could_be_updated = true;}
 else if( $system_allow_exec_and_have_unxz && file_exists("cache/status_newFilmlisteFileVorhanden") && !$filmlisten_autoUpdate ){  echo"<span style=\"color:grew\"></span><a href=\"liste.php?list_update=1\" class=\"abstandlinks\">Neue Filmliste laden</a> &nbsp; "; $fileliste_could_be_updated = true;}
}
if( file_exists('cache/status_lastFilmlistenFileModified') ) $d = str_replace(' GMT','', substr(file_get_contents('cache/status_lastFilmlistenFileModified'),19)); else $d = '';
if(!isset($_GET['sender'])) echo " &nbsp; Stand: ".$d.' &nbsp;'; //date ("d.m.Y H:i", filemtime($file))
echo "</span><span style=\"clear:both\"></span>\n";

/* Suchen/Filtern aus (geht auch nur in aktueller Filmliste)
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
      require_once 'inc/inc.filmliste_eintraege.php';
      onFilmlisteSeite_linkAddToSchnellauswahl();
}




echo "<br>&nbsp;";




if( isset($_COOKIE['favs']) && !isset($_GET['sender']) && !isset($_GET['thema'])){
  if(isset($_GET['sender'])) $d =' display:none';else $d = '';
  echo "<div id=\"schnellauswahl\" style=\"$d\">";
  echo "<br/ ><br />Schnellauswahl:</br>";
  /*$favs = JSON_decode($_COOKIE['favs']);
  natcasesort($favs);
  foreach( $favs as $fav){ 
    //if( isset($_GET["quality"]) && $_GET["quality"]!='' ) $s2 = "&quality=".$_GET['quality']; else $s2="";//verschoben in Cookie/Javascript
    $s2 = '';
    echo "<span class=\"line_schnellauswahl line_span\">
             <a href=\"#\" style=\"float:right\" onclick=\"if( removeFav('".$fav."')){window.location.reload();this.text='wird gelöscht';}\" style=\"color:blue\">löschen</a>
             <a style=\"display:block;width:100%\" href=\"".$fav.$s2."\">".str_replace('liste.php?sender=','',str_replace('&thema=',' ',($fav)))."</a>
        </span>";
  }
  echo "<span style=\"cursor:pointer;text-decoration:underline; color:blue;float:right;text-align:right;display:inline-block\" onClick=\"  var date = new Date();      date.setTime(date.getTime() + (0 * 24 * 60 * 60 * 1000));expires = '; expires=' + date.toGMTString();    document.cookie = 'favs' + '=' + '' + expires + '; path=/';window.location.reload();\">Alle löschen</span><span style=\"clear:both\"></span>";*/
  echo "</div>";
  echo "<script language=\"javascript\" type=\"text/javascript\">getSchnellauswahl()</script>";
}

//if(isset($_GET['sender']))echo "<a href=\"#\" onclick=\"document.getElementById('schnellauswahl').style.display='inline';this.style.display='none';\" >Zeige Schnellauswahl</a><br>";

echo "<span id=\"start_list_beginn\"></span>";

//dann ist der ander Teil nicht mehr klickbar: echo " <span style=\"width:100%;min-width:100%;bottom:0px;display:block;position:fixed;margin:0pt;padding:10pt;padding-left:8pt;margin-left:-8pt;padding-right:20pt;z-index:10;text-align:center;;\"><a href=\"liste.php?\">⌂</a></span>";

echo "<a name=\"top\"></a>\n";
//echo "<br><br><hr>";

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

//prüfe ob beim Startseite aus den Cache lesen
//if($cache_for_startseite_is_fresh !='' && (!isset($_GET['sender']) || $_GET['sender']=='') ){
//  if( file_exists($file.'__cache__select_sender') && filemtime($file) <= filemtime($file.'__cache__select_sender') &&
//    file_exists($file.'__cache__select_thema')  && filemtime($file) <= filemtime($file.'__cache__select_thema') ){
//  	$cache_for_startseite_is_fresh = 1;	
//   }
//}


echo "
  <span style=\"float:left;position:relative;left:50%;\">
   <span style=\"float:left;font-size:20pt;position:relative;left:-50%;\">";
   
if($loaderAnimation!==0) echo     "<span id=\"please_wait\" style=\"font-size:8pt;color:#666666\"> <!--warten--> </span>";
else             echo     "<span id=\"please_wait\" style=\"font-size:8pt;color:#666666\"> warten </span>";

if($loaderAnimation===1) echo 	"<span id=\"spinner_elem\"></span>";
if($loaderAnimation===2) echo 	"<span id=\"spinner_elem\"><span class=\"rotate rotate_correctur\">&#1161;</span></span>";

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
//if($cache_for_startseite_is_fresh==1) echo "	<script language=\"javascript\"  type=\"text/javascript\">document.getElementById('spinner_elem').style.display='none';</script>";
echo "
    </span>
  </span>
  <span style=\"clear:both\"></span>
";



//if( !isset($_GET['sender']) || $_GET['sender']=='' )echo "Bitte sende auswählen liste.php?sender=ABC<br>";
 
//if( isset($_GET['sender']))$s=$_GET['sender'];else $s='';
echo "
<div id=\"fixed_head\" style=\"width:100%;min-width:100%;top:0px;display:block;position:fixed;margin:0pt;padding:0pt;padding-left:8pt;margin-left:-8pt;padding-right:20pt;z-index:9;\"></span>\n";

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



//document.getElementById('list_auswahl_links_sender').style.display='block';document.body.insertBefore(document.getElementById('list_auswahl_links_sender'),document.getElementById('schnellauswahl'));
//doen not work (probleme bei HBBTV): formItemFocus(document.getElementById('sender_alle_link') )

echo "<a href='#sender_select' onClick=\"document.getElementById('list_auswahl_links_sender').style.display='block';\"  id=\"link_sender_select\" class=\"link_black_before_onload\" tabindex=\"1\"><span style=\"background:yellow\"  class=\"hbbtv_button\">&nbsp;&nbsp;&nbsp;</span> Sender wählen&nbsp;&nbsp;&nbsp;";
if( isset($_GET['sender']))$s=$_GET['sender'];else $s='';
echo "<span style=\"color:black\">$s <span id=\"sender_waehlen_append\"></span> </span>";
echo "</a>";





echo "<div style=\"float:right;padding-right: 12pt;text-align:right\">
  <a href=\"#settings\" onclick=\"toggleShowOptions('');\" style=\"margin-left:0px;padding-left:3pt;padding-right:10pt;text-decoration:none\">⚙ Einstellungen</a> <!-- <a href=\"#\" onclick=\"document.getElementById('filmliste_search').style.display='inline';this.style.display='none';\" class=\"abstandlinks\" >&#x2315; Suchen</a> -->
  <a href=\"#bottom\" title=\"nach unten scrollen\" style=\"margin-left:0px;padding-left:3pt;padding-right:10pt;text-decoration:none\" tabindex=\"0\">↧</a>
  <a href=\"#top\" title=\"nach oben scrollen\" style=\"margin-left:0px;padding-left:0px;text-decoration:none\" tabindex=\"0\">↥</a>";
//if(isset($_GET['sender']))echo "<a style=\"float:right;padding-right: 25pt;\" href=\"#\" onclick=\"document.getElementById('schnellauswahl').style.display='inline';\" >Zeige Schnellauswahl</a>";

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
//if(isset($_GET['sender']))echo "<a href='#thema_select' id=\"link_thema_select\" onclick=\"document.getElementById('link_thema_select').style.background='#f3f3f3'; document.getElementById('spinner_elem').style.display='inline'; window.setTimeout(show_thema_select,10);\"  class=\"link_black_before_onload\" tabindex=\"2\"><span style=\"background:green\" class=\"hbbtv_button\">&nbsp;&nbsp;&nbsp;</span> Thema wählen&nbsp;&nbsp;&nbsp;";
if(isset($_GET['sender']))echo "<a href='liste.php?sender=".$_GET['sender']."' id=\"link_thema_select\" onclick=\"document.getElementById('link_thema_select').style.background='#f3f3f3'; document.getElementById('spinner_elem').style.display='inline'; window.setTimeout(show_thema_select,10);\"  class=\"link_black_before_onload\" tabindex=\"2\"><span style=\"background:green\" class=\"hbbtv_button\">&nbsp;&nbsp;&nbsp;</span> Thema wählen&nbsp;&nbsp;&nbsp;
<script type=\"text/javascript\">
var c = getCookie('hideShorterThen');
if(c>0) document.getElementById('link_thema_select').href = document.getElementById('link_thema_select').href+'&hide_shorter_then='+c+'';
</script>
";
if( isset($_GET['thema']))$t=$_GET['thema'];else $t='';
echo "<span style=\"color:black\">$t</span>";
echo "</a>";
 

//echo"</div></div>\n"; //ende von Header-Leiste nach Unten verschoben


	

echo "<div style=\"float:right;padding-right:3pt;\">
      <small style=\"color:#777777\">".$footerText. " </small>";
      echo "<small style=\"\"><a href=\"#impressum\" onclick=\"if(document.getElementById('impressum').style.display=='none'){document.getElementById('impressum').style.display = 'block';}else{document.getElementById('impressum').style.display = 'none';}\">Impressum</a></small></div>";






echo "</div></div>"; //schließe fixed header Leiste vom oben









//options Start

echo "<a name=\"settings\" class=\"anker\" ></a>";
echo "<div id=\"options\" style=\"z-index:991;display:none;background:#ffffff;padding:25pt;min-width:500pt;\">";
echo "<span style=\"float:right\"><a href=\"#\" onclick=\"toggleShowOptions('close');return false;\" title=\"close\">x</a></span>
<h3>⚙ Optionen</h3><br>
       <div>Standart-Qualität: 
        &nbsp;&nbsp;&nbsp;&nbsp; 
        <a href=\"#\" onClick=\"createCookie('quality','hd',365*5);loadNewSite();location.reload();return false;\" id=\"set_quality_hd\" >Hoch (=HD)</a>
        &nbsp;&nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;&nbsp; 
        <a href=\"#\" onClick=\"createCookie('quality','normal',0);     loadNewSite();location.reload();return false;\" id=\"set_quality_normal\" >Normal (=meist ca. 2Mbit)</a> 
        &nbsp;&nbsp;&nbsp;&nbsp;  oder &nbsp; &nbsp;&nbsp;&nbsp; 
        <a href=\"#\" onClick=\"createCookie('quality','low',365*5);loadNewSite();location.reload();return false;\" id=\"set_quality_low\" >Gering (=ca. 0,5 - 1Mbit)</a> 
        <script  language=\"javascript\"  type=\"text/javascript\">
            if(getCookie('quality')=='low')document.getElementById('set_quality_low').innerHTML+=' &#10008;';
            else if(getCookie('quality')=='hd')document.getElementById('set_quality_hd').innerHTML+=' &#10008;';
            else document.getElementById('set_quality_normal').innerHTML+=' &#10008;';
        </script>
       </div>\n";
	 

echo '
      <br>
      <hr>
      <br>
      
      <img id="vorschaltseite_thumb" title="so siht die vorschaltseite aus" src="" data-src="img/vorschaltseite-mittext-thumb400px.png" height="200" border="1" style="margin-left:15pt;float:right;margin-right:5pt;margin-bottom:5pt;">
      Vorschaltseite (sihe Bild rechts):
      <span style="float:right; text-align:right">
            &nbsp;&nbsp;&nbsp;<a href="#" id="options_link_vorschaltseite_an" onClick="createCookie(\'direkt_zur_mediathek_liste\',\'1\',356*10);window.location.reload();">ausblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
            <a href="#" id="options_link_vorschaltseite_aus" onClick="createCookie(\'direkt_zur_mediathek_liste\',\'\',0);window.location.reload();">anzeigen</a>
            <script  language="javascript"  type="text/javascript"> if(getCookie(\'direkt_zur_mediathek_liste\')==1)document.getElementById(\'options_link_vorschaltseite_an\').innerHTML=\' ausblenden &#10008;\';else document.getElementById(\'options_link_vorschaltseite_aus\').innerHTML=\'anzeigen &#10008;\'; </script>
      </span>
 
      <span style="clean:both"></span>
      <hr>
       Filme mit Hörfassung im Namen   
       <span style="float:right; text-align:right">
            &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_hideHoerfassung_an" onClick="createCookie(\'hideHoerfassung\',\'1\',356*10);window.location.reload();">ausblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
            <a href="#" id="options_link_hideHoerfassung_aus" onClick="createCookie(\'hideHoerfassung\',\'\',0);window.location.reload();">anzeigen</a>
      </span>
      <script  language="javascript"  type="text/javascript"> if(getCookie(\'hideHoerfassung\')==1)document.getElementById(\'options_link_hideHoerfassung_an\').innerHTML=\' ausblenden &#10008;\';else document.getElementById(\'options_link_hideHoerfassung_aus\').innerHTML=\'anzeigen &#10008;\'; </script>
      
       <hr>
       Filme mit Audiodeskription oder "AD |" im Namen   
       <span style="float:right; text-align:right">
            &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_hideAudioDeskription_an" onClick="createCookie(\'hideAudioDeskription\',\'1\',356*10);window.location.reload();">ausgeblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
            <a href="#" id="options_link_hideAudioDeskription_aus" onClick="createCookie(\'hideAudioDeskription\',\'\',0);window.location.reload();">anzeigen</a>
      </span>
      <script  language="javascript"  type="text/javascript"> if(getCookie(\'hideAudioDeskription\')==1)document.getElementById(\'options_link_hideAudioDeskription_an\').innerHTML=\' ausblenden &#10008;\';else document.getElementById(\'options_link_hideAudioDeskription_aus\').innerHTML=\'anzeigen &#10008;\'; </script>
      
       <hr>
       Filme mit Trailer im Namen   
       <span style="float:right; text-align:right">
            &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_hideTrailer_an" onClick="createCookie(\'hideTrailer\',\'1\',356*10);window.location.reload();">ausgeblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
            <a href="#" id="options_link_hideTrailer_aus" onClick="createCookie(\'hideTrailer\',\'\',0);window.location.reload();">anzeigen</a>
      </span>
      <script  language="javascript"  type="text/javascript"> if(getCookie(\'hideTrailer\')==1)document.getElementById(\'options_link_hideTrailer_an\').innerHTML=\' ausblenden &#10008;\';else document.getElementById(\'options_link_hideTrailer_aus\').innerHTML=\'anzeigen &#10008;\'; </script>
      
       <hr>
       Themeliste
       <span style="float:right; text-align:right">
            &nbsp;&nbsp;&nbsp;Seitenweise:
            &nbsp;&nbsp;
            <a href="#" id="options_link_pageination10" onClick="createCookie(\'pageination\',\'10\',356*10);window.location.reload();"> 10 </a> &nbsp;&nbsp;
            <a href="#" id="options_link_pageination20" onClick="createCookie(\'pageination\',\'20\',356*10);window.location.reload();"> 20 </a> &nbsp;&nbsp;
            <a href="#" id="options_link_pageination30" onClick="createCookie(\'pageination\',\'30\',356*10);window.location.reload();"> 30 </a> &nbsp;&nbsp;
            <a href="#" id="options_link_pageination40" onClick="createCookie(\'pageination\',\'40\',356*10);window.location.reload();"> 40 </a>
            &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
            <a href="#" id="options_link_pageination_aus" onClick="createCookie(\'pageination\',\'\',0);window.location = (window.location.href).replace(/start=-?[0-9]*/,\'\').replace(/ende=-?[0-9]*/,\'\'); if(window.location == window.location.href.replace(/start=-?[0-9]*/,\'\').replace(/ende=-?[0-9]*/,\'\'))window.location.reload();">lange Listen</a>
      </span>
      <script  language="javascript"  type="text/javascript"> if(getCookie(\'pageination\')>0)document.getElementById(\'options_link_pageination\'+getCookie(\'pageination\')).innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_pageination_aus\').innerHTML+=\' &#10008;\'; </script>
      
       <span style="float:right; text-align:right"></span>
       
       <hr>
       Bessere Performance langer Themenlisten (testweise):<br> 
       <span style="float:right; text-align:right">
            &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_no_table_an" onClick="createCookie(\'no_table\',\'1\',356*10);window.location.reload();">Textliste</a>, &nbsp;&nbsp;
            <a href="#" id="options_link_no_table_an2" onClick="createCookie(\'no_table\',\'2\',356*10);window.location.reload();">TextlisteTabelle</a>, &nbsp;&nbsp;
            <a href="#" id="options_link_no_table_aus" onClick="createCookie(\'no_table\',\'\',0);window.location.reload();">Tabelle</a>
      </span>
      <script  language="javascript"  type="text/javascript"> if(getCookie(\'no_table\')==1)document.getElementById(\'options_link_no_table_an\').innerHTML+=\' &#10008;\';else if(getCookie(\'no_table\')==2)document.getElementById(\'options_link_no_table_an2\').innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_no_table_aus\').innerHTML+=\' &#10008;\'; </script>
      
       <br><hr>
       Filme ausblenden, die kürzer sind als:<br>
       <span style="float:left; text-align:left">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="#" id="options_link_hideShorterThen_0" onClick="createCookie(\'hideShorterThen\',\'\',0);window.location.reload();">ausschalten</a> &nbsp;&nbsp;&nbsp; oder: &nbsp;&nbsp;&nbsp;
       </span>
       <span style="float:right; text-align:right">';

            foreach ($hideShorterThenList as $h){
                echo '<a href="#" id="options_link_hideShorterThen_'.$h.'" onClick="createCookie(\'hideShorterThen\',\''.$h.'\',356*10);window.location=\'#\';window.location.reload();"><'.$h.'Min.</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            }
echo '
      </span>
      <script  language="javascript"  type="text/javascript">
        var c=getCookie(\'hideShorterThen\');
        if(c==\'\') c=0;
        document.getElementById(\'options_link_hideShorterThen_\'+parseInt(c)+\'\').innerHTML+=\' &#10008;\';
        //elseif(c>0) document.getElementById(\'options_link_hideShorterThen_aus\').innerHTML=\'anzeigen &#10008;\';
      </script><br><br><br>
      <span style="clean:both"></span>
      ';
      $url = 'liste.php?';

      //if($hideHoerfassung==1) echo "<hr><span style=\"color:#999999\">Filme mit Hörfassung im Namen werden ausgeblendet</span>";//verschoben in Javascript
      //if($hideTrailer == 1) echo "<hr><span style=\"color:#999999\">Filme mit Trailer im Namen werden ausgeblendet</span><br>";//verschoben in Javascript
      if($hideArte_fr == 1) echo "<hr><span style=\"color:#999999\">Filme vom Sender arte.fr werden ausgeblendet</span><br>"; 

//<br><br>
      echo "<hr>Themen ausblenden: 
      <span style=\"float:right;padding:1pt\">
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#\" style=\"display:none\" class=\"link_every_same_color_underl\" onClick=\"if(confirm('Alle versteckten Einträge wieder anzeigen?')==true){createCookie('hide_thema','',-1);window.location.reload();}return false;\" id=\"options_hide_themen_liste_del__del_all\">Alle löschen</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=\"#\" class=\"link_every_same_color_underl\" onClick=\"showAlleFromHideThema();return false;\">Liste aktualisieren &#x21B4;</a><span style=\"clear:both\"></span>
      </span>
      
            <div id=\"show_hideElementsList\" style=\"\"> </div>
            <script language=\"javascript\"  type=\"text/javascript\">
            if(getCookie('hide_thema')!='')document.getElementById('show_hideElementsList').innerHTML = '&nbsp; &nbsp; ';
            else{ /*document.getElementById('show_hideElementsList').innerHTML = '&nbsp; &nbsp; -keine- ';*/ }
            //document.getElementById('show_hideElementsList').innerHTML += '<a class=\"link_every_same_color_underl\" href=\"#\" onClick=\"showAlleFromHideThema();return false;\">neu laden<a/>'; 
            </script>
      ";  
      
   

echo "
<span style=\"float:right\"><a href=\"#\" onclick=\"toggleShowOptions('close');return false;\" title=\"close\">x</a></span>
</div>"; //ende von id=options

//options ende










/**
 * Liste zur Sender- / Themen-wahl
 */
require_once 'inc/inc.senderliste_themenliste.php';

$options_showSenderAndThemenliste = array('hideArte_fr'=>$hideArte_fr,'hideShorterThen'=>$hideShorterThen); //, 'hideHoerfassung'=>$hideHoerfassung
$senderListOutArray = getSenderListe($options_showSenderAndThemenliste);
$dp = 'display:none'; $anker = parse_url($_SERVER["REQUEST_URI"],PHP_URL_FRAGMENT);
if( $anker=='#sender_select'  || strstr($anker,'#thema_sel')!==false) $dp = 'display:block';
      
echo "
<div id=\"div-sender-select\">
      <div id=\"list_auswahl_links_sender\" style=\"$dp\">
            <a name=\"sender_select\"></a>&nbsp;<br>&nbsp;<br>
            <span style=\"float:right\">
                <a href='#sender_select' onclick=\"document.getElementById('list_auswahl_links_sender').style.display='none'\">X</a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </span><br>";
            
            $i=0;
            foreach($senderListOutArray as $senderTitel => $senderUrl){
                $i++;
                echo "<a href=\"$senderUrl\" name=\"sender_sel$i\" onClick=\"window.location='#sender_sel$i';loadNewSite()\" style=\"display:block;width:100%;margin-left:-3pt;\" class=\"link_every_same_color\">$senderTitel</a>\n";     
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
    echo "<p style=\"text-align:center;width:50%;display:block;float:left\">";
    if( isset($h_prev) )echo "<a id=\"prev_page\" style=\"width:100%;min-width:100%;display:inline-block;\" onClick=\"createCookieInSeconds('prev_page',Date.now(),5)\" href=\"$h_prev\" ><</a>";
    echo "</p>";
    echo "<p style=\"text-align:center;width:50%;display:block;float:left\">";
    if( isset($h_next) )echo "<a id=\"next_page\" style=\"width:100%;min-width:100%;display:inline-block;\" onClick=\"createCookieInSeconds('next_page',Date.now(),5)\" href=\"$h_next\">></a>";
    echo "</p>";
  }
  echo "
  <div id=\"div-thema-select\">
      <!--<a name=\"thema_select\"></a>-->
      <a name=\"anker1_thema_sel_0\"></a><br><br><br>
      <div id=\"list_auswahl_links_thema\" style=\"$dp;position:auto;z-index:102;\">
        <!--<span style=\"float:right\"> <a href='#thema_select' onclick=\"document.getElementById('list_auswahl_links_thema').style.display='none'\">x</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>--><br>\n
        ";
        if($hideShorterThen>0)echo "<p align=\"right\" style=\"padding-right:6pt;\">Filme kürzer als ".$hideShorterThen." Min. werden ausgeblendet (sihe Einstellungen).</p>";
        //else echo "<br><br>";
        $ll = 0;
        
        /*

      <a name="anker1_thema_sel_1507" class="anker_thema"></a>
      <span class="link_to_thema_span t_sel">
          <a href="http://localhost/Mediathek/liste.php?sender=alle&amp;thema=Landesschau%20Baden-W%C3%BCrttemberg" id="mainlink_thema_sel_1507" onclick="if( location.hash.search('#anker1_thema_sel_')!==-1)window.history.back();window.location='#anker1_thema_sel_1502';loadNewSite()" class="t_sel_a">Landesschau Baden-Württemberg (3509) &nbsp; 8Min∅ ard, swr<span class="t_sel_date">Fr, 15.09.2017 17:45</span></a>
        	<a href="#" style="margin-left:15pt" class="link_every_same_color_underl link_every_same_color" onclick="appendHideThemaSelf(this);return false;">Ausblenden</a>
          <a href="#" class="t_sel_add_schnellauswahl link_every_same_color_underl link_every_same_color" onclick="appendFavSelf(this);return false;">zur Schnellauswahl hinzufügen</a>
          <span style="clear:both"></span>
     </span>
     */
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
        foreach($themen as $b => $themen){
              $showBuchstabenlink = true;
              foreach($themen as $url => $more){
                $ll++;
                if( isset($ll_von) && isset($ll_bis) && ($ll<$ll_von || $ll>$ll_bis) )continue;
                //$aktiv;
                $anker_ll = $ll;
	        if($anker_ll<=6)$anker_ll = 0;
	        else if($anker_ll>6) $anker_ll -=5; //damit der aktuelle Eintrag nicht verdeckt ist von der Oberen Leiste
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
//wird inzwischen per Javascript hinzugefügt:
//        	<a href=\"#\" style=\"margin-left:15pt\" class=\"link_every_same_color_underl link_every_same_color\" onClick=\"appendHideThemaSelf(this);return false;\">Ausblenden</a>
//          <a href=\"#\" class=\"t_sel_add_schnellauswahl link_every_same_color_underl link_every_same_color\" onClick=\"appendFavSelf(this);return false;\">zur Schnellauswahl hinzufügen</a>
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
createAllElements();




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
<div id=\"notice_before_filmliste__hideShorterThen\" style=\"display:none\"></div>
";
echo "
    <a name=\"anker1_film_0\"></a>
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
            <td>
            <br>";
            //$dpVideoLink='style="display:none"';
            //if(count($outArray['videofiles_links'])>1) echo "<a href=\"#\" class=\"abstandrechts link_every_same_color show_videolinks\" $dpShowLink>VideoLinks</a><br />";            
           // echo "<span class=\"videofiles_links\" $dpVideoLink>\n";
	            
	    foreach ($outArray['videofiles_links'] as $videoTitle => $videoUrl ){
	        $class = strtolower( substr(strip_tags($videoTitle), 0,2) );
		echo "
		    <a class=\"videolink_$class\" style=\"left:20pt;padding-left:5pt;\" href=\"".$videoUrl."\" > $videoTitle</a><br />";
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
<h3>Impressum</h3>".$impressumText."</div>";







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
if($cacheActive==false) printf("<small><span style=\"color:#000000;float:right;right:35pt;\">page created in %.2f sec.</span></small><br>&nbsp;", $creationtime);
echo '';
echo "<div id=\"fixed_footer\" style=\"width:100%;min-width:100%;bottom:0px;display:block;position:fixed;margin:0pt;padding-left:8pt;margin-left:-8pt;padding-right:20pt;z-index:9;;\">
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
