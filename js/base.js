

document.addEventListener("keydown", function(e) {
     //Taste 1: || e.which == 49
     if(e.which == 405){    document.getElementById('div-sender-select').style.display='block'; formItemFocus( document.getElementById('senderliste_2') ); } //Gelb
     if(e.which == 404)    document.getElementById('div-sender-select').style.display='block'; //Grün
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
     if(document.activeElement.id=='options_hide_film_liste_add_form_text') return;
     else{
       var parentSpanSelBuchstaben = document.getElementById('thema_sel_buchstaben');
       if(e.which == 49){
             var buchstaben = new Array('#' , '(' , ')' , '[' , ']' , "'" ,0,1,2,3,4,5,6,7,8,9);
             toggleInLinkListe_andFocus(parentSpanSelBuchstaben, buchstaben);
       }  //Taste Zahl \"1\"
       if(e.which == 50){ //   || e.which==65 oder auch bustabe A auf normaler Tastatur (test)
             var buchstaben = new Array('A','B','C');
             toggleInLinkListe_andFocus(parentSpanSelBuchstaben, buchstaben);
       }  //Taste Zahl \"2\"
       if(e.which == 51){
             var buchstaben = new Array('D','E','F');
             toggleInLinkListe_andFocus(parentSpanSelBuchstaben, buchstaben);
       }  //Taste Zahl \"3\"
       if(e.which == 52){
             var buchstaben = new Array('G','H','I');
             toggleInLinkListe_andFocus(parentSpanSelBuchstaben, buchstaben);
       }  //Taste Zahl \"4\"
       if(e.which == 53){
             var buchstaben = new Array('J','K','L');
             toggleInLinkListe_andFocus(parentSpanSelBuchstaben, buchstaben);
       }  //Taste Zahl \"5\"
       if(e.which == 54){
             var buchstaben = new Array('M','N','O');
             toggleInLinkListe_andFocus(parentSpanSelBuchstaben, buchstaben);
       }  //Taste Zahl \"6\"
       if(e.which == 55){
             var buchstaben = new Array('P','Q','R','S');
             toggleInLinkListe_andFocus(parentSpanSelBuchstaben, buchstaben);
       }  //Taste Zahl \"7\"
       if(e.which == 56){
             var buchstaben = new Array('T','U','V');
             toggleInLinkListe_andFocus(parentSpanSelBuchstaben, buchstaben);
       }  //Taste Zahl \"8\"
       if(e.which == 57){ // || e.which==87 oder auch buchstabe W auf normaler Tastatur (test)
             var buchstaben = new Array('W','X','Y');
             toggleInLinkListe_andFocus(parentSpanSelBuchstaben, buchstaben);
       }  //Taste Zahl \"9\"
       if(e.which == 58){
             var buchstaben = new Array('0');
             toggleInLinkListe_andFocus(parentSpanSelBuchstaben, buchstaben);
       }  //Taste Zahl \"0\"
     }//ende if document.activeElement
}, false); //ende Event Listener

var toggleInLinkListe_andFocus__lastNo = 0;
var toggleInLinkListe_andFocus__lastB = '';
function toggleInLinkListe_andFocus(parentSpan, buchstabenVorlage){
   var link;
   var linkListBuchstaben = new Object();
   var buchstaben = new Array();
   var currentActiveBuchstabe = '';
   
   //console.log( "toggleInLinkListe_andFocus__lastNo " + toggleInLinkListe_andFocus__lastNo + " toggleInLinkListe_andFocus__lastB "+toggleInLinkListe_andFocus__lastB);
   
   //wurde diese Funktion schonmal/gerade erst aufgerufen?
   if( toggleInLinkListe_andFocus__lastNo>0 &&
       document.activeElement == document.getElementById('mainlink_thema_sel_'+toggleInLinkListe_andFocus__lastNo) ){
       //wenn noch beim zuletzte (via toggleInLinkListe_andFocus) aktivierten Element
       currentActiveBuchstabe = toggleInLinkListe_andFocus__lastB;
       //console.log( "Diese Funktion schonmal/gerade erst aufgerufen? " + currentActiveBuchstabe );
   }
   
   
   //welche Buchstaben gibt es tatsächlich
   for(i=0;i<buchstabenVorlage.length;i++){ 
      var b = buchstabenVorlage[i]; 
      link = parentSpan.querySelector('[href="#buchstabe_'+b+'"]'); // gibt es einen BuchstabenLink dafür
      if(link==undefined || link==null || link.href=='')continue;
      buchstaben.push(b);
      linkListBuchstaben[b] = link;
   }
   if(buchstaben.length==0)return; //nix zu tun
   
   var nextB = '';
   //finde den aktuell aktiven Buchstaben
   for(i=0;i<buchstaben.length;i++){ 
      var b = buchstaben[i]; 
      var link = linkListBuchstaben[b];
      if(link.href!='' && link.href.split('#').length>1) var link_href_hash = link.href.split('#')[1]; else continue;
      //console.log( location.hash.replace('#','') + "=?=" + link_href_hash );
      if(document.activeElement == link || location.hash.replace('#','')==link_href_hash || currentActiveBuchstabe==b){ //aktiver Buchstabe?
              found = true;
              if( buchstaben[i+1]!=undefined ) var nextB = buchstaben[i+1]; //nächster Buchstaben
              else var nextB = buchstaben[0]; //im Kreis zurück zum erster Buchstaben
              //console.log("derzeit aktiver Buchstabe "+b+" ");
              break;
      }
   }
   
   if(nextB=='') nextB = buchstaben[0]; //beginne beim ersten Buchstaben
   //console.log("nächster aktiver Buchstabe wird sein: "+nextB);
   link = linkListBuchstaben[ nextB ];
   //console.log("aktiver Buchstaben-Link: "+link);
   
  
   
   //setzt Focus auf Buchstaben-Link
   link.focus(); formItemFocus( link );
   
   //setzt Focus auf ersten Themen-Listen-Link (passend zum Buchstaben)
   var firstElementNo = link.getAttribute('data-starts-with-no');
   var a = document.getElementById('mainlink_thema_sel_'+firstElementNo);
   a.focus();    formItemFocus( a );
   //window.setTimeout(function(){ location.href = link.href; },50);
   //window.setTimeout(function(){ location.href = a.href; },50);
   
   toggleInLinkListe_andFocus__lastB = nextB;
   toggleInLinkListe_andFocus__lastNo = firstElementNo;
   //console.log( "toggleInLinkListe_andFocus__lastNo " + toggleInLinkListe_andFocus__lastNo + " toggleInLinkListe_andFocus__lastB "+toggleInLinkListe_andFocus__lastB);
   
}
 
 

