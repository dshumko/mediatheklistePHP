

document.addEventListener("keydown", function(e) {
          //Taste 1: || e.which == 49
     if(e.which == 405)    document.getElementById('list_auswahl_links_sender').style.display='block'; //Gelb
     if(e.which == 404)    document.getElementById('list_auswahl_links_thema').style.display='block'; //Grün
     if(e.which == 406){
           var span = document.getElementById('thema_sel_buchstaben');
           var link = span.getElementsByTagName('a')[0];
           if(document.activeElement!=null && document.activeElement.getAttribute('href').search('#buchstabe_')!=-1){
                   var possibleNext = document.activeElement.nextSibling.nextSibling;
                   if(  possibleNext!=null && possibleNext.getAttribute('href')!=null && possibleNext.getAttribute('href').search('#buchstabe_')!=-1
                        ) link = possibleNext; //springe zum nächsten
                   else link = document.getElementById('thema_sel_buchstaben').getElementsByTagName('a')[0] //springe zurück zum ersten
           }
           link.focus();
           formItemFocus( link );
     } //Blau
     
     if(e.which == 49){
           var parentSpan = document.getElementById('thema_sel_buchstaben');
           var buchstaben = new Array('#' , '(' , ')' , '[' , ']' , "'" ,0,1,2,3,4,5,6,7,8,9);
           toggleInLinkListe_andFocus(parentSpan, buchstaben);
     }  //Taste Zahl \"1\"
     if(e.which == 50){
           var parentSpan = document.getElementById('thema_sel_buchstaben');
           var buchstaben = new Array('A','B','C');
           toggleInLinkListe_andFocus(parentSpan, buchstaben);
     }  //Taste Zahl \"2\"
     if(e.which == 51){
           var parentSpan = document.getElementById('thema_sel_buchstaben');
           var buchstaben = new Array('D','E','F');
           toggleInLinkListe_andFocus(parentSpan, buchstaben);
     }  //Taste Zahl \"3\"
     if(e.which == 52){
           var parentSpan = document.getElementById('thema_sel_buchstaben');
           var buchstaben = new Array('G','H','I');
           toggleInLinkListe_andFocus(parentSpan, buchstaben);
     }  //Taste Zahl \"4\"
     if(e.which == 53){
           var parentSpan = document.getElementById('thema_sel_buchstaben');
           var buchstaben = new Array('J','K','L');
           toggleInLinkListe_andFocus(parentSpan, buchstaben);
     }  //Taste Zahl \"5\"
     if(e.which == 54){
           var parentSpan = document.getElementById('thema_sel_buchstaben');
           var buchstaben = new Array('M','N','O');
           toggleInLinkListe_andFocus(parentSpan, buchstaben);
     }  //Taste Zahl \"6\"
     if(e.which == 55){
           var parentSpan = document.getElementById('thema_sel_buchstaben');
           var buchstaben = new Array('P','Q','R','S');
           toggleInLinkListe_andFocus(parentSpan, buchstaben);
     }  //Taste Zahl \"7\"
     if(e.which == 56){
           var parentSpan = document.getElementById('thema_sel_buchstaben');
           var buchstaben = new Array('T','U','V');
           toggleInLinkListe_andFocus(parentSpan, buchstaben);
     }  //Taste Zahl \"8\"
     if(e.which == 57){
           var parentSpan = document.getElementById('thema_sel_buchstaben');
           var buchstaben = new Array('W','X','Y','Z');
           toggleInLinkListe_andFocus(parentSpan, buchstaben);
     }  //Taste Zahl \"9\"
     if(e.which == 58){
           var parentSpan = document.getElementById('thema_sel_buchstaben');
           var buchstaben = new Array('0');
           toggleInLinkListe_andFocus(parentSpan, buchstaben);
     }  //Taste Zahl \"0\"
     
}, false); //ende Event Listener
  
function toggleInLinkListe_andFocus(parentSpan, buchstaben){
   var found_takeNextElement = false;
   var ready = false;
   var link;
   var firstElement = null;
   for(i=0;i<buchstaben.length;i++){
        link = parentSpan.querySelectorAll('[href="#buchstabe_'+buchstaben[i]+'"]')[0];
        if(link!=undefined && firstElement==null) firstElement = link; //falls man zurückspringen muss auf's erste
        var link_href_hash = '';
        if(link.href!='' && link.href.split('#').length>1) link_href_hash = link.href.split('#')[1];
         if(found_takeNextElement && link!=undefined && link!=null){ready=true; break;} //gefunden
        if(document.activeElement==link || location.hash.replace('#','')==link_href_hash){ //'link' ist aktuell aktives Element
                if( i == buchstaben.length-1 )link = firstElement; //'link' ist letztes Element der Reihe; also das erste nehmen;
                else found_takeNextElement = true; 
        }
   }
   if( !ready && firstElement!=undefined )link = firstElement;
   link.focus();
   formItemFocus( link );
   window.setTimeout(function(){ location.href = link; },50);
}
  
  
  

