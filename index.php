<?php 

//Cache

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
  $if_modified_since = preg_replace('/;.*$/', '',   $_SERVER['HTTP_IF_MODIFIED_SINCE']);
} else {
  $if_modified_since = '';
}

$mtime = filemtime($_SERVER['SCRIPT_FILENAME']);
if(file_exists('wetter_embed.html') && filemtime($_SERVER['SCRIPT_FILENAME'])>$mtime ) $mtime = filemtime("wetter_embed.html"); 
$gmdate_mod = date('D, d M Y H:i:s', $mtime) . ' GMT';
if ($if_modified_since == $gmdate_mod && !isset($_GET['privat_wetter_']) ){
  header("HTTP/1.0 304 Not Modified");
  exit;
}

header("Last-Modified: $gmdate_mod");
header("Content-Type: text/html; charset=utf-8");
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + (60*60*24*30)) . ' GMT'); //maximal Gültig für 30 Tag

?><!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8"/>
<title>Mediat. Wahl</title>
<style type="text/css">
body{
 background: white;
}
#links .element{
        text-align:center;
        float:left;
        display:block;
        min-width:400px;width:400px;
        min-height:400px;height:400px
        background:#dddddd;
        font-size:20pt;
        border: 1pt solid #dfdfdf;
        border-radius: 8pt;
        margin:5pt;
}

#links a{
	border:outset 0pt #888888;
 	padding:25pt;
}
#links a:focus{
	border:outset 20pt #888888;
        padding:5pt;	
        background:#dddddd;
}

a.links_lower{
	opacity: 0.5;
}
a.links_lower:focus{
	opacity: 1.0;
}
a small{
  text-decoration:none !important;
  color: #aaaaaa;      
}

</style>
<script language="javascript" type="text/javascript">
function saveUrl(){
	document.getElementById('form_edit_url').style.display = 'none';
	title = document.getElementById('title').value;
	url = document.getElementById('url').value;

	var add_urls_string = getCookie('add_urls'); 
	if(add_urls_string!='')add_urls_array = JSON.parse(add_urls_string);else add_urls_array = Array();
	var a = new Object;
	a[title] = url;	console.log(a);
	add_urls_array.push( a );

	createCookie('add_urls', JSON.stringify( add_urls_array ),30*12*2);
	window.location.reload();
}
function showForm(title='', url=''){
        //var name = window.prompt("Names des Links:","");
        //var adresse = window.prompt("Adresse des Links:","http://"); return false;
        document.getElementById('form_edit_url').style.display = 'block';
        window.location = '#showform';
        if(title!='')document.getElementById('title').value = title;
        if(url!='')document.getElementById('url').value = url;
        
}
function deleteUrl(titel, url){
	var add_urls_string = getCookie('add_urls'); 
	if(add_urls_string!='')add_urls_array = JSON.parse(add_urls_string);else add_urls_array = Array();

        new_array = Array();
	for (var key in add_urls_array) {
	    if (add_urls_array.hasOwnProperty(key)){
	      for (var key2 in add_urls_array[key]) {
		if(key!=title && url!= add_urls_array[key][key2]) new_array.push( add_urls_array[key] );
	      }
            }
	 }
	createCookie('add_urls', JSON.stringify( new_array ),30*12*2);
	window.location.reload();
}

