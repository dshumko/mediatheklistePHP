<?php
if($orf_filcheck_legal__filesize>0 && !file_exists('cache/orf_legalcheck')) mkdir('cache/orf_legalcheck');



function getFilmlistContentCache($file, $sender, $thema){
        global $use_cache_filmlist_sender, $use_cache_filmlist_thema; //Konfig, ob Cache aktiv ist
        
        $thema = str_replace('"','\\"',$thema);
        //finde richtig Cache-Datei (oder nehme komplette Liste)
        $file_cache_sender_thema = '';
        if($use_cache_filmlist_thema && isset($sender) && $sender!='' && isset($thema) && $thema!=''){
            $file_cache_sender_thema = 'cache/thema/cache_filmliste_';
            $file_cache_sender_thema.= substr(str_replace('/','',$sender),0,15).'_'.md5($thema);
        }
        
        $file_cache_sender = '';
        if(isset($sender) && $sender!='')
            $file_cache_sender = 'cache/sender/'.$file.'_sender_'.substr(str_replace('/','',$sender),0,15);
        if(       $use_cache_filmlist_thema && 
                  isset($sender) && $sender!='' && 
                  isset($thema) && $thema!='' &&
                  file_exists($file_cache_sender_thema)){
                        return file_get_contents($file_cache_sender_thema);
        }else if( $use_cache_filmlist_sender && 
                  isset($sender) && $sender!='' && $sender!='alle' &&
                  file_exists($file_cache_sender)){
                        return file_get_contents($file_cache_sender);       
        }else return NULL;      
} 



function checkIfVideoPlayable($url){ //betrifft nur normale Browser
        if( isset($url) && (substr($url,-5)=='.m3u8' || substr($url,-4)=='.m3u') ) return '<span class="notice_fileformat_not_playable">Nicht abspielbar im Browser?? (.m3u) </span>';
        else if( isset($url) && substr($url,-4)=='.flv') return ' <span class="notice_fileformat_not_playable">Nicht abspielbar im Browser?? (.flv)</span> ';
        else return '';
}


