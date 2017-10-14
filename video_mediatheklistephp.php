<?php

    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
      $if_modified_since = preg_replace('/;.*$/', '',   $_SERVER['HTTP_IF_MODIFIED_SINCE']);
    } else {
      $if_modified_since = '';
    }

    $mtime = filemtime($_SERVER['SCRIPT_FILENAME']);
    
    $hasAndShowFavs = false;
    $gmdate_mod = date('D, d M Y H:i:s', $mtime) . ' GMT';
    if ($if_modified_since == $gmdate_mod) {
      header("HTTP/1.0 304 Not Modified");
      exit;
    }

    header("Last-Modified: $gmdate_mod");
    header("Content-Type: text/html; charset=utf-8");

    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (60*60*24)) . ' GMT'); //maximal Gültig für ein Tag

?>

<style>

video#video { 
        /*position: fixed; right: 0; bottom: 0;*/
        /*min-width: 100%; min-height: 100%;*/
        min-height: 100%;
        height: 100%;
        width: auto; /*height: auto;*/ z-index: -100;
        background:  no-repeat;
        background-size: contain;
        z-index:100;
}
body{
        background:black;
        padding: 0px;
        margin: 0px;
}

</style>
<div id="status_show" style="z-index:101; position: fixed;right: 10pt;background:#ffffff;font-size:14pt;"></div>
<div align="center">
    <video id="video" height="100" width="100" preload="auto" controls autoplay="autoplay" onClick="togglePlay()" src=""></video>
</div>
<script language="javascript">
        document.getElementById('video').src = location.hash.replace('#',''); document.getElementById('video').play()
</script>
<script type='text/javascript'>

    

  //Springe zurück, wenn Video zuende
  document.getElementById('video').addEventListener('ended',myHandler,false);
    function myHandler(e) {
	window.history.back();
  }
  
  function togglePlay(){
        var v = document.getElementById('video');
        var t = document.getElementById('status_show');
        
        if(v.paused || v.playbackRate==0){
                v.play();
                t.innerText = 'Play';
        }else{
                v.pause();
                t.innerText = 'Pause';
        }
        
        v.playbackRate=1; //Geschwingkeit zurücksetzen

        //Anzeige Playrate (=Abspiel-Geschwindkeit)
       if(timeout!=undefined) window.clearTimeout(timeout);
       if(  ! v.paused )timeout = window.setTimeout('document.getElementById(\'status_show\').innerText=\'\'',1000);
    }
    
  //Tastatur/Fernbedienung-Eingaben
  var timeout;
  document.addEventListener("keydown", function(e) {
     var v = document.getElementById('video');
     var t = document.getElementById('status_show');
     var src = document.getElementById('video').src;
     var split_ = src.split('.');
     var type =  split_[ split_.length-1 ];
     if(e.which == 415){     v.play();  v.playbackRate = 1; t.innerText="Play";}
     if(e.which == 19){      v.pause(); v.playbackRate = 1; t.innerText="Pause";}
     //if(e.which == 32){      //doingRewind = false; 
     //                        v.playbackRate = 1; togglePlay(); return; } //Leertaste
     //if(e.which == 13){      //doingRewind = false; 
     //                        v.playbackRate = 1; togglePlay(); return; } //"OK"
     if(e.which == 412 || e.which == 82){ //Rewind //R
                            if(type=='m3u8' || type=='m3u'){ //hier konnte ich in meinen OperaTV HbbTV Browser nicht vorspulen
                                    t.innerText="Bitte mit den Pfeiltasten navigieren (←-30Sek. +30Sek.→   ↓-5Min.  ↑+5Min.)";
                            }else{
                                    v.play();
                                    if(v.playbackRate==1.00)v.playbackRate=-1.0;
                                    else  v.playbackRate=v.playbackRate-1.0;
                                    //if(v.playbackRate<-8.00) //Wenn der Wert größer als erlaubt ist, springt mein TV auf Play zurück
                                    
                                    if(v.playbackRate==1.00) t.innerText="Play";
                           }
                                        } 
     if(e.which == 417 || e.which == 70){ //F // Foward
                            if(type=='m3u8' || type=='m3u'){ //hier konnte ich in meinen OperaTV HbbTV Browser nicht rueckspulen
                                    t.innerText="Bitte mit den Pfeiltasten navigieren (←-30Sek. +30Sek.→   ↓-5Min.  ↑+5Min.)";
                            }else{
                                    v.play();
                                    if(v.playbackRate==-1.0)v.playbackRate=1.0;
                                    else v.playbackRate+=1.0;
                                    //if(v.playbackRate<8.00) 
                                    
                                    if(v.playbackRate==1.00) t.innerText="Play";
                                    
                            }  
                                       } 
     if(e.which == 39 ) stepForward(v,30)//Rechts Pfeil
     if(e.which == 37 ) stepBackward(v,30)//Links Pfeil
     if(e.which == 38 ) stepForward(v,60*5)//Oben Pfeil
     if(e.which == 40 ) stepBackward(v,60*5)//unten Pfeil
     
     if(e.which == 413 ) window.history.back();
     //console.log(e.which);
     //console.log('Playback'+v.playbackRate);
     
     //Anzeige Playrate (=Abspiel-Geschwindkeit)
     if(timeout!=undefined) window.clearTimeout(timeout);
     if(v.playbackRate!=1){ //läuft schneller oder langsamer 
        t.innerText=v.playbackRate+"x Geschwindigkeit";
        //if(v.playbackRate===0)t.innerText = "Auch Pfeiltasten verwendenbar (link/unten)";
        t.style.display = 'block';
     }
     if(  ! v.paused && v.playbackRate>0 && t.innerText!='Bitte Pfeiltasten verwenden (link/unten)')timeout = window.setTimeout('document.getElementById(\'status_show\').innerText=\'\'',5000);
     //doingRewind = false; 
     
  }, false); //ende Event Listener
 
  function stepForward(v,t) {
      var now=v.currentTime;
      v.currentTime=now+t;
  }

  function stepBackward(v,t) {
      var now=v.currentTime;
      v.currentTime=now-t;
  }

  /*
  
  OK:13 (Entspricht ENTER auf normaler Tastatur)
  Vol: KEINE
  Play:415
  Pause:19  (Entspricht PAUSE auf normaler Tastatur)
  Stop:413
  Rwd:412
  Fwd:417
  ROT: 403
  GRÜN:404
  GELB:405
  BLAU:406
  1:49 (alle zahlen, wie auf Tastatur)
  ...
  9:57
  0:48
  I:457
  Links37 (alle Pfeile, wie auf Tastatur)
  Hoch38
  Recht:39
  Unten:40 
  P+: 427
  P-: 428
  
  
  Tastatur f = 70
  Tastatur r = 18
  */
</script>

