<?php
###################################################################
# kvwmap - Kartenserver f�r Kreisverwaltungen                     #
###################################################################
# Lizenz                                                          #
#                                                                 # 
# Copyright (C) 2004  Peter Korduan                               #
#                                                                 # 
# This program is free software; you can redistribute it and/or   #
# modify it under the terms of the GNU General Public License as  # 
# published by the Free Software Foundation; either version 2 of  # 
# the License, or (at your option) any later version.             # 
#                                                                 #   
# This program is distributed in the hope that it will be useful, #  
# but WITHOUT ANY WARRANTY; without even the implied warranty of  #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the    #
# GNU General Public License for more details.                    #
#                                                                 #  
# You should have received a copy of the GNU General Public       #
# License along with this program; if not, write to the Free      #
# Software Foundation, Inc., 59 Temple Place, Suite 330, Boston,  # 
# MA 02111-1307, USA.                                             # 
#                                                                 #
# Kontakt:                                                        #
# peter.korduan@gdi-service.de                                    #
# stefan.rahn@gdi-service.de                                      #
###################################################################
#############################
# Klasse synchro #
#############################

class synchro {
	
	function synchro($stelle, $user, $database) {
		$this->Stelle = $stelle;
		$this->user = $user;
		$this->database = $database;
  }
   
  function export_layer_tables($export_layerset, $formvars){		# geht bisher nur mit Layern mit einer Tabelle
		$mapDB = new db_mapObj($this->Stelle->id,$this->user->id);
		$this->count = 0;
		for($i = 0; $i < count($export_layerset); $i++){
			$this->commands = array();
			$layerdb = $mapDB->getlayerdatabase($export_layerset[$i]['Layer_ID'], $this->Stelle->pgdbhost);
			$attributes = $mapDB->read_layer_attributes($export_layerset[$i]['Layer_ID'], $layerdb, NULL);
			$currenttime=date('Y-m-d_H-i-s',time());
    	$this->trans_id[$i] = $this->user->id."_".$currenttime."_".$export_layerset[$i]['Layer_ID'];
    	$where = " WHERE ST_WITHIN(st_transform(".$attributes['all_table_names'][0].".".$attributes['the_geom'].", ".$this->user->rolle->epsg_code."), st_geomfromtext('".$formvars['newpathwkt']."', ".$this->user->rolle->epsg_code."))";
			if($this->export_layer_table_data($mapDB, $this->trans_id[$i], $attributes, $layerdb, $export_layerset[$i]['Layer_ID'], $where, $formvars['leeren'], $formvars['mitbildern'], $formvars['username'], $formvars['passwort'])){
				$this->commands[] = POSTGRESBINPATH."psql -U ".$this->database->user." -f ".SYNC_PATH.$this->trans_id[$i].".sql ".$this->database->dbName;
				$this->commands = array_reverse($this->commands);		# Die Reihenfolge der Datenimporte muss umgedreht werden, damit erst die �bergeordneten Tabellen eingespielt werden und dann die abh�ngigen (ansonsten k�nnte es sein, dass abh�ngige Tabellen auf Grund eines Delete Cascade-Constraints wieder gel�scht werden)
				foreach($this->commands AS $command){
					exec($command, $output, $ret);
				}
			}
			if($ret == 0){
				$this->result['count'] = $this->count;
			}
		}
	}
	