function createAllElements(){
        global $allOuts, $out1, $hideArte_fr, $debugTestMaxLineRead, $file, $maxJeSeite, $minLength, $remove_https_at_video_links, $dereff, $showSize, $showMinDSLSpeed, $letzterListeneintragOben, $use_cache_filmlist_sender, $use_cache_filmlist_thema, $fullscreen_play, $system_allow_exec_and_have_unxz, $orf_filcheck_legal__filesize, $maxRender;

        $allOuts = array();
        $out =''; //eine 
        $out1 = ''; //sammlung fuer spätere Ausgabe (vor der Liste)
        $i=0;
        $rendered_line_count=0;
        //if($cache_for_startseite_is_fresh===0){
        myFlush();


        $i = 0;
        //$handle = fopen($file, "r");
        //$chunk_size = 4000; //max length einer Zeile/Elements (beim Einlesen)
        //$readerBuffer = fread($handle,$chunk_size);

        $lineArray = array();
        if(isset($_GET['sender']) && ($_GET['sender'] == 'alle' ||  $_GET['sender']=='alle_ad' ||  $_GET['sender']=='alle_gebaerde') )$allSenderFromFilme = array();

        $lastSender = 'ohne';
        $lastThema = 'ohne';
        if( (!isset($_GET['sender']) ||$_GET['sender']=='' ) && (!isset($_COOKIE["favs"]) || strlen($_COOKIE["favs"])<5)  ){
             return; //leere Startseite (also ohne Schnellauswahl einträge)
        }
        $hasAndShowFavs = false;
        if( isset($_COOKIE["favs"]) && strlen($_COOKIE["favs"])>5  ){
             if( (!isset($_GET['sender']) ||$_GET['sender']=='' ) )    $hasAndShowFavs = true;
        }
        $beOnThemenSelect = false;
        if( isset($_GET['sender']) && $_GET['sender']!='' && (!isset($_GET['thema']) || $_GET['thema']=='') ) $beOnThemenSelect = true;

        /*if( 
            ( (!isset($_GET['sender']) ||$_GET['sender']=='' ) && (isset($_COOKIE["favs"]) && strlen($_COOKIE["favs"])<5) ) || 
            ( !isset($_GET['thema']) || $_GET['thema']=='' && (isset($_COOKIE["favs"]) && strlen($_COOKIE["favs"])<5) )
           ) $lineArray = array(); //KEINE  EInträge laden, bei: Auf der Startseite (ohne Cookies fuer Schnellauswahl), sowie bei der Thema-Auswahlseite 
        else*/


        if( $beOnThemenSelect ){}
        else {
                /* alt, muell:
                if( isset($_GET['thema']) && 
                    ( || !isset($_GET['sender']) || $_GET['sender']=='' || $_GET['sender']=='alle' || !file_exists('cache/sender/'.$file.'_sender_'.substr($_GET['sender'].'_'.md5($_GET['thema'])),0,15)) ) 
                elseif( !$use_cache_filmlist_sender || !isset($_GET['sender']) || $_GET['sender']=='' || $_GET['sender']=='alle' || !file_exists('cache/sender/'.$file.'_sender_'.substr($_GET['sender'],0,15)) ) $lineArray = explode('"X":', file_get_contents($file) );
                else{ */
                
                //zeige nur Schnellauswahl
                $is_in_schnellauwahl = false;$favs = array();
                if( $use_cache_filmlist_thema && !isset($_GET['sender']) && !isset($_GET['thema']) ){
                        if( isset($_COOKIE['favs']) )$favs = JSON_decode($_COOKIE['favs']); else $favs= array();
                        //foreach($favs as &$f)  $f = str_replace('x4sdy0ANDx4sdy0','&',$f);
                        $line0='';
                        $stringWithLines='';
                        foreach($favs as $f){
                                preg_match('/liste\.php\?sender=([^"]*)&thema=(.*)/',$f, $treffer);
                                $stringS = getFilmlistContentCache($file, $treffer[1], $treffer[2]);
                                if($stringS===NULL)continue;
                                
                                if($line0=='') $line0 = explode('"X":',$stringS)[0]; //"Filmliste:[FilmlistDatumk, etc]"
                                else { $stringS = ','.str_replace($line0,'',$stringS);}
                                $stringWithLines.= substr($stringS,0,-1);//ohne}
                        }
                        if($stringWithLines!='') $lineArray = explode('"X":', $stringWithLines.'}'); 
                        else                     $lineArray = explode('"X":', file_get_contents($file) );//wenn cache leer, nimm alle
                }else{
                        
                        //finde richtig Cache-Datei (oder nehme komplette Liste)
                        if($use_cache_filmlist_thema){
                                $lineArray = explode('"X":', getFilmlistContentCache($file, $_GET['sender'], $_GET['thema']) );
                                if( count($lineArray)<=1 && $lineArray[0]=='') $lineArray = explode('"X":', file_get_contents($file) ); //wenn cache leer, nimm alle
                        }else $lineArray = explode('"X":', file_get_contents($file) ); 
                        
                } 

            
                
                
        }


        //Gehe die Liste druch
        //$lastLine=''; //nur fuer testen
        foreach( $lineArray as $line ){
                $outArray = array( 
                                'title'=>'',
                                'titleNotice'=>'',
                                'mainlink'=>'',
                                'possibleThema'=>'',
                                'date_time'=>'',
                                'length'=>'',
                                'possibleSender'=>'',
                                'possibleAddationalDataAtNormalQ'=>'',
                                'desc'=>'',
                                'notice'=>'',
                                'videofiles_links' => array(),
                                'mediathekTitle'=>'',
                                'mediathekUrl'=>'' );
                
                 /* //Alternativ::Langsamer, geht dafür aber auch bei Servern mit wenig speicher:
                 while( strlen($readerBuffer)> 1000 || strpos($readerBuffer,'"X":')>0 ){
                 //Buffer auffüllen
                 if(strlen($readerBuffer)<4000 && !feof($handle)){ //fülle Buffer auf
                    $readerBuffer.= fread($handle,$chunk_size);
                 }
                 //echo '<br>'.$readerBuffer; if($i>100)exit;
                 //line einlesen
                 $posBeginn = strpos($readerBuffer,'"X":');
                 $lineRaw   = substr($readerBuffer,$posBeginn);
                 $lineArray = explode('"X":',$lineRaw);
                 $line = $lineArray[0];
                 if($lineArray[0]=='' && isset($lineArray[1])) $line = $lineArray[1];
                 //var_dump($lineArray);echo $line;
                 //if($i>5)die($line.'AA');
                 //lösche aus Buffer
                 ////echo $readerBuffer.'<hr>';
                 $readerBuffer = substr($readerBuffer,$posBeginn+strlen($line)+4);
                 ////echo $readerBuffer.'<hr>';
                 ////die();
                 */

                 $i++;
                 if($i==1)continue; //Titelzeile überspringen
                 if(isset($debugTestMaxLineRead) && $debugTestMaxLineRead>1 && $debugTestMaxLineRead==$i) $out1.= "<p>Im Testmodus: Lesen der Filmliste wurde nach $debugTestMaxLineRead Einträgen abgebrochen.</p>";
                 if(isset($debugTestMaxLineRead) && $debugTestMaxLineRead>1 && $debugTestMaxLineRead<$i)break; //Titelzeile überspringen
                 if( substr($line, -1, 1)==',')$line = substr($line, 0, strlen($line)-1);
                 $anker_i = $i;
                 if($anker_i<=6)$anker_i = 0;
                 else if($anker_i>6)$anker_i -=2;  //damit der aktuelle Eintrag nicht verdeckt ist von der Oberen Leiste
                 //Element einlesen (zuerst mit RegEx, später richtig mit JSON_Decode)
                 //$json_line = json_decode( ("{\"X\":".$line."}")); //11Sek.
                 if(substr($line,-1)=="}") $line=substr($line,0,strlen($line)-1); //letzte Zeile der Filmliste hat ein überflüssiges } am Ende (stört JSON)
                 $json_line = json_decode( ("{\"X\":".$line."}"));

                 //Trage Sender nach;
                 if(!isset($_GET['sender']) || $_GET['sender']=='' || $_GET['sender']=='alle' || $_GET['sender']=='alle_ad' || $_GET['sender']=='alle_gebaerde') {}
                 else $json_line->X[0] = substr($_GET["sender"],0,15); 

                 //thema
                 if( isset($json_line->X[1]) ){
                    $json_line->X[1] = trim($json_line->X[1]);//leerzeichen löschen
                    //$json_line->X[1] = str_replace('"',"'",$json_line->X[1]);//ohne ", sonst probleme im Javascript(JSON) später
                    //$json_line->X[1] = str_replace('#',"_",$json_line->X[1]);//ohne # wird als Anker interpretiert
                }


                //alternative Einleweg, aber am Ende nicht schneller, sondern lngsamer
                //$return = preg_match('/^\["([^"]*)","([^"]*)","([^"]*)"/',$line,$treffer); //8Sek
                //$return = preg_match('/^\["([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","([^"]*)","/',$line,$treffer);
                // var_dump( $line);echo'<br><hr>';var_dump($treffer); die("ww");
                /*
                if( $return==1 && isset($treffer) && isset($treffer[1]) && $treffer[1]!='' && isset($treffer[2]) && $treffer[2]!="" && isset($treffer[3]) && $treffer[3]!="" && isset($treffer[17]) ){
                    $a = array('X'=>array());
                    $json_line = (object) $a;
                    $json_line->X[0] = $treffer[1];
                    $json_line->X[1] = trim($treffer[2]);
                    $json_line->X[2] = $treffer[3];
                    $json_line->X[3] = $treffer[4];
                    $json_line->X[4] = $treffer[5];
                    $json_line->X[5] = $treffer[6];
                    $json_line->X[6] = $treffer[7];
                    $json_line->X[7] = $treffer[8];
                    $json_line->X[8] = $treffer[9];
                    $json_line->X[9] = $treffer[10];
                    $json_line->X[10] = $treffer[11];
                    $json_line->X[11] = $treffer[12];
                    $json_line->X[12] = $treffer[13];
                    $json_line->X[13] = $treffer[14];
                    $json_line->X[14] = $treffer[15];
                    $json_line->X[15] = $treffer[16];
                    $json_line->X[16] = $treffer[17];
                }else{
                    //wenn Inhalte(sender/thema) Fehlen - oder RegEx fehlgeschlagen (bspw. wegen " im Titel)
                        $json_line = json_decode( ("{\"X\":".$line."}")); //11Sek.
                    if(isset($json_line->X[1]))$json_line->X[1] = trim($json_line->X[1]); //da war schonmal ein leerzeichen davor
                }*/
                if(isset($json_line->X[1]))$json_line->X[1] = trim($json_line->X[1]); //da war schonmal ein leerzeichen davor


                if( !isset($json_line->X[0]) || $json_line->X[0]=='')      $json_line->X[0] = $lastSender;
                else if(  isset($json_line->X[0]) && $json_line->X[0]!='') $lastSender      = $json_line->X[0];
                if( !isset($json_line->X[1])  || $json_line->X[1]=='')     $json_line->X[1] = $lastThema;
                else if(  isset($json_line->X[1]) && $json_line->X[1]!='') $lastThema       = $json_line->X[1];


                if(!isset($json_line->X[2])){ $json_line->X[2] = 'Ohne';}

                if( isset($hideArte_fr) && $hideArte_fr==1 && $json_line->X[0] == "arte.fr")continue; //ausblenden 


                $json_line->X[0] = strtolower($json_line->X[0]);
                $aktuellerSender = $json_line->X[0];
                $json_line->X[1] = trim($json_line->X[1]); //zur sicherheit ein zweites mal
                 
                if( isset($json_line->X[5]) && $json_line->X[5]!=''){
                     $e = explode(':',$json_line->X[5]); 
                     $l = $e[0]*60+$e[1];
                     if( !isset( $allLengths[$l]) )   $allLengths[$l] = 0;
                     $allLengths[ $l ]++; 
                }
                  
                //Aus-Filter nach Länge
                if( isset($l) && $l!=='' && isset($_GET['filter_minFilmLength']) && $_GET['filter_minFilmLength']!='' && $_GET['filter_minFilmLength']>$l)continue; 
                if( isset($l) && $l!=='' && isset($_GET['filter_maxFilmLength']) && $_GET['filter_maxFilmLength']!='' && $_GET['filter_maxFilmLength']<$l)continue; 
                //Wortsuche
                if( isset($_GET['search']) && $_GET['search']!=''){
                    $s = $_GET['search'];
                    if(isset($_GET['search_fulltext']) && $_GET['search_fulltext']==1 ) $ft = true; else $ft = false;
                    if(             stristr($json_line->X[1], $s) ) { echo '';}
                    else if(        stristr($json_line->X[2], $s) ) { echo '';}
                    else if( $ft && stristr($json_line->X[7], $s) ) { echo '';}
                    else continue; //nicht gefunden
                }
                 

                        
                        
                //zeige nur Schnellauswahl
                $is_in_schnellauwahl = false;$favs = array();
                if( !isset($_GET['sender']) && !isset($_GET['thema']) ){
                  if( isset($_COOKIE['favs']) )$favs = JSON_decode($_COOKIE['favs']); else $favs= array();
                  foreach($favs as &$f)  $f = str_replace('x4sdy0ANDx4sdy0','&',$f);
                  if(array_search('liste.php?sender='.$json_line->X[0].'&thema='.$json_line->X[1],$favs) !==FALSE) $is_in_schnellauwahl = true;
                  elseif(array_search('liste.php?sender=alle&thema='.$json_line->X[1],$favs) !==FALSE) $is_in_schnellauwahl = true;
                  else $is_in_schnellauwahl = false;
                }        
            
                $linkHd = '';
                $linkLow = '';
                $linkVeryLow = '';
                    
                if( isset($json_line->X[12]) && $json_line->X[12]!=''){            
                        $e = explode('|',$json_line->X[12]);
                        if(strtolower($json_line->X[0])=='zdf')$link = substr($json_line->X[8],0,$e[0]+1).$e[1];
                        if(count($e)>1) $linkLow = substr($json_line->X[8],0,$e[0]).$e[1];
                        else $linkLow = $json_line->X[8].$json_line->X[12];
                        if($remove_https_at_video_links)$linkLow = str_replace('https://','http://',$linkLow); 
                }

                if( isset($json_line->X[14]) && $json_line->X[14]!=''){            
                        $e = explode('|',$json_line->X[14]);
                        if(strtolower($json_line->X[0])=='zdf')$link = substr($json_line->X[8],0,$e[0]+1).$e[1];
                        if(count($e)>1) $linkHd = substr($json_line->X[8],0,$e[0]).$e[1];
                        else $linkHd = $json_line->X[8].$json_line->X[14];
                        if($remove_https_at_video_links)$linkHd = str_replace('https://','http://',$linkHd);
                }
                
                
                
               //jetzt die Zeile die darstellt (nicht alle werden dargestellt, wenn liste zu lang werden würde)
               if( (isset($_GET['sender']) && ( $_GET['sender']=='alle' || $_GET['sender']=='alle_ad' || $_GET['sender']=='alle_gebaerde' || strtolower($_GET['sender']) == $aktuellerSender)) || $is_in_schnellauwahl ){
                   if($rendered_line_count>=$maxRender)continue;
                   if( !isset($_GET['thema']) || $_GET['thema']=='alle' ||  $_GET['sender']=='alle_ad' ||  $_GET['sender']=='alle_gebaerde' || 
                        strtolower($_GET['thema']) == strtolower($json_line->X[1]) ||
                        (isset($_GET['search']) && $_GET['search']!='') || 
                   $is_in_schnellauwahl ){

                      $out = "<tr>";
                  
                      $link = urlencode($json_line->X[8]);
                      $linkMain = $json_line->X[8];
                      /* verschoben in Javascript
                      if(isset($_GET['quality']) && $_GET['quality']!='normal' && $_GET['quality']!=''){
                          if($_GET['quality']=='low' && $linkLow!=''){
                                  $linkMain = $linkLow;
                                  if( ( $outArray['titleNotice'].= checkIfVideoPlayable($json_line->X[12]) )!=''){ //vlt. ist ja eine der anderen VideoVersionen abspielbar
                                          if( isset($json_line->X[8]) && $json_line->X[8]!='' && checkIfVideoPlayable($json_line->X[8])=='' ) $outArray['titleNotice'] = 'Bitte VideoLinks klicken. '.$outArray['titleNotice'];
                                          else if( isset($json_line->X[14]) && $json_line->X[14]!='' && checkIfVideoPlayable($json_line->X[8])=='' ) $outArray['titleNotice'] = 'Bitte VideoLinks klicken. '.$outArray['titleNotice'];
                                  }
                          }
                          if($_GET['quality']=='hd'  && $linkHd!='' ){
                                  $linkMain = $linkHd;
                                  if( ( $outArray['titleNotice'].= checkIfVideoPlayable($json_line->X[14]) )!=''){ //vlt. ist ja eine der anderen VideoVersionen abspielbar
                                          if( isset($json_line->X[8]) && $json_line->X[8]!='' && checkIfVideoPlayable($json_line->X[8])=='' ) $outArray['titleNotice'] = 'Bitte VideoLinks klicken. '.$outArray['titleNotice'];
                                          else if( isset($json_line->X[12]) && $json_line->X[12]!='' && checkIfVideoPlayable($json_line->X[8])=='' ) $outArray['titleNotice'] = 'Bitte VideoLinks klicken. '.$outArray['titleNotice'];
                                  }
                          }
                          
                      }else{
                              if( ( $outArray['titleNotice'].= checkIfVideoPlayable($json_line->X[8]) )!=''){}
                      }*/
                      if( ( $outArray['titleNotice'].= checkIfVideoPlayable($json_line->X[8]) )!=''){}
                      if($remove_https_at_video_links)$json_line->X[8] = str_replace('https://','http://',$json_line->X[8]); 
                      $href = $dereff.$linkMain;
                      if(isset($fullscreen_play) && $fullscreen_play==1) $href = "video.php#".( $linkMain );

                      
                                  
                      
                      $outArray['mainlink'].= $href;
                      $Y = date('Y');
                      //Prüfe ob der Film von Sender orf in Deutschland ansehbar ist
                      if($json_line->X[0]=='orf' && $orf_filcheck_legal__filesize>0 && $system_allow_exec_and_have_unxz){ //check Legal
                          unset($return);
                          exec("cat cache/orf_legalcheck/whitelist$Y | grep '^".$json_line->X[8]."'",$return);
                          if( count($return)==0 ){
                             unset($return);
                             exec("cat cache/orf_legalcheck/blacklist$Y | grep '^".$json_line->X[8]."'",$return);
                             if( count($return)>0 ){
                                 $outArray['titleNotice'].= "<span style=\"float:left\">Nicht in Deutschland ansehbar. &nbsp;</span>"; //&#9401;
                             }else if( count($return)==0 ){
                                   
                                 //Prüfe anhand der Dateigröße
                                 unset($return);
                                 exec("curl -s -X HEAD -I --connect-timeout 10 -m 10 ".$json_line->X[8]." 2>&1  | grep '^Content-Length: '",$return);
                                 if( count($return)==0 || (strstr($return[0],'Content-Length')!=='' && str_replace('Content-Length: ','',$return[0])<= ($orf_filcheck_legal__filesize*1.5)) )
                                 {
                                   $outArray['titleNotice'].= "<span style=\"float:left\"><u>Nicht</u> in Deutschland &nbsp;</span>"; 
                                   file_put_contents("cache/orf_legalcheck/blacklist$Y", $json_line->X[8].PHP_EOL , FILE_APPEND);
                                 }else file_put_contents("cache/orf_legalcheck/whitelist$Y", $json_line->X[8].PHP_EOL , FILE_APPEND);
                             }
                          }
                      } //ende if  //check Legal

                      if(isset($json_line->X[1]) && !isset($_GET['thema']) ) $outArray['possibleThema'] = $json_line->X[1].' - '; //Thema
                      if(isset($json_line->X[2]) )$outArray['title'] .= $json_line->X[2]; //Titel
                      if(!isset($json_line->X[8]) || $json_line->X[8] =='') $outArray['notice'].= " <br><b style=\"\">Fehler: Kann nicht abgespielt werden</b> ";
                      
                      //datum
                      $outArray['date_time'].= "".$json_line->X[3].'';
                          
                      //uhrzeit
                      if($json_line->X[4]!='') $e = explode(':', $json_line->X[4]);
                      if($json_line->X[4]!='') $outArray['date_time'].= " ".$e[0].':'.$e[1];

                      //dauer
                      if($json_line->X[5]!=''){
                             $e = explode(':',$json_line->X[5]); 
                             $l = $e[0]*60+$e[1];
                             $outArray['length'].= ', <span class="film_length">'.$l.'</span> Min.';
                      }
                      if(isset($l) && $minLength>0 && $l<$minLength && isset($_GET['sender']) && $_GET['sender']!='alle_ad' && $_GET['sender']!='alle_gebaerde'){ unset($outArray); continue; }

                          if( isset($_GET['sender']) && ($_GET['sender']=='alle' ||  $_GET['sender']=='alle_ad' ||  $_GET['sender']=='alle_gebaerde') ){
                          $outArray['possibleSender'] = ' '.$json_line->X[0].' ';        
                      }

                      //Dateigröße
                      if($showSize == 1 && $json_line->X[6]!=''){
                            $outArray['possibleAddationalDataAtNormalQ'] .= ', Datei: '.$json_line->X[6].'MB ';
                      }
                      //mindest DSL Speed
                      if($showMinDSLSpeed == 1 && $json_line->X[6]!='' && $json_line->X[5]!=''){
                          $e = explode(":", $json_line->X[5]);
                          $duration_Seconds = ($e[0]*60*60)+($e[1]*60)+$e[2];
                          $mb_je_sec = $json_line->X[6]/$duration_Seconds; //durch MB
                          $mbit_je_sec = $mb_je_sec *8;
                          $outArray['possibleAddationalDataAtNormalQ'] .= " &nbsp; min.DSL: ".number_format($mbit_je_sec,1,',','.'). " Mbit";
                      }

                      $outArray['desc'] = $json_line->X[7];
                      $outArray['videofiles_links'] = array();
                        
                      //$out.=var_dump($json_line->X,true);
                      /* array(20) { [0]=> string(6) "Sender" [1]=> string(5) "Thema" [2]=> string(5)
                      "Titel" [3]=> string(5) "Datum" [4]=> string(4) "Zeit"
                      [5]=> string(5) "Dauer" [6]=> string(12) "GrÃ¶ÃŸe [MB]" [7]=> string(12) "Beschreibung" 
                      [8]=> string(3) "Url" [9]=> string(7) "Website" [10]=> string(14) "Url Untertitel"
                      [11]=> string(8) "Url RTMP" [12]=> string(9) "Url Klein" [13]=> string(14) "Url RTMP Klein"
                      [14]=> string(6) "Url HD" [15]=> string(11) "Url RTMP HD" [16]=> string(6) "DatumL"
                      [17]=> string(11) "Url History" [18]=> string(3) "Geo" [19]=> string(3) "neu" } */

                        
                      if(   ( isset($json_line->X[8]) && strstr($json_line->X[8],'ndr.de/') ) ||
                            ( isset($json_line->X[9]) && strstr($json_line->X[9],'ndr.de/') ) 
                          ){
                          if(strstr($json_line->X[8],'.hq.mp4') || strstr($json_line->X[8],'.hi.mp4') ){
                                $linkVeryLow = str_replace('.hq.mp4','.lo.mp4',$json_line->X[8]);
                                $linkVeryLow = str_replace('.hi.mp4','.lo.mp4',$linkVeryLow);
                                    if($remove_https_at_video_links)$linkVeryLow = str_replace('https://','http://',$linkVeryLow);
                                $href = $dereff.$linkVeryLow;
                                if(isset($fullscreen_play) && $fullscreen_play==1) $href = "video.php#".($linkVeryLow);  
                                $outArray['videofiles_links']['sehrKlein?'] = $href;
                          }
                      }
                      
                      if(   ( isset($json_line->X[8]) && strstr($json_line->X[8],'zdf.de/') ) ||
                            ( isset($json_line->X[9]) && strstr($json_line->X[9],'zdf.de/') ) 
                          ){
                          if(strstr($json_line->X[8],'_2436k_p9v11.mp4') || strstr($json_line->X[8],'_2328k_p35v11.mp4') ){
                                $linkVeryLow = str_replace('_2436k_p9v11.mp4','_928k_p34v11.mp4',$json_line->X[8]);
                                $linkVeryLow = str_replace('_2328k_p35v11.mp4','_928k_p34v11.mp4',$linkVeryLow);
                                    if($remove_https_at_video_links)$linkVeryLow = str_replace('https://','http://',$linkVeryLow);
                                $href = $dereff.$linkVeryLow;
                                if(isset($fullscreen_play) && $fullscreen_play==1) $href = "video.php#".($linkVeryLow);  
                                $outArray['videofiles_links']['sehrKlein?'] = $href;
                          }
                      }
                      
                      
                          
                      if(isset($json_line->X[12]) && $json_line->X[12]!=''){
                        $href = $dereff.$linkLow;
                        if(isset($fullscreen_play) && $fullscreen_play==1) $href = "video.php#".($linkLow);  
                        $outArray['videofiles_links']['Klein'] = $href;
                      }

                      $href = $dereff.urlencode($json_line->X[8]);
                      if($remove_https_at_video_links)$href = str_replace('https://','http://',$href);
                      if(isset($fullscreen_play) && $fullscreen_play==1) $href = "video.php#".($json_line->X[8]);  
                      $outArray['videofiles_links']['Normal'] = $href;
                       
                      if(isset($json_line->X[14]) && $json_line->X[14]!=''){
                        $href = $dereff.$linkHd;
                        if(isset($fullscreen_play) && $fullscreen_play==1) $href = "video.php#".($linkHd);  
                        $outArray['videofiles_links']['HD'] = $href;
                      }
                      
                      if(isset($json_line->X[13]) && $json_line->X[13]!='')$outArray['videofiles_links']['RTMP klein'] = $dereff.$json_line->X[13];
                      /* wenn mehr als ein Videolink + verschiedene Formate */
                      $type = '';
                      if( count($outArray['videofiles_links'])>1){
                          foreach($outArray['videofiles_links'] as $name => $url){
                              if($type=='' || $type==substr($url,-4))$type = substr($url,-4);
                              else{
                                      //verschiedene Formate
                                      foreach($outArray['videofiles_links'] as $videoName => $videoUrl){
                                              $outArray['videofiles_links']['<nobr>'.$videoName .' <small>'.substr($videoUrl,-4).'</small></nobr>'] = $videoUrl;
                                              unset($outArray['videofiles_links'][$videoName]); //old
                                      }
                                      break;
                              }
                          }
                      }
                      
                      
                      if(isset($json_line->X[11]) && $json_line->X[11]!='')$outArray['videofiles_links']['RTMP'] = $dereff.$json_line->X[11];
                      if(isset($json_line->X[15]) && $json_line->X[15]!='')$outArray['videofiles_links']['RTMP HD'] = $dereff.$json_line->X[15];
                  
                      /* wofuer?
                      if(isset($_GET['search']) && $_GET['search']!=''){
                          $href = "liste.php?sender=".$json_line->X[0]."&thema=".rawurlencode($json_line->X[1])."";
                          if( $_GET["quality"]!='' ) $s2 ="&quality=".$_GET["quality"]; else $s2 = '';
                          $out.= "<a href=\"$href$s2\" >".$json_line->X[1]."</a><br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;";
                      }*/
                      $ssonderz=''; if( isset($json_line->X[9]) && $json_line->X[9]=='http://www.srf.ch/play/tv/sendungen')$ssonderz='~';
                      if( isset($json_line->X[9]) ){
                          $outArray['mediathekTitle'] = "Mediathek$ssonderz";
                          $outArray['mediathekUrl'] = $dereff.$json_line->X[9];
                      }
                        


                      $rendered_line_count++;
                      if($rendered_line_count==$maxJeSeite){
                      //echo      ("<span id=\"notice\"><p>Liste wurde zu lang (abgebrochen bei $rendered_line_count)</p></span>");
                      $out.= ("<span id=\"notice\"><p>Liste wurde zu lang (abgebrochen bei $rendered_line_count)</p></span>");

                        echo "
                        <script language=\"javascript\"  type=\"text/javascript\">
                                //verschiebe die \"notice\" nach oberhalb der Liste
                            var newParent = document.getElementById('start_list_beginn');
                            var noticeElement = document.getElementById('notice');
                                    if(noticeElement!=undefined)newParent.appendChild(noticeElement);
                        </script>
                        ";
                      }
                      if($rendered_line_count==$maxRender){
                          //echo      ("<span id=\"notice\"><p>Liste wurde zu nicht (abgebrochen beim sortieren bei $rendered_line_count)</p></span>");
                          $out.= ("<span id=\"notice\"><p>Liste wurde zu lang (abgebrochen beim sortieren bei $rendered_line_count)</p></span>");
                      }
                    
                      if( isset($_GET['sender']) && ($_GET['sender'] == 'alle' || $_GET['sender']=='alle_ad' || $_GET['sender']=='alle_ad')) $allSenderFromFilme[ $json_line->X[0] ] = $json_line->X[0];
                    }
                    if($outArray['title']!='')$allOuts[ $json_line->X[16] ][] = $outArray;
                 }
        }//end foreach
        //}//end if($cache_for_startseite_is_fresh==0)
        if( isset($_GET['sender']) && ($_GET['sender'] == 'alle' ||  $_GET['sender']=='alle_ad' ||  $_GET['sender']=='alle_gebaerde') && count($allSenderFromFilme)>0 )echo "<script language=\"javascript\"  type=\"text/javascript\">document.getElementById('sender_waehlen_append').innerText = '".implode(', ',$allSenderFromFilme)."' </script>";
        return $allOuts;
        
}//end function create All Elements



