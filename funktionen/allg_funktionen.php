<?php
/* hier befindet sich ein lose Sammlung von Funktionen, die so oder ähnlich im php
 * Funktionenumfang nicht existieren, in älteren Versionen nicht existiert haben,
 * nicht gefunden wurden, nicht verstanden wurden oder zu umfrangreich waren.
 */
 
$errors = array();
  
function MapserverErrorHandler($errno, $errstr, $errfile, $errline){
	global $errors;	
	if(!(error_reporting() & $errno)){
		// This error code is not included in error_reporting
		return;
	}
	$errors[] = $errstr;
	/* Don't execute PHP internal error handler */
	return true;
}

function compare_names($a, $b){
	return strcmp($a['name'], $b['name']);
}

function JSON_to_PG($json, $quote = ''){
	if(is_array($json)){
		for($i = 0; $i < count($json); $i++){
			$elems[] = JSON_to_PG($json[$i], '"');
		}
		$pg = '{'.@implode(',', $elems).'}';
	}
	elseif(is_object($json)){
		if($quote == '')$new_quote = '"';
		else $new_quote = '\\'.$quote;
		foreach($json as $elem){
			$elems[] = JSON_to_PG($elem, $new_quote);
		}
		$pg = $quote.'('.implode(',', $elems).')'.$quote;
	}
	else{
		$pg = $json;
	}
	return $pg;
}

function strip_pg_escape_string($string){
	$string = str_replace("''", "'", $string);
	return $string;
}

function InchesPerUnit($unit, $center_y){
	if($unit == MS_METERS){
		return 39.3701;
	}
	elseif($unit == MS_DD){
		if($center_y != 0.0){
			$cos_lat = cos(pi() * $center_y/180.0);
			$lat_adj = sqrt(1 + $cos_lat * $cos_lat)/sqrt(2);
		}
		return 4374754 * $lat_adj;
	}
}

function ie_check(){
	$browser = $_SERVER['HTTP_USER_AGENT'];
	if (preg_match("/MSIE/i", $browser)){
			return TRUE;
	}
	else{
			return FALSE;
	}
} 
 
if(!function_exists('mb_strrpos')){		# Workaround, falls es die Funktion nicht gibt
	function mb_strrpos($str, $search, $offset = 0, $encoding){
		return strrpos($str, $search, $offset);
	}
}

if(!function_exists('mb_substr')){		# Workaround, falls es die Funktion nicht gibt
	function mb_substr($str, $start, $length, $enc = NULL){
		return substr($str, $start, $length);
	}
}


function formatFlurstkennzALKIS($FlurstKennzListe){
	$Flurstuecke = explode(';', $FlurstKennzListe);
	for($i = 0; $i < count($Flurstuecke); $i++){
		$FlurstKennz = $Flurstuecke[$i];
		$explosion = explode('-', $FlurstKennz);
		$gem = trim($explosion[0]);
		$flur = trim($explosion[1]);
		$flurst = trim($explosion[2]);
		$explosion = explode('/',$flurst);
		$zaehler = $explosion[0];
		$nenner = $explosion[1];
		if($nenner != '000.00'){
			$explosion = explode('.',$nenner);
			$vorkomma = '0'.$explosion[0];
		}
		else $vorkomma = '';
		$FlurstKennz = $gem.$flur.$zaehler.$vorkomma;
		$Flurstuecke[$i] = str_pad($FlurstKennz, 20, '_', STR_PAD_RIGHT);
	}
  return implode(';', $Flurstuecke);
}

function formatFlurstkennzALK($FlurstKennz){
	$gem = substr($FlurstKennz, 0, 6);
	$flur = substr($FlurstKennz, 6, 3);
	$zaehler = substr($FlurstKennz, 9, 5);
	$vorkomma = str_pad(intval(substr($FlurstKennz, 15, 3)), 3, '0', STR_PAD_LEFT);  
  $FlurstKennz = $gem.'-'.$flur.'-'.$zaehler.'/'.$vorkomma.'.00';
  return $FlurstKennz;
}

function tausenderTrenner($number){
	if(strpos($number, ' ') === false){
		$explosion = explode('.', $number);
		$length = strlen($explosion[0]);
		$length = $length - 3;
		while($length > 0){
			$new_number = substr($explosion[0], $length, 3).' '.$new_number;
			$length = $length-3;
		}
		$new_number = substr($explosion[0], 0, $length+3).' '.$new_number;
		$new_number = trim($new_number);
		if($explosion[1] != ''){
			$new_number .= '.'.$explosion[1];
		}
		return $new_number;
	}
	else{
		return $number;
	}	
}

function transformCoordsSVG($path){
	$path = str_replace('L ', '', $path);		# neuere Postgis-Versionen haben ein L mit drin
  $svgcoords = explode(' ',$path);
  $anzahl = count($svgcoords);
  for($i = 0; $i < count($svgcoords); $i++){
    if($svgcoords[$i] == 'M'){
    	$newsvgcoords[] = 'M';
    	$last_startcoordx = $svgcoords[$i+1];
    	$last_startcoordy = -1 * $svgcoords[$i+2]; 
    }
    if($svgcoords[$i] != 'M' AND $svgcoords[$i] != 'Z' AND $svgcoords[$i] != ''){
    	$newsvgcoords[] = $svgcoords[$i];
      $newsvgcoords[] = -1 * $svgcoords[$i+1];
      $i++;
    }
    if($svgcoords[$i] == 'Z'){			# neuere Postgis-Versionen liefern bei asSVG ein Z zum Schließen des Rings anstatt der Startkoordinate
    	$newsvgcoords[] = $last_startcoordx;
    	$newsvgcoords[] = $last_startcoordy;
    }
  }
  $svgresult = 'M';
  for($i = 1; $i < count($newsvgcoords); $i++){
    $svgresult .= ' '.$newsvgcoords[$i];
  }
  return $svgresult;
}

function dms2dec($number){
	$part1 = explode('°', $number);
	$degrees = $part1[0];
	$part2 = explode("'", $part1[1]);
	$minutes = $part2[0];
	$seconds = trim($part2[1], '"');
	$seconds = $seconds / 60;
	$minutes = ($minutes+$seconds) / 60;
	return $degrees + $minutes;  
}

function dec2dms($number){
	$part1 = explode('.', $number);
	$degrees = $part1[0];
	$minutes = ('0.'.$part1[1]) * 60;
	$part2 = explode('.', $minutes);
	$minutes = $part2[0];
	$seconds = round(('0.'.$part2[1]) * 60);
	return $degrees."°".$minutes."'".$seconds.'"';
}

function dec2dmin($number){
	# convert decimal degree value to degree and decimal minutes
	$part1 = explode('.', $number);
	$degrees = $part1[0];
	$minutes = ('0.'.$part1[1]) * 60;
	return $degrees."°".round($minutes,3);
} 