function append(){
	var add_urls_string = getCookie('add_urls'); 
	if(add_urls_string!='')add_urls_array = JSON.parse(add_urls_string);else add_urls_array = Array();

	for (var key in add_urls_array) {
	    if (add_urls_array.hasOwnProperty(key)){
	      	//Link erstellen
	      var n = document.createElement('div');
	      var a = document.createElement('a');
	      for (var key2 in add_urls_array[key]) {
	        a.href = ''+add_urls_array[key][key2]+'';
	        a.innerHTML = ''+key2+'<br><small>'+a.href+'</small>';
		a.classList.add('element');
	      }
	      n.appendChild(a);
	      document.getElementById('links').insertBefore(n, document.getElementById('links_new') );
	
		//löschlisten-Eintrag
	      n = document.createElement('p');
	      a = document.createElement('a');
	      for (var key2 in add_urls_array[key]) {
	        a.setAttribute('onclick', "deleteUrl('"+key2+"','"+add_urls_array[key][key2]+"')" );
                a.href = "#";
	        a.text = ''+key2+' löschen';
		//a.addEventListener('click', erzeugeZeitStempel)
	      }
	      n.appendChild(a);
	      document.getElementById('form_delete_url').appendChild(n);
	    }
	}
	
	//Verstecke "Lösch" Button
	if(add_urls_array.length==0 || add_urls_string=='' || add_urls_string.length<5)document.getElementById('links_delete').style.display = 'none';
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

if(getCookie('direkt_zur_mediathek_liste')!='' && location.href.search(/direkt_zur_mediathek_liste_/)==-1){
        window.location.href = 'liste.php';
}

function setFocus(item){    
        //https://stackoverflow.com/questions/4014935/why-doesnt-this-javascript-focus-work
        if( !item )  return;

        var savedTabIndex = item.getAttribute('tabindex');
        item.setAttribute('tabindex', '-1');
        item.focus();
        item.setAttribute('tabindex', savedTabIndex);
        
}

function hideNoneHbbTVLinks(){
    if(navigator.userAgent.search('HbbTV/')!=-1) return false;

    //var list = document.getElementsByClassName('hbblink');
    //for (var i = 0, len = list.length; i < len; i++) {
    //    list[i].style.display = 'none';
    //}
    document.getElementById('append').innerHTML = '<span style="color:#aaaaaa"><small />Ihr Browser unterstützt (wahrscheinlich) kein HbbTV</small></span>';
}

 
</script>
</head>
<body onload="append();setFocus(document.getElementById('firstLink'));hideNoneHbbTVLinks();">

<span id="links">

  
  <a href="liste.php" class="element" tab-index="1" id="firstLink"><img src="img/mediathek-liste-thumb400px.png" /><br>Mediathek-Liste</a>
  <a href="http://www.heise.de/extras/ct/hbbtv" class="hbblink element"><img src="img/HBBTV-links-thumb400px.png" /><br>HBB-TV<br><small>http://www.heise.de/extras/ct/hbbtv</small></a>
  <a href="http://urju.de/hbbtv/" class="hbblink element links_lower"><br>weitere HBB-TV Links 1<br>(einige davon offline)<br><small>http://urju.de/hbbtv/</small></a>
  <a href="http://www.hbbig.com/" class="hbblink element links_lower"><br>weitere HBB-TV Links 2<br><small>http://www.hbbig.com/</small></a>
  <!-- geht nicht <a href="http://itv.mit-xperts.com/zdfmediathek/"><br>http://itv.mit-xperts.com/zdfmediathek/</a>-->
  <!-- einige davon gehen nicht (bspw. zdf); ansonsten nur für tests <a href="http://tv-html.irt.de/demos.php">http://tv-html.irt.de/demos.php</a>-->
  
  
  <a href="#showform" style="text-decoration:none;" onclick="showForm();" class="element links_lower" id="links_new">
      <span style="font-size:150pt">+</span><br><span style="text-decoration:underline;">Neuer Eintrag</span>
  </a>
  <a href="#deleteform" style="text-decoration:none;" onclick="document.getElementById('form_delete_url').style.display='';" class="element links_lower" id="links_delete">
      <span style="font-size:150pt">-</span><br><span style="text-decoration:underline;">Lösche Eintrag</span>
  </a>
  <div id="form_edit_url" style="display:none" class="element">
        <a name="showform"></a>
        <br><h3>Eintrag erstellen:</h3>
	<label for="title"><nobr>Titel:<input type="text" name="title" id="title" /></nobr></label><br>
	<label for="url"><nobr>Adresse:<input type="text" name="url" id="url" value="http://"/></nobr></label><br>
	<input type="button" onclick="saveUrl();return false;" value="Speichern" />
  </div>
  <div id="form_delete_url" style="display:none" class="element">
        <a name="deleteform"></a>
        
        <br><h3>Löschen:</h3>
  </div>
  

</span>

<span style="clear:both" ></span>
<span id="append" style="display:block;bottom: 5pt;left: 5pt;"></span>