function onFilmlisteSeite_linkAddToSchnellauswahl(){
        //Schnellauswahl hinzufügen (Link auf der Themseite selbst)
        $senderUrlPart = '';
        if(isset($_GET['sender']) && $_GET['sender']!='') $senderUrlPart = 'sender='.$_GET['sender'].'&';
        $href = "liste.php?".$senderUrlPart."thema=".rawurlencode($_GET['thema'])."";
        if( isset($_COOKIE['favs']) ) $favs = JSON_decode($_COOKIE['favs']); else $favs = array();
        $s2 = '';
        if( isset($_GET["search"]) && $_GET["search"]!='' ) $s2 .= "&search=".rawurlencode($_GET['search']);
        if( isset($_GET["search_fulltext"]) && $_GET["search_fulltext"]!='' ) $s2 .= "&search_fulltext=".$_GET['search_fulltext'];
        if( isset($_GET["filter_minFilmLength"]) && $_GET["filter_minFilmLength"]!='' ) $s2 .= "&filter_minFilmLength=".$_GET['filter_minFilmLength'];
        if( isset($_GET["filter_maxFilmLength"]) && $_GET["filter_maxFilmLength"]!='' ) $s2 .= "&filter_maxFilmLength=".$_GET['filter_maxFilmLength'];

        $onClick = "if(appendFavDataHrefSelf(this)){this.innerText='★...später in der Liste';}";
        echo "<span style=\"text-align:right\">";
        echo "<a href=\"#\" data-href=\"$href\" class=\"schnellausw_h t_sel_add_schnellauswahl link_every_same_color_underl link_every_same_color\" onClick=\"appendFavDataHrefSelf(this);return false;\">zur Schnellauswahl hinzufügen</a>\n";
        echo "<span style=\"clear:both\"></span> &nbsp;</span>\n";
        echo "<br>\n";

        
}