function allocateImageColors($image, $colors) {
	$imageColors = Array();
	foreach($colors AS $colorName => $rgbValues) {
		$imageColors[$colorName] = ImageColorAllocate($image, $rgbValues[0], $rgbValues[1], $rgbValues[2]);
	}
	return $imageColors;
}

function rgb2hsv($R,$G,$B) {
	$var_R = $R / 255;                     //RGB from 0 to 255
	$var_G = $G / 255;
	$var_B = $B / 255;
	$var_Min = min(array($var_R,$var_G,$var_B));    //Min. value of RGB
	$var_Max = max(array($var_R,$var_G,$var_B));    //Max. value of RGB
	$del_Max = $var_Max - $var_Min;			              //Delta RGB value
	$V = $var_Max;
	if($del_Max == 0){                     //This is a gray, no chroma...
		$H = 0;                                //HSV results from 0 to 1
		$S = 0;
	}
	else{																	//Chromatic data...
		$S = $del_Max / $var_Max;
		$del_R = ((($var_Max-$var_R)/6)+($del_Max/2))/$del_Max;
		$del_G = ((($var_Max-$var_G)/6)+($del_Max/2))/$del_Max;
		$del_B = ((($var_Max-$var_B)/6)+($del_Max/2))/$del_Max;
		if($var_R == $var_Max){
			$H = $del_B - $del_G;
		}
	  else{
			if($var_G == $var_Max){
				$H = (1/3)+$del_R - $del_B;
			}
	   	else{
				if($var_B == $var_Max){
					$H = (2/3) + $del_G - $del_R;
				}
			}
		}
		if($H < 0){
			$H += 1;
		}
		if($H > 1){
			$H -= 1;
		}
	}
	return array($H,$S,$V);
}


function hsv2rgb($Hdeg,$S,$V) {
  $H = $Hdeg;
  if ($S==0) {       // HSV values = From 0 to 1
    $R = $V*255;     // RGB results = From 0 to 255
    $G = $V*255;
    $B = $V*255;}
  else {
    $var_h = $H*6;
    $var_i = floor( $var_h );     //Or ... var_i = floor( var_h )
    $var_1 = $V*(1-$S);
    $var_2 = $V*(1-$S*($var_h-$var_i));
    $var_3 = $V*(1-$S*(1-($var_h-$var_i)));
    if($var_i==0){
    	$var_r=$V ;    	$var_g=$var_3;    	$var_b=$var_1;
    }
    elseif($var_i==1){
    	$var_r=$var_2; $var_g=$V;     $var_b=$var_1;
    }
    elseif($var_i==2){
    	$var_r=$var_1; $var_g=$V;     $var_b=$var_3;
    }
    elseif($var_i==3){
    	$var_r=$var_1; $var_g=$var_2; $var_b=$V;
    }
    elseif($var_i==4){
    	$var_r=$var_3; $var_g=$var_1; $var_b=$V;
    }
    else{
    	$var_r=$V;     $var_g=$var_1; $var_b=$var_2;
    }
    $R = round($var_r*255);   //RGB results = From 0 to 255
    $G = round($var_g*255);
    $B = round($var_b*255);
  }
  return array($R,$G,$B);
}
 
 
if(!function_exists('imagerotate')){
	function imagerotate($source_image, $angle, $bgd_color){
		$angle = 360-$angle; // GD rotates CCW, imagick rotates CW
    $file1 = IMAGEPATH.'imagick_' . rand( 10000,99999 ) . '.png';
    $file2 = IMAGEPATH.'imagick_' . rand( 10000,99999 ) . '.png';
    if(@imagepng($source_image, $file1)){
    	exec(IMAGEMAGICKPATH.'convert -rotate ' . $angle . ' ' . $file1 . ' ' . $file2 );
      if(file_exists($file2)){
        $new_image = imagecreatefrompng($file2);
        unlink($file1);
        unlink($file2);
        return $new_image;
      }
      else{
      	echo 'Bildrotation mit ImageMagick fehlgeschlagen.';
      	return $source_image;
      }
    }
    else{
    	echo 'Kann temporäre Bilddateien nicht anlegen.';
    	return $source_image;
    }
	}
}

 
function st_transform($x,$y,$from_epsg,$to_epsg) {
	#$x = 12.099281283333;
	#$y = 54.075214183333;
  $point = ms_newPointObj();
	$point->setXY($x,$y);
	$projFROM = ms_newprojectionobj("init=epsg:".$from_epsg);
  $projTO = ms_newprojectionobj("init=epsg:".$to_epsg);
  $point->project($projFROM, $projTO);
  return $point;
} 

function checkPasswordAge($passwordSettingTime,$allowedPassordAgeMonth) {
  $passwordSettingUnixTime=strtotime($passwordSettingTime); # Unix Zeit in Sekunden an dem das Passwort gesetzt wurde
  $allowedPasswordAgeDays=round($allowedPassordAgeMonth*30.5); # Zeitintervall, wie alt das Password sein darf in Tagen
  $passwordAgeDays=round((time()-$passwordSettingUnixTime)/60/60/24); # Zeitinterval zwischen setzen des Passwortes und aktueller Zeit in Tagen
  $allowedPasswordAgeRemainDays=$allowedPasswordAgeDays-$passwordAgeDays; # Zeitinterval wie lange das Passwort noch gilt in Tagen
	return $allowedPasswordAgeRemainDays; // Passwort ist abgelaufen wenn Wert < 1  
}