//source: https://stackoverflow.com/questions/4825683/how-do-i-create-and-read-a-value-from-cookie
var createCookie = function(name, value, days) {
    var expires;
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    }
    else {
        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {
                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return "";
}


//https://stackoverflow.com/questions/4014935/why-doesnt-this-javascript-focus-work
function formItemFocus( item ){
    if( !item ){
        //console.warn('no focusable item: ', item)
        return;
    }

    var savedTabIndex = item.getAttribute('tabindex');
    item.setAttribute('tabindex', '-1');
    item.focus();
    item.setAttribute('tabindex', savedTabIndex);
}


/**
 * Wenn kein HbbTV Browser: Verstecke Farb-Buttons
 */
 
function possibleHideHbbTVButtons(){
  //if(!isHbbTV()) hideHbbTVButtons();
}
function hideHbbTVButtons(){
        var arr = document.getElementsByClassName('hbbtv_button');
        for (var i=0;i<arr.length;i++) {
            arr[i].style.display = 'none';    
        }
}
function isHbbTV(){
        if(navigator.userAgent.search('HbbTV/')!=-1) return true;
        else return false;        
}
// funktioniert nicht
/*
var mousemoveFunctionOnce = function (event) {
    hideHbbTVButtons();
    //window.removeEventListener('mousemove',mousemoveFunctionOnce, false );
      return false;  
};*/







function filmliste_eintrage_event_onclick(){
	       
          addEventListenerList( document.getElementsByClassName('list_video_mainlink'), "click", function(e) {
                          if( location.hash.search('#anker1_film_')!==-1)window.history.back();
                          var nummer = this.id;
                          nummer = nummer.replace('mainlink_film_' ,'');
                          nummer = Number(nummer);
                          if(nummer<2)  var scrollup_i = 0;                          
                          else var scrollup_i = nummer-2;
                          window.location = '#anker1_film_'+scrollup_i;
                          loadNewSite();
                          //test e.preventDefault();
                          //test return false;
                  });
          
}

function filmliste_eintrage_event_videolinks_onclick(){
          addEventListenerList(  document.getElementsByClassName('show_videolinks'), "click", function(e) {
                          var a = this.parentNode.getElementsByClassName('videofiles_links')[0];
                          if(a.style.display=='none')a.style.display='block';
                          else a.style.display='none';
                          e.preventDefault();
                          return false;
                  }); 
          addEventListenerList(  document.getElementsByClassName('show_videolinks'), "focus", function(e) {
                          var a = this.parentNode.getElementsByClassName('videofiles_links')[0];
                          if(a.style.display=='none')a.style.display='block';
                          //else a.style.display='none';
                          e.preventDefault();
                          return false;
                  }); 
          addEventListenerList(  document.getElementsByClassName('show_videolinks'), "focusin", function(e) {
                          var a = this.parentNode.getElementsByClassName('videofiles_links')[0];
                          if(a.style.display=='none')a.style.display='block';
                          //else a.style.display='none';
                          e.preventDefault();
                          return false;
                  }); 
          addEventListenerList(  document.getElementsByClassName('show_videolinks'), "mouseover", function(e) {
                          var a = this.parentNode.getElementsByClassName('videofiles_links')[0];
                          if(a.style.display=='none')a.style.display='block';
                          //else a.style.display='none';
                          e.preventDefault();
                          return false;
                  }); 
}

function addEventListenerList(list, event, fn) {
    for (var i = 0, len = list.length; i < len; i++) {
        list[i].addEventListener(event, fn, false);
    }
}


//Ersetze den Haupt-Videolink durch den passenen Videolink (bspw. durch Videolink auf "geringe" Videoqualität)
function updateVideoMainLink_withQualityLink(){;
        var c = getCookie('quality');
        if( c=='' || c=='normal' ) return; //prüfe ob überhaupt gesetzt/bzw. abweichend
        if( c!='hd' && c!='low' )  return; //prüfe ob richtig gesetzt
        var list = document.getElementsByClassName('list_video_mainlink');
        	for (var i = 0; i < list.length; i++) {
        	        var href = '';
        	        var e;
        	        var parent = filmliste_line_getParentNode(list[i].parentNode);
        	        if(parent==undefined) return; //Fehler
        	        

        	        if(c=='hd')  e = parent.getElementsByClassName('videolink_hd');
        	        if(c=='low') e = parent.getElementsByClassName('videolink_kl');
        	        if(e.length>0){ href = e[0].href; list[i].href = href; } //setzt Link
        	        
        	        e = parent.getElementsByClassName('notice_fileformat_not_playable');
        	        if(e.length>0){ e[0].innerHTML = ''; e[0].innerText = '';} //Der hinweis galt nur für "Normal-Video"
        	        
        	        //Prüfe Videoformat
        	        if(href!=''){
        	                var type =  getFileType(href);
        	                if( !isHbbTV() && type!='mp4' ){ //bspw. bei flv m3u m3u8
        	                        preText = '';
        	                        e = parent.getElementsByClassName('videolink_kl'); var hrefLo = ''; if(e.length>0) hrefLo = e[0].href;
        	                        if(type != getFileType(hrefLo) && hrefLo!='' )preText = 'Bitte anderen VideoLinks wählen.';
        	                        e = parent.getElementsByClassName('videolink_no'); var hrefNo = ''; if(e.length>0) hrefNo = e[0].href;
        	                        if(type != getFileType(hrefNo) && hrefNo!='' )preText = 'Bitte anderen VideoLinks wählen.';
        	                        e = parent.getElementsByClassName('videolink_hd'); var hrefHd = ''; if(e.length>0) hrefHd = e[0].href;
        	                        if(type != getFileType(hrefHd) && hrefHd!='' )preText = 'Bitte anderen VideoLinks wählen.';
        	                        parent.getElementsByClassName('line_headline')[0].innerHTML = type + ' ' + preText + parent.getElementsByClassName('line_headline')[0].innerHTML;
        	                }
        	                
        	        }
        	        
        	}
        
}
function getFileType(href){
        var s = href.split('.');
        var type = s[s.length-1];
        return type;	                
}

function filmliste_line_getParentNode(node){ //suche parent-Element für diesen Film
        var parent = node;
        //suche parent-Element für diesen Film
        while( parent.className==undefined || parent.className.search(/videolink_row/)==-1 ) {
                if(parent.tagName=='BODY') return undefined; //Elementen-Ordnung stimmt nicht
                parent = parent.parentNode;
        }
        return parent;
}


function updateFilmliste_HideElements(hideHoerfassungFilme, hideAudioDeskriptionFilme, hideTrailerFilme, hideShorterThen){
        var count_hideShorterThen = 0;
        var list = document.getElementsByClassName('list_video_mainlink');
        for (var i = 0; i < list.length; i++) {
        	        if(hideHoerfassungFilme==1 && list[i].innerText.search(/hörfassung/i)!=-1 || list[i].innerHTML.search(/hörfassung/i)!=-1){
        	                var parent = filmliste_line_getParentNode( list[i].parentNode );
        	                if(parent==undefined) return; else parent.style.display = 'none';   
        	        }
        	        if( hideAudioDeskriptionFilme==1 && list[i].innerText.search(/Audiodeskription/i)!=-1 || list[i].innerHTML.search(/Audiodeskription/i)!=-1){
        	                var parent = filmliste_line_getParentNode( list[i].parentNode );
        	                if(parent==undefined) return; else parent.style.display = 'none';   
        	        }
        	        if( hideAudioDeskriptionFilme==1 && (list[i].innerText.search(/AD | /i)!=-1 || list[i].innerHTML.search(/AD | /i)!=-1)){
        	                var parent = filmliste_line_getParentNode( list[i].parentNode );
        	                if(parent==undefined) return; else parent.style.display = 'none';   
        	        }
        	        if(hideTrailerFilme==1 && list[i].innerText.search(/Trailer/i)!=-1 || list[i].innerHTML.search(/Trailer/i)!=-1){
        	                var parent = filmliste_line_getParentNode( list[i].parentNode );
        	                if(parent==undefined) return; else parent.style.display = 'none';   
        	        }
	                if(hideShorterThen>0){
	                    var lObj = list[i].getElementsByClassName('film_length');
	                    if(lObj.length>0) var l = lObj[0]; else continue;
	                    if(parseInt(l.innerText)!=NaN && parseInt(l.innerText) < hideShorterThen){
	                        var parent = filmliste_line_getParentNode( list[i].parentNode );
	                        if(parent==undefined) return; else{ parent.style.display = 'none'; count_hideShorterThen++;}  
	                    }
	                }
        }//ende FOR-Schleife
        if(count_hideShorterThen>0){
           document.getElementById('notice_before_filmliste__hideShorterThen').style.display = 'block';
           document.getElementById('notice_before_filmliste__hideShorterThen').innerHTML = count_hideShorterThen+' Filme kürzer als '+getCookie('hideShorterThen')+'Minuten werden ausgeblendet. <a href="#" onclick="createCookie(\'hideShorterThen\',\'\',0);window.location.reload();return false;">ausschalten</a>';
        } 
}
        
function toggleShowOptions(state){
        
        if(state==''){
                if(document.getElementById('options').style.display=='none') state = 'show';       
                else state = 'close';
        }
        
        if(state== 'show'){document.getElementById('options').style.display = 'block';showAlleFromHideThema()}
        else if(state=='close'){
                document.getElementById('options').style.display = 'none';
                if( location.hash.search('#settings')!==-1)window.history.back();//location.hash = '';
        }
        document.getElementById('vorschaltseite_thumb').src = document.getElementById('vorschaltseite_thumb').getAttribute('data-src');
}
/*

 document.getElementById('link_thema_select').style.display='block';
      document.getElementById('link_thema_select').text = 'aab';
      
  document.addEventListener(\"keydown\", function(e) {

      document.getElementById('link_thema_select').text = 'a'+e.which+'a';
  }, false);
  
  OK:13
  Vol: KEINE
  Play:415
  Pause:19
  Stop:413
  Rwd:412
  Fwd:417
  ROT: 403
  GRÜN:404
  GELB:405
  BLAU:406
  1:49
  ...
  9:57
  0:48
  I:457 (Info-Button unten)
  Links37
  Hoch38
  Recht:39
  Unten:40
  P+: 427
  P-: 428

*/
/*
  document.addEventListener("keydown", function(e) {
    if (handleKeyCode(e.keyCode)) {
      e.preventDefault();
      alert();
    }
  }, false);

*/
/* auf HbbTV-Testsuite-master zum testen



window.onload = function() {
  menuInit();
  registerMenuListener(function(liid) {
    if (liid=='exit') {
      document.location.href = '../index.php';
    } else {
      runStep(liid);
    }
  }, true);
  registerKeyEventListener();
  document.addEventListener("keypress", function(e) {
    handleKeyPress(e.keyCode);
  }, false);
  document.addEventListener("keyup", function(e) {
    handleKeyUp(e.keyCode);
  }, false);
  initApp();
};
function handleKeyPress(kc) {
  if (kc==VK_LEFT && state>=0) {
    logtxt += '<br />Keypress was sent.';
    if (state==1) {
      state = 2;
    }
    setInstr(logtxt);
  } else if (kc!==VK_ENTER && state>=0) {
    logtxt += '<br />Keypress was sent (but wrong keycode '+kc+').';
    setInstr(logtxt);
  }
}
function handleKeyUp(kc) {
  if (kc==VK_LEFT && state>=0) {
    logtxt += '<br />Keyup was sent.';
    if (state==2) {
      state = 3;
    }
    if (state==3) {
      showStatus(true, 'All key events were received correctly.');
    } else if (state==1) {
      showStatus(false, 'Key events were not sent correctly: keypress event was missing (see OIPF DAE Annex B CE-HTML Profiling).');
    } else {
      showStatus(false, 'Key events were not sent correctly: we need 1. keydown, 2. keypress, and 3. keyup.');
    }
    setInstr(logtxt);
    state = -1;
  } else if (kc!==VK_ENTER && state>=0) {
    showStatus(false, 'Key up was received for incorrect keycode: '+kc);
    setInstr(logtxt);
    state = -1;
  }
}
function registerKeyEventListener() {
  document.addEventListener("keydown", function(e) {
    if (handleKeyCode(e.keyCode)) {
      e.preventDefault();
    }
  }, false);
}
if (typeof(KeyEvent)!='undefined') {
    var VK_FAST_FWD = KeyEvent.VK_FAST_FWD;
    var VK_REWIND = KeyEvent.VK_REWIND;
    
   if (typeof(KeyEvent.VK_PLAY)!='undefined') {
    var VK_PLAY = KeyEvent.VK_PLAY;
    var VK_PAUSE = KeyEvent.VK_PAUSE;
    var VK_STOP = KeyEvent.VK_STOP;
  }
  
  
    if (typeof(KeyEvent.VK_RED)!='undefined') {
    var VK_RED = KeyEvent.VK_RED;
    var VK_GREEN = KeyEvent.VK_GREEN;
    var VK_YELLOW = KeyEvent.VK_YELLOW;
    var VK_BLUE = KeyEvent.VK_BLUE;
    }
}




v.playbackRate = 0.5 ....


//seek() //ueber Position.... (currentTime?)

  */
  
  
