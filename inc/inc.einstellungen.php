<?php

echo "        
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

         <div style="clear:both"></div>
         Mindest Film-Länge
         <!--<span style="float:left; text-align:left">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              &nbsp;&nbsp;&nbsp; oder: &nbsp;&nbsp;&nbsp;
         </span>-->
         <span style="float:right; text-align:right">';

              foreach ($minLengthVorlagenMinuten as $h){
                  echo '<a href="#" id="options_link_minLength_'.$h.'" onClick="createCookie(\'minLength\',\''.$h.'\',356*10);window.location=\'#\';;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">'.$h.'Min.</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
              }
  echo '
        oder &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_minLength_0" onClick="createCookie(\'minLength\',\'\',0);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">aus</a>
        </span>
        <script  language="javascript"  type="text/javascript">
          var c=getCookie(\'minLength\');
          if(c==\'\') c=0;
          document.getElementById(\'options_link_minLength_\'+parseInt(c)+\'\').innerHTML+=\' &#10008;\';
          //elseif(c>0) document.getElementById(\'options_link_minLength_aus\').innerHTML=\'anzeigen &#10008;\';
        </script>
        <div style="clear:both"></div>

        
        <hr>
        <br>
        <br>
        ';
        
        
        echo '
        Themenliste seitenweise. <i>Je Seite<!--<br><span style="color:#999999"></span>--></i>
         <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp;
              &nbsp;&nbsp;
              <a href="#" id="options_link_pageination10" onClick="createCookie(\'pageination\',\'10\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;"> 10 </a> &nbsp;&nbsp;
              <a href="#" id="options_link_pageination20" onClick="createCookie(\'pageination\',\'20\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;"> 20 </a> &nbsp;&nbsp;
              <a href="#" id="options_link_pageination30" onClick="createCookie(\'pageination\',\'30\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;"> 30 </a> &nbsp;&nbsp;
              <a href="#" id="options_link_pageination40" onClick="createCookie(\'pageination\',\'40\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;"> 40 </a>
              &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_link_pageination_aus" onClick="createCookie(\'pageination\',\'\',0);window.location = (window.location.href).replace(/start=-?[0-9]*/,\'\').replace(/ende=-?[0-9]*/,\'\'); if(window.location == window.location.href.replace(/start=-?[0-9]*/,\'\').replace(/ende=-?[0-9]*/,\'\'));optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">eine lange Listen zum scrollen</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'pageination\')>0)document.getElementById(\'options_link_pageination\'+getCookie(\'pageination\')).innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_pageination_aus\').innerHTML+=\' &#10008;\'; </script>
        <div style="clear:both"></div>
        ';
        

        echo "<hr>
        <div id=\"optionen_themen_ausblenden_deaktiv\">
          Themen ausblenden
          <span style=\"float:right;padding:0pt\">
                <a href=\"#\" style=\"\" class=\"link_every_same_color_underl\" onClick=\"createCookie('hide_thema_aktiv','1',365*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;return false;\" id=\"options_hide_thema_enable_link\">Aktivieren</a>
          </span>
        </div>
        <div id=\"optionen_themen_ausblenden_aktiv\" style=\"display:none;\">
        Themen ausgeblendet:
        <span style=\"float:right;padding:1pt\">
                <a href=\"#\" style=\"\" class=\"link_every_same_color_underl\" onClick=\"createCookie('hide_thema_aktiv','',-1);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;return false;\" id=\"options_hide_thema_disable_link\">deaktivieren</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"#\" style=\"display:none\" class=\"link_every_same_color_underl\" onClick=\"document.getElementById('options_hide_thema_liste_del__del_all_realy').style.display='block';return false;\" id=\"options_hide_thema_liste_del__del_all\">Alle löschen</a> <a href=\"#\" style=\"border: 3pt solid blue;margin:3pt;display:none\" class=\"link_every_same_color_underl\" onClick=\"createCookie('hide_thema','',-1);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;return false;\" id=\"options_hide_thema_liste_del__del_all_realy\">Wirklich diese Liste löschen?</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=\"#\" class=\"link_every_same_color_underl\" onClick=\"showAlleFromHide('thema');return false;\">aktualisieren &#x21B4;</a><span style=\"clear:both\"></span>
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
        
        ";  
        
        
        
        echo "<hr>
        <div id=\"optionen_film_ausblenden_deaktiv\">
          Filme ausblenden
          <span style=\"float:right;padding:0pt\">
                <a href=\"#\" style=\"\" class=\"link_every_same_color_underl\" onClick=\"createCookie('hide_film_aktiv','1',365*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;return false;\" id=\"options_hide_film_enable_link\">Aktivieren</a>
          </span>
        </div>
        <div id=\"optionen_film_ausblenden_aktiv\" style=\"display:none;\">
          Filme ausgeblendet:
        <span style=\"float:right;padding:1pt\">
                <a href=\"#\" style=\"\" class=\"link_every_same_color_underl\" onClick=\"createCookie('hide_film_aktiv','',-1);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;return false;\" id=\"options_hide_film_disable_link\">deaktivieren</a><a href=\"#\" style=\"display:none\" class=\"link_every_same_color_underl\" onClick=\"document.getElementById('options_hide_film_liste_del__del_all_realy').style.display='block';return false;\" id=\"options_hide_film_liste_del__del_all\">Alle löschen</a> <a href=\"#\" style=\"border: 3pt solid blue;margin:3pt;display:none\" class=\"link_every_same_color_underl\" onClick=\"createCookie('hide_film','',-1);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;return false;\" id=\"options_hide_film_liste_del__del_all_realy\">Wirklich diese Liste löschen?</a> &nbsp;&nbsp;&nbsp; <a href=\"#\" style=\"\" class=\"link_every_same_color_underl\" onClick=\"document.getElementById('options_hide_film_liste_add_form').style.display='block';return false;\" id=\"options_hide_film_liste_add\">+</a> &nbsp;&nbsp; <a href=\"#\" class=\"link_every_same_color_underl\" onClick=\"showAlleFromHide('film');return false;\">aktualisieren &#x21B4;</a><span style=\"clear:both\"></span>
        </span>
        <div id=\"options_hide_film_liste_add_form\" style=\"text-align:right;display:none\"><br>
          Folgenden Film ausblenden <input type=\"text\" id=\"options_hide_film_liste_add_form_text\" >* 
          <input type=\"button\" value=\"ok\" onClick=\" if(document.getElementById('options_hide_film_liste_add_form_text').value!='')){ showAlleFromHide_add('options_hide_film_liste_add_form_text','film');document.getElementById('options_hide_film_liste_add_form').style.display='none';}\" />
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
        <br>
        <br>

        ";  
        

        
        echo '
        
        <i>Hörfassung</i>   
        <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_hideHoerfassung_an" onClick="createCookie(\'hideHoerfassung\',\'1\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">ausblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_link_hideHoerfassung_aus" onClick="createCookie(\'hideHoerfassung\',\'\',0);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">anzeigen</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'hideHoerfassung\')==1)document.getElementById(\'options_link_hideHoerfassung_an\').innerHTML=\' ausblenden &#10008;\';else document.getElementById(\'options_link_hideHoerfassung_aus\').innerHTML=\'anzeigen &#10008;\'; </script>
        
         <hr>
         <i>Audiodeskription</i> 
         <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_hideAudioDeskription_an" onClick="createCookie(\'hideAudioDeskription\',\'1\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">ausblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_link_hideAudioDeskription_aus" onClick="createCookie(\'hideAudioDeskription\',\'\',0);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">anzeigen</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'hideAudioDeskription\')==1)document.getElementById(\'options_link_hideAudioDeskription_an\').innerHTML=\' ausblenden &#10008;\';else document.getElementById(\'options_link_hideAudioDeskription_aus\').innerHTML=\'anzeigen &#10008;\'; </script>
         <div style="clear:both"></div>
         <hr>
         <i>Trailer/Vorschau</i>
         <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_hideTrailer_an" onClick="createCookie(\'hideTrailer\',\'1\',356*10);optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">ausblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_link_hideTrailer_aus" onClick="createCookie(\'hideTrailer\',\'\',0);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">anzeigen</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'hideTrailer\')==1)document.getElementById(\'options_link_hideTrailer_an\').innerHTML=\' ausblenden &#10008;\';else document.getElementById(\'options_link_hideTrailer_aus\').innerHTML=\'anzeigen &#10008;\'; </script>
         <div style="clear:both"></div>
         
        
        
        ';

        
        if($hideArte_fr == 1) echo "<hr><span style=\"color:#999999\">Sender arte.fr ausgeblendet <span style=\"\">(fest eingestellt im Server)</span></span><br>";
        else if( $hideArte_fr==2 ){
        echo '
        <hr>
        <span title="Arte Frankreich">Sender Arte.fr</span>
         <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_show_arte_fr_aus" onClick="createCookie(\'show_arte_fr\',\'\',0);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">ausblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_show_arte_fr_an" onClick="createCookie(\'show_arte_fr\',\'1\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">anzeigen</a>
              
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'show_arte_fr\')==1)document.getElementById(\'options_link_show_arte_fr_an\').innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_show_arte_fr_aus\').innerHTML+=\' &#10008;\'; </script>
         <div style="clear:both"></div>
         ';
         }
         echo '<hr><br><br>';
         
         
         echo '
         
        Extra-Liste für Audio-Deskription/Hörfassung-Liste <span style="color:#999999; font-size:0.9em">unter Sender aufgeführt</span>
        <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_extra_sender_AudioDeskription_an" onClick="createCookie(\'extra_sender_AudioDeskription\',\'1\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">anzeigen</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_link_extra_sender_AudioDeskription_aus" onClick="createCookie(\'extra_sender_AudioDeskription\',\'\',0);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">ausblenden</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'extra_sender_AudioDeskription\')==1)document.getElementById(\'options_link_extra_sender_AudioDeskription_an\').innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_extra_sender_AudioDeskription_aus\').innerHTML+=\' &#10008;\'; </script>
         
         
        <hr>
        Extra-Liste für Gebärdensprache <span style="color:#999999; font-size:0.9em">unter Sender aufgeführt</span>
        <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_extra_sender_Gebaerdensprache_an" onClick="createCookie(\'extra_sender_Gebaerdensprache\',\'1\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">anzeigen</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_link_extra_sender_Gebaerdensprache_aus" onClick="createCookie(\'extra_sender_Gebaerdensprache\',\'\',0);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">ausblenden</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'extra_sender_Gebaerdensprache\')==1)document.getElementById(\'options_link_extra_sender_Gebaerdensprache_an\').innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_extra_sender_Gebaerdensprache_aus\').innerHTML+=\' &#10008;\'; </script>
         
        <hr>
        
        ';
                 echo '<br><br>';
        if( !isset($fullscreen_play) || $fullscreen_play==2){
        echo '

        Videoplayer
         <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_video_direktlink_aus" onClick="createCookie(\'video_direktlink\',\'\',0);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">an</a>&nbsp;&nbsp;&nbsp; oder  &nbsp;&nbsp;&nbsp;<a href="#" id="options_link_video_direktlink_an" onClick="createCookie(\'video_direktlink\',\'1\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">aus (nur Link)</a> 
              
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'video_direktlink\')==1)document.getElementById(\'options_link_video_direktlink_an\').innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_video_direktlink_aus\').innerHTML+=\' &#10008;\'; </script>
         <div style="clear:both"></div>
                 <hr>
         ';
         }
         

        echo '

        Videoplayer-Technik  <span style="color:#999999; font-size:0.9em">Wenn bei alten TVs alle Video nicht laufen; Auch videoplayer aussschalten versuchen</span>
         <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; 
              <a href="#" id="options_videoplayer_version_2" onClick="createCookie(\'videoplayer_version\',\'2\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">Object-Tag</a>  &nbsp;&nbsp;&nbsp; oder  &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_videoplayer_version_3" onClick="createCookie(\'videoplayer_version\',\'3\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">Embed-Tag</a>  &nbsp;&nbsp;&nbsp; oder  &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_videoplayer_version" onClick="createCookie(\'videoplayer_version\',\'\',0);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">Video-Tag (Normal)</a>
              
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'videoplayer_version\')==2)document.getElementById(\'options_videoplayer_version_2\').innerHTML+=\' &#10008;\';else if(getCookie(\'videoplayer_version\')==3)document.getElementById(\'options_videoplayer_version_3\').innerHTML+=\' &#10008;\'; else document.getElementById(\'options_videoplayer_version\').innerHTML+=\' &#10008;\'; </script>
         <div style="clear:both"></div>
                 <hr>
         ';
         



        echo '
        <span style="float:right; text-align:right"></span>
        Feste Fußleiste 
        <span style="color:#999999; font-size:0.9em">&nbsp;hilfreich bei Maus-bedienung am Smart-TV um einfacher nach oben zu springen</span>
        <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_showFooter_aus" onClick="createCookie(\'showFooter\',\'\',0);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">ausblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_showFooter_an" onClick="createCookie(\'showFooter\',\'1\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">anzeigen</a>


              
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'showFooter\')==1)document.getElementById(\'options_link_showFooter_an\').innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_showFooter_aus\').innerHTML+=\' &#10008;\'; </script>
        <div style="clear:both"></div>';
        
echo '
        
        <hr>               
         Start/Schnellauswahl schneller laden (=nur Links anzeigen)<span style="color:#999999; font-size:0.9em"><i></i></span>
         <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_schnellauswahl_nurlinks_an" onClick="createCookie(\'schnellauswahl_nurlinks_an\',\'1\',356*10);setUpdateDate();optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">nur Links</a> &nbsp;&nbsp;oder&nbsp;&nbsp;
              <a href="#" id="options_schnellauswahl_nurlinks_aus" onClick="createCookie(\'schnellauswahl_nurlinks_an\',\'\',0);setUpdateDate();optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">mit Videoliste</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'schnellauswahl_nurlinks_an\')==1)document.getElementById(\'options_schnellauswahl_nurlinks_an\').innerHTML+=\' &#10008;\'; else document.getElementById(\'options_schnellauswahl_nurlinks_aus\').innerHTML+=\' &#10008;\'; </script>
        <div style="clear:both"></div>
';
echo '
        
        <hr>
       
        Vorschaltseite ausblenden (sihe Bild)
        <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp;<a href="#" id="options_link_vorschaltseite_an" onClick="createCookie(\'direkt_zur_mediathek_liste\',\'1\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">ausblenden</a> &nbsp;&nbsp;&nbsp; oder &nbsp;&nbsp;&nbsp;
              <a href="#" id="options_link_vorschaltseite_aus" onClick="createCookie(\'direkt_zur_mediathek_liste\',\'\',0);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">anzeigen</a>
              <script  language="javascript"  type="text/javascript"> if(getCookie(\'direkt_zur_mediathek_liste\')==1)document.getElementById(\'options_link_vorschaltseite_an\').innerHTML=\' ausblenden &#10008;\';else document.getElementById(\'options_link_vorschaltseite_aus\').innerHTML=\'anzeigen &#10008;\'; </script>
        </span>
         <img id="vorschaltseite_thumb" title="so siht die vorschaltseite aus" src="" data-src="img/vorschaltseite_320px.png" height="200" border="1" style="margin-left:15pt;float:right;border:dotted #555555 1pt;margin-right:5pt;margin-bottom:5pt;height:100pt;">
   
   
   
        <span style="clean:both"></span>
         <div style="clear:both"></div>
        
        ';
        
        


if($loaderAnimation>0)echo '  
        <hr>
         Performance: <span style="color:#555555; ">Lade-Animation </span><span style="color:#999999; font-size:0.9em"><i>Am TVs besser auslassen, wegen Performance</i></span>
         <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_spinner_an" onClick="createCookie(\'spinner_show\',\'1\',356*10);optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">Anschalten</a>, &nbsp;&nbsp;
              <a href="#" id="options_link_spinner_aus" onClick="createCookie(\'spinner_show\',\'\',0);optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">aus</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'spinner_show\')==1)document.getElementById(\'options_link_spinner_an\').innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_spinner_aus\').innerHTML+=\' &#10008;\'; </script>
        <div style="clear:both"></div>
';



echo '

        <hr>               
         Performance nur testweise: lange Themenlisten<span style="color:#999999; font-size:0.9em"><i>Möglicherweise besser??; Nur testweise drin; Funktion wird wieder entfernt</i></span>
         <span style="float:right; text-align:right">
              &nbsp;&nbsp;&nbsp; <a href="#" id="options_link_no_table_an" onClick="createCookie(\'no_table\',\'1\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">Textliste</a>, &nbsp;&nbsp;
              <a href="#" id="options_link_no_table_an2" onClick="createCookie(\'no_table\',\'2\',356*10);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">TextlisteTabelle</a>, &nbsp;&nbsp;
              <a href="#" id="options_link_no_table_aus" onClick="createCookie(\'no_table\',\'\',0);;optionsSafeLastUsesLinkAsHash(this);window.location.reload();return false;">Tabelle</a>
        </span>
        <script  language="javascript"  type="text/javascript"> if(getCookie(\'no_table\')==1)document.getElementById(\'options_link_no_table_an\').innerHTML+=\' &#10008;\';else if(getCookie(\'no_table\')==2)document.getElementById(\'options_link_no_table_an2\').innerHTML+=\' &#10008;\';else document.getElementById(\'options_link_no_table_aus\').innerHTML+=\' &#10008;\'; </script>
        <div style="clear:both"></div>

        
        
         <div style="clear:both"></div>
         <hr> Bedienungs-Hilfe für Hbb-TV:
         <div style="padding-left:20pt">
         Navigieren:   &nbsp;&nbsp; &#8594; &#8593; &#8592; &#8595; Pfeiltasten,  &nbsp;&nbsp; Zurück/Back
         <br>Farbtasten:  &nbsp;&nbsp; Gelb = Senderwahl,  &nbsp;&nbsp; Grün = Themenwahl,  &nbsp;&nbsp; Blau = Buchstabenwahl<br>
         Themenliste:
         <br>&nbsp;&nbsp;&nbsp;&nbsp;A-Z [ 1-9 Tasten ] für springen zum Buchstaben<br>
         Während Film: 
         <br>&nbsp;&nbsp;&nbsp;&nbsp;Play,  &nbsp;&nbsp;  &nbsp;&nbsp; Pause,  &nbsp;&nbsp; Vorwärts, &nbsp;&nbsp;  Rückwärts, &nbsp;&nbsp;  Stop
         <br>&nbsp;&nbsp;&nbsp;&nbsp;Pfeiltasten  &nbsp;&nbsp; &#8594; +30 &nbsp;&nbsp; &#8593; +60 &nbsp;&nbsp; &#8592; -30  &nbsp;&nbsp; &#8595; -60 &nbsp;&nbsp;  Sekunden springen
         </div>
         ';
         
        echo " <br><br>";
        ?>