/**
* Prüft ob ein Passwort ein gutes Passwort ist.
* 
* Diese Funktion prüft die Länge, Anzahl wiederholter Zeichen und einfachheit von Passwörtern
* Code wurde abgeleitet von http://scripts.franciscocharrua.com/check-password.php und
* http://www.vbforums.com/showthread.php?p=2347960 und wurde stark verändert und ergänzt.
* Vielen Dank trotzdem an die Autoren.
* 
* Reihenfolge: Übersichtssatz - Kommentar - Tags.
* 
* @param string password Zu prüfendes Password als Text
* @return string Fehlermeldung zur Beschreibung, was an dem Password schlecht ist, oder leerer String, wenn Password gut ist.
* @see    createRandomPassword(), checkPasswordAge, $GUI, $user, $stelle
*/
# Passwortprüfung
function isPasswordValide($oldPassword,$newPassword,$newPassword2) {
  $password_errors = array();
  $check = 0;

  # Prüft ob überhaupt etwas eingegeben wurde
  if (strlen($newPassword)==0 or strlen($newPassword2)==0) {
  	$password_errors[] = "ist leer";
  	$check = 1;
  }

  # Prüft ob neues Passwort nicht genau dem alten Passwort entspricht
  if ($check == 0 and $oldPassword==$newPassword) {
    $password_errors[] = "muss sich vom alten unterscheiden";
    $check = 1;
  }

  # Prüft ob neues Passwort der Wiederholung entspricht
  if ($check == 0 and $newPassword!=$newPassword2) {
    $password_errors[] = "muss mit der Wiederholung übereinstimmen";
    $check = 1;
  }

  # Prüft die Länge des Passwortes
  $strlen = strlen($newPassword);
  if($check == 0 and $strlen <= 5) {
    $password_errors[] = "ist zu kurz";
    $check = 1;
  }

  if($check == 0 and $strlen > PASSWORD_MAXLENGTH) {
    $password_errors[] = "ist zu lang (maximal ".PASSWORD_MAXLENGTH." Zeichen)";
    $check = 1;
  }
	
  if($check == 0 and $strlen < PASSWORD_MINLENGTH) {
    $password_errors[] = "ist zu kurz (mindestens ".PASSWORD_MINLENGTH." Zeichen)";
    $check = 1;
  }

  # Prüft die Anzahl unterschiedlicher Zeichen
  $count_chars = count_chars($newPassword, 3);
  if($check == 0 and strlen($count_chars) < $strlen / 2) {
    $password_errors[] = "hat zu viele gleiche Zeichen";
    $check = 1;
  }

  if($check == 0) {

    # Prüft die Stärke des Passworts
    if (substr(PASSWORD_CHECK,0,1)=='0') {
      $strength = 0;
      $patterns = array('#[a-z]#','#[A-Z]#','#[0-9]#','/[¬!"£$%^&*()`{}\[\]:@~;\'#<>?,.\/\\-=_+\|]/');
      foreach($patterns as $pattern) {
      	if(preg_match($pattern,$newPassword,$matches)) {
          $strength++;
        }
      }
      // strength=
      // 1 - weak
      // 2 - not weak
      // 3 - acceptable
      // 4 - strong
      if ($strength<3) {
      	$password_errors[] = "ist zu schwach";
      }
    }

    # Prüft auf Kleinbuchstaben
    if (substr(PASSWORD_CHECK,0,1)=='1' and substr(PASSWORD_CHECK,1,1)=='1') {
      if(!preg_match('/[a-z]/',$newPassword)) {
        $password_errors[] = "weist keine Kleinbuchstaben auf";
      }
    }

    # Prüft auf Großbuchstaben
    if (substr(PASSWORD_CHECK,0,1)=='1' and substr(PASSWORD_CHECK,2,1)=='1') {
      if(!preg_match('/[A-Z]/',$newPassword)) {
        $password_errors[] = "weist keine Großbuchstaben auf";
      }
    }

    # Prüft auf Zahlen
    if (substr(PASSWORD_CHECK,0,1)=='1' and substr(PASSWORD_CHECK,3,1)=='1') {
      if(!preg_match('/[0-9]/',$newPassword)) {
        $password_errors[] = "weist keine Zahlen auf";
      }
    }

    # Prüft auf Sonderzeichen
    if (substr(PASSWORD_CHECK,0,1)=='1' and substr(PASSWORD_CHECK,4,1)=='1') {
      if(!preg_match('/[¬!"£$%^&*()`{}\[\]:@~;\'#<>?,.\/\\-=_+\|]/',$newPassword)) {
        $password_errors[] = "weist keine Sonderzeichen auf";
      }
    }

  }

  //Zusammenstellung der Fehlermeldung - wenn kein Fehler vorlag: Rückgabe eines leeren Strings
  $return_string = "";
  $anzErrors=count($password_errors);
  for($i=0;$i<$anzErrors;$i++) {
    if($i==0) {
      $return_string.="Das neue Passwort ";
    }
    else {
    	if($i<$anzErrors-1)
        $return_string.=", ";
      if($i==$anzErrors-1)
        $return_string.=" und ";
    }
    $return_string.=$password_errors[$i];
  }
  return $return_string;
}

/**
* Erzeugen eines zufälligen Passwortes
* 
* Diese Funktion erzeugt ein zufälliges sicheres Password. Die Funktion wurde von Totally PHP übernommen und mit zusätzlichen Zeichen versehen
* siehe: http://www.totallyphp.co.uk/code/create_a_random_password.htm
* Vielen Dank an den Autor. 
* 
* Reihenfolge: Übersichtssatz - Kommentar - Tags.
* 
* @return string ein achtstelliges Password
* @see    isPasswordValide(), checkPasswordAge, $GUI, $user, $stelle
*/
function createRandomPassword($passwordLength) {
	if ($passwordLength<8)
		$passwordLength=8;
	if ($passwordLength>16)
	  $passwordLength=16;
  $chars[0]= "abcdefghijkmnopqrstuvwxyz";
  $chars[1]= "ABCDEFGHIJKMNOPQRSTUVWXYZ";
  $chars[2]= "0234567890234567890234567";
  $chars[3]= "()_+*-.:,;!§$%&=#()_+*-.:";
  $password='';
  $charListNumbers=array();
  $charListNumber=rand(0,3);
  $loops=0;
  while (strlen($password)<$passwordLength AND $loops++ < 100) {
  	while (count($charListNumbers)<4) {
  		if (!in_array($charListNumber,$charListNumbers)) { # wenn die charListNumber noch nicht in der Liste ist
  			$charListNumbers[]=$charListNumber; # charListNumber in die Liste aufnehmen
  			$char=substr($chars[$charListNumber],rand(0,24),1); # Character aus der Characterliste mit charListNumber entnehmen
  			#if ($char==' ') $char='_'; # darf nur auf keinen Fall ein Leerzeichen beinhalten
  			$password.=$char;
  			#echo '<br>'.strlen($password).' '.$password;
  		}
  		$charListNumber=rand(0,3);
  	}
  	$charListNumbers = array();
  }
  return $password;
}
 
function in_subnet($ip,$net) {
	$ipparts=explode('.',$ip);
	$netparts=explode('.',$net);

	# Direkter Vergleich
	if ($ip==$net) {
		return 1;
	}

  # Test auf C-Netz
	if (trim($netparts[3],'0')=='' OR $netparts[3]=='*') {
		# C-Netzvergleich
	  if ($ipparts[0].'.'.$ipparts[1].'.'.$ipparts[2]==$netparts[0].'.'.$netparts[1].'.'.$netparts[2]) {
	  	return 1;
	  }
	}

  # Test auf B-Netz
	if ((trim($netparts[3],'0')=='' OR $netparts[3]=='*') AND (trim($netparts[2],'0')=='' OR $netparts[2]=='*')) {
		# B-Netzvergleich
	  if ($ipparts[0].'.'.$ipparts[1]==$netparts[0].'.'.$netparts[1]) {
	  	return 1;
	  }
	}

  # Test auf A-Netz
	if ((trim($netparts[3],'0')=='' OR $netparts[3]=='*') AND (trim($netparts[2],'0')=='' OR $netparts[2]=='*') AND (trim($netparts[1],'0')=='' OR $netparts[1]=='*')) {
		# A-Netzvergleich
	  if ($ipparts[0]==$netparts[0]) {
	  	return 1;
	  }
	}
	return 0;
}