	function import_layer_tables($import_layerset, $formvars){		# geht bisher nur mit Layern mit einer Tabelle
		$mapDB = new db_mapObj($this->Stelle->id,$this->user->id);
		$this->newcount = 0;
		$this->oldcount = 0;
		for($i = 0; $i < count($import_layerset); $i++){
			$this->commands = array();
			$layerdb = $mapDB->getlayerdatabase($import_layerset[$i]['Layer_ID'], $this->Stelle->pgdbhost);
			$attributes = $mapDB->read_layer_attributes($import_layerset[$i]['Layer_ID'], $layerdb, NULL);
			if($this->import_layer_table_data($mapDB, $attributes, $layerdb, $import_layerset[$i]['Layer_ID'], $import_layerset[$i]['Name'], $formvars['mitbildern'], $formvars['username'], $formvars['passwort'])){
				$this->commands[] = POSTGRESBINPATH."psql -U ".$this->database->user." -f ".SYNC_PATH.$import_layerset[$i]['Layer_ID'].".sql ".$this->database->dbName;
				$this->commands = array_reverse($this->commands);		# Die Reihenfolge der Datenimporte muss umgedreht werden, damit erst die �bergeordneten Tabellen eingespielt werden und dann die abh�ngigen (ansonsten k�nnte es sein, dass abh�ngige Tabellen auf Grund eines Delete Cascade-Constraints wieder gel�scht werden)
				foreach($this->commands AS $command){
					exec($command, $output, $ret);
				}
			}
			if($ret == 0){
				$this->result['newcount'] = $this->newcount;
				$this->result['oldcount'] = $this->oldcount;
			}
		}
	}
	
	function export_layer_table_data($mapDB, $trans_id, $attributes, $layerdb, $layer_id, $where, $truncate, $withimages, $username, $passwort){
		$this->already_exported_layers[] = $layer_id;
		$sql = "UPDATE ".$attributes['all_table_names'][0]." SET lock = '".$trans_id."|'||oid ".$where." AND lock IS NULL";
	  #echo $sql.'<br>';
	  $ret = $layerdb->execSQL($sql, 4, 0);
		$sql = "SELECT * FROM ".$attributes['all_table_names'][0];
		$sql.= $where;																		
		$sql.= " AND '".$trans_id."' = split_part(lock, '|', 1)";			# nur die mit dem entsprechenden Lock abfragen
		#echo $sql;
    $ret = $layerdb->execSQL($sql, 4, 0);
    if(pg_num_rows($ret[1]) > 0){
	    $fp = fopen(SYNC_PATH.$trans_id.".sql", "a");
	    fwrite($fp, "SET datestyle TO 'German';".chr(10));
	    if($truncate == 'on'){
				fwrite($fp, "DELETE FROM ".$layerdb->schema.".".$attributes['all_table_names'][0].";".chr(10));
			}
	    fwrite($fp, "COPY ".$layerdb->schema.".".$attributes['all_table_names'][0]." FROM STDIN WITH DELIMITER AS '~' CSV;".chr(10));
	    
	    # abh�ngige Layer auch exportieren, hier erstmal die Verkn�pfungsattribute f�r jeden verkn�pften Layer zusammen sammeln
	    $j = 0;
	    for($i = 0; $i < count($attributes['name']); $i++){
	    	if(in_array($attributes['form_element_type'][$i], array('SubFormEmbeddedPK', 'SubFormPK', 'SubFormFK'))){
					$options = explode(';', $attributes['options'][$i]);  
          $subform = explode(',', $options[0]);  
          if(!in_array($subform[0], $this->already_exported_layers)){
          	$subform_layer[$j]['id'] = $subform[0];
	          if($attributes['form_element_type'][$i] == 'SubFormEmbeddedPK')$minus = 1;
	          else $minus = 0;
	          for($k = 1; $k < count($subform)-$minus; $k++){
	            $subform_layer[$j]['subform_attribute'][$subform[$k]] = array();
	          }
	          $j++;
          }
	    	}
	    }
	    $count = 0;
	    while($rs = pg_fetch_assoc($ret[1])){
	    	$count++;
	    	$this->count++;
	    	for($k = 0; $k < count($rs); $k++){
	    		if($withimages == 'on' AND $attributes['form_element_type'][key($rs)] == 'Dokument' AND $rs[key($rs)] != ''){			# Bilder vom Server holen und auf lokalem Server speichern
	    			$this->imagecount++;
	    			$image_string = file_get_contents($attributes['options'][key($rs)].$rs[key($rs)].'&username='.$username.'&passwort='.$passwort);
	          $name_array=explode('.', $rs[key($rs)]);
	          $datei_erweiterung = array_pop($name_array);
	          $filename = CUSTOM_IMAGE_PATH.rand(10000, 1000000).'.'.$datei_erweiterung;
	          $new_image = fopen($filename, 'w');
	          fwrite($new_image, $image_string);
	          fclose($new_image);
	          $rs[key($rs)] = $filename;
	    		}
	    		if($k > 0){
	    			fwrite($fp, '~');
	    		}
	      	fwrite($fp, $rs[key($rs)]);
	      	# abh�ngige Layer auch exportieren, hier dann die Werte der Verkn�pfungsattribute zu einzelnen WHERE-Bedingungen zusammen bauen
	      	for($j = 0; $j < count($subform_layer); $j++){
	      		if(is_array($subform_layer[$j]['subform_attribute'][key($rs)])){
	      			$subform_layer[$j]['where'][$count] .= key($rs)." = '".$rs[key($rs)]."' AND "; 
	      		}
	      	}
	      	next($rs);
	    	}
	    	fwrite($fp, chr(10));
	    }
	    fwrite($fp, "\.".chr(10));
	    fclose($fp);
	    # abh�ngige Layer auch exportieren, hier wird diese Funktion f�r jeden verkn�pften Layer rekursiv mit der zusammengesetzten WHERE-Bedingung aufgerufen
	    for($j = 0; $j < count($subform_layer); $j++){
	    	$subform_layer[$j]['where_gesamt'] = " WHERE (".implode('1=1 OR ', $subform_layer[$j]['where'])." 1=1)";
    		$attributes = $mapDB->read_layer_attributes($subform_layer[$j]['id'], $layerdb, NULL);
    		$currenttime=date('Y-m-d_H-i-s',time());
	    	$trans_id = $this->user->id."_".$currenttime."_".$subform_layer[$j]['id'];
				if($this->export_layer_table_data($mapDB, $trans_id, $attributes, $layerdb, $subform_layer[$j]['id'], $subform_layer[$j]['where_gesamt'], $truncate, $withimages, $username, $passwort)){
					$this->commands[] = POSTGRESBINPATH."psql -U ".$this->database->user." -f ".SYNC_PATH.$trans_id.".sql ".$this->database->dbName;
				}
    	}
    	return true;
    }
    return false;
	}
	
