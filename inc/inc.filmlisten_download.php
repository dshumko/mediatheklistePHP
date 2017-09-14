<?php


function filmlist_download_and_extract_exec_getcommand($filmlisteUrl, $file, $step='all'){
    $command = array();
    if($step=='download' || $step = 'all'){
           $command[] = 'if [ -e "cache/status_startFilmlistenExtract" ]; then exit; else touch cache/status_startFilmlistenDownload; fi'; //läuft parall noch entpacke?
           //echo "";
           $command[] = 'rm -f '.$file.'_old.xz';
           $command[] = 'mv '.$file.'.xz '.$file.'_old.xz';
           $command[] = 'curl -s -X HEAD -I '.$filmlisteUrl.' 2>&1  | grep \'^Last-Modified:\' > cache/status_lastFilmlistenFileModified_temp';
           $command[] = 'if [ -e "cache/status_startFilmlistenDownload" ]; then touch \'cache/status_startFilmlistenDownload\'; wget '.$filmlisteUrl.'; else exit; fi';
           $command[] = 'if [ -e "'.$file.'" ]; then rm -f '.$file.'_old; fi'; //lösche alte Backup-Datei von Filmliste
           $command[] = 'mv '.$file.' '.$file.'_old'; //speichere alte Filmliste als Backup (Dateiname)
           $command[] = 'mv -f cache/status_lastFilmlistenFileModified_temp cache/status_lastFilmlistenFileModified';
           $command[] = 'rm -f cache/status_newFilmlisteFileVorhanden';
           $command[] = 'rm -f cache/status_startFilmlistenDownload';
    }
    if($step=='extract' || $step = 'all'){
        $command[] = 'if [ -e "cache/status_startFilmlistenExtract" ]; then echo ""; else touch cache/status_startFilmlistenExtract; fi';
        $command[] = 'if [ -e "cache/status_startFilmlistenExtract" ]; then unxz -k Filmliste-akt.xz; fi'; //-k == OrginalDatei behalten
        $command[] = 'rm -f cache/status_startFilmlistenExtract';
    }
    return implode(';',$command).';';
}





function downloadTheFileAndExtract($webServiceUrl, $filepath, $cloud_convert_apikey, $filmlisteUrl){
      //Webserice lädt die Datei runter und sendet Sie entpackt zurück.
      $ch = curl_init($webServiceUrl);
      curl_setopt($ch, CURLOPT_HEADER, 0); //darf keine Header sein (wird sonst mit gespeichert in Filmliste-akt)
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_POST, 1);
      $data = array("apikey"=>$cloud_convert_apikey, "inputformat"=>"xz", "outputformat"=>"*", "mode"=>"extract", "input"=>"download", "file"=>$filmlisteUrl, "wait"=>"true", "download"=>"inline");
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

      $raw_file_data = curl_exec($ch);
      //echo substr($raw_file_data, 0,1000);
      if(curl_errno($ch)){
         echo substr($raw_file_data, 0,500);
         echo 'error:' . curl_error($ch);
         return false;
      }
      curl_close($ch);

      file_put_contents($filepath, $raw_file_data);
      return false;
}
   

function is_audiodeskription($title_raw){
  if( stristr($title_raw, 'hörfassung')!=FALSE || stristr($title_raw,'Audiodeskription')!=FALSE  || stristr($title_raw,'AD |')!=FALSE ) return true;
  else return false;
}


function is_gebaerdensprache($title_raw){
  if( stristr($title_raw, 'gebärdensprache')!=FALSE ) return true;
  else return false;
}




