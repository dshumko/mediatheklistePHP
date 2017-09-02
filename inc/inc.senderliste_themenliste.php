<?php
//Mülleimer:	      echo '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQAgMAAABinRfyAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAAAlQTFRF/wAA////AAAAOomPOgAAAAJ0Uk5TAAB2k804AAAAAWJLR0QAiAUdSAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAC1JREFUCB1jZA1g+M8IJUJWM7IGMvxjZE11+MXIOuEAMyPrUhA3kTAB1gE0CgBy2xa5RVbz1gAAACV0RVh0ZGF0ZTpjcmVhdGUAMjAxNS0wNi0xN1QxMDoyMjowNiswMjowMN485REAAAAldEVYdGRhdGU6bW9kaWZ5ADIwMTQtMTAtMjRUMDM6MDM6MDUrMDI6MDCcI6OUAAAAAElFTkSuQmCC" alt="🗑" width="16" height="16">';

function getSenderListe($options){
      //$hideHoerfassung = 0; if($options['hideHoerfassung'])     $hideHoerfassung     = $options['hideHoerfassung']; //verschoben auf Clientseite in Javascript
      $hideArte_fr     = 0; if($options['hideArte_fr'])       $hideArte_fr       = $options['hideArte_fr'];
      
      if( file_exists('cache/1/senderliste.serialize') )$sender = unserialize( utf8_decode(file_get_contents('cache/1/senderliste.serialize'))); else $sender = array();
      
      $senderListOutArray = array();
      
      uksort($sender, 'strcasecmp');
      if( isset($_GET['quality']) && $_GET["quality"]!='' )  $s2 = "&quality=".$_GET['quality']; else $s2="";
      if( isset($_GET['hide_shorter_then']) && $_GET["hide_shorter_then"]!='' )  $s3 = "&hide_shorter_then=".$_GET['hide_shorter_then']; else $s3="";
      if( isset($_GET['no_table']) && $_GET["no_table"]!='' )  $s3 .= "&no_table=".(int)$_GET['no_table'];
      
      //if( isset($hideHoerfassung) && $hideHoerfassung!='1' ) $s2 .="&hideHoerfassung=".$hideHoerfassung;  //verschoben auf Clientseite in Javascript
      $senderListOutArray['Start / Schnellauswahl'] = "liste.php?$s2"; //&#x2302;&#x2302;
      $senderListOutArray['Alle'] = "liste.php?sender=alle$s2$s3";
      
      foreach($sender as $s=>$count ){
         if($hideArte_fr && $s =='arte.fr' ) continue;
         $senderListOutArray[substr($s,0,10)." ($count)"] = "liste.php?sender=".urlencode($s)."$s2";
      }
      return $senderListOutArray;

}