function stripScript($request) {
	# Definition von Tags, die gestripped werde sollen
	$search = array('@<script[^>]*?>.*?</script>@si');
	foreach($request AS $key => $value) {
		if (is_array($value)) {
			$ret[$key]=stripScript($value);
		}
		else {
			$ret[$key]=preg_replace($search, '', $value); 
		}
	}
	return $ret;
}

function isTag($word) {
	if (substr($word,1)=='<') {
		if (substr($word,-1)=='>') {
			return true;
		}
	}
	return false;
}

function drawColorBox($color,$outlinecolor) {
  # Funktion liefert eine Box als überlagerte Div in html,
  # die die Farbe $color und die Border $outlinecolor hat.
  $c=explode(' ',trim($color));
  $bgcolor='#';
  for ($i=0;$i<3;$i++) {
    $bgcolor.=strtoupper(str_pad(dechex($c[$i]), 2, 0, STR_PAD_LEFT));
  }
  $oc=explode(' ',trim($outlinecolor));
  $bordercolor='#';
  for ($i=0;$i<3;$i++) {
    $bordercolor.=strtoupper(str_pad(dechex($oc[$i]), 2, 0, STR_PAD_LEFT));
  }
  ?>
<div id="Layer1" style="position:relative; width:22px; height:12px; z-index:1; left: 0px; top: 0px; background-color: <?php echo $bordercolor; ?>; layer-background-color: #00CCFF; ?>; border: 0px none;">
  <div id="Layer2" style="position:relative; width:20px; height:10px; z-index:2; left: 1px; top: 1px; background-color: <?php echo $bgcolor; ?>; layer-background-color: #00CCFF; border: 0px none;">
  </div>
</div>
  <?php
}


// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Aidan Lister <aidan@php.net>                                |
// +----------------------------------------------------------------------+
//
// $Id: str_split.php,v 1.13 2004/11/21 14:21:17 aidan Exp $

/**
 * Replace str_split()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.str_split
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.13 $
 * @since       PHP 5
 * @require     PHP 4.0.1 (trigger_error)
 */
if (!function_exists('str_split')) {
    function str_split($string, $split_length = 1)
    {
        if (!is_scalar($split_length)) {
            trigger_error('str_split() expects parameter 2 to be long, ' . gettype($split_length) . ' given', E_USER_WARNING);
            return false;
        }

        $split_length = (int) $split_length;
        if ($split_length < 1) {
            trigger_error('str_split() The length of each segment must be greater than zero', E_USER_WARNING);
            return false;
        }

        preg_match_all('/.{1,' . $split_length . '}/s', $string, $matches);
        return $matches[0];
    }
}

