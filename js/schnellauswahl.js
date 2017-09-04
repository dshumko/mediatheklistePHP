// Schnellauswahl und Hide Thema

function appendFavSelf(self){
        var link_element = self.parentNode.parentNode.getElementsByClassName('t_sel_a')[0];
  newValueToAdd = link_element.getAttribute('href');
  newValueToAdd = newValueToAdd.replace(/&quality=[^&]*/,'').replace(/&hide_shorter_then=[^&]*/,'');
  appendFav(newValueToAdd);
  self.innerText='★...in der Liste';
  return true;
}

function appendHideThemaSelf(self){
        var link_element = self.parentNode.parentNode.getElementsByClassName('t_sel_a')[0];
  newValueToAdd = link_element.getAttribute('href');
  newValueToAdd = newValueToAdd.replace(/&quality=[^&]*/,'').replace(/&hide_shorter_then=[^&]*/,'');
  appendHideThema(newValueToAdd);
  self.setAttribute('onclick','removeHideThema(\''+newValueToAdd+'\');this.innerText=\'\'; this.parentNode.parentNode.style.opacity=1;return false;');
  self.innerHTML = 'ausgeblendet';
  self.parentNode.parentNode.style.opacity = 0.4;
  return true;
}


function appendFavDataHrefSelf(self){
  newValueToAdd = self.getAttribute('data-href');
  appendFav(newValueToAdd);
  self.innerText='★...in der Liste';
  return true;
}

function appendFav(href){
        href = href.replace(/&quality=[^&]*/,'').replace(/&hide_shorter_then=[^&]*/,'');
        return append(href, 'favs');
}
function appendHideThema(href){
        href = href.replace(/&quality=[^&]*/,'').replace(/&hide_shorter_then=[^&]*/,'');
        return append(href, 'hide_thema');
}

function append(href, cookie_name){
        var raw = getCookie(cookie_name);
        //console.log(raw);
        if(raw=='') raw='[]';
        var cookie_favs = JSON.parse(raw);
  if(cookie_favs.length>0){
   var index = cookie_favs.indexOf( encodeURIComponent(href) ); 
   if (index > -1) return false; //element bereits vorhanden in Liste
  }else cookie_favs = new Array();
  //href =href.replace(/"/g,'\"');  alert(href);
        //href =href.replace(/%22/g,"%2F%22"); alert(href); //falsch rum /
  cookie_favs.push( decodeURIComponent(href) );
        //var newAr = Array(); for (var j = 0; j < cookie_favs.length; j++) {  newAr.push ( encodeURIComponent(cookie_favs[j]) ); }
        var myJSONString = JSON.stringify(cookie_favs);
        //var myEscapedJSONString = myJSONString.escapeSpecialChars();
  createCookie( cookie_name, myJSONString, 365*5);
        setUpdateDate();
  return true;
}

//damit der Server ggf. bescheidsagen kann, das Seite noch aktuell (=aus den Cache laden)
function setUpdateDate(){  var d = new Date();       createCookie( 'favs_last_update', d.toUTCString(),365*5); }

function removeFavSelf(self){
  newValueToRemove = self.parentNode.parentNode.getElementsByClassName('t_sel_a')[0].getAttribute('href');
  removeFav(newValueToRemove);
  self.innerText='☆...später gelöscht aus Liste'; return true;
}

function removeFavDataHrefSelf(self){
  newValueToAdd = self.getAttribute('data-href');
  removeFav(newValueToAdd);
  self.innerText='☆...später gelöscht aus Liste'; return true;
}


function removeFav(href){
        href = href.replace(/&quality=[^&]*/,'').replace(/&hide_shorter_then=[^&]*/,'');
        return remove(href, 'favs');}
        
function removeHideThema(href){ 
        href = href.replace(/&quality=[^&]*/,'').replace(/&hide_shorter_then=[^&]*/,'');
        return remove(href, 'hide_thema'); }
        
function removeHideThemaDataHrefSelf(self){
  newValueToAdd = self.getAttribute('data-href');
  removeHideThema(newValueToAdd);
  self.innerText='...gelöscht'; return true;}

