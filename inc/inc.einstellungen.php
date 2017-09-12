<?php

echo "         <hr>
         <div>Standart-Qualität: <span style=\"float:right\">
              &nbsp;&nbsp;&nbsp;&nbsp; 
              <a href=\"#\" onClick=\"createCookie('quality','hd',365*5);loadNewSite();location.reload();return false;\" id=\"set_quality_hd\" >Hoch (HD)</a>
              &nbsp;&nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;&nbsp; 
              <a href=\"#\" onClick=\"createCookie('quality','normal',0);     loadNewSite();location.reload();return false;\" id=\"set_quality_normal\" >Normal (~meist ca. 2Mbit)</a> 
              &nbsp;&nbsp;&nbsp;&nbsp;  oder &nbsp; &nbsp;&nbsp;&nbsp; 
              <a href=\"#\" onClick=\"createCookie('quality','low',365*5);loadNewSite();location.reload();return false;\" id=\"set_quality_low\" >Gering (~ca. 0,5 - 1Mbit)</a> 
              <script  language=\"javascript\"  type=\"text/javascript\">
                  if(getCookie('quality')=='low')document.getElementById('set_quality_low').innerHTML+=' &#10008;';
                  else if(getCookie('quality')=='hd')document.getElementById('set_quality_hd').innerHTML+=' &#10008;';
                  else document.getElementById('set_quality_normal').innerHTML+=' &#10008;';
              </script>
              </span>
         </div>
         <div style=\"clear:both\"></div>\n";
     

  echo '
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
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_hideAudioDeskription_an" onClick="createCookie(\'hideAudioDeskription\',\'1\',356*10);window.location.reload();">ausblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_link_hideAudioDeskription_aus" onClick="createCookie(\'hideAudioDeskription\',\'\',0);window.location.reload();">anzeigen</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'hideAudioDeskription\')==1)document.getElementById(\'options_link_hideAudioDeskription_an\').innerHTML=\' ausblenden &#10008;\';else document.getElementById(\'options_link_hideAudioDeskription_aus\').innerHTML=\'anzeigen &#10008;\'; </script>
         <div style="clear:both"></div>
         <hr>
         Filme mit Trailer im Namen   
         <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_hideTrailer_an" onClick="createCookie(\'hideTrailer\',\'1\',356*10);window.location.reload();">ausblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_link_hideTrailer_aus" onClick="createCookie(\'hideTrailer\',\'\',0);window.location.reload();">anzeigen</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'hideTrailer\')==1)document.getElementById(\'options_link_hideTrailer_an\').innerHTML=\' ausblenden &#10008;\';else document.getElementById(\'options_link_hideTrailer_aus\').innerHTML=\'anzeigen &#10008;\'; </script>
         <div style="clear:both"></div>
         <hr>

         <div style="clear:both"></div>
         Mindest Film-Länge
         <!--<span style="float:left; text-align:left">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              &nbsp;&nbsp;&nbsp; oder: &nbsp;&nbsp;&nbsp;
         </span>-->
         <span style="float:right; text-align:right">';

              foreach ($minLengthVorlagenMinuten as $h){
                  echo '<a href="#" id="options_link_minLength_'.$h.'" onClick="createCookie(\'minLength\',\''.$h.'\',356*10);window.location=\'#\';window.location.reload();">'.$h.'Min.</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
              }
  echo '
        oder &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_minLength_0" onClick="createCookie(\'minLength\',\'\',0);window.location.reload();">aus</a>
        </span>
        <script  language="javascript"  type="text/javascript">
          var c=getCookie(\'minLength\');
          if(c==\'\') c=0;
          document.getElementById(\'options_link_minLength_\'+parseInt(c)+\'\').innerHTML+=\' &#10008;\';
          //elseif(c>0) document.getElementById(\'options_link_minLength_aus\').innerHTML=\'anzeigen &#10008;\';
        </script>
        <div style="clear:both"></div>

        
    
        ';
        $url = 'liste.php?';

        
        echo '
        
        <hr>
        <span style="float:right; text-align:right"></span>
        Feste Fußleiste
        <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_showFooter_aus" onClick="createCookie(\'showFooter\',\'\',0);window.location.reload();">ausblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_showFooter_an" onClick="createCookie(\'showFooter\',\'1\',356*10);window.location.reload();">anzeigen</a>


              
        </span>
        <div align="right">
          <img style="float:right; opacity:0.5" src="img/fussleiste_w500px.png" />
          <span style="color:#999999">
          <span style="color:#999999">&nbsp;Für Maus-bedienung am Smart-TV,<br>
          &nbsp;durch Link unten schneller nach oben springen 
          </span>
        </div>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'showFooter\')==1)document.getElementById(\'options_link_showFooter_an\').innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_showFooter_aus\').innerHTML+=\' &#10008;\'; </script>
        <div style="clear:both"></div>
        <hr>
        <img id="vorschaltseite_thumb" title="so siht die vorschaltseite aus" src="" data-src="img/vorschaltseite-mittext-thumb400px.png" height="200" border="1" style="margin-left:15pt;float:right;margin-right:5pt;margin-bottom:5pt;">
        Vorschaltseite (sihe Bild rechts)
        <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp;<a href="#" id="options_link_vorschaltseite_an" onClick="createCookie(\'direkt_zur_mediathek_liste\',\'1\',356*10);window.location.reload();">ausblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_link_vorschaltseite_aus" onClick="createCookie(\'direkt_zur_mediathek_liste\',\'\',0);window.location.reload();">anzeigen</a>
              <script  language="javascript"  type="text/javascript"> if(getCookie(\'direkt_zur_mediathek_liste\')==1)document.getElementById(\'options_link_vorschaltseite_an\').innerHTML=\' ausblenden &#10008;\';else document.getElementById(\'options_link_vorschaltseite_aus\').innerHTML=\'anzeigen &#10008;\'; </script>
        </span>
   
        <span style="clean:both"></span>
         <div style="clear:both"></div>
         
         <hr>
         Performance möglicherweise bessere bei langen Themenlisten:
         <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_no_table_an" onClick="createCookie(\'no_table\',\'1\',356*10);window.location.reload();">Textliste</a>, &nbsp;&nbsp;
              <a href="#" id="options_link_no_table_an2" onClick="createCookie(\'no_table\',\'2\',356*10);window.location.reload();">TextlisteTabelle</a>, &nbsp;&nbsp;
              <a href="#" id="options_link_no_table_aus" onClick="createCookie(\'no_table\',\'\',0);window.location.reload();">Tabelle</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'no_table\')==1)document.getElementById(\'options_link_no_table_an\').innerHTML+=\' &#10008;\';else if(getCookie(\'no_table\')==2)document.getElementById(\'options_link_no_table_an2\').innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_no_table_aus\').innerHTML+=\' &#10008;\'; </script>
        <div style="clear:both"></div>
        
        ';
        if($hideArte_fr == 1) echo "<hr><span style=\"color:#999999\">Sender arte.fr ausgeblendet <span style=\"\">(fest eingestellt im Server)</span></span><br>";
        
        
        echo '
        <hr>Themenliste seitenweise. Je Seite<!--<br><span style="color:#999999"></span>-->
         <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp;
              &nbsp;&nbsp;
              <a href="#" id="options_link_pageination10" onClick="createCookie(\'pageination\',\'10\',356*10);window.location.reload();"> 10 </a> &nbsp;&nbsp;
              <a href="#" id="options_link_pageination20" onClick="createCookie(\'pageination\',\'20\',356*10);window.location.reload();"> 20 </a> &nbsp;&nbsp;
              <a href="#" id="options_link_pageination30" onClick="createCookie(\'pageination\',\'30\',356*10);window.location.reload();"> 30 </a> &nbsp;&nbsp;
              <a href="#" id="options_link_pageination40" onClick="createCookie(\'pageination\',\'40\',356*10);window.location.reload();"> 40 </a>
              &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_link_pageination_aus" onClick="createCookie(\'pageination\',\'\',0);window.location = (window.location.href).replace(/start=-?[0-9]*/,\'\').replace(/ende=-?[0-9]*/,\'\'); if(window.location == window.location.href.replace(/start=-?[0-9]*/,\'\').replace(/ende=-?[0-9]*/,\'\'))window.location.reload();">eine lange Listen zum scrollen</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'pageination\')>0)document.getElementById(\'options_link_pageination\'+getCookie(\'pageination\')).innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_pageination_aus\').innerHTML+=\' &#10008;\'; </script>
        <div style="clear:both"></div>
        ';
        

        echo "<hr>
        <div id=\"optionen_themen_ausblenden_deaktiv\">
          Blacklist / Themen ausblenden
          <span style=\"float:right;padding:6pt\">
                <a href=\"#\" style=\"\" class=\"link_every_same_color_underl\" onClick=\"createCookie('hide_thema_aktiv','1',365*10);window.location.reload();return false;\" id=\"options_hide_thema_enable_link\">Aktivieren</a>
          </span>
        </div>
        <div id=\"optionen_themen_ausblenden_aktiv\" style=\"display:none;\">
        Blacklist / Themen ausgeblendet: 
        <span style=\"float:right;padding:1pt\">
                <a href=\"#\" style=\"\" class=\"link_every_same_color_underl\" onClick=\"createCookie('hide_thema_aktiv','',-1);window.location.reload();return false;\" id=\"options_hide_thema_disable_link\">deaktivieren</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#\" style=\"display:none\" class=\"link_every_same_color_underl\" onClick=\"document.getElementById('options_hide_thema_liste_del__del_all_realy').style.display='block';return false;\" id=\"options_hide_thema_liste_del__del_all\">Alle löschen</a> <a href=\"#\" style=\"border: 3pt solid blue;margin:3pt;display:none\" class=\"link_every_same_color_underl\" onClick=\"createCookie('hide_thema','',-1);window.location.reload();return false;\" id=\"options_hide_thema_liste_del__del_all_realy\">Wirklich diese Blackliste löschen?</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=\"#\" class=\"link_every_same_color_underl\" onClick=\"showAlleFromHide('thema');return false;\">aktualisieren &#x21B4;</a><span style=\"clear:both\"></span>
        </span>
        
        <div id=\"show_hide_thema_ElementsList\" style=\"\"> </div>
        <script language=\"javascript\"  type=\"text/javascript\">
            if(getCookie('hide_thema_aktiv')!=''){
              document.getElementById('optionen_themen_ausblenden_aktiv').style.display = 'block';
              document.getElementById('optionen_themen_ausblenden_deaktiv').style.display = 'none';
              if(getCookie('hide_thema')!='')document.getElementById('show_hide_thema_ElementsList').innerHTML = '&nbsp; &nbsp; ';
              else{ /*document.getElementById('show_hide_thema_ElementsList').innerHTML = '&nbsp; &nbsp; -keine- ';*/ }
                //document.getElementById('show_hide_thema_ElementsList').innerHTML += '<a class=\"link_every_same_color_underl\" href=\"#\" onClick=\"showAlleFromHide('thema');return false;\">neu laden<a/>'; 
            }
        </script>
        </div>
        <div style=\"clear:both\"></div>
        <hr>
        ";  
        
        
        
        echo "<hr>
        <div id=\"optionen_film_ausblenden_deaktiv\">
          Blacklist / Filme ausblenden
          <span style=\"float:right;padding:6pt\">
                <a href=\"#\" style=\"\" class=\"link_every_same_color_underl\" onClick=\"createCookie('hide_film_aktiv','1',365*10);window.location.reload();return false;\" id=\"options_hide_film_enable_link\">Aktivieren</a>
          </span>
        </div>
        <div id=\"optionen_film_ausblenden_aktiv\" style=\"display:none;\">
          Blacklist / Filme ausgeblendet:
        <span style=\"float:right;padding:1pt\">
                <a href=\"#\" style=\"\" class=\"link_every_same_color_underl\" onClick=\"createCookie('hide_film_aktiv','',-1);window.location.reload();return false;\" id=\"options_hide_film_disable_link\">deaktivieren</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#\" style=\"display:none\" class=\"link_every_same_color_underl\" onClick=\"document.getElementById('options_hide_film_liste_del__del_all_realy').style.display='block';return false;\" id=\"options_hide_film_liste_del__del_all\">Alle löschen</a> <a href=\"#\" style=\"border: 3pt solid blue;margin:3pt;display:none\" class=\"link_every_same_color_underl\" onClick=\"createCookie('hide_film','',-1);window.location.reload();return false;\" id=\"options_hide_film_liste_del__del_all_realy\">Wirklich diese Blackliste löschen?</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=\"#\" style=\"\" class=\"link_every_same_color_underl\" onClick=\"document.getElementById('options_hide_film_liste_add_form').style.display='block';return false;\" id=\"options_hide_film_liste_add\">+</a> &nbsp; <a href=\"#\" class=\"link_every_same_color_underl\" onClick=\"showAlleFromHide('film');return false;\">aktualisieren &#x21B4;</a><span style=\"clear:both\"></span>
        </span>
        <div id=\"options_hide_film_liste_add_form\" style=\"text-align:right;display:none\"><br>
          Folgenden Film ausblenden <input type=\"text\" id=\"options_hide_film_liste_add_form_text\" >* 
          <input type=\"button\" value=\"ok\" onClick=\" showAlleFromHide_add('options_hide_film_liste_add_form_text','film');document.getElementById('options_hide_film_liste_add_form').style.display='none'\" />
        </div>
        <div id=\"show_hide_film_ElementsList\" style=\"\"> </div>
        <script language=\"javascript\"  type=\"text/javascript\">
            if(getCookie('hide_film_aktiv')!=''){
              document.getElementById('optionen_film_ausblenden_aktiv').style.display = 'block';
              document.getElementById('optionen_film_ausblenden_deaktiv').style.display = 'none';
              if(getCookie('hide_film')!='')document.getElementById('show_hide_film_ElementsList').innerHTML = '&nbsp; &nbsp; ';
              else{ /*document.getElementById('show_hide_film_ElementsList').innerHTML = '&nbsp; &nbsp; -keine- ';*/ }
                //document.getElementById('show_hide_film_ElementsList').innerHTML += '<a class=\"link_every_same_color_underl\" href=\"#\" onClick=\"showAlleFromHide('film');return false;\">neu laden<a/>'; 
            }
        </script>
        </div>
        <div style=\"clear:both\"></div>
        <hr>
        ";  
        
        echo '';
         
        echo " <br><br>";
        ?>