function unzip($src_file, $dest_dir=false, $create_zip_name_dir=true, $overwrite=true){
	# 1. Methode über unzip (nur Linux)
	$output = array();
	$entries = NULL;
	exec('export LD_LIBRARY_PATH=;unzip -l "'.$src_file.'" -d '.dirname($src_file), $output);
	#echo 'unzip -l "'.$src_file.'" -d '.dirname($src_file);
	for($i = 3; $i < count($output)-2; $i++){
  		$entries[] = array_pop(explode('   ', $output[$i]));
	}
	if($entries != NULL){
		exec('export LD_LIBRARY_PATH=;unzip -o "'.$src_file.'" -d '.dirname($src_file));
	}
	# 2. Methode über php_zip Extension
	else{
	  if ($zip = zip_open($src_file)){
	    if ($zip){
	      $splitter = ($create_zip_name_dir === true) ? "." : "/";
	      if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";
	      @mkdir($dest_dir);
	      while ($zip_entry = zip_read($zip)){
	        $entries[] = zip_entry_name($zip_entry);
	        $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
	        if ($pos_last_slash !== false){
	          @mkdir($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
	        }
	        if (zip_entry_open($zip,$zip_entry,"r")){
	          $file_name = $dest_dir.zip_entry_name($zip_entry);
	          if ($overwrite === true || $overwrite === false && !is_file($file_name)){
	            $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							$fp = fopen($file_name, 'w');
	            fwrite($fp, $fstream );
	            fclose($fp);
	            chmod($file_name, 0777);
	          }
	          zip_entry_close($zip_entry);
	        }      
	      }
	      zip_close($zip);
	    }
	  }
	}
	return $entries;
}

function html_umlaute($string){
	$string = str_replace('ä', '&auml;', $string);
	$string = str_replace('ü', '&uuml;', $string);
	$string = str_replace('ö', '&ouml;', $string);
	$string = str_replace('Ä', '&Auml;', $string);
	$string = str_replace('Ü', '&Uuml;', $string);
	$string = str_replace('Ö', '&Ouml;', $string);
	$string = str_replace('ß', '&szlig;', $string);
	$string = str_replace('ø', '&oslash;', $string);
	$string = str_replace('æ', '&aelig;', $string);
	return $string;
}

function umlaute_html($string){
	$string = str_replace('&auml;', 'ä', $string);
	$string = str_replace('&uuml;', 'ü', $string);
	$string = str_replace('&ouml;', 'ö', $string);
	$string = str_replace('&Auml;', 'Ä', $string);
	$string = str_replace('&Uuml;', 'Ü', $string);
	$string = str_replace('&Ouml;', 'Ö', $string);
	$string = str_replace('&szlig;', 'ß', $string);
	$string = str_replace('&oslash;', 'ø', $string);
	$string = str_replace('&aelig;', 'æ', $string);
	$string = str_replace('&nbsp;', ' ', $string);
	return $string;
}

function umlaute_sortieren($array, $second_array){
	// Diese Funktion sortiert das Array $array unter Berücksichtigung von Umlauten.
	// Zusätzlich läßt sich ein zweites Array $second_array übergeben, welches genauso viele 
	// Elemente haben muß wie das erste und dessen Elemente entsprechend der Sortierung des
	// ersten Arrays angeordnet werden, dadurch bleiben die Index-Beziehungen beider Arrays erhalten.
	// Außerdem werden alle Array-Elemente unabhängig von Groß/Kleinschreibung sortiert. 
	if(is_array($array)){
		$oldarray = $array;
		for($i = 0; $i < count($array); $i++){
			$array[$i] = strtoupper($array[$i]);
	  	$array[$i] = str_replace('Ä', 'A', $array[$i]);
	  	$array[$i] = str_replace('Ü', 'U', $array[$i]);
	  	$array[$i] = str_replace('Ö', 'O', $array[$i]);
	  	$array[$i] = str_replace('ß', 's', $array[$i]);
		}
		@asort($array);
		if($second_array != NULL){
			for($i = 0; $i < count($array); $i++){
				$newarray[] = $oldarray[key($array)];
				$new_second_array[] = $second_array[key($array)];
				next($array);
			}
			$arrays['array'] = $newarray;
			$arrays['second_array'] = $new_second_array;
			return $arrays;
		}
		else{
			for($i = 0; $i < count($array); $i++){
				$newarray[] = $oldarray[key($array)];
				next($array);
			}
			return $newarray;
		}
	}
}

function umlaute_umwandeln($name){
  $name = str_replace('ä', 'ae', $name);
  $name = str_replace('ü', 'ue', $name);
  $name = str_replace('ö', 'oe', $name);
  $name = str_replace('Ä', 'Ae', $name);  
  $name = str_replace('Ü', 'Ue', $name);
  $name = str_replace('Ö', 'Oe', $name);
  $name = str_replace('a?', 'ae', $name);
  $name = str_replace('u?', 'ue', $name);
  $name = str_replace('o?', 'oe', $name);
  $name = str_replace('A?', 'ae', $name);
  $name = str_replace('U?', 'ue', $name);
  $name = str_replace('O?', 'oe', $name);
  $name = str_replace('ß', 'ss', $name);
  $name = str_replace('.', '', $name);
  $name = str_replace(':', '', $name);
  $name = str_replace('/', '-', $name);
  $name = str_replace(' ', '', $name);
  $name = str_replace('-', '_', $name);
  $name = str_replace('?', '_', $name);
  return $name;
}

function umlaute_javascript($text){
	$text = str_replace("ä", "%E4", $text);
	$text = str_replace("ö", "%F6", $text);
	$text = str_replace("ü", "%FC", $text);
	$text = str_replace("Ä", "%C4", $text);
	$text = str_replace("Ö", "%D6", $text);
	$text = str_replace("Ü", "%DC", $text);
	$text = str_replace("ß", "%DF", $text);
	$text = str_replace("²", "%B2", $text);
	$text = str_replace('"', '%A8', $text);
	$text = str_replace('&', '%26', $text);
	return $text;
}


function stringrpos($haystack,$needle){   # findet das letzte Vorkommen eines Strings (gibs erst ab php 5)
   return strlen($haystack)- strpos( strrev($haystack) , strrev($needle) , NULL)- strlen($needle);
}

function rotate($polygon, $angle){
  for($i = 0; $i < count($polygon); $i++){
    $newpolygon[$i] = cos(deg2rad($angle))*$polygon[$i] - sin(deg2rad($angle))*$polygon[$i+1];
    $newpolygon[$i+1] = sin(deg2rad($angle))*$polygon[$i] + cos(deg2rad($angle))*$polygon[$i+1];
    $i++;
  }
  return $newpolygon;
}

function translate($polygon, $transx, $transy){
  for($i = 0; $i < count($polygon); $i++){
    $newpolygon[$i] = $polygon[$i] + $transx;
    $newpolygon[$i+1] = $polygon[$i+1] + $transy;
    $i++;
  }
  return $newpolygon;
}

function compare_groups($a, $b){
  if($a->group > $b->group){
    return 1;
  }
  else return 0;
}

function is_dir_empty($path){
	$handle = opendir($path);
	$one = readdir($handle);		# .
	$two = readdir($handle);		# ..
	$three = readdir($handle);	# ???
	closedir($handle);
	if($three == ''){
		return true;
	}
	else{
		return false;
	}
}

function searchdir($path, $recursive){
    # liefert ein Array mit den Pfaden aller Dateien im Verzeichnis
    if (substr($path, strlen($path) - 1 ) != '/' ){
      $path .= '/';
    }     
    $dirlist = array() ;
    //$dirlist[] = $path ;
    if ($handle = opendir($path)){
      while ( false !== ($file = readdir($handle))){
        if ($file != '.' && $file != '..' ){
          $file = $path.$file;
          if (!is_dir($file)){
            $dirlist[] = $file;
          }
          elseif($recursive == true){ 
            $result = searchdir($file . '/', true) ;
            $dirlist = array_merge($dirlist,$result) ;
          }
        }
      }
      closedir ( $handle ) ;
    }
    sort($dirlist);
    return ($dirlist);
}


function get_select_parts($select){
	$column = explode(',', $select);
  for($i = 0; $i < count($column); $i++){
  	$klammerauf = substr_count($column[$i], '(');
  	$klammerzu = substr_count($column[$i], ')');
  	if($klammerauf > $klammerzu){			# mehr Klammern auf als zu --> hier wurde eine Funktion oder eine Unterabfrage mit Kommas verwendet
  		$column[$i] = $column[$i].', '.$column[$i+1];
  		array_splice($column, $i+1, 1);
			$i--;							# und nochmal prüfen, falls mehrere Kommas drin sind
  	}
  }
  return $column;
}

  
function microtime_float(){
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function copy_file_to_tmp($frompath, $dateiname = ''){
  $dateityp = explode('.',$frompath);
  $dateipfad=IMAGEPATH;
  if($dateiname == '')$dateiname=rand(100000,999999).'.'.$dateityp[1];
  if(copy($frompath, $dateipfad.$dateiname) == true){
    return TEMPPATH_REL.$dateiname;
  }
  else{
    echo 'Datei '.$frompath.' konnte nicht nach '.$dateipfad.$dateiname.' kopiert werden.';
  }
	#exec('ln -s '.$frompath.' '.$dateipfad.$dateiname);
	#return TEMPPATH_REL.$dateiname;
}

function read_epsg_codes($database){
  $epsg_codes = $database->read_epsg_codes();
  return $epsg_codes; 
}

function read_colors($database){
  $colors = $database->read_colors();
  return $colors; 
}

function delete_files($target, $exceptions, $output){
	if(is_dir($target)){
	   $sourcedir = opendir($target);
	   while(false !== ($filename = readdir($sourcedir)))
	   {
	       if(!in_array($filename, $exceptions))
	       {
	           if($output)
	           { echo "Processing: ".$target."/".$filename."<br>"; }
	           if(is_dir($target."/".$filename))
	           {
	               // recurse subdirectory; call of function recursive
	               delete_files($target."/".$filename, $exceptions,0);
	           }
	           else if(is_file($target."/".$filename))
	           {
	               // unlink file
	               unlink($target."/".$filename);
	           }
	       }
	   }
	   closedir($sourcedir);
	   if(rmdir($target))
	   { return true; }
	   else
	   { return false; }
	}
}

function str_space($string, $split_length = 1) {
  # str_split...include the pieces of the reversed string splittet by the str_split function
  # e.g. 12345 mit split_length=3 -> piece[0]...543, piece[1]...21
  $pieces=str_split(strrev($string), 3);
  # amtpieces... amount of pieces created from str_split function
  $amtpieces=count($pieces);
  # returnstr... include the string composed with the backward reversed string pieces
  # and the required space character between ($returnstr... 12 345)
  $returnstr=strrev($pieces[$amtpieces-1]);
  for ($i=$amtpieces-1;$i>0;$i--) {
    $returnstr.=' '.strrev($pieces[$i-1]);
  }
  return $returnstr;
}

########### Zeigt Text in einem Java-Script Alarmfenster an
function showAlert($text) {
  ?>
  <script type="text/javascript">
    alert("<?php echo $text; ?>");
  </script><?php
}

function showMessage($text, $fade = true) {
  ?>
  <script type="text/javascript">
		var Msg = document.getElementById("message_box");
		if(Msg == undefined){
			document.write('<div id="message_box" class="message_box_hidden"></div>');
			var Msg = document.getElementById("message_box");
		}
		Msg.className = 'message_box_visible';
		Msg.style.top = document.body.scrollTop + 350;		
		var innerhtml = '<?php echo $text; ?>';
		<? if($fade == true){ ?>
			setTimeout(function() {Msg.className = 'message_box_hide';},1000);
			setTimeout(function() {Msg.className = 'message_box_hidden';},3000);
		<? }else{ ?>
			innerhtml += '<br><br><input type="button" onclick="this.parentNode.className = \'message_box_hidden\';" value="ok">';
		<? } ?>
		Msg.innerHTML = innerhtml;
  </script><?php
}

function ArtCode2Abk($code) {
  switch ($code) {
    case '100' : {
      $abk='ffr';
    } break;
    case '010' : {
      $abk='kvz';
    } break;
    case '001' : {
      $abk='gn';
    } break;
    case '111' : {
      $abk='andere';
    } break;
  }
  return $abk;
}

########### Funktion wandelt UNIX Zeichen in DOS Zeichen um für Konvertierung WLDGE-Dateien
function unix2dos($text) {
   $search  = array ("{", "|", "}", "~","'","[","\\","]","@"); 
   $replace = array ("ä", "ö", "ü", "ß","\"","Ä","Ö","Ü","§");
   return str_replace($search, $replace, $text);
}

function ANSII2DOS($text) {
  $search  = array ('"',chr(132),chr(142),chr(148),chr(153),chr(129),chr(154),chr(225)); 
  $replace = array ('\'','ä','Ä','ö','Ö','ü','Ü','ß');
  return str_replace($search, $replace, $text);
}

function convertDBFCodePage($filename) {
  $dbfid=dbase_open ($filename,2);
  if ($dbfid==0) {
    echo "<b>Fehler beim öffnen der dbf-Tabelle!</b>";
    return 0;
  }  
  echo "<br>Beginne mit schreiben der Tabelle ".$filename."...";
  for ($i=1;$i<=dbase_numrecords($dbfid);$i++) {
    $dbfrs=dbase_get_record ($dbfid,$i);
    echo "<br>";
    for ($j=0;$j<dbase_numfields($dbfid);$j++) {
      $dbfrs[$j]=trim(ANSII2DOS($dbfrs[$j]));
      echo $dbfrs[$j]." ";
    }
    # Löschen des letzten Arrayelements (deleted)
    array_pop($dbfrs);
    if (!dbase_replace_record($dbfid,$dbfrs,$i)) {
      echo "<br><b>Fehler beim umschreiben der dbf-Tabelle in Zeile ".$i."!</b>";
    }
  }
  echo "<br>...fertig";
  echo "<br>".$i." Zeilen in neue dbf-Tabelle geschrieben";
  dbase_close ($dbfid);
}

# Funktion bricht $text in Wörtern in ein Array von Zeilen der Länge $laenge um 
# Beispiel: 
# $block=zeilenumbruch('Dies ist ein Beilspiel.',12);
# echo $block[0]; # liefert "Dies ist ein"
# echo $block[1]; # liefert "Beispiel"
function zeilenumbruch($text,$laenge) {
  $wort=explode(' ',$text);
  $ausgabetext=$wort[0];
  for ($i=1;$i<count($wort);$i++) {
    if ((strlen($ausgabetext)+strlen($wort[$i])+1)>$laenge) {
      $ausgabe[]=$ausgabetext;
      $ausgabetext=$wort[$i];
    }
    else {
      $ausgabetext.=' '.$wort[$i];
    }
  }
  $ausgabe[]=$ausgabetext;
  return $ausgabe;
}

function runLenComp($liste) {
  # Funktion führt eine Lauflängenkodierung des Arrays aus
  # und komprimiert somit das Array von Einzelwerten
  # array runLenComp(array array);
  # von den vorkommenden ganzen Zahlen werden von-bis Intervalle gebildet
  # sortieren der Liste
  sort($liste);
  $anz=count($liste);
  $comp=$liste[0];
  $intervalwidth=0;
  for ($i=1;$i<$anz;$i++) {
    if ($liste[$i]==$liste[$i-1]+1) {
      # der nächste Wert ist eins größer im gleichen Intervall 
      $intervalwidth++;
      # wenn das letzte Element erreicht ist Intervall schließen
      if ($i==$anz-1) {
        $comp.='-'.$liste[$i];
      }
    }
    else {
      # der nächste Wert ist mehr als eins größer, Intervallende
      # wenn das Interval bis hierhin nur 1 war wird das Ende nicht geschrieben
      # sonst wird das Intervalende geschrieben.
      if ($intervalwidth>1) {
        $comp.='-'.$liste[$i-1];
      }
      # neues Intervall beginnen.
      $comp.=';'.$liste[$i];
      $intervalwidth=1;
    }
  }
  return $comp;
}

#**** Funktion, die prüft, ob Datum sinnvoll ist.

function date_ok($date) {

   $today = date(Ymd);

   $ty = substr($today, 0,4);
   $tm = substr($today, 4,2);
   $td = substr($today, 6,2);


   $yy = strtok($date,"-");

   $ok = True;



   /* Stringoperationen */

   // Datum muss richtig formatiert sein ("YYYY-MM-DD").

   $c = strlen($date);

   if($c != 10) $ok= False;


   if(!ereg ("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $date)) $ok =False;



   /* Numerische Operationen */

   //Datum sollte möglich sein

   if($ok == True)
   {
       $date_tst = $yy.$mm.'01';
       $date_tst = strtotime($date_tst);

       $max_day = date(t,$date_tst);

       settype($yy, "integer");
       settype($mm, "integer");
       settype($dd, "integer");
       settype($ty, "integer");
       settype($tm, "integer");
       settype($td, "integer");

       #if($yy>2037) $ok = False;  // Sonst ausserhalb des Gültigkeitsbereiches des Timestamps, siehe auch PHP-Manual "date()".


       if(($max_day<$dd)||(1>$dd)) $ok =False;;

       if(($mm>12)||($mm<1)) $ok =False;

   }


   // Datum darf nicht in Vergangenheit liegen

   if($ok==True)
   {
       if($yy==$ty)
       {
           if($mm<$tm) $ok =False;
           if(($mm==$tm)&&($dd<$td)) $ok =False;
       }

       if($yy<$ty) $ok =False;
   }


   return $ok;

}

function split_edbs_file($pfad,$filename) {
  # Funktion teilt eine EDBS Datei in einzelne Dateien
  # Jeder Auftrag wird in eine separate Datei geschrieben
  # Öffnen der EDBS Datei
  $fp=fopen($pfad.$filename,'r');
  $i=1;
  $fpteil=fopen($pfad.'EDBS_teil_'.$i.'.edbs','w');
  echo '<p>Öffne EDBS_teil_'.$i.'.edbs<br>';
  $zaehler=0;
  while (!feof($fp)) {
    $zaehler++;
    # Lesen der Zeile
    $line=fgets($fp);
    # Schreiben der Zeile in aktuell offene Teildatei
    fwrite($fpteil,$line);
    echo ' *';
    # Wenn die Zeile den String zur Beendigung des Auftrages enthält, Datei schließen und neue unter neuen namen Öffnen
    if (ereg('EDBS00240000AEND000000  0000',$line)>0 AND $zaehler>250000) {
      $zaehler=0;
      # Schließen der Datei
      fclose ($fpteil);
      $i++;
      $fpteil=fopen($pfad.'EDBS_teil_'.$i.'.edbs','w');
      echo '<p>Öffne EDBS_teil_'.$i.'.edbs<br>';
    }
  }
  fclose($fp);
}

############################################################################
# Prüft eine E-Mail adresse auf richtige schreibweise
############################################################################
function emailcheck($email) {
  $Meldung='';
  # enthält die Adresse ein Leerzeichen?
  if (strstr(trim($email)," ")) {
    $Meldung.='<br>E-Mail enthält Leerzeichen.';
  }
  
  # hat die Adresse ein @
  if (!strstr($email,"@")) {
    $Meldung.='<br>E-Mail enthält kein @.';
  }
  
  $postfix=strlen(strrchr($email,"."))-1;
  if (!($postfix >1 AND $postfix < 4)) {
    #echo " postfix ist zu kurz oder zu lang";
    $Meldung.='<br>E-Mail ist zu kurz oder zu lang.';
  }
  return $Meldung;
}

function buildExpressionString($str) {
  $intervalle=explode(';',$str);
  $anzInt=count($intervalle);
  if ($intervalle[$anzInt-1]=='') { $anzInt--; }
  # Beginne mit der Erstellung des Ausdrucks
  $expr.='(';
  # man neheme das erste Intervall
  # Zerlege es in Anfang und Ende
  $grenzen=explode('-',$intervalle[0]);
  # Teste ob es überhaupt ein Ende gibt, oder nur einen einzelnen Wert
  if (count($grenzen)==1) {
    # Wenn ja, wird die erste einschränkung geschrieben.
    $expr.='[ID]='.$grenzen[0];
  }
  else {
    # Wenn es Anfang und Ende gibt, müssen zwei Bedingungen geschrieben werden
    $expr.='([ID]>'.$grenzen[0].' AND [ID]<'.$grenzen[1].')';
  }
  # weiter geht es mit den nächsten Intervallen
  for ($i=1;$i<$anzInt;$i++) {
    # wieder Zerlegen in Anfang und Ende
    $grenzen=explode('-',$intervalle[$i]);
    if (count($grenzen)==1) {
      # Es gibt nur einen Wert
      $expr.=' OR [ID]='.$grenzen[0];
    }
    else {
      # Es gibt Anfang und Ende im Intervall
      $expr.=' OR ([ID]>'.$grenzen[0].' AND [ID]<'.$grenzen[1].')';
    }
  }
  # Beenden des Ausdrucks
  $expr.=')';
  return $expr;
}

function rectObj2WKTPolygon($rect) {
  $polygon="";
  if (is_object($rect)) {
    if ($rect->minx>0) {
      $polygon ='POLYGON(('.$rect->minx.' '.$rect->miny.','.$rect->maxx.' '.$rect->miny;
      $polygon.=','.$rect->maxx.' '.$rect->maxy.','.$rect->minx.' '.$rect->maxy;
      $polygon.=','.$rect->minx.' '.$rect->miny.'))';
    }
  }
  return $polygon;
}

function output_handler($img) {
   header('Content-type: image/png');
   header('Content-Length: ' . strlen($img));
   return $img;
}
function getArrayOfChars() {
	$characters = array();
	$characterNumbers = array();

	for ($i=48; $i<=57; $i++) {
	  $characterNumbers[]=$i; # Zahlen
	}

	for ($i=65; $i<=90; $i++) {
	  $characterNumbers[]=$i; # Großbuchstaben
	}
	for ($i=97; $i<=122; $i++) {
	  $characterNumbers[]=$i; # Kleinbuchstaben
	}
	
	array_push($characterNumbers,223,196,228,214,246,220,252); # Sonderzeichen
	
	foreach ($characterNumbers as $characterNumber) {
	  $characters[] = chr($characterNumber);
	}
	return $characters;
}

function url_get_contents($url, $username = NULL, $password = NULL) {
	$hostname = parse_url($url, PHP_URL_HOST);
	try {
		$ctx['http']['timeout'] = 20;
		if($username)$ctx['http']['header'] = "Authorization: Basic ".base64_encode($username.':'.$password);
		if(defined('HTTP_PROXY') AND $hostname != 'localhost'){
			$ctx['http']['proxy'] = HTTP_PROXY;
			$ctx['http']['request_fulluri'] = true;
			$ctx['ssl']['SNI_server_name'] = $hostname;
			$ctx['ssl']['SNI_enabled'] = true;
		}
		$context = stream_context_create($ctx);
		$response = @ file_get_contents($url, false, $context);
		if ($response === false) {
			throw new Exception("Fehler beim Abfragen der URL mit file_get_contents(".$url.")");
		}
	}	
	catch (Exception $e) {
		$response = curl_get_contents($url, $username, $password);
	}
	return $response;
}

function curl_get_contents($url, $username = NULL, $password = NULL) {
	$url_parts = explode('?',  $url);
	parse_str($url_parts[1], $get_array);
  $ch = curl_init($url_parts[0]);		# url
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if($username)curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);	
	curl_setopt($ch, CURLOPT_POST, true);
	#curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $get_array);	
	$result = curl_exec($ch);
  if (curl_getinfo($ch, CURLINFO_HTTP_CODE)==404) {
		$result = "Fehler 404: File not found. Die Resource konnte mit der URL: ".$url." nicht auf dem Server gefunden werden!";
  }
  curl_close($ch);
  return $result;
}

function debug_write($msg, $debug = false) {
  #$fp = fopen(LOGPATH.'debug.htm','a+');
	#$log = getTimestamp().":\n";
	#$msg = "- ".$msg."\n";
	#fwrite($fp,$log.$msg);
	#fclose($fp);
}

function getTimestamp() {
  $microtime = floatval(substr((string)microtime(), 1, 8));
  $rounded = round($microtime, 5);
  return date("d.m.Y H:i:s") . substr((string)$rounded, 1, strlen($rounded));
}

function formatBytes($size, $precision = 2) {
  $base = log($size) / log(1024);
  $suffixes = array('', 'kB', 'MB', 'GB', 'TB');   
  return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
}

function get_upload_error_message($code) { 
  switch ($code) { 
    case UPLOAD_ERR_INI_SIZE: 
        $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
        break; 
    case UPLOAD_ERR_FORM_SIZE: 
        $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form"; 
        break; 
    case UPLOAD_ERR_PARTIAL: 
        $message = "The uploaded file was only partially uploaded"; 
        break; 
    case UPLOAD_ERR_NO_FILE: 
        $message = "No file was uploaded"; 
        break; 
    case UPLOAD_ERR_NO_TMP_DIR: 
        $message = "Missing a temporary folder"; 
        break; 
    case UPLOAD_ERR_CANT_WRITE: 
        $message = "Failed to write file to disk"; 
        break; 
    case UPLOAD_ERR_EXTENSION: 
        $message = "File upload stopped by extension"; 
        break; 

    default: 
        $message = "Unknown upload error"; 
        break; 
	} 
  return $message;
}

function formvars_strip($formvars, $strip_list) {
	$strip_array = explode(', ', $strip_list);
	$stripped_formvars = array();
	foreach($formvars AS $key => $value) {
		if (!in_array($key, $strip_array)) {
#			if (array_key_exists($key, $stripped_formvars)) {
#				// 1. occurance of key in stripped_formvars: first_value, 2. occurance: [first_val, second_val], more occurances [first_val, second_val, third_val, ...]
#				is_array($stripped_formvars[$key]) ? array_push($stripped_formvars[$key], $value) : $stripped_formvars[$key] = array($stripped_formvars[$key], $value);
#			} else {
				$pos = strpos($value, '[');
				if ($pos === false) {
					$stripped_formvars[$key] = stripslashes($value);	
				} else {
					$stripped_formvars[$key] = arrStrToArr(stripslashes($value), ',');
				}
#			}
		}
	}
	return $stripped_formvars;
}

/**
* Funktion sendet e-mail mit Dateien im Anhang
* siehe http://www.php-einfach.de/codeschnipsel_1114.php
* @param $anhang Array mit den Elementen "name", "size" und "data" oder Array mit Elementen solcher Arrays
* $pfad = array(); 
* $pfad[] = "ordner/datei1.exe"; 
* $pfad[] = "ordner/datei2.zip"; 
* $pfad[] = "ordner/datei3.gif"; 
* 
* $anhang = array(); 
* foreach($pfad AS $name) {
*   $name = basename($name); 
*   $size = filesize($name); 
*   $data = implode("",file($name)); 
*   if (function_exists("mime_content_type")) 
*     $type = mime_content_type($name); 
*   else 
*     $type = "application/octet-stream"; 
*     $anhang[] = array("name"=>$name, "size"=>$size, "type"=>$type, "data"=>$data); 
* }
* mail_att("empf@domain","Email mit Anhang","Im Anhang sind mehrere Datei",$anhang); 
**/
function mail_att($from_name, $from_email, $to_email, $cc_email, $reply_email, $subject, $message, $attachement, $mode, $smtp_server, $smtp_port) {
	$success = false;
	switch ($mode) {
		case 'sendEmail async': {
			# Erstelle Befehl für sendEmail und schreibe in mail queue Verzeichnis.
			$str = array('to_email' => $to_email, 'from_email' => $from_email, 'subject' => $subject, 'message' => $message, 'attachment' => $attachement);
			if(!is_dir(MAILQUEUEPATH)){
				mkdir(MAILQUEUEPATH);
				chmod(MAILQUEUEPATH, 'g+w');
			}
			$file = MAILQUEUEPATH . 'email' . date('YmdHis', time()) . '_' . uniqid('', false) . '.txt';
			$success = file_put_contents(
				$file,
				json_encode($str)
			);
		} break;
		default : {
			$grenze = "---" . md5(uniqid(mt_rand(), 1)) . "---";

			$headers ="MIME-Version: 1.0\r\n";
			$headers .= 'From: ' . $from_email . "\r\n";
			$headers .= 'Reply-To: ' . $reply_email . "\r\n";
			if (!empty($cc_email)) $headers .= 'Cc: ' . $cc_email . "\r\n";
			$headers .= "Content-Type: multipart/mixed;\n\tboundary=$grenze\r\n";

			$botschaft = "\n--$grenze\n";
			$botschaft.="Content-transfer-encoding: 7BIT\r\n";
			$botschaft.="Content-type: text/plain; charset=UTF-8\n\n";
			$botschaft.= $message;

			if ($attachement) {
				$botschaft.="\n\n";
				$botschaft.="\n--$grenze\n";

				$botschaft.="Content-Type: application/octetstream;\n\tname=" . basename($attachement) . "\n";
				$botschaft.="Content-Transfer-Encoding: base64\n";
				$botschaft.="Content-Disposition: attachment;\n\tfilename=" . basename($attachement) . "\n\n";

				$zeiger_auf_datei=fopen($attachement,"rb");
				$inhalt_der_datei=fread($zeiger_auf_datei,filesize($attachement));
				fclose($zeiger_auf_datei);

				$inhalt_der_datei=chunk_split(base64_encode($inhalt_der_datei));
				$botschaft.=$inhalt_der_datei;
				$botschaft.="\n\n";
				$botschaft.="--$grenze";
			}
			#  echo 'to_email: '.$to_email.'<br>';
			#  echo 'subject: '.$subject.'<br>';
			#  echo 'botschaft: '.$botschaft.'<br>';
			#  echo 'headers: '.$headers.'<br>';  
			$success = @mail($to_email, $subject, $botschaft, $headers);
		}
	}
	if ($success)
		return 1;
	else
		return 0;
}

/*
* function replaced square brackets at the beginning and the end of the string
* and return the elements of the string as array separated by the delimmiter.
* The elements of the string will be replaced by slashes and timed from white spaces and ".
*/
function arrStrToArr($str, $delimiter) {
#	if(is_string($delimiter) and in_array())
#	echo gettype($delimiter);
	$arr = explode($delimiter, trim($str, '[]'));
	foreach ($arr as &$value) {
		$value = trim(stripslashes($value), '"[]"');
	}
	return $arr;
}
?>