	function import_layer_table_data($mapDB, $attributes, $layerdb, $layer_id, $layername, $withimages, $username, $passwort){
		$this->already_imported_layers[] = $layer_id;
		# erst alle neuen Datens�tze
		$sql = "SELECT * FROM ".$attributes['all_table_names'][0];
		//for($j = 1; $j < count($attributes['all_table_names']); $j++){
			//$sql.= ", ".$attributes['all_table_names'][$j];
		//}
		$sql.= " WHERE lock IS NULL";
		#echo $sql;
    $ret = $layerdb->execSQL($sql, 4, 0);
    
    $fp = fopen(SYNC_PATH.$layer_id.".sql", "w");
    fwrite($fp, "SET datestyle TO 'German';".chr(10));
    fwrite($fp, "COPY ".$layerdb->schema.".".$attributes['all_table_names'][0]." FROM STDIN WITH DELIMITER AS '~' CSV;".chr(10));
    $i = 0;
    while($rs = pg_fetch_assoc($ret[1])){
    	$this->newcount++;
    	for($k = 0; $k < count($rs); $k++){
    		if($withimages == 'on' AND $attributes['form_element_type'][key($rs)] == 'Dokument' AND $rs[key($rs)] != ''){			# Bilder vom Server holen und auf lokalem Server speichern
    			$i++;
    			$image_string = file_get_contents($attributes['options'][key($rs)].$rs[key($rs)].'&username='.$username.'&passwort='.$passwort);
          $name_array=explode('.', $rs[key($rs)]);
          $datei_erweiterung = array_pop($name_array);
          $filename = CUSTOM_IMAGE_PATH.rand(10000, 1000000).'.'.$datei_erweiterung;
          $new_image = fopen($filename, 'w');
          fwrite($new_image, $image_string);
          fclose($new_image);
          $rs[key($rs)] = $filename.'&original_name='.$layername.'_'.$i.'.'.$datei_erweiterung;
    		}
    		if($k > 0){
    			fwrite($fp, '~');
    		}
      	fwrite($fp, $rs[key($rs)]);
      	next($rs);
    	}
    	fwrite($fp, chr(10));
    }
    fwrite($fp, "\.".chr(10));
    
    # dann die bearbeiteten
    $sql = "SELECT * FROM ".$attributes['all_table_names'][0];
		//for($j = 1; $j < count($attributes['all_table_names']); $j++){
		//	$sql.= ", ".$attributes['all_table_names'][$j];
		//}
		$sql.= " WHERE lock IS NOT NULL";
		#echo $sql;
    $ret = $layerdb->execSQL($sql, 4, 0);
    $fp = fopen(SYNC_PATH.$layer_id.".sql", "a");
    while($rs = pg_fetch_assoc($ret[1])){
    	if($rs['lock'] != 'bereits �bertragen'){
	    	$this->oldcount++;
	    	$trans_id = $rs['lock']; 
	    	fwrite($fp, "UPDATE ".$layerdb->schema.".".$attributes['all_table_names'][0]." SET ");
	    	for($k = 0; $k < count($rs); $k++){
	    		if($withimages == 'on' AND $attributes['form_element_type'][key($rs)] == 'Dokument' AND $rs[key($rs)] != ''){			# Bilder vom Server holen und auf lokalem Server speichern
	    			$i++;
	    			$image_string = file_get_contents($attributes['options'][key($rs)].$rs[key($rs)].'&username='.$username.'&passwort='.$passwort);
	          $name_array=explode('.', $rs[key($rs)]);
	          $datei_erweiterung = array_pop($name_array);
	          $filename = CUSTOM_IMAGE_PATH.rand(10000, 1000000).'.'.$datei_erweiterung;
	          $new_image = fopen($filename, 'w');
	          fwrite($new_image, $image_string);
	          fclose($new_image);
	          $rs[key($rs)] = $filename.'&original_name='.$layername.'_'.$i.'.'.$datei_erweiterung;
	    		}
	    		if($k > 0){
	    			fwrite($fp, ',');
	    		}
	    		if(key($rs) == 'lock' OR $rs[key($rs)] == ''){
	    			fwrite($fp, key($rs)."= NULL");							# lock wieder freigeben
	    		}
	    		else{
	      		fwrite($fp, key($rs)."='".addslashes($rs[key($rs)])."'");
	    		}
	      	next($rs);
	    	}
	    	fwrite($fp, " WHERE lock = '".$trans_id."';".chr(10));
    	}
    }
    #die Datens�tze in der Spalte lock als bereits �bertragen kennzeichnen
    $sql = "UPDATE ".$attributes['all_table_names'][0]." SET lock = 'bereits �bertragen'";
    #echo $sql;
    $ret1 = $layerdb->execSQL($sql, 4, 0);
    
    # dann die Datens�tze l�schen, die exportiert wurden aber nicht wieder zur�ckgespielt
    if($trans_id != ''){
    	$t_id = explode('|', $trans_id);
	    fwrite($fp, "DELETE FROM ".$layerdb->schema.".".$attributes['all_table_names'][0]." WHERE '".$t_id[0]."' = split_part(lock, '|', 1);".chr(10));
    }
    fclose($fp);
    
    # abh�ngige Layer auch importieren
    for($i = 0; $i < count($attributes['name']); $i++){
    	if(in_array($attributes['form_element_type'][$i], array('SubFormEmbeddedPK', 'SubFormPK', 'SubFormFK'))){
				$options = explode(';', $attributes['options'][$i]);  
        $subform = explode(',', $options[0]);  
        if(!in_array($subform[0], $this->already_imported_layers)){
        	$subform_layer = $subform[0];
          $attributes = $mapDB->read_layer_attributes($subform_layer, $layerdb, NULL);
					if($this->import_layer_table_data($mapDB, $attributes, $layerdb, $subform_layer, $withimages, $username, $passwort)){
						$this->commands[] = POSTGRESBINPATH."psql -U ".$this->database->user." -f ".SYNC_PATH.$subform_layer.".sql ".$this->database->dbName;
					}
        }
    	}
    }    
    return true;
	}
 
}
?>