function remove(href, cookie_name){
  //alert(href+'\n;;;;'+getCookie('favs'));
  var raw = getCookie(cookie_name);
  if(raw=='') raw='{}';
  var array = JSON.parse(raw); 
  //console.log(decodeURIComponent(cleanURL(href)));
  var index = array.indexOf( cleanURL(href) ); //mus auf beiden Wegen
  if(index ==-1 )index = array.indexOf( decodeURIComponent(cleanURL(href)) ); //bei &
  if(index ==-1 )index = array.indexOf( decodeURIComponent(href) ); // bei "
  if (index > -1) {
    array.splice(index, 1);
  }else return false;
  if(array.length>0)  createCookie( cookie_name,JSON.stringify(array), 365*5);
  else                createCookie( cookie_name,JSON.stringify(array), -1);
  //alert(href+'\n;;;;'+getCookie('favs'));
  setUpdateDate();
  return true;

}

function showAlleFromHideThema(){
    var ziel = document.getElementById('show_hideElementsList');
    var raw = getCookie('hide_thema');
    if(raw==''){
     raw='{}';
     ziel.innerHTML = '&nbsp;&nbsp;&nbsp;<span style="color:#999999">(keine)</span>';
     return; //abbruch
    }
    //console.log(raw);
    var cookie = JSON.parse(raw);
    //console.log(cookie_favs);
    
    var mainlinks = '';

    var ziel = document.getElementById("show_hideElementsList");
    ziel.style.width = '100%';
          var out = mainlinks;
    for (var j = 0; j < cookie.length; j++) {  
         cookie[j] = cleanURL(cookie[j]);
         var link_part0 = cookie[j].split('?sender=')[0];
         var readable_link = cookie[j].replace(link_part0+"?",'').replace(/liste.php\?/,' ').replace(/sender=/,'').replace(/&thema=/,'&nbsp; ').replace(/"/g,'x4sdy0Anfuehrungsz4sdy0').replace(/x4sdy0ANDx4sdy0/g,'&');
         //console.log(link+' ?=? '+cookie_favs[j]);
         out += '&nbsp;&nbsp;&nbsp;'+readable_link ;
         out += ' <a href="#" class="link_every_same_color_underl" data-href="'+cookie[j]+'" onClick="removeHideThemaDataHrefSelf(this);showAlleFromHideThema();var del_hide_thema=1;return false;">Löschen</a><br>';
    }
    if(cookie.length>0 && cookie[0]!='') document.getElementById('options_hide_themen_liste_del__del_all').style.display = 'inline';
    ziel.innerHTML =  '<span style="background:#4eff001a;display:block;margin-left: 12pt;padding-right:1pt;padding-left:1pt;"> ' + out + '<br>Um Veränderungen zu sehen: <a href="#" class="link_every_same_color_underl" onClick="window.location.reload();return false;">Seite neu laden</a><br><span>';
    //var index 
    return out;
}

function cleanURL(link){
    if(link.length<5) return '';
    link = link.replace(/&quality=[^&]*/,'');
    link = link.replace(/&hideHoerfassung=[^&]*/,'');
    link = link.replace(/%3Fsender%3D/,'?sender=').replace(/%26thema%3D/,'&thema=');//repair
    //link = decodeURIComponent(link);
    var link_part0 = link.split('?sender=')[0];
    var sender = ( link.split('?sender=')[1] );
    if( sender.search(/\&/)!=-1)sender = sender.split('&')[0];
    var thema  = ( link.split('&thema=')[1] );
    thema = thema.replace(/&/g,'x4sdy0ANDx4sdy0');
    thema = thema.replace(/x4sdy0Anfuehrungsz4sdy0/g,'"');
    link = link_part0+'?sender='+sender+'&thema='+thema+'';

    return link    
}
function showURL(link){
    link = link.replace(/&quality=[^&]*/,'');
    link = link.replace(/&hideHoerfassung=[^&]*/,'');
    link = link.replace(/%3Fsender%3D/,'?sender=').replace(/%26thema%3D/,'&thema=');//repair
    //link = decodeURIComponent(link);
    var link_part0 = link.split('?sender=')[0];
    var sender = ( link.split('?sender=')[1] ).split('&')[0];
    var thema  = ( link.split('&thema=')[1] );
    //thema = thema.replace(/&/g,'x4sdy0ANDx4sdy0');
    thema = thema.replace(/x4sdy0Anfuehrungsz4sdy0/g,'"');
    link = link_part0+'?sender='+sender+'&thema='+thema+'';
    return link    
}
function updateThemenListeLink_addSchnellauswahl(){
      
       var e = document.getElementById('table_sel_thema'); // ebene darüber div#list_auswahl_links_thema
       if(e==undefined || e==null) return;
       var elements = e.childNodes;
       if(elements[0].tagName != 'TR' && elements.length>1) elements = elements[1].childNodes; //ueberspringe <tbody>
       if(elements[0].tagName != 'TR' && elements.length==1) elements = elements[0].childNodes; //ueberspringe <tbody>
       //console.log(elements);
  
       for (var i = 0; i < elements.length; i++) {
           //console.log(elements[i].getElementsByClassName('t_sel_a'));
           if(elements[i].tagName=='TR')elements[i].addEventListener('mouseenter', showAddMenuLinks, false);
           if(elements[i].tagName=='TR'){
                  elements[i].getElementsByClassName('t_sel_a')[0].addEventListener('focus', showAddMenuLinks, false);
                  elements[i].getElementsByClassName('t_sel_a')[0].addEventListener('focusin', showAddMenuLinks, false);
           }
           if(elements[i].tagName=='TR')elements[i].addEventListener('mouseleave', hideAddMenuLinks, false);
           //if(elements[i].tagName=='TR')elements[i].getElementsByClassName('t_sel_a')[0].addEventListener('focusout', hideAddMenuLinks, false);
       }
       

       for (var i = 0; i < 40 && i<elements.length; i++) { //ersten X/2 schon im verarbeiten, damit breite richtig ist
           if(elements[i].tagName == undefined || elements[i].nodeName=='text' || elements[i].nodeName=='#text') continue;
           if(elements[i].nodeName=='TR' || elements[i].className != undefined ){ //|| elements[i].className.search(/t_row/)!=-1
             showAddMenuLinks( elements[i] );
             hideAddMenuLinks( elements[i] );
           }
       }
}

function themenliste_tr_found_parent(e){
        var i = 0;
        while(e.nodeName!='TR' && e.className.search(/t_row/)==-1){ //ist quasi doppelt, aber notwendig, wenn keine Tabele/TR verwendet wird
              e = e.parentNode;
              if(i>5) return null; //fehler  
        }
        return e;
}

var themenliste_ausblenden_innerhtml = '<a href=\"#\" style=\"margin-left:15pt\" class=\"link_every_same_color_underl link_every_same_color\" onClick=\"appendHideThemaSelf(this);return false;\">Ausblenden</a>&nbsp;';
var themenliste_schnellauswahl_hinzufuegen_innerhtml = '<a href=\"#\" class=\"t_sel_add_schnellauswahl link_every_same_color_underl link_every_same_color\" onClick=\"appendFavSelf(this);return false;\" title=\"zur Schnellauswahl hinzufügen\">+Schnellauswahl</a>';


function showAddMenuLinks(event){ 
        if(event.target!=null)var e = themenliste_tr_found_parent( event.target );
        else var e = event;
        
        var raw = getCookie('favs'); if (raw=='') raw='{}';
        var cookie_favs = JSON.parse(raw);
        for (var j = 0; j < cookie_favs.length; j++) cookie_favs[j] = cleanURL(cookie_favs[j]);

        var a_link            = e.getElementsByClassName('t_sel_a')[0];
        var tr_schnellauswahl = e.getElementsByClassName("td_schnell")[0];
        var tr_del            = e.getElementsByClassName("td_del")[0];
        
        var href = a_link.getAttribute('href');
        href = decodeURIComponent(href);
        href = cleanURL(href);

        //Schnellauswahl hinzufügen Link
        tr_schnellauswahl.style.visibility = '';
        tr_schnellauswahl.innerHTML = themenliste_schnellauswahl_hinzufuegen_innerhtml;
  
        for (var j = 0; j < cookie_favs.length; j++) {               
          if (href==cookie_favs[j] ){
             tr_schnellauswahl.firstChild.innerHTML = '★(löschen)';
             tr_schnellauswahl.firstChild.setAttribute("onclick", "removeFavSelf(this)");
           }
        }
  
        //Ausblenden Link
        if(tr_del.innerHTML=='')tr_del.innerHTML = themenliste_ausblenden_innerhtml;
        tr_del.style.visibility = '';
}

function hideAddMenuLinks(event){
        if(event.target!=null)var e = themenliste_tr_found_parent( event.target );
        else var e = event;
        
        var tr_schnellauswahl = e.getElementsByClassName("td_schnell")[0];
        var tr_del            = e.getElementsByClassName("td_del")[0];
        tr_schnellauswahl.style.visibility = 'hidden';
        tr_del.style.visibility = 'hidden';
}


//in der ThemenList den Schnellauswahl-Link aktualisieren
function updateThemenListeLink_addSchnellauswahl_old(){
      //Link: "zur Schnellauswahl hinzufügen"
      //var innerh_link_hinzufuegen = 'aa';
      //list = document.getElementsByClassName('td_schnell');  //alt; verschoben in anderes for()
      //for(var i=0; i<list.length; i++) list[i].innerHTML = innerh_link_hinzufuegen; //alt; verschoben in anderes for()
      
      var raw = getCookie('favs');
      if(raw==''){
       raw='{}';
       //return; //abbruch (ne, weil ,muss ja Link zum hinzufügen setzen)
      }
      //console.log(raw);
      var cookie_favs = JSON.parse(raw);
      for (var j = 0; j < cookie_favs.length; j++) cookie_favs[j] = cleanURL(cookie_favs[j]);
      //console.log(cookie_favs);
     //var elements = document.getElementsByClassName("t_sel_add_schnellauswahl"); //alt
      var elements = document.getElementsByClassName('t_sel_a');
      
      for (var i = 0; i < elements.length; i++) {  
          var href = elements[i].getAttribute('href');
                            href = decodeURIComponent(href);
                            href = cleanURL(href);

                            var schnellauswahl_tr = elements[i].parentNode.parentNode.getElementsByClassName("td_schnell")[0];
                            schnellauswahl_tr.innerHTML = themenliste_schnellauswahl_hinzufuegen_innerhtml;
          
          for (var j = 0; j < cookie_favs.length; j++) {               
            if(href==cookie_favs[j] ){
              schnellauswahl_tr.firstChild.innerHTML = '★ (löschen)';
              schnellauswahl_tr.firstChild.setAttribute("onclick", "removeFavSelf(this)");
            }
          }
      }
      //var index = array.indexOf(href);
      //if (index > -1) {
      //  array.splice(index, 1);
      //}else return false;
      //createCookie( 'favs',JSON.stringify(array), 365*5);
      //alert(href+'\n;;;;'+getCookie('favs'));
      //return true;
}

//Themen ausblenden (ggf. nach Einstellungen); Link kürzer als X Min. ggf. hinzufügen
function updateListeThemenLink_hideElements_andRepairLinks(){
        
      //Link: "Ausblenden"
      //var list = document.getElementsByClassName('td_del'); //läuft inzwischen in jeder Zeile bei Bedarf in showAddMenuLinks
      //for(var i=0; i<list.length; i++) list[i].innerHTML = themenliste_ausblenden_innerhtml;  //läuft inzwischen in jeder Zeile bei Bedarf in showAddMenuLinks
      //var a = document.createElement('a');  //test
      //a.innerText = 'aaa';  //test
      //for(var i=0; i<list.length; i++) list[i].appendChild(a); //test
      
      
      var hideAudioDeskription = getCookie('hideAudioDeskription');
      var hideTrailer = getCookie('hideTrailer');

      var raw = getCookie('hide_thema'); if(raw=='') raw = '{}';
      if(raw.length<=5 && hideAudioDeskription!=1 && hideTrailer!=1) return; //nix zu tun;
      
      var c = getCookie('hideShorterThen');
      var cookie_favs = JSON.parse(raw);
      var e = document.getElementById("list_auswahl_links_thema");
      if(e==undefined)return;
      var elements = e.getElementsByClassName("t_sel_a");
      for (var i = 0; i < elements.length; i++) {  
                      var link = elements[i].getAttribute('href');//parentNode.firstChild.
                      link = decodeURIComponent(link);
                      link = cleanURL(link);
    
                      for (var j = 0; j < cookie_favs.length; j++) {  //Ausblenden-Liste
                           cookie_favs[j] = cleanURL(cookie_favs[j]);             
                           if(link==cookie_favs[j] ){
                              var span = elements[i].parentNode.parentNode.style.display = "none";
                           }
                      }
    
                      if( hideAudioDeskription==1 && elements[i].innerText.substr(0,5)=='AD | ' ){
                        var span = elements[i].parentNode.parentNode.style.display = "none";
                      }
                      if( hideTrailer==1          && elements[i].innerText.substr(0,7)=='Trailer' ){
                        var span = elements[i].parentNode.parentNode.style.display = "none";
                      }
    
                      if(c>0){
                        elements[i].href = elements[i].href + '&hide_shorter_then' + '=' + c;
                      }
      }
}


function updateListeSenderLink(){
        var c = getCookie('hideShorterThen');
        var e = document.getElementById("list_auswahl_links_sender");
        if(e==undefined)return;
        var elements = e.getElementsByTagName("A");
        if(c>0){
            for (var i = 0; i < elements.length; i++) {    
              elements[i].href = elements[i].href + '&hide_shorter_then' + '=' + c;
            }
        }
        var c = getCookie('pageination');
        if(c>0){
              for (var i = 0; i < elements.length; i++) {    
                if(c>0) elements[i].href = elements[i].href + '&start=1' + '&ende=' + c;
              }
        }
}


//updates eines Links nur am Anfang einer Seite
function updateFilmlistenSeite_ListeSchnellauswahl(){
      var raw = getCookie('favs');
      if(raw==''){
       raw='{}';
       return; //abbruch
      }
      var cookie_favs = JSON.parse(raw);

      var elements = document.getElementsByClassName("schnellausw_h");

      for (var i = 0; i < elements.length; i++) {  
          var link = elements[i].getAttribute('data-href');
                      link = decodeURIComponent(link);
          for (var j = 0; j < cookie_favs.length; j++) {  
                  //console.log(cookie_favs[j]);
                                    //console.log( (link) + "??==??"+cookie_favs[j] );
            if(link==cookie_favs[j]){
              elements[i].innerHTML = '★ (löschen)';
              elements[i].setAttribute("onclick", "removeFavDataHrefSelf(this)"); //'"+link.replace("'","\'")+"'
              //console.log(elements[i].getAttribute("onClick")+'Fund in ');
            }
          }
      }
}


//in der ThemenList den Schnellauswahl-Link aktualisieren
function getSchnellauswahl(){
      var raw = getCookie('favs');
      if(raw==''){
       raw='{}';
       return; //abbruch
      }
      //console.log(raw);
      var cookie_favs = JSON.parse(raw);
      //console.log(cookie_favs);

      var target = document.getElementById("schnellauswahl");
      var a = new Array();
      //fuer Sortieren nach Thema (primitive Lösung, geh aber :_) )
      for (var j = 0; j < cookie_favs.length; j++) {
             sortstring = decodeURIComponent(cookie_favs[j]).replace(/liste\.php\?sender=([^&]*)/,'');
             cookie_favs[j] = sortstring+'###sortstring###ENDE###'+cookie_favs[j];
      }
      cookie_favs.sort();
      for (var j = 0; j < cookie_favs.length; j++) {
              cookie_favs[j] = cookie_favs[j].split('###sortstring###ENDE###')[1];
        var link = cookie_favs[j];
        span = document.createElement('span');
        span.classList.add('line_schnellauswahl');  
        span.classList.add('line_span');

        a_delete = document.createElement('a');
        a_delete.href = '#';
        a_delete.innerText   = 'löschen';
        a_delete.style.float ='right';
        a_delete.style.color ='blue';
        a_delete.setAttribute('onclick',"if( removeFav('"+link.replace('\'','\\\'')+"')){window.location.reload();this.text='wird gelöscht';}");
        span.appendChild(a_delete);

        a_link = document.createElement('a');
        var quality_cookie = getCookie('quality');
        var hideHoerfassung_cookie = getCookie('hideHoerfassung');
                                link = cleanURL(link);
        
        if(hideHoerfassung_cookie!='') link += '&hideHoerfassung='+hideHoerfassung_cookie;
        if(quality_cookie!='') link += '&quality='+quality_cookie;
        a_link.href = link;
        a_link.innerText = ''+showURL(decodeURIComponent(cookie_favs[j])).replace('liste.php?sender=','').replace('&thema=',' ').replace(/&quality=[^&]*/,'').replace(/&hideHoerfassung=[^&]*/,'');
        //console.log(cookie_favs[j]);
        //console.log( decodeURIComponent(cookie_favs[j]) );
        span.appendChild(a_link);

          target.appendChild(span);
        
      }
      //var index = array.indexOf(href);
      //if (index > -1) {
      //  array.splice(index, 1);
      //}else return false;
      //createCookie( 'favs',JSON.stringify(array), 365*5);
      //alert(href+'\n;;;;'+getCookie('favs'));
      //return true;

}