function getThemenliste($options){
      //$hideHoerfassung = 0; if($options['hideHoerfassung'])     $hideHoerfassung     = $options['hideHoerfassung']; 
      $hideArte_fr     = 0; if($options['hideArte_fr'])         $hideArte_fr       = $options['hideArte_fr'];
      $hideShorterThen = 0; if($options['hideShorterThen'])     $hideShorterThen   = $options['hideShorterThen'];
      //die('aa'.$hideShorterThen);
      if( $hideShorterThen>0)$fileNameAppend='hide_shorter_then'.(int)$hideShorterThen; else $fileNameAppend='';
      if( file_exists('cache/1/themenliste'.$fileNameAppend.'.serialize') )$senderThema = unserialize( utf8_decode(file_get_contents('cache/1/themenliste'.$fileNameAppend.'.serialize')));
      else if( file_exists('cache/1/themenliste.serialize') )$senderThema = unserialize( utf8_decode(file_get_contents('cache/1/themenliste.serialize'))); else $senderThema = array();
      
      
      $themenListOutArray = array();
      
      $dp = '';
      //display:none';
      $anker=parse_url($_SERVER["REQUEST_URI"],PHP_URL_FRAGMENT);
      //if( $anker=='#thema_select' || strstr($anker,'#buchstabe_')!==false || strstr($anker,'#thema_sel_')!==false) $dp = 'display:block';
        
      $out = '';
   
	    
      $arrayThemen = array();
	        
      $out='';
      //if(  $cache_for_startseite_is_fresh==='' || !file_exists($file.'__cache__select_thema') || filemtime($file) > filemtime($file.'__cache__select_thema')){
      //alle Themen Liste, für alle Sender
      if( isset($_GET['sender'])){

        
        if( $_GET['sender']=='alle'){
          $allThemen = array();
          foreach($senderThema as $sender=>$themen ){
	          foreach($themen as $thema=>$more_data  ){
		         $count    = $more_data['count'];
		         $lastDate = $more_data['lastDate']; 
		         $aLen     = (isset( $more_data['aLen']))?$more_data['aLen']:''; 
	       	     if( !isset($allThemen[ $thema ]) ) $allThemen[ $thema ] = array('count'=>0,'lastDate'=>'','aLen'=>'');
		           $allThemen[ $thema ]['count'] += $count;
		           if($allThemen[ $thema ]['lastDate']<$lastDate) $allThemen[ $thema ]['lastDate'] += $lastDate;
		           //if($thema=="3nach9"){echo"a".$aLen.'a'; var_dump($more_data);}
		           if($allThemen[ $thema ]['aLen']=='') $allThemen[ $thema ]['aLen'] += $aLen;
		           else{   //durchschnittslänge neu Berechnen (das Ergebnis ist aber etwas ungenau)
		             if($aLen>0){  
		                $a = $allThemen[ $thema ]['count'] * $allThemen[ $thema ]['aLen'];
		                $b = $count * $aLen;
		                $c = ( $a + $b ) / ($allThemen[ $thema ]['count'] + $count);
		                $allThemen[ $thema ]['aLen'] = round($c);
		             }
		           }
		           if(!isset($allThemen[ $thema ]['sender']))$allThemen[ $thema ]['sender'] = array();
		           if(array_search($sender,$allThemen[ $thema ]['sender'])===FALSE)$allThemen[ $thema ]['sender'][] = $sender;
		           // else $allThemen[ $thema ]['aLen'] = NULL;
	         }
          }
          
          uksort($allThemen, 'strcasecmp');
          $senderThema['alle'] = $allThemen;
        }
        

        if( isset($senderThema[ strtolower($_GET['sender']) ]) ) uksort($senderThema[ strtolower($_GET['sender']) ] , 'strcasecmp');
        $senderUrlPart = '';
        
        if( isset($_GET['sender']) && $_GET['sender']!='' && (!isset($_GET['thema']) || $_GET['thema']=='') ) $dp = 'display:block';


        if(isset($_GET['sender']) && $_GET['sender']!='') $senderUrlPart = 'sender='.$_GET['sender'].'&';
        $lastBuchstabe = '';
        $allBuchstaben = array();
        $listThemenOut = '';
        $favs = array();
        if( isset($_COOKIE['favs']) ) $favs = JSON_decode( $_COOKIE['favs'] );


        //richtig sortieren (auch Umlaute richtig)
        //source: https://stackoverflow.com/questions/120334/how-to-sort-an-array-of-utf-8-strings
        function traceStrColl($a, $b) {
          $outValue=strcoll($a, $b);
          //echo "$a $b $outValue\r\n";
          return $outValue;
        }

        $locale=(defined('PHP_OS') && stristr(PHP_OS, 'win')) ? 'German_Germany.65001' : 'de_DE.utf8';
        $oldLocale=setlocale(LC_COLLATE, "0");
        uksort($senderThema[ strtolower($_GET['sender']) ], 'traceStrColl');
        setlocale(LC_COLLATE, $oldLocale);
        
        unset($senderThema[ strtolower($_GET['sender']) ]['zzz'] ); //ist nur plathalter

        $ll = 0;

        foreach($senderThema[ strtolower($_GET['sender']) ] as $s=>$more_data ){
          $ll++;
	      $count    = $more_data['count'];
	      $lastDate = $more_data['lastDate'];         
	      if(isset($_GET['sender']) && $_GET['sender']=='alle') $senderUrlPart = 'sender='.$_GET['sender'].'&';
	      if( isset($_GET['hide_shorter_then']) && $_GET["hide_shorter_then"]!='' )  $s3 = "&hide_shorter_then=".$_GET['hide_shorter_then']; else $s3="";
	      $href     = "liste.php?".$senderUrlPart."thema=".rawurlencode(str_replace('\\"','"',$s))."".$s3;
          if( isset($_GET["quality"]) && $_GET["quality"]!='' ) $s2 ="&quality=".$_GET["quality"]; else $s2 = '';
          //if( isset($hideHoerfassung) && $hideHoerfassung!='1' ) $s2 .="&hideHoerfassung=".$hideHoerfassung;
	      $b = strtoupper(substr($s,0,1));
	      $lastBuchstabe = $b;
	      if(!isset($allBuchstaben[$b])) $allBuchstaben[$b] = 0;
	      $allBuchstaben[$b] += $count;


	      $themenListOutArray[$b][substr(str_replace('\\"','"',$s),0,65)] = $href.$s2;

          $title = substr($s,0,50);
	      if($count>1)$title.= " ($count)";
	      if( isset($more_data['aLen']) && $more_data['aLen']!==NULL ){
	          $title.= ' &nbsp; '.round($more_data['aLen']).'Min'; //durchschnittslaenge
	          if($count>1) $title.= '∅';
	      }
	      if( isset($more_data['sender']) )$title.= " ".implode(', ',$more_data['sender'])."";
          if($lastDate!='') $lastDateFormat = de_getWochentag($lastDate+(60*60)).gmdate(', d.m.Y H:i', $lastDate+(60*60));
	      //if($lastDate!='') $title.= "<span class=\"t_sel_date\" >".$lastDateFormat."</span>";
              if($lastDate!='') $date = $lastDateFormat; else $date=''; //"<span class=\"t_sel_date\" >"....</span>

	      $arrayThemen[ $b ][ $href ] = array('name'=>substr($s,0,50), 'count'=>"$count", 'title'=>$title, 'date'=>$date, 'lastDate'=>$lastDateFormat);
        }

        if( isset($allBuchstaben) ){
            $locale=(defined('PHP_OS') && stristr(PHP_OS, 'win')) ? 'German_Germany.65001' : 'de_DE.utf8';
            $oldLocale=setlocale(LC_COLLATE, "0");
            uksort($allBuchstaben, 'traceStrColl');
            setlocale(LC_COLLATE, $oldLocale);
            $dp = '';
            //$dp = 'display:none;';
            //if( $anker=='#thema_select' || strstr($anker,'#buchstabe_')!==false || strstr($anker,'#thema_sel_')!==false) $dp = 'display:inline-block';
            if( isset($_GET['sender']) && $_GET['sender']!='' && (!isset($_GET['thema']) || $_GET['thema']=='') ) $dp = 'display:inline-block';
            $out.= "<div style=\"$dp;width:100%;padding-left:1pt;text-indent: -10pt;padding-left: 11pt;margin-top: -1pt;\" id=\"thema_sel_buchstaben\"><span style=\"background:blue\" class=\"hbbtv_button\">&nbsp;&nbsp;&nbsp;</span> Springe zu Buchstabe: ";
            $out.= "<script language=\"javascript\" type=\"text/javascript\"> possibleHideHbbTVButtons(); </script>";
            foreach($allBuchstaben as $b=>$count){
             $out.= "&nbsp;<a href=\"#buchstabe_".rawurlencode(utf8_encode($b))."\"  onClick=\"document.getElementById('list_auswahl_links_thema').style.display='block';\" ><b>".utf8_encode($b)."</b>&nbsp;<small>($count)</small> </a> &nbsp;\n";	
            }
            $out.= "
             <script language=\"javascript\" type=\"text/javascript\">
                 if(screen.availWidth>800 && window.innerHeight>300 && screen.availHeight>500)document.getElementById('start').appendChild(  document.getElementById('thema_sel_buchstaben') );
                 else{ // wenn Bildschirm zu klein: Buchstabenlinks nicht in fester Headerleiste, sondern oberhalb der Themen (scrollbar)
                    var r = document.getElementById('list_auswahl_links_thema');
                    r.insertBefore(  document.getElementById('thema_sel_buchstaben'), r.firstChild );
                   }
                //function onload2y(){   } //window.addEventListener(\"load\", onload2y); 
             </script>";
            $out.= "</div><br><br>\n";
        }

        $out.= $listThemenOut;
        $out.= "</div>";
        //if(isset($_GET['sender']) && $_GET['sender']!='' && (!isset($_GET['thema']) && $_GET['thema']==''))$out.= "<script language=\"javascript\"  type=\"text/javascript\">var liste_from_select_thema = JSON.parse('".json_encode($arrayThemen)."');</script>";
        //file_put_contents($file.'__cache__select_thema',$out);
       }
      //}else{
      //   $out = file_get_contents($file.'__cache__select_thema');	
      //}
      


      return array('themen'=>$arrayThemen, 'buchstabenLinks'=>$out);
}



function de_getWochenTag($timestamp_fuer_wochentag){
	$trans = array(
		'Monday'    => 'Montag',
		'Tuesday'   => 'Dienstag',
		'Wednesday' => 'Mittwoch',
		'Thursday'  => 'Donnerstag',
		'Friday'    => 'Freitag',
		'Saturday'  => 'Samstag',
		'Sunday'    => 'Sonntag',
		'Mon'     => 'Mo',
		'Tue'     => 'Di',
		'Wed'     => 'Mi',
		'Thu'     => 'Do',
		'Fri'     => 'Fr',
		'Sat'     => 'Sa',
		'Sun'     => 'So'
	);
	$wochentag = date("D", $timestamp_fuer_wochentag);
	$wochentag = strtr($wochentag, $trans);
	return $wochentag;
}
function de_getMonat($timestamp_fuer_monat){
	$trans = array(
		'January'   => 'Januar',
		'February'  => 'Februar',
		'March'     => 'März',
		'May'     => 'Mai',
		'June'    => 'Juni',
		'July'    => 'Juli',
		'October'   => 'Oktober',
		'December'  => 'Dezember',
	);
	$monat = date("F", $timestamp_fuer_monat);
	$monat = strtr($monat, $trans);
	return $monat;
}