/**
* Erstellt Cache
* a)Liste aller Sender
* b)Liste aller Themen
* Dannach wird die Filmliste in mehrer einzelne Dateien aufgeteilt
* c)je Sender
* d)je Thema
*/
function createCopyEachSender($file,$options,$minLength){
        global $use_cache_filmlist_sender, $use_cache_filmlist_thema; //Konfig, ob Cache aktiv ist
        
        //$hideTrailer   = 0; if($options['hideTrailer'])     $hideTrailer     = $options['hideTrailer'];
        $hideArte_fr     = 0; if($options['hideArte_fr'])     $hideArte_fr     = $options['hideArte_fr'];
        $extra_audiodeskription     = 0; if(isset($options['extra_audiodeskription']))     $extra_audiodeskription     = $options['extra_audiodeskription'];
        $extra_gebaerdensprache     = 0; if(isset($options['extra_gebaerdensprache']))     $extra_gebaerdensprache     = $options['extra_gebaerdensprache'];
        //$hideHoerfassung = 0; //lasse ich später ausblenden if($options['hideHoerfassung']) $hideHoerfassung = $options['hideHoerfassung'];
        $minLengthVorlagenMinuten = 0; if($options['minLengthVorlagenMinuten'])     $minLengthVorlagenMinuten     = $options['minLengthVorlagenMinuten'];
        
        if(!file_exists($file)){echo '<p><b>Fehler:</b>'.$file.' nicht gefunden </p>';return;}
        $lastSender = '';
        $lastThema = '';
        $lineArray = explode('"X":', file_get_contents($file) );
        array_push($lineArray, '["zzz","zzz","zzz","zzz"]'); //leere Endzeile (damit auch letzter gespeichert wird)
        $lineSum = '';
        $senderlist = array();
        $senderliste_withMinLength = array();
        $themenlist = array();
        $themen_withMinLength  = array(); //themenliste (wenn Mindest-Filmlänge angegeben)
        $i=0;
        $line0 = ''; //erste Zeile von Filmliste mit Datum/Spaltennamen //'{"Filmliste":[""],"Filmliste":[""],'
        $outlines = ''; //sammelt eintraege eines Themas; wird bei jeden Themawechsel abgespeichert und geleert
        $outlines_extra_audiodeskription = '';
        $outlines_extra_gebaerdensprache = '';
        if(!file_exists('cache'))         mkdir('cache');
        if( file_exists('cache/thema'))   delTree('cache/thema');
        if( file_exists('cache/thema'))   rmdir('cache/thema');
        if(!file_exists('cache/thema'))   mkdir('cache/thema');
        if( file_exists('cache/sender'))  delTree('cache/sender');
        if( file_exists('cache/sender'))  rmdir('cache/sender');
        if(!file_exists('cache/sender'))  mkdir('cache/sender');
        foreach( $lineArray as $line){
                $i++;
                if($i==1) {$line0 = $line; continue;}

                //Sender und Thema auslesen
                $return = preg_match('/\["([^"]*)","(.*)","(.*)","/U',$line,$treffer); //mit Ungierig (U) arbeiten, weil sonst Themen mit " drin Probleme machen
                $sender_raw  = trim( strtolower( ''.$treffer[1].'') );
                $thema_raw  = ''.$treffer[2].'';
                $title_raw  = ''.$treffer[3].'';
                $i++;


             

                //Länge/Dauer auslesen (bislang haben nur die wenigsten eine Längenangabe); in Minuten
                $laenge = '';
                $return = preg_match('/\["[^"]*","[^"]*",".*","([0-9][0-9]\.[0-9][0-9]\.[0-9][0-9][0-9][0-9])?","([0-9][0-9]:[0-9][0-9]:[0-9][0-9])?","([0-9][0-9]:[0-9][0-9]:[0-9][0-9])","/U',str_replace('\"','',$line),$treffer);
                //if(strstr($thema_raw,'moma vom')!==false) {var_dump($treffer);echo $line;die("aaaaaaaaaaa       !!");}
                if( count($treffer)>3 && $treffer[3]!=''){
                     $e = explode(':',$treffer[3]);
                     if( count($e)==3){  
                       $laenge = $e[0]*60;
                       $laenge+=$e[1];
                       $laenge+=((60/100)*$e[2])/100;
                     }
                }

                if($sender_raw=='') $s = $lastSender; else $s = ''.$sender_raw.'';
                if($thema_raw=='')  $t = $lastThema;  else $t = ''.$thema_raw.'';
                if( !isset($senderlist[$s]) && $s!='zzz')$senderlist[$s] = 0;
                if($s!='zzz')$senderlist[$s]++;
            
                //filter
                
                if( isset($hideArte_fr) && $hideArte_fr==1 && $s == "arte.fr"){
                if($sender_raw!='')$lastSender = $sender_raw;
                if($thema_raw!='') $lastThema  = $thema_raw;
                continue;} //ausblenden
                
                //verstecke Arte.fr in "alle"
                //$saveInAll = true;
                //if(isset($hideArte_fr) && $hideArte_fr==2 && $s == "arte.fr") $saveInAll = false;

                
               
                if($use_cache_filmlist_thema){
                    if($t!= $lastThema && $outlines!='') {  //themwechsel; Speicher ggf. Cache von letzten Thema ab
                        if($outlines[strlen($outlines)-1]==',')$outlines=substr($outlines,0,strlen($outlines)-1); //lösche letzte ,  am Ende
                        $outlines=str_replace('["","','["'.$lastSender.'","',$outlines);
                        file_put_contents('cache/thema/cache_filmliste_'.$lastSender.'_'.md5($lastThema), $line0."".$outlines."}");
                        
                        //verstecke Arte.fr in "alle"
                        $saveInAll_lastSender = true;
                        if(isset($hideArte_fr) && $hideArte_fr==2 && $lastSender == "arte.fr") $saveInAll_lastSender = false;
                        
                        //nun fuer alle sender
                        if( $saveInAll_lastSender ){ 
                            if( file_exists('cache/thema/cache_filmliste_alle_'.md5($lastThema)) ){ //öffnet alten Themen-Cache beim Sender "alle"
                                $old = file_get_contents('cache/thema/cache_filmliste_alle_'.md5($lastThema));
                                $saveContent = substr($old,0,-1).",".$outlines."}"; //die("aaa".$lastSender.$lastThema.md5($lastThema));  
                            }else $saveContent = $line0."".$outlines."}";
                            file_put_contents('cache/thema/cache_filmliste_alle_'.md5($lastThema), $saveContent);  
                        }
                        $outlines = '';  
                        
                        //extra liste für Audiodeskription / Hörfassung
                        if( $extra_audiodeskription==1 && $outlines_extra_audiodeskription!='' ){
                            if($outlines_extra_audiodeskription[strlen($outlines_extra_audiodeskription)-1]==',')$outlines_extra_audiodeskription=substr($outlines_extra_audiodeskription,0,strlen($outlines_extra_audiodeskription)-1); //lösche letzte ,  am Ende
                            $outlines_extra_audiodeskription=str_replace('["","','["'.$lastSender.'","',$outlines_extra_audiodeskription);
                            //die('A'.$lastThema.$outlines_extra_audiodeskription.' '.md5($lastThema));
                            //nun fuer alle sender
                            if( file_exists('cache/thema/cache_filmliste_alle_ad_'.md5($lastThema)) ){ //öffnet alten Themen-Cache beim Sender "alle"
                                $old = file_get_contents('cache/thema/cache_filmliste_alle_ad_'.md5($lastThema));
                                $saveContent = substr($old,0,-1).",".$outlines_extra_audiodeskription."}"; //die("aaa".$lastSender.$lastThema.md5($lastThema));  
                            }else $saveContent = $line0."".$outlines_extra_audiodeskription."}";
                            file_put_contents('cache/thema/cache_filmliste_alle_ad_'.md5($lastThema), $saveContent);
                            $outlines_extra_audiodeskription = '';
                        }
                        
                        //extra liste für Gebährdensprache
                        if( $extra_gebaerdensprache==1 && $outlines_extra_gebaerdensprache!='' ){
                            if($outlines_extra_gebaerdensprache[strlen($outlines_extra_gebaerdensprache)-1]==',')$outlines_extra_gebaerdensprache=substr($outlines_extra_gebaerdensprache,0,strlen($outlines_extra_gebaerdensprache)-1); //lösche letzte ,  am Ende
                            $outlines_extra_gebaerdensprache=str_replace('["","','["'.$lastSender.'","',$outlines_extra_gebaerdensprache);
                            //die('A'.$lastThema.$outlines_extra_gebaerdensprache.' '.md5($lastThema));
                            //nun fuer alle sender
                            if( file_exists('cache/thema/cache_filmliste_alle_gebaerde_'.md5($lastThema)) ){ //öffnet alten Themen-Cache beim Sender "alle"
                                $old = file_get_contents('cache/thema/cache_filmliste_alle_gebaerde_'.md5($lastThema));
                                $saveContent = substr($old,0,-1).",".$outlines_extra_gebaerdensprache."}"; //die("aaa".$lastSender.$lastThema.md5($lastThema));  
                            }else $saveContent = $line0."".$outlines_extra_gebaerdensprache."}";
                            file_put_contents('cache/thema/cache_filmliste_alle_gebaerde_'.md5($lastThema), $saveContent);
                            $outlines_extra_gebaerdensprache = '';
                        }     
                        
                    } //ende if themenwechsel

                    if($outlines=='') $outlines .="\"X\":[\"".substr($line,2,strlen($line));
                    else              $outlines .="\"X\":".$line;
                }
                
                
                if( is_audiodeskription($title_raw) ){
                  if($outlines_extra_audiodeskription=='') $outlines_extra_audiodeskription .="\"X\":[\"".substr($line,2,strlen($line));
                  else              $outlines_extra_audiodeskription .="\"X\":".$line;
                }
                if( is_gebaerdensprache($title_raw) ){ 
                  if($outlines_extra_gebaerdensprache=='') $outlines_extra_gebaerdensprache .="\"X\":[\"".substr($line,2,strlen($line));
                  else              $outlines_extra_gebaerdensprache .="\"X\":".$line;
                    //if( stristr($title_raw,'MAX gies'))die('a'.$title_raw.$outlines_extra_gebaerdensprache.' '.md5($lastThema));
                }
                
                //Datum  
                $return = preg_match('/,"([0-9]{10})",/',$line,$treffer); // [16]=> string(6) "DatumL"
                $datum  = isset($treffer[1])?$treffer[1]:'';
          

                //extra liste für Audiodeskription / Hörfassung
                if( $extra_audiodeskription==1 && isset($title_raw) && is_audiodeskription($title_raw) ){
                   if( !isset($themenlist['alle_ad'][$t]) )$themenlist['alle_ad'][$t] = array('count'=>0,'lastDate'=>0,'countFuerGesamtLaenge'=>0,'gesamtLaenge'=>0);
                   if($themenlist['alle_ad'][$t]['lastDate']<$datum) $themenlist['alle_ad'][$t]['lastDate'] = $datum;
                   $themenlist['alle_ad'][$t]['count']++;
                   if(!isset($senderlist['alle_ad']))$senderlist['alle_ad'] = 0;
                   $senderlist['alle_ad']++;
                }
                
                //extra liste für Gebärdensprache
                if( $extra_gebaerdensprache==1 && isset($title_raw) && is_gebaerdensprache($title_raw) ){
                   if( !isset($themenlist['alle_gebaerde'][$t]) )$themenlist['alle_gebaerde'][$t] = array('count'=>0,'lastDate'=>0,'countFuerGesamtLaenge'=>0,'gesamtLaenge'=>0);
                   if($themenlist['alle_gebaerde'][$t]['lastDate']<$datum) $themenlist['alle_gebaerde'][$t]['lastDate'] = $datum;
                   $themenlist['alle_gebaerde'][$t]['count']++;
                   if(!isset($senderlist['alle_gebaerde']))$senderlist['alle_gebaerde'] = 0;
                   $senderlist['alle_gebaerde']++;
                }
                
                
                //Themenliste  
                if( !isset($themenlist[$s]) )$themenlist[$s] = array();
                if( !isset($themenlist[$s][$t]) )$themenlist[$s][$t] = array('count'=>0,'lastDate'=>0,'countFuerGesamtLaenge'=>0,'gesamtLaenge'=>0);
                
                if($themenlist[$s][$t]['lastDate']<$datum) $themenlist[$s][$t]['lastDate'] = $datum;
                $themenlist[$s][$t]['count']++;
                if($laenge!=''){ $themenlist[$s][$t]['countFuerGesamtLaenge']++; $themenlist[$s][$t]['gesamtLaenge'] += $laenge; }
                
                //Themenliste für zu kurze Filme
                if( count($minLengthVorlagenMinuten)>0 ){
                    foreach($minLengthVorlagenMinuten as $l){
                        if($laenge<$l || $laenge==0 || $laenge=='')continue; //zu Kurz
                        //Themenliste  
                            if( !isset($themen_withMinLength [$l][$s]) )$themen_withMinLength [$l][$s] = array();
                            if( !isset($themen_withMinLength [$l][$s][$t]) )$themen_withMinLength [$l][$s][$t] = array('count'=>0,'lastDate'=>0,'countFuerGesamtLaenge'=>0,'gesamtLaenge'=>0);
                            
                            if( !isset($senderliste_withMinLength[$l]) ) $senderliste_withMinLength[$l] = array();
                            if( !isset($senderliste_withMinLength[$l][$s]) ) $senderliste_withMinLength[$l][$s] = 0;
                            $senderliste_withMinLength[$l][$s] ++;
                            
                            if($themen_withMinLength [$l][$s][$t]['lastDate']<$datum) $themen_withMinLength [$l][$s][$t]['lastDate'] = $datum;
                            $themen_withMinLength [$l][$s][$t]['count']++;
                            if($laenge!=''){ $themen_withMinLength [$l][$s][$t]['countFuerGesamtLaenge']++; $themen_withMinLength [$l][$s][$t]['gesamtLaenge'] += $laenge; }
                        
                    }
                }

                //Senderwechsel; speichere Sender-Filmliste
                if($use_cache_filmlist_sender && $i>3 && $sender_raw!='' && $sender_raw!=$lastSender && $lineSum!=''){ //$i>3 (damit die ersten Zeile(n) Beschriftung/etc. ausgelasen werden)
                        if( substr($lineSum,-1)==',') $lineSum = substr($lineSum,0,-1);
                        file_put_contents('cache/sender/'.$file.'_sender_'.$lastSender,$line0.$lineSum.'}');
                        $lineSum = '';
                }
                      
                if($sender_raw!='')$lastSender = $sender_raw;
                if($thema_raw!='') $lastThema = $thema_raw;
                if($use_cache_filmlist_sender) $lineSum .= "\"X\":".$line;
        } //ende foreach
        
        $themenlist = proccess_themenliste_getAndRenderDurchschnittslaenge($themenlist);
        
        foreach($minLengthVorlagenMinuten as $l){
            $themen_withMinLength [$l] = proccess_themenliste_getAndRenderDurchschnittslaenge($themen_withMinLength [$l]);
        }
        
        if(!file_exists('cache')) mkdir('cache');
        if(!file_exists('cache/1')) mkdir('cache/1');
        file_put_contents('cache/1/senderliste.serialize', utf8_encode(serialize($senderlist)));
        
        foreach($minLengthVorlagenMinuten as $l){
                $fileNameAppend='min_length'.$l;
                $senderliste_withMinLength [$l]['alle_ad'] = ''; $senderliste_withMinLength [$l]['alle_gebaerde'] = '';
                file_put_contents('cache/1/senderliste_'.$fileNameAppend.'.serialize', utf8_encode(serialize($senderliste_withMinLength [$l])));
        }
        
        //Themenlisten mit mindeste-Film-Längen speichern
        foreach($minLengthVorlagenMinuten as $l){
                $fileNameAppend='min_length'.$l;
                file_put_contents('cache/1/themenliste'.$fileNameAppend.'.serialize', utf8_encode(serialize($themen_withMinLength [$l])));
        }
        file_put_contents('cache/1/themenliste.serialize', utf8_encode(serialize($themenlist)));
        
        $return = preg_match('/^{"Filmliste":\["([^"]*)","([^"]*)"/U',$line0,$treffer);
        file_put_contents('cache/filmliste_date', utf8_encode($treffer[1]));
}//end function


function proccess_themenliste_getAndRenderDurchschnittslaenge($themenlist){
            //Durchschnittslänge der Filme ermiteln
            foreach( $themenlist as &$line_themenliste_s){
                 foreach( $line_themenliste_s as &$line_themenliste_t){
                            if( $line_themenliste_t['countFuerGesamtLaenge'] == $line_themenliste_t['count']) $line_themenliste_t['aLen'] = (int) round( $line_themenliste_t['gesamtLaenge'] / $line_themenliste_t['countFuerGesamtLaenge'] ,2);
                            if( $line_themenliste_t['countFuerGesamtLaenge'] > 0)$line_themenliste_t['aLen'] = (int) round( $line_themenliste_t['gesamtLaenge'] / $line_themenliste_t['countFuerGesamtLaenge'] ,2);
                            unset($line_themenliste_t['countFuerGesamtLaenge']);
                            unset($line_themenliste_t['gesamtLaenge']);
                 }
            }
            return $themenlist;
}
   
function delTree($dir) { //source: http://php.net/manual/de/function.rmdir.php
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
} 
  
?>