function updateHash(hash){ //hash include starting #
  var hash_ohne_nummer = hash.substring(0,hash.length - hash.match(/[0-9]$/)[0].length);
  if( location.hash.search(hash_ohne_nummer)!==-1){
          if(history.replaceState) history.replaceState( history.state, document.title , hash);
          else window.history.back();
  }
  else{
          if(history.replaceState) history.replaceState( history.state, document.title , hash);
          else location.hash = hash;
  }
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

var createCookieInSeconds = function(name, value, seconds) {
    var expires;
    if (seconds) {
        var date = new Date();
        date.setTime(date.getTime() + (seconds * 1000));
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
    if(savedTabIndex==null)savedTabIndex='';
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
                          //alt if( location.hash.search('#anker1_film_')!==-1)window.history.back();
                          var nummer = this.id;
                          nummer = nummer.replace('mainlink_film_' ,'');
                          nummer = Number(nummer);
                          if(nummer<2)  var scrollup_i = 0;                          
                          else var scrollup_i = nummer; //-2
                          //alt window.location = '#anker1_film_'+scrollup_i;
                          updateHash('#anker1_film_'+scrollup_i);
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
function updateVideoMainLink_withQualityLink_andPossibleHideElements(){;
        var c = getCookie('quality');
        var raw = '';
        if(getCookie('hide_film_aktiv')!='')raw = getCookie('hide_film');
        if(raw=='') raw='[]';
        var hide_film = JSON.parse(raw); for (var j = 0; j < hide_film.length; j++) { hide_film[j] = hide_film[j].toLowerCase() } 
        if(hide_film.length==0 && getCookie('video_direktlink')=='' ){
          if( c=='' || c=='normal' ) return; //prüfe ob überhaupt gesetzt/bzw. abweichend
          if( c!='hd' && c!='low' )  return; //prüfe ob richtig gesetzt
        }
        var list = document.getElementsByClassName('list_video_mainlink');
          for (var i = 0; i < list.length; i++) {
                  var href = '';
                  var e;
                  var parent = filmliste_line_getParentNode(list[i].parentNode);
                  if(parent==undefined) return; //Fehler
                  
                  //ausblenden anhand des Namens (Blackliste)
                  var title = list[i].innerText;
                  var exit = false; 
                  for (var j = 0; j < hide_film.length; j++) {
                    if( title.substring(0, hide_film[j].length).toLowerCase() == hide_film[j]){
                        parent.style.display='none';
                        exit = true;
                        break;                      
                    }
                  }
                  if(exit) continue;
                  
                  
                  if(parent.style.display == 'none' ) parent.style.display = ''; //zurücksetzten
                  
                  
                  if( getCookie('video_direktlink') ==1 ){
                    var all_videolinks = parent.getElementsByClassName('videolink');
                    var h;
                    for (var m = 0; m < all_videolinks.length; m++) {
                        h = all_videolinks[m].href.split('video.php#');
                        if(h.length>1)all_videolinks[m].href = h[1];
                    }
                    h = list[i].href.split('video.php#');
                    if(h.length>1)list[i].href = h[1];
                  }
                  
                  if(c=='hd')  e = parent.getElementsByClassName('videolink_hd');
                  if(c=='low') e = parent.getElementsByClassName('videolink_kl');
                  if(e!=undefined && e.length>0){ href = e[0].href; list[i].href = href; } //setzt Link
                  
                  e = parent.getElementsByClassName('notice_fileformat_not_playable');
                  if(e!=undefined && e.length>0){ e[0].innerHTML = ''; e[0].innerText = '';} //Der hinweis galt nur für "Normal-Video"
                  
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


function updateFilmliste_HideElements(hideHoerfassungFilme, hideAudioDeskriptionFilme, hideTrailerFilme, minLength){
        var count_minLength = 0;
        var list = document.getElementsByClassName('list_video_mainlink');
        for (var i = 0; i < list.length; i++) {
                  var h = list[i].innerHTML;
                  var t = list[i].innerText;
                  if(hideHoerfassungFilme==1 && (t.search(/hörfassung/i)!=-1 || html.search(/hörfassung/i)!=-1) ){
                          var parent = filmliste_line_getParentNode( list[i].parentNode );
                          if(parent==undefined) return; else parent.style.display = 'none';
                  }
                  if( hideAudioDeskriptionFilme==1 && (t.search(/Audiodeskription/i)!=-1 || h.search(/Audiodeskription/i)!=-1) ){
                          var parent = filmliste_line_getParentNode( list[i].parentNode );
                          if(parent==undefined) return; else parent.style.display = 'none';   
                  }
                  if( hideAudioDeskriptionFilme==1 && (t.search(/AD | /i)!=-1 || h.search(/AD | /i)!=-1)){
                          var parent = filmliste_line_getParentNode( list[i].parentNode );
                          if(parent==undefined) return; else parent.style.display = 'none';   
                  }
                  if(hideTrailerFilme==1 && 
                       (t.search(/Trailer/i)!=-1 || h.search(/Trailer/i)!=-1 ||
                        t.substring(0,9) == 'Vorschau ' || h.substring(0,9) == 'Vorschau ' ||
                        t.substring(0,9) == 'Vorschau: ' || h.substring(0,9) == 'Vorschau: ')
                     ){
                          var parent = filmliste_line_getParentNode( list[i].parentNode );
                          if(parent==undefined) return; else parent.style.display = 'none';   
                  }

                  if(minLength>0){
                      var lObj = list[i].getElementsByClassName('film_length');
                      if(lObj.length>0) var l = lObj[0]; else continue;
                      if(parseInt(l.innerText)!=NaN && parseInt(l.innerText) < minLength){
                          var parent = filmliste_line_getParentNode( list[i].parentNode );
                          if(parent==undefined) return; else{ parent.style.display = 'none'; count_minLength++;}  
                      }
                  }
        }//ende FOR-Schleife
        if(count_minLength>0){
           document.getElementById('notice_before_filmliste__minLength').style.display = 'block';
           document.getElementById('notice_before_filmliste__minLength').innerHTML = count_minLength+' Filme kürzer als '+getCookie('minLength')+'Minuten werden ausgeblendet. <a href="#" onclick="createCookie(\'minLength\',\'\',0);window.location.reload();return false;">ausschalten</a>';
        } 
}


//Einstellungs-Link als Hash
function optionsSafeLastUsesLinkAsHash(self){
        var p = document.getElementById('options');
        var list = p.getElementsByTagName('A');
        var found = i;
        for(var i =0;i<list.length;i++){
          if(list[i]==self){ found=i; break; }
        }
        //console.log( 'found '+ found);
        if(found>0){
          var hash = 'settings_'+found;
          if(history.replaceState) history.replaceState( history.state, document.title , window.location.href.replace( window.location.hash, '').replace('#','')+'#'+hash);
          //else window.history.back();
          window.location.hash = hash;
        }
}

//Einstellungen zeigen/versteckten 
function toggleShowOptions(state){
        
        if(state==''){
                if(document.getElementById('options').style.display=='none') state = 'show';       
                else state = 'close';
        }
        
        if(state== 'show'){
          document.getElementById('options').style.display = 'block';
          showAlleFromHide('thema'); showAlleFromHide('film');
          formItemFocus(document.getElementById('options').getElementsByTagName('A')[0]);
          if( location.hash.search('#settings_')!==-1 ){
              var number = location.hash.replace('#settings_','');
              var p = document.getElementById('options');
              var list = p.getElementsByTagName('A');

              if( list[ number] != undefined ){
                list[ number].focus();
                formItemFocus( list[ number] );
              }
          }
        }
        else if(state=='close'){
                document.getElementById('options').style.display = 'none';
                history.replaceState( history.state, document.title , '#');
                //if( location.hash.search('#settings')!==-1)window.history.back();//location.hash = '';
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
  
  
