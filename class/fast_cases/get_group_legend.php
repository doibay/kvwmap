<?
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

  $passwordSettingUnixTime=strtotime($passwordSettingTime); # Unix Zeit in Sekunden an dem das Passwort gesetzt wurde
  $allowedPasswordAgeDays=round($allowedPassordAgeMonth*30.5); # Zeitintervall, wie alt das Password sein darf in Tagen
  $passwordAgeDays=round((time()-$passwordSettingUnixTime)/60/60/24); # Zeitinterval zwischen setzen des Passwortes und aktueller Zeit in Tagen
  $allowedPasswordAgeRemainDays=$allowedPasswordAgeDays-$passwordAgeDays; # Zeitinterval wie lange das Passwort noch gilt in Tagen
	return $allowedPasswordAgeRemainDays; // Passwort ist abgelaufen wenn Wert < 1  
}

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

    # Debugdatei setzen
    global $debug;
    $this->debug=$debug;
    # Logdatei für Mysql setzen
    global $log_mysql;
    $this->log_mysql=$log_mysql;
    # Logdatei für PostgreSQL setzten
    global $log_postgres;
    $this->log_postgres=$log_postgres;
    # layout Templatedatei zur Anzeige der Daten
    if ($main!="") $this->main=$main;
    # Stylesheetdatei
    if (isset($style)) $this->style=$style;
    # mime_type html, pdf
    if (isset ($mime_type)) $this->mime_type=$mime_type;
		$this->scaleUnitSwitchScale = 239210;
  }

    #echo 'In der Rolle eingestellte Sprache: '.$GUI->user->rolle->language;
    $this->Stelle->language=$language;
    $this->Stelle->getName();
    include(LAYOUTPATH.'languages/'.$this->user->rolle->language.'.php');
  }

    # Änderungen in den Gruppen werden gesetzt
    $this->formvars = $this->user->rolle->setGroupStatus($this->formvars);
    # Ein- oder Ausblenden der Klassen
    #$this->user->rolle->setClassStatus($this->formvars); ???
    $this->loadMap('DataBase');
    echo $this->create_group_legend($this->formvars['group']);
  } 

    $this->debug->write("<p>Funktion: loadMap('".$loadMapSource."','".$connStr."')",4);
    switch ($loadMapSource) {
      # lade Karte aus Post-Parametern
      case 'Post' : {
        if (MAPSERVERVERSION < 600) {
				  $map = ms_newMapObj(SHAPEPATH.'MapFiles/tk_niedersachsen.map');
				}
				else {
				  $map = new mapObj(SHAPEPATH.'MapFiles/tk_niedersachsen.map');
				}
				echo '<br>MapServer Version: '.ms_GetVersionInt();
				echo '<br>Details: '.ms_GetVersion();	

        # Allgemeine Parameter
        #var_dump($this->formvars);
        $map->width = $this->formvars['post_width'];
        $map->set('height', $this->formvars['post_height']);
        $map->set('resolution',72);
        $map->set('units',MS_METERS);
        #$map->set('transparent', MS_OFF);
        #$map->set('interlace', MS_ON);
        $map->set('status', MS_ON);
        $map->set('name', MAPFILENAME);
        $map->imagecolor->setRGB(255,255,255);
        if($this->formvars['post_minx'] != ''){
          $map->setextent($this->formvars['post_minx'], $this->formvars['post_miny'], $this->formvars['post_maxx'], $this->formvars['post_maxy']);
        }
        else{
          $map->setextent($this->user->rolle->oGeorefExt->minx,$this->user->rolle->oGeorefExt->miny,$this->user->rolle->oGeorefExt->maxx,$this->user->rolle->oGeorefExt->maxy);
        }
        $map->setProjection('+init='.strtolower($this->formvars['post_epsg']),MS_TRUE);

        $map->setSymbolSet(SYMBOLSET);
        $map->setFontSet(FONTSET);
        $map->set('shapepath', SHAPEPATH);

        # Webobject
        $map->web->set('imagepath', IMAGEPATH);
        $map->web->set('imageurl', IMAGEURL);

        # OWS Metadaten
        $map->setMetaData('ows_title', 'WMS Ausdruck');
        $map->setMetaData('wms_extent',$this->formvars['post_minx'].''.$this->formvars['post_miny'].' '.$this->formvars['post_maxx'].' '.$this->formvars['post_maxy']);

        # Legendobject
        $map->legend->set('status', MS_ON);
        #$map->legend->set('transparent', MS_OFF);
        $map->legend->set('keysizex', '16');
        $map->legend->set('keysizey', '16');
        $map->legend->set('template', LAYOUTPATH.'legend_layer.htm');
        $map->legend->imagecolor -> setRGB(255,255,255);
        $map->legend->outlinecolor -> setRGB(-1,-1,-1);
        $map->legend->label->set('type', MS_TRUETYPE);
        $map->legend->label->set('font', 'arial');
        $map->legend->label->set('size', 12);
        $map->legend->label->color->setRGB(5,30,220);

        # layer
        if (is_array($this->formvars['layer'])) {
          $layerset=array_values($this->formvars['layer']);
        }
        else {
          $layerset=array();
        }
        for ($i=0; $i<count($layerset); $i++) {
				  if (MAPSERVERVERSION < 600) {
            $layer = ms_newLayerObj($map);
          }
					else {
					  $layer = new layerObj($map);
					}
					$layer->setMetaData('wms_name', $layerset[$i][name]);
          $layer->setMetaData('wms_server_version','1.1.1');
          $layer->setMetaData('wms_format','image/png');
          $layer->setMetaData('wms_extent',$this->formvars['post_minx'].' '.$this->formvars['post_miny'].' '.$this->formvars['post_maxx'].' '.$this->formvars['post_maxy']);
          $layer->setMetaData('ows_title', $layerset[$i][name]);
          if($layerset[$i][epsg_code] != ''){
            $layer->setMetaData('ows_srs', $layerset[$i][epsg_code]);
          }
          else{
            $layer->setMetaData('ows_srs', $this->formvars['post_epsg']);
          }
          $layer->setMetaData('wms_exceptions_format', 'application/vnd.ogc.se_inimage');
          $layer->setMetaData('real_layer_status', 1);
          $layer->setMetaData('off_requires',0);
          $layer->setMetaData('wms_connectiontimeout',60);
          $layer->setMetaData('wms_queryable',0);
          $layer->setMetaData('wms_group_title','WMS');
          $layer->set('type', 3);
          $layer->set('name', $layerset[$i][name]);
          $layer->set('status', 1);
          if($this->map_factor == ''){
            $this->map_factor=1;
          }
          if ($layerset[$i]['maxscale'] > 0) {
            if(MAPSERVERVERSION > 500){
              $layer->set('maxscaledenom', $layerset[$i]['maxscale']/$this->map_factor*1.414);
            }
            else{
              $layer->set('maxscale', $layerset[$i]['maxscale']/$this->map_factor*1.414);
            }
          }
          if ($layerset[$i]['minscale'] > 0) {
            if(MAPSERVERVERSION > 500){
              $layer->set('minscaledenom', $layerset[$i]['minscale']/$this->map_factor*1.414);
            }
            else{
              $layer->set('minscale', $layerset[$i]['minscale']/$this->map_factor*1.414);
            }
          }
          if($layerset[$i][epsg_code] != ''){
            $layer->setProjection('+init='.strtolower($layerset[$i][epsg_code])); # recommended
          }
          else{
            $layer->setProjection('+init='.strtolower($this->formvars['post_epsg']));
          }
          #$layer->set('connection',"http://www.kartenserver.niedersachsen.de/wmsconnector/com.esri.wms.Esrimap/Biotope?LAYERS=7&REQUEST=GetMap&TRANSPARENT=true&FORMAT=image/png&SERVICE=WMS&VERSION=1.1.1&STYLES=&EXCEPTIONS=application/vnd.ogc.se_xml&SRS=EPSG:31467");
          #echo '<br>Name: '.$layerset[$i][name];
          #echo '<br>Connection: '.$layerset[$i][connection];
          $layer->set('connection', $layerset[$i][connection]);
          if (MAPSERVERVERSION < 540) {
			      $layer->set('connectiontype', 7);
			    }
			    else {
			      $layer->setConnectionType(7);
			    }
          if($layerset[$i]['transparency'] != ''){
            if(MAPSERVERVERSION > 500){
              $layer->set('opacity',$layerset[$i]['transparency']);
            }
            else{
              $layer->set('transparency',$layerset[$i]['transparency']);
            }
          }
        } # end of Schleife layer
        $this->map=$map;
      } break;

      # lade Karte von einer Map-Datei
      case 'File' : {
        $debug->write("MapDatei $connStr laden",4);
				if (MAPSERVERVERSION < 600) {
          $this->map = ms_newMapObj(DEFAULTMAPFILE);
        }
				else {
				  $this->map = new mapObj(DEFAULTMAPFILE);
				}
			} break;

      # lade Karte von Datenbank
      case 'DataBase' : {
				if (MAPSERVERVERSION < 600) {
							$map = ms_newMapObj(DEFAULTMAPFILE);
						}
				else {
					$map = new mapObj(DEFAULTMAPFILE);
				}
        $mapDB = new db_mapObj($this->Stelle->id,$this->user->id);
        
        # Allgemeine Parameter
        $map->set('width',$this->user->rolle->nImageWidth);
        $map->set('height',$this->user->rolle->nImageHeight);
        $map->set('resolution',96);      
        #$map->set('transparent', MS_OFF);
        #$map->set('interlace', MS_ON);
        $map->set('status', MS_ON);
        $map->set('name', MAPFILENAME);
        $map->set('debug', MS_ON);
        $map->imagecolor->setRGB(255,255,255);
        $map->maxsize = 4096;
        $map->setProjection('+init=epsg:'.$this->user->rolle->epsg_code,MS_TRUE);
				
				# setzen der Kartenausdehnung über die letzten Benutzereinstellungen
				if($this->user->rolle->oGeorefExt->minx==='') {
				  echo "Richten Sie mit phpMyAdmin in der kvwmap Datenbank eine Referenzkarte, eine Stelle, einen Benutzer und eine Rolle ein ";
				  echo "<br>(Tabellen referenzkarten, stelle, user, rolle) ";
				  echo "<br>oder wenden Sie sich an ihren Systemverwalter.";
				  exit;
				}
				else {
				  $map->setextent($this->user->rolle->oGeorefExt->minx,$this->user->rolle->oGeorefExt->miny,$this->user->rolle->oGeorefExt->maxx,$this->user->rolle->oGeorefExt->maxy);
        }
        
        # OWS Metadaten

        if($this->Stelle->ows_title != ''){
          $map->setMetaData("ows_title",$this->Stelle->ows_title);}
        else{
          $map->setMetaData("ows_title",OWS_TITLE);
        }
        if($this->Stelle->ows_abstract != ''){
          $map->setMetaData("ows_abstract",$this->Stelle->ows_abstract);}
        else{
          $map->setMetaData("ows_title",OWS_ABSTRACT);
        }
        if($this->Stelle->wms_accessconstraints != ''){
          $map->setMetaData("wms_accessconstraints",$this->Stelle->wms_accessconstraints);}
        else{
          $map->setMetaData("wms_accessconstraints",OWS_ACCESSCONSTRAINTS);
        }
        if($this->Stelle->ows_contactperson != ''){
          $map->setMetaData("ows_contactperson",$this->Stelle->ows_contactperson);}
        else{
          $map->setMetaData("ows_contactperson",OWS_CONTACTPERSON);
        }
        if($this->Stelle->ows_contactorganization != ''){
          $map->setMetaData("ows_contactorganization",$this->Stelle->ows_contactorganization);}
        else{
          $map->setMetaData("ows_contactorganization",OWS_CONTACTORGANIZATION);
        }
        if($this->Stelle->ows_contactelectronicmailaddress != ''){
          $map->setMetaData("ows_contactelectronicmailaddress",$this->Stelle->ows_contactelectronicmailaddress);}
        else{
          $map->setMetaData("ows_contactelectronicmailaddress",OWS_CONTACTELECTRONICMAILADDRESS);
        }
        if($this->Stelle->ows_contactposition != ''){
          $map->setMetaData("ows_contactposition",$this->Stelle->ows_contactposition);}
        else{
          $map->setMetaData("ows_contactposition",OWS_CONTACTPOSITION);
        }
        if($this->Stelle->ows_fees != ''){
          $map->setMetaData("ows_fees",$this->Stelle->ows_fees);}
        else{
          $map->setMetaData("ows_fees",OWS_FEES);
        }
        if($this->Stelle->ows_srs != ''){
          $map->setMetaData("ows_srs",$this->Stelle->ows_srs);}
        else{
          $map->setMetaData("ows_srs",OWS_SRS);
        }
        $ows_onlineresource = OWS_SERVICE_ONLINERESOURCE.'&Stelle_ID='.$this->Stelle->id;
        $map->setMetaData("ows_onlineresource",$ows_onlineresource);
        $bb=$this->Stelle->MaxGeorefExt;
        $map->setMetaData("wms_extent",$bb->minx.' '.$bb->miny.' '.$bb->maxx.' '.$bb->maxy);
        ///------------------------------////

        $map->setSymbolSet(SYMBOLSET);
        $map->setFontSet(FONTSET);
        $map->set('shapepath', SHAPEPATH);        

        # Umrechnen des Stellenextents kann hier raus, weil es schon in start.php gemacht wird
                
        # Webobject
        $map->web->set('imagepath', IMAGEPATH);
        $map->web->set('imageurl', IMAGEURL);
        $map->web->set('log', LOGPATH.'mapserver.log');
        $map->setMetaData('wms_feature_info_mime_type',  'text/html');
        //$map->web->set('ERRORFILE', LOGPATH.'mapserver_error.log');

        # Referenzkarte
				$this->ref=$mapDB->read_ReferenceMap();
				if(MAPSERVERVERSION < 600){
					$reference_map = ms_newMapObj(DEFAULTMAPFILE);
				}
				else {
					$reference_map = new mapObj(DEFAULTMAPFILE);
				}
				$reference_map->web->set('imagepath', IMAGEPATH);
				$reference_map->setProjection('+init=epsg:'.$this->ref['epsg_code'],MS_FALSE);
				#$reference_map->extent->setextent in drawreferencemap() ausgelagert, da der Extent sich geändert haben kann nach dem loadmap
				$reference_map->reference->extent->setextent(round($this->ref['xmin']),round($this->ref['ymin']),round($this->ref['xmax']),round($this->ref['ymax']));
        $reference_map->reference->set('image',REFERENCEMAPPATH.$this->ref['Dateiname']);
        $reference_map->reference->set('width',$this->ref['width']);
        $reference_map->reference->set('height',$this->ref['height']);
        $reference_map->reference->set('status','MS_ON');
				if (MAPSERVERVERSION < 600) {
					$extent=ms_newRectObj();
				}
				else {
				  $extent = new rectObj();
				}        
        $reference_map->reference->color->setRGB(-1,-1,-1);
        $reference_map->reference->outlinecolor->setRGB(255,0,0);

        # Scalebarobject
        $map->scalebar->set('status', MS_ON);
        $map->scalebar->set('units', MS_METERS);
        $map->scalebar->set('intervals', 4);
        $map->scalebar->color->setRGB(0,0,0);
        $r = substr(BG_MENUETOP, 1, 2);
        $g = substr(BG_MENUETOP, 3, 2);
        $b = substr(BG_MENUETOP, 5, 2);
        $map->scalebar->imagecolor->setRGB(hexdec($r), hexdec($g), hexdec($b));
        $map->scalebar->outlinecolor->setRGB(0,0,0);
				$map->scalebar->label->type = 'truetype';
				$map->scalebar->label->font = 'SourceSansPro';
				$map->scalebar->label->size = 10.5;

        # Groups
        if($this->formvars['nurAufgeklappteLayer'] == ''){
	        $this->groupset=$mapDB->read_Groups();
        }

        # Layer
        $mapDB->nurAufgeklappteLayer=$this->formvars['nurAufgeklappteLayer'];
        $mapDB->nurAktiveLayerOhneRequires=$this->formvars['nurAktiveLayerOhneRequires'];
        $mapDB->nurFremdeLayer=$this->formvars['nurFremdeLayer'];
        if($this->class_load_level == ''){
          $this->class_load_level = 1;
        }
        $layer = $mapDB->read_Layer($this->class_load_level, $this->list_subgroups($this->formvars['group']));     # class_load_level: 2 = für alle Layer die Klassen laden, 1 = nur für aktive Layer laden, 0 = keine Klassen laden
        $rollenlayer = $mapDB->read_RollenLayer();
        $layerset = array_merge($layer, $rollenlayer);
        $layerset['anzLayer'] = count($layerset) - 1; # wegen $layerset['layer_ids']
        unset($this->layers_of_group);		# falls loadmap zweimal aufgerufen wird
				unset($this->groups_with_layers);	# falls loadmap zweimal aufgerufen wird				
        for($i=0; $i < $layerset['anzLayer']; $i++){					
					if($layerset[$i]['alias'] == '' OR !$this->Stelle->useLayerAliases){
						$layerset[$i]['alias'] = $layerset[$i]['Name'];			# kann vielleicht auch in read_layer gesetzt werden
					}
					$this->groups_with_layers[$layerset[$i]['Gruppe']][] = $i;			# die $i's pro Gruppe im layerset-Array
					if($layerset[$i]['requires'] == ''){
						$this->layers_of_group[$layerset[$i]['Gruppe']][] = $layerset[$i]['Layer_ID'];				# die Layer-IDs in einer Gruppe
					}
					$this->layer_id_string .= $layerset[$i]['Layer_ID'].'|';							# alle Layer-IDs hintereinander in einem String
											
					if($layerset[$i]['requires'] != ''){
						$layerset[$i]['aktivStatus'] = $layerset['layer_ids'][$layerset[$i]['requires']]['aktivStatus'];
						$layerset[$i]['showclasses'] = $layerset['layer_ids'][$layerset[$i]['requires']];
					}
					
					if($this->class_load_level == 2 OR $layerset[$i]['requires'] != '' OR ($this->class_load_level == 1 AND $layerset[$i]['aktivStatus'] != 0)){      # nur wenn der Layer aktiv ist (oder ein requires-Layer), sollen seine Parameter gesetzt werden
						$layer = ms_newLayerObj($map);
						$layer->setMetaData('wfs_request_method', 'GET');
						$layer->setMetaData('wms_name', $layerset[$i]['wms_name']);
						$layer->setMetaData('wfs_typename', $layerset[$i]['wms_name']);
						$layer->setMetaData('ows_title', $layerset[$i]['Name']); # required
						$layer->setMetaData('wms_group_title',$layerset[$i]['Gruppenname']);
						$layer->setMetaData('wms_queryable',$layerset[$i]['queryable']);
						$layer->setMetaData('wms_format',$layerset[$i]['wms_format']);
						$layer->setMetaData('ows_server_version',$layerset[$i]['wms_server_version']);
						$layer->setMetaData('ows_version',$layerset[$i]['wms_server_version']);
						$layer->setMetaData('ows_srs',$layerset[$i]['ows_srs']);
						$layer->setMetaData('wms_connectiontimeout',$layerset[$i]['wms_connectiontimeout']);
						$layer->setMetaData('wms_auth_username', $layerset[$i]['wms_auth_username']);
						$layer->setMetaData('wms_auth_password', '{'.$layerset[$i]['wms_auth_password'].'}');
						$layer->setMetaData('wms_auth_type', 'any');
						
						$layer->set('dump', 0);
						$layer->set('type',$layerset[$i]['Datentyp']);
						$layer->set('group',$layerset[$i]['Gruppenname']);
												 
						$layer->set('name', $layerset[$i]['alias']);          

						if($layerset[$i]['status'] != ''){
							$layerset[$i]['aktivStatus'] = 0;
						}
						
						//---- wenn die Layer einer eingeklappten Gruppe nicht in der Karte //
						//---- dargestellt werden sollen, muß hier bei aktivStatus != 1 //
						//---- der layer_status auf 0 gesetzt werden//
						if($layerset[$i]['aktivStatus'] == 0){
						$layer->set('status', 0);
						}
						else{
						$layer->set('status', 1);
						}
						$layer->set('debug',MS_ON);
						
						# fremde Layer werden auf Verbindung getestet 
						if($layerset[$i]['aktivStatus'] != 0 AND $layerset[$i]['connectiontype'] == 6 AND strpos($layerset[$i]['connection'], 'host') !== false AND strpos($layerset[$i]['connection'], 'host=localhost') === false){
						$connection = explode(' ', trim($layerset[$i]['connection']));
								for($j = 0; $j < count($connection); $j++){
								if($connection[$j] != ''){
									$value = explode('=', $connection[$j]);
									if(strtolower($value[0]) == 'host'){
									$host = $value[1];
									}
									if(strtolower($value[0]) == 'port'){
									$port = $value[1];
									}
								}
								}
								if($port == '')$port = '5432';
						$fp = @fsockopen($host, $port, $errno, $errstr, 5);
						if(!$fp){			# keine Verbindung --> Layer ausschalten
							$layer->set('status', 0);
							$layer->setMetaData('queryStatus', 0);
									$this->Fehlermeldung = $errstr.' für Layer: '.$layerset[$i]['Name'].'<br>';
						}
						}
						
						if($layerset[$i]['aktivStatus'] != 0){
							$collapsed = false;
							$group = $this->groupset[$layerset[$i]['Gruppe']];				# die Gruppe des Layers
							if($group['status'] == 0){
								$this->group_has_active_layers[$layerset[$i]['Gruppe']] = 1;  	# die zugeklappte Gruppe hat aktive Layer
								$collapsed = true;
							}
							while($group['obergruppe'] != ''){
								$group = $this->groupset[$group['obergruppe']];
								if($collapsed OR $group['status'] == 0){
									$this->group_has_active_layers[$group['id']] = 1;  	# auch alle Obergruppen durchlaufen
									$collapsed = true;
								}						
							}
						}
						
						if ($layerset[$i]['minscale']>=0) {
						if($this->map_factor != ''){
							if(MAPSERVERVERSION > 500){
							$layer->set('minscaledenom', $layerset[$i]['minscale']/$this->map_factor*1.414);
							}
							else{
							$layer->set('minscale', $layerset[$i]['minscale']/$this->map_factor*1.414);
							}
						}
						else{
							if(MAPSERVERVERSION > 500){
							$layer->set('minscaledenom', $layerset[$i]['minscale']);
							}
							else{
							$layer->set('minscale', $layerset[$i]['minscale']);
							}
						}
						}
						if ($layerset[$i]['maxscale']>0) {
							if($this->map_factor != ''){
								if(MAPSERVERVERSION > 500){
								$layer->set('maxscaledenom', $layerset[$i]['maxscale']/$this->map_factor*1.414);
								}
								else{
								$layer->set('maxscale', $layerset[$i]['maxscale']/$this->map_factor*1.414);
								}
							}
							else{
								if(MAPSERVERVERSION > 500){
								$layer->set('maxscaledenom', $layerset[$i]['maxscale']);
								}
								else{
								$layer->set('maxscale', $layerset[$i]['maxscale']);
								}
							}
						}
            $layer->setProjection('+init=epsg:'.$layerset[$i]['epsg_code']); # recommended
            if ($layerset[$i]['connection']!='') {
              if($this->map_factor != '' AND $layerset[$i]['connectiontype'] == 7){		# WMS-Layer
              	if($layerset[$i]['printconnection']!=''){
              		$layerset[$i]['connection'] = $layerset[$i]['printconnection']; 		# wenn es eine Druck-Connection gibt, wird diese verwendet
              	}
              	else{
                	//$layerset[$i]['connection'] .= '&mapfactor='.$this->map_factor;			# bei WMS-Layern wird der map_factor durchgeschleift (für die eigenen WMS) erstmal rausgenommen, weil einige WMS-Server der zusätzliche Parameter mapfactor stört
              	}
              }
              if($layerset[$i]['connectiontype'] == 6)$layerset[$i]['connection'] .= " options='-c client_encoding=".MYSQL_CHARSET."'";		# z.B. für Klassen mit Umlauten
              $layer->set('connection', $layerset[$i]['connection']);
            }
            if ($layerset[$i]['connectiontype']>0) {
              if (MAPSERVERVERSION >= 540) {
                $layer->setConnectionType($layerset[$i]['connectiontype']);
              }
              else {
                $layer->set('connectiontype',$layerset[$i]['connectiontype']);
              }
            }
            
						$layerset[$i]['processing'] = 'CLOSE_CONNECTION=DEFER;'.$layerset[$i]['processing'];		# DB-Connection erst am Ende schliessen und nicht für jeden Layer neu aufmachen
            if ($layerset[$i]['processing'] != "") {
              $processings = explode(";",$layerset[$i]['processing']);
              foreach ($processings as $processing) {
                $layer->setProcessing($processing);
              }
            }
                
            if ($layerset[$i]['Datentyp']=='3') {
              if($layerset[$i]['transparency'] != ''){
                if(MAPSERVERVERSION > 500){
                  $layer->set('opacity',$layerset[$i]['transparency']);
                }
                else{
                  $layer->set('transparency',$layerset[$i]['transparency']);
                }
              }
              if ($layerset[$i]['tileindex']!='') {
                $layer->set('tileindex',SHAPEPATH.$layerset[$i]['tileindex']);
              }
              else {
                $layer->set('data', $layerset[$i]['Data']);
              }
              $layer->set('tileitem',$layerset[$i]['tileitem']);
              if ($layerset[$i]['offsite']!='') {
                $RGB=explode(' ',$layerset[$i]['offsite']);
                $layer->offsite->setRGB($RGB[0],$RGB[1],$RGB[2]);
              }
            }
            else {
              # Vektorlayer
              if($layerset[$i]['Data'] != ''){
								$layerset[$i]['Data'] = str_replace('$hist_timestamp', HIST_TIMESTAMP, $layerset[$i]['Data']);
                $layer->set('data', $layerset[$i]['Data']);
              }
  
              # Setzen der Templatedateien für die Sachdatenanzeige inclt. Footer und Header.
              # Template (Body der Anzeige)
              if ($layerset[$i]['template']!='') {
                $layer->set('template',$layerset[$i]['template']);
              }
              else {
                $layer->set('template',DEFAULTTEMPLATE);
              }
              # Header (Kopfdatei)
              if ($layerset[$i]['header']!='') {
                $layer->set('header',$layerset[$i]['header']);
              }
              # Footer (Fusszeile)
              if ($layerset[$i]['footer']!='') {
                $layer->set('footer',$layerset[$i]['footer']);
              }
              # Setzen der Spalte nach der der Layer klassifiziert werden soll
              if ($layerset[$i]['classitem']!='') {
                $layer->set('classitem',$layerset[$i]['classitem']);
              }
              else {
                #$layer->set('classitem','id');
              }
              # Setzen des Filters
              if($layerset[$i]['Filter'] != ''){
              	$layerset[$i]['Filter'] = str_replace('$userid', $this->user->id, $layerset[$i]['Filter']);
               if (substr($layerset[$i]['Filter'],0,1)=='(') {
                 $expr=$layerset[$i]['Filter'];
               }
               else {
                 $expr=buildExpressionString($layerset[$i]['Filter']);
               }
               $layer->setFilter($expr);
              }
              # Layerweite Labelangaben
              if (MAPSERVERVERSION < 500 AND $layerset[$i]['labelangleitem']!='') {
                $layer->set('labelangleitem',$layerset[$i]['labelangleitem']);
              }
              if ($layerset[$i]['labelitem']!='') {
                $layer->set('labelitem',$layerset[$i]['labelitem']);
              }
              if ($layerset[$i]['labelmaxscale']!='') {
                if(MAPSERVERVERSION > 500){
                  $layer->set('labelmaxscaledenom',$layerset[$i]['labelmaxscale']);
                }
                else{
                  $layer->set('labelmaxscale',$layerset[$i]['labelmaxscale']);
                }
              }
              if ($layerset[$i]['labelminscale']!='') {
                if(MAPSERVERVERSION > 500){
                  $layer->set('labelminscaledenom',$layerset[$i]['labelminscale']);
                }
                else{
                  $layer->set('labelminscale',$layerset[$i]['labelminscale']);
                }
              }
              if ($layerset[$i]['labelrequires']!='') {
                $layer->set('labelrequires',$layerset[$i]['labelrequires']);
              }
              if ($layerset[$i]['postlabelcache']!='') {
                $layer->set('postlabelcache',$layerset[$i]['postlabelcache']);
              }
              if ($layerset[$i]['tolerance']!='3') {
                $layer->set('tolerance',$layerset[$i]['tolerance']);
              }
              if ($layerset[$i]['toleranceunits']!='pixels') {
                $layer->set('toleranceunits',$layerset[$i]['toleranceunits']);
              }
              if ($layerset[$i]['transparency']!=''){
                if(MAPSERVERVERSION > 500){
                  if ($layerset[$i]['transparency']==-1) {
                      $layer->set('opacity',MS_GD_ALPHA);
                  }
                  else {
                      $layer->set('opacity',$layerset[$i]['transparency']);
                  }
                }
                else {
                  if ($layerset[$i]['transparency']==-1) {
                      $layer->set('transparency',MS_GD_ALPHA);
                  }
                  else {
                      $layer->set('transparency',$layerset[$i]['transparency']);
                  }
                }
              }
              if ($layerset[$i]['symbolscale']!='') {
                if($this->map_factor != ''){
                  if(MAPSERVERVERSION > 500){
                    $layer->set('symbolscaledenom',$layerset[$i]['symbolscale']/$this->map_factor*1.414);
                  }
                  else{
                    $layer->set('symbolscale',$layerset[$i]['symbolscale']/$this->map_factor*1.414);
                  }
                }
                else{
                  if(MAPSERVERVERSION > 500){
                    $layer->set('symbolscaledenom',$layerset[$i]['symbolscale']);
                  }
                  else{
                    $layer->set('symbolscale',$layerset[$i]['symbolscale']);
                  }
                }
              }
            } # ende of Vektorlayer
            # Klassen
            $classset=$layerset[$i]['Class'];
            $this->loadclasses($layer, $layerset[$i], $classset, $map);
          } # Ende Layer ist aktiv
        } # end of Schleife layer
        
				$this->layerset = $layerset;        
        $this->map=$map;
				$this->reference_map = $reference_map;
				if (MAPSERVERVERSION >= 600 ) {
					$this->map_scaledenom = $map->scaledenom;
				}
				else {
					$this->map_scaledenom = $map->scale;
				}
        $this->mapDB=$mapDB;
      } break; # end of lade Karte von Datenbank
    } # end of switch loadMapSource
    return 1;
  }

		if($groupid != ''){
			$group = $this->groupset[$groupid];
			if($group['untergruppen'] != ''){
				foreach($group['untergruppen'] as $untergruppe){
					$subgroups .= ', '.$this->list_subgroups($untergruppe);
				}
				return $groupid.$subgroups;
			}
			else return $groupid;
		}
	}

    $anzClass=count($classset);
    for ($j=0;$j<$anzClass;$j++) {
      $klasse = ms_newClassObj($layer);
      if ($classset[$j]['Name']!='') {
        $klasse -> set('name',$classset[$j]['Name']);
      }
      if($classset[$j]['Status']=='1'){
      	$klasse->set('status', MS_ON);
      }
      else{
      	$klasse->set('status', MS_OFF);
      }
      $klasse -> set('template', $layerset['template']);
      $klasse -> setexpression($classset[$j]['Expression']);
      if ($classset[$j]['text']!='') {
        $klasse -> settext($classset[$j]['text']);
      }
      # setzen eines oder mehrerer Styles
      # Änderung am 12.07.2005 Korduan
      for ($k=0;$k<count($classset[$j]['Style']);$k++) {
        $dbStyle=$classset[$j]['Style'][$k];
				if (MAPSERVERVERSION < 600) {
          $style = ms_newStyleObj($klasse);
        }
				else {
				  $style = new styleObj($klasse);
				}
				if($dbStyle['geomtransform'] != '') {
					$style->updateFromString("STYLE GEOMTRANSFORM '".$dbStyle['geomtransform']."' END"); 
				}				
				if ($dbStyle['symbolname']!='') {
          $style -> set('symbolname',$dbStyle['symbolname']);
        }
        if ($dbStyle['symbol']>0) {
          $style->set('symbol',$dbStyle['symbol']);
        }                
        if (MAPSERVERVERSION >= 620) {
					if($dbStyle['geomtransform'] != '') {
						$style->setGeomTransform($dbStyle['geomtransform']);
					}
          if ($dbStyle['pattern']!='') {
            $style->setPattern(explode(' ',$dbStyle['pattern']));
            $style->linecap = 'butt';
          }
					if($dbStyle['gap'] != '') {
	          $style->set('gap', $dbStyle['gap']);
	        }
					if($dbStyle['linecap'] != '') {
	          $style->set('linecap', constant(MS_CJC_.strtoupper($dbStyle['linecap'])));
	        }
					if($dbStyle['linejoin'] != '') {
	          $style->set('linejoin', constant(MS_CJC_.strtoupper($dbStyle['linejoin'])));
	        }
					if($dbStyle['linejoinmaxsize'] != '') {
	          $style->set('linejoinmaxsize', $dbStyle['linejoinmaxsize']);
	        }
        }  
                
        if($this->map_factor != ''){
          if (MAPSERVERVERSION >= 620) {
            $pattern = $style->getpatternarray();
            if($pattern){
					    foreach($pattern as &$pat){
					      $pat = $pat * $this->map_factor;
					    }
					    $style->setPattern($pattern);
				    }
          }
          else {
            if($style->symbol > 0){
              $symbol = $map->getSymbolObjectById($style->symbol);
              $pattern = $symbol->getpatternarray();
              if(is_array($pattern) AND $symbol->inmapfile != 1){
                foreach($pattern as &$pat){
                  $pat = $pat * $this->map_factor;
                }
                $symbol->setpattern($pattern);
                $symbol->set('inmapfile', 1);
              }
            }
          }
        }

        if($this->map_factor != '' and $layerset['Datentyp'] != 8){ 
          # Skalierung der Stylegröße, wenn map_factor gesetzt und nicht vom Type Chart
          $style->set('size', $dbStyle['size']*$this->map_factor);
        }
        else{
          $style->set('size', $dbStyle['size']);
        }

        if ($dbStyle['minsize']!='') {
          if($this->map_factor != ''){
            $style -> set('minsize',$dbStyle['minsize']*$this->map_factor);
          }
          else{
            $style -> set('minsize',$dbStyle['minsize']);
          }
        }

        if ($dbStyle['maxsize']!='') {
          if($this->map_factor != ''){
            $style -> set('maxsize',$dbStyle['maxsize']*$this->map_factor);
          }
          else{
            $style -> set('maxsize',$dbStyle['maxsize']);
          }
        }

				if($dbStyle['angle'] != '') {
					$style->updateFromString("STYLE ANGLE ".$dbStyle['angle']." END"); 		# wegen AUTO
				}
        if ($dbStyle['angleitem']!=''){
          if(MAPSERVERVERSION < 500){
            $style->set('angleitem',$dbStyle['angleitem']);
          }
          else{
            $style->setbinding(MS_STYLE_BINDING_ANGLE, $dbStyle['angleitem']);
          }
        }
        if ($dbStyle['width']!='') {
          if ($dbStyle['antialias']!='') {
            $style -> set('antialias',$dbStyle['antialias']);
          }
          if($this->map_factor != ''){
            $style -> set('width',$dbStyle['width']*$this->map_factor);
          }
          else{
            $style->set('width',$dbStyle['width']);
          }
        }

        if ($dbStyle['minwidth']!='') {
          if($this->map_factor != ''){
            $style->set('minwidth',$dbStyle['minwidth']*$this->map_factor);
          }
          else{
            $style->set('minwidth',$dbStyle['minwidth']);
          }
        }

        if ($dbStyle['maxwidth']!='') {
          if($this->map_factor != ''){
            $style->set('maxwidth',$dbStyle['maxwidth']*$this->map_factor);
          }
          else{
            $style->set('maxwidth',$dbStyle['maxwidth']);
          }
        }

        if (MAPSERVERVERSION < 500 AND $dbStyle['sizeitem']!='') {
          $style->set('sizeitem', $dbStyle['sizeitem']);
        }
        if ($dbStyle['color']!='') {
          $RGB=explode(" ",$dbStyle['color']);
          if ($RGB[0]=='') { $RGB[0]=0; $RGB[1]=0; $RGB[2]=0; }
          $style->color->setRGB($RGB[0],$RGB[1],$RGB[2]);
        }
        if ($dbStyle['outlinecolor']!='') {
          $RGB=explode(" ",$dbStyle['outlinecolor']);
        	if ($RGB[0]=='') { $RGB[0]=0; $RGB[1]=0; $RGB[2]=0; }
          $style->outlinecolor->setRGB($RGB[0],$RGB[1],$RGB[2]);
        }
        if ($dbStyle['backgroundcolor']!='') {
          $RGB=explode(" ",$dbStyle['backgroundcolor']);
        	if ($RGB[0]=='') { $RGB[0]=0; $RGB[1]=0; $RGB[2]=0; }
          $style->backgroundcolor->setRGB($RGB[0],$RGB[1],$RGB[2]);
        }
        if ($dbStyle['offsetx']!='') {
          $style->set('offsetx', $dbStyle['offsetx']);
        }
        if ($dbStyle['offsety']!='') {
          $style->set('offsety', $dbStyle['offsety']);
        }
      } # Ende Schleife für mehrere Styles

      # setzen eines oder mehrerer Labels
      # Änderung am 12.07.2005 Korduan
      for ($k=0;$k<count($classset[$j]['Label']);$k++) {
        $dbLabel=$classset[$j]['Label'][$k];
        if (MAPSERVERVERSION < 600) { 
          $klasse->label->set('type',$dbLabel['type']);
          $klasse->label->set('font',$dbLabel['font']);
          $RGB=explode(" ",$dbLabel['color']);
          if ($RGB[0]=='') { $RGB[0]=0; }
          if ($RGB[1]=='') { $RGB[1]=0; }
          if ($RGB[2]=='') { $RGB[2]=0; }
          $klasse->label->color->setRGB($RGB[0],$RGB[1],$RGB[2]);
          $RGB=explode(" ",$dbLabel['outlinecolor']);
          $klasse->label->outlinecolor->setRGB($RGB[0],$RGB[1],$RGB[2]);
          if ($dbLabel['shadowcolor']!='') {
            $RGB=explode(" ",$dbLabel['shadowcolor']);
            $klasse->label->shadowcolor->setRGB($RGB[0],$RGB[1],$RGB[2]);
            $klasse->label->set('shadowsizex',$dbLabel['shadowsizex']);
            $klasse->label->set('shadowsizey',$dbLabel['shadowsizey']);
          }
          if ($dbLabel['backgroundcolor']!='') {
            $RGB=explode(" ",$dbLabel['backgroundcolor']);
            $klasse->label->backgroundcolor->setRGB($RGB[0],$RGB[1],$RGB[2]);
          }
          if ($dbLabel['backgroundshadowcolor']!='') {
            $RGB=explode(" ",$dbLabel['backgroundshadowcolor']);
            $klasse->label->backgroundshadowcolor->setRGB($RGB[0],$RGB[1],$RGB[2]);
            $klasse->label->set('backgroundshadowsizex',$dbLabel['backgroundshadowsizex']);
            $klasse->label->set('backgroundshadowsizey',$dbLabel['backgroundshadowsizey']);
          }
          $klasse->label->set('angle',$dbLabel['angle']);
          if(MAPSERVERVERSION > 500 AND $layerset['labelangleitem']!=''){
            $klasse->label->setbinding(MS_LABEL_BINDING_ANGLE, $layerset['labelangleitem']);
          }
        	if($dbLabel['autoangle']==1) {
            if(MAPSERVERVERSION >= 600){
	          	$klasse->label->set('anglemode', MS_AUTO);
	          }
	          else{
	          	$klasse->label->set('autoangle',$dbLabel['autoangle']);
            }
          }
          if ($dbLabel['buffer']!='') {
            $klasse->label->set('buffer',$dbLabel['buffer']);
          }
          $klasse->label->set('wrap',$dbLabel['wrap']);
          $klasse->label->set('force',$dbLabel['the_force']);
          $klasse->label->set('partials',$dbLabel['partials']);
          $klasse->label->set('size',$dbLabel['size']);
          $klasse->label->set('minsize',$dbLabel['minsize']);
          $klasse->label->set('maxsize',$dbLabel['maxsize']);
          # Skalierung der Labelschriftgröße, wenn map_factor gesetzt
          if($this->map_factor != ''){
            $klasse->label->set('minsize',$dbLabel['minsize']*$this->map_factor);
            $klasse->label->set('maxsize',$dbLabel['size']*$this->map_factor);
            $klasse->label->set('size',$dbLabel['size']*$this->map_factor);
          }
          if ($dbLabel['position']!='') {
            switch ($dbLabel['position']){
              case '0' :{
                $klasse->label->set('position', MS_UL);
              }break;
              case '1' :{
                $klasse->label->set('position', MS_LR);
              }break;
              case '2' :{
                $klasse->label->set('position', MS_UR);
              }break;
              case '3' :{
                $klasse->label->set('position', MS_LL);
              }break;
              case '4' :{
                $klasse->label->set('position', MS_CR);
              }break;
              case '5' :{
                $klasse->label->set('position', MS_CL);
              }break;
              case '6' :{
                $klasse->label->set('position', MS_UC);
              }break;
              case '7' :{
                $klasse->label->set('position', MS_LC);
              }break;
              case '8' :{
                $klasse->label->set('position', MS_CC);
              }break;
              case '9' :{
                $klasse->label->set('position', MS_AUTO);
              }break;
            }
          }
          if ($dbLabel['offsetx']!='') {
            $klasse->label->set('offsetx',$dbLabel['offsetx']);
          }
          if ($dbLabel['offsety']!='') {
            $klasse->label->set('offsety',$dbLabel['offsety']);
          }          
        } # ende mapserver < 600
        else {
          $label = new labelObj();
          $label->type = $dbLabel['type'];
          $label->font = $dbLabel['font'];
          $RGB=explode(" ",$dbLabel['color']);
          if ($RGB[0]=='') { $RGB[0]=0; $RGB[1]=0; $RGB[2]=0; }
          $label->color->setRGB($RGB[0],$RGB[1],$RGB[2]);
          if($dbLabel['outlinecolor'] != ''){
						$RGB=explode(" ",$dbLabel['outlinecolor']);
						$label->outlinecolor->setRGB($RGB[0],$RGB[1],$RGB[2]);
					}
          if ($dbLabel['shadowcolor']!='') {
            $RGB=explode(" ",$dbLabel['shadowcolor']);
            $label->shadowcolor->setRGB($RGB[0],$RGB[1],$RGB[2]);
            $label->shadowsizex = $dbLabel['shadowsizex'];
            $label->shadowsizey = $dbLabel['shadowsizey'];
          }
					
          if($dbLabel['backgroundshadowcolor']!='') {
            $RGB=explode(" ",$dbLabel['backgroundshadowcolor']);
            $style = new styleObj($label);
						$style->setGeomTransform('labelpoly');
            $style->color->setRGB($RGB[0],$RGB[1],$RGB[2]);
            $style->set('offsetx', $dbLabel['backgroundshadowsizex']);
						$style->set('offsety', $dbLabel['backgroundshadowsizey']);
          }
					if ($dbLabel['backgroundcolor']!='') {
            $RGB=explode(" ",$dbLabel['backgroundcolor']);
						$style = new styleObj($label);
						$style->setGeomTransform('labelpoly');
            $style->color->setRGB($RGB[0],$RGB[1],$RGB[2]);
          }
					
          $label->angle = $dbLabel['angle'];
          if($layerset['labelangleitem']!=''){
            $label->setBinding(MS_LABEL_BINDING_ANGLE, $layerset['labelangleitem']);
          }          
        	if($dbLabel['autoangle']==1) {
            if(MAPSERVERVERSION >= 600){
            	$label->set('anglemode', MS_AUTO);
            }
            else{
            	$label->autoangle = $dbLabel['autoangle'];
            }
          }
          if ($dbLabel['buffer']!='') {
            $label->buffer = $dbLabel['buffer'];
          }
          $label->wrap = $dbLabel['wrap'];
          $label->force = $dbLabel['the_force'];
          $label->partials = $dbLabel['partials'];
          $label->size = $dbLabel['size'];
          $label->minsize = $dbLabel['minsize'];
          $label->maxsize = $dbLabel['maxsize'];
          # Skalierung der Labelschriftgröße, wenn map_factor gesetzt
          if($this->map_factor != ''){
            $label->minsize = $dbLabel['minsize']*$this->map_factor;
            $label->maxsize = $dbLabel['size']*$this->map_factor;
            $label->size = $dbLabel['size']*$this->map_factor;
          }
          if ($dbLabel['position']!='') {
            switch ($dbLabel['position']){
              case '0' :{
                $label->set('position', MS_UL);
              }break;
              case '1' :{
                $label->set('position', MS_LR);
              }break;
              case '2' :{
                $label->set('position', MS_UR);
              }break;
              case '3' :{
                $label->set('position', MS_LL);
              }break;
              case '4' :{
                $label->set('position', MS_CR);
              }break;
              case '5' :{
                $label->set('position', MS_CL);
              }break;
              case '6' :{
                $label->set('position', MS_UC);
              }break;
              case '7' :{
                $label->set('position', MS_LC);
              }break;
              case '8' :{
                $label->set('position', MS_CC);
              }break;
              case '9' :{
                $label->set('position', MS_AUTO);
              }break;
            }
          }
          if ($dbLabel['offsetx']!='') {
            $label->offsetx = $dbLabel['offsetx'];
          }
          if ($dbLabel['offsety']!='') {
            $label->offsety = $dbLabel['offsety'];
          }
          $klasse->addLabel($label);
        } # ende mapserver >=600
      } # ende Schleife für mehrere Label
    } # end of Schleife Class
  }

		if($this->groupset[$group_id]['untergruppen'] == NULL AND $this->groups_with_layers[$group_id] == NULL)return;			# wenns keine Layer oder Untergruppen gibt, nix machen
    $groupname = $this->groupset[$group_id]['Gruppenname'];
	  $groupstatus = $this->groupset[$group_id]['status'];
    $legend .=  '
	  <div id="groupdiv_'.$group_id.'" style="width:100%">
      <table cellspacing="0" cellpadding="0" border="0" style="width:100%"><tr><td>
      <input id="group_'.$group_id.'" name="group_'.$group_id.'" type="hidden" value="'.$groupstatus.'">
      <a href="javascript:getlegend(\''.$group_id.'\', \'\', document.GUI.nurFremdeLayer.value)">
        <img border="0" id="groupimg_'.$group_id.'" src="graphics/';
		if($groupstatus == 1){
			$legend .=  'minus.gif">&nbsp;';
		}
		else{
			$legend .=  'plus.gif">&nbsp;';
		}
    $legend .=  '</a>';
		if($this->group_has_active_layers[$group_id] == ''){
			$legend .=  '<span class="legend_group">'.html_umlaute($groupname).'</span><br>';
		}
		else{
			$legend .=  '<span class="legend_group_active_layers">'.html_umlaute($groupname).'</span><br>';
		}
		$legend .= '</td></tr><tr><td><div id="layergroupdiv_'.$group_id.'" style="width:100%"><table cellspacing="0" cellpadding="0">';
		$layercount = count($this->groups_with_layers[$group_id]);
    if($groupstatus == 1){		# Gruppe aufgeklappt
			for($u = 0; $u < count($this->groupset[$group_id]['untergruppen']); $u++){			# die Untergruppen rekursiv durchlaufen
				$legend .= '<tr><td colspan="3"><table cellspacing="0" cellpadding="0" style="width:100%"><tr><td><img src="'.GRAPHICSPATH.'leer.gif" width="13" height="1" border="0"></td><td style="width: 100%">';
				$legend .= $this->create_group_legend($this->groupset[$group_id]['untergruppen'][$u]);
				$legend .= '</td></tr></table></td></tr>';
			}
			if($layercount > 0){		# Layer vorhanden
				$this->groups_with_layers[$group_id] = array_reverse($this->groups_with_layers[$group_id]);		# Layerreihenfolge umdrehen
				if(!$this->formvars['nurFremdeLayer']){
					$legend .=  '<tr>
												<td align="center">
													<input name="layers_of_group_'.$group_id.'" type="hidden" value="'.implode(',', $this->layers_of_group[$group_id]).'">
													<a href="javascript:selectgroupquery(document.GUI.layers_of_group_'.$group_id.')"><img border="0" src="graphics/pfeil.gif" title="Alle Abfragen ein/ausschalten"></a>
												</td>
												<td align="center">
													<a href="javascript:selectgroupthema(document.GUI.layers_of_group_'.$group_id.')"><img border="0" src="graphics/pfeil.gif" title="Alle Themen ein/ausschalten"></a>
												</td>
												<td>
													<span class="legend_layer">alle</span>
												</td>
											</tr>';
				}
				for($j = 0; $j < $layercount; $j++){
					$layer = $this->layerset[$this->groups_with_layers[$group_id][$j]];
					$visible = $this->check_layer_visibility($layer);
					# sichtbare Layer					
					if($visible){
						if($layer['requires'] == ''){
							$legend .= '<tr><td valign="top">';
							if($layer['queryable'] == 1 AND !$this->formvars['nurFremdeLayer']){
								$legend .=  '<input id="qLayer'.$layer['Layer_ID'].'"';
								
								if($this->user->rolle->singlequery){			# singlequery-Modus
									$legend .=  'type="radio" ';
									if($layer['selectiontype'] == 'radio'){
										$legend .=  ' onClick="this.checked = this.checked2;" onMouseUp="this.checked = this.checked2;" onMouseDown="updateThema(event, document.getElementById(\'thema_'.$layer['Layer_ID'].'\'), document.getElementById(\'qLayer'.$layer['Layer_ID'].'\'), document.GUI.radiolayers_'.$group_id.', document.GUI.layers)"';
									}
									else{
										$legend .=  ' onClick="this.checked = this.checked2;" onMouseUp="this.checked = this.checked2;" onMouseDown="updateThema(event, document.getElementById(\'thema_'.$layer['Layer_ID'].'\'), document.getElementById(\'qLayer'.$layer['Layer_ID'].'\'), \'\', document.GUI.layers)"';
									}
								}
								else{			# normaler Modus
									if($layer['selectiontype'] == 'radio'){
										$legend .=  'type="radio" ';
										$legend .=  ' onClick="this.checked = this.checked2;" onMouseUp="this.checked = this.checked2;" onMouseDown="updateThema(event, document.getElementById(\'thema_'.$layer['Layer_ID'].'\'), document.getElementById(\'qLayer'.$layer['Layer_ID'].'\'), document.GUI.radiolayers_'.$group_id.', \'\')"';
									}								
									else{
										$legend .=  'type="checkbox" ';
										$legend .=  ' onClick="updateThema(event, document.getElementById(\'thema_'.$layer['Layer_ID'].'\'), document.getElementById(\'qLayer'.$layer['Layer_ID'].'\'), \'\', \'\')"';
									}
								}
								
								$legend .=  ' name="qLayer'.$layer['Layer_ID'].'" value="1" ';
								if($layer['queryStatus'] == 1){
									$legend .=  'checked title="Die Abfrage dieses Themas ausschalten"';
								}
								$legend .=  ' title="Dieses Thema abfragbar schalten">';
							} 
							else{
								$legend .= '<img src="'.GRAPHICSPATH.'leer.gif" width="17" height="1" border="0">'; 
							}
							$legend .=  '</td><td valign="top">';
							// die sichtbaren Layer brauchen dieses Hiddenfeld mit dem gleichen Namen, welches immer den value 0 hat, damit sie beim Neuladen ausgeschaltet werden können, denn eine nicht angehakte Checkbox/Radiobutton wird ja nicht übergeben
							$legend .=  '<input type="hidden" id="thema'.$layer['Layer_ID'].'" name="thema'.$layer['Layer_ID'].'" value="0">';
							
							$legend .=  '<input id="thema_'.$layer['Layer_ID'].'" ';
							if($layer['selectiontype'] == 'radio'){
								$legend .=  'type="radio" ';
								$legend .=  ' onClick="this.checked = this.checked2;" onMouseUp="this.checked = this.checked2;" onMouseDown="updateQuery(event, document.getElementById(\'thema_'.$layer['Layer_ID'].'\'), document.getElementById(\'qLayer'.$layer['Layer_ID'].'\'), document.GUI.radiolayers_'.$group_id.')"';
								$radiolayers[$group_id] .= $layer['Layer_ID'].'|';
							}
							else{
								$legend .=  'type="checkbox" ';
								$legend .=  ' onClick="updateQuery(event, document.getElementById(\'thema_'.$layer['Layer_ID'].'\'), document.getElementById(\'qLayer'.$layer['Layer_ID'].'\'), \'\')"';
							}
							$legend .=  ' name="thema'.$layer['Layer_ID'].'" value="1" ';
							if($layer['aktivStatus'] == 1){
								$legend .=  'checked title="Dieses Thema ausschalten"';
							}
							else{
								$legend .=  ' title="Dieses Thema sichtbar schalten"'; 
							}
							$legend .= ' ></td><td valign="middle">';							
							if($layer['metalink'] != ''){
								$legend .= '<a ';
								if(substr($layer['metalink'], 0, 10) != 'javascript'){
									$legend .= 'target="_blank"';
								}
								$legend .= ' class="metalink" href="'.$layer['metalink'].'">';
							}
							$legend .= '<span ';
							if($layer['minscale'] != -1 AND $layer['maxscale'] > 0){
								$legend .= 'title="'.$layer['minscale'].' - '.$layer['maxscale'].'"';
							}			  
							$legend .=' class="legend_layer">'.html_umlaute($layer['alias']).'</span>';
							if($layer['metalink'] != ''){
								$legend .= '</a>';
							}
							# Bei eingeschalteten Layern kann man auf die maximale Ausdehnung des Layers zoomen
							if ($layer['aktivStatus'] == 1) {
								if ($layer['connectiontype']==6) {
									# Link zum Zoomen auf maximalen Extent des Layers erstmal nur für PostGIS Layer
									$legend.='&nbsp;<a href="index.php?go=zoomToMaxLayerExtent&layer_id='.$layer['Layer_ID'].'"><img src="graphics/maxLayerExtent.gif" border="0" title="volle Layerausdehnung"></a>';
								}
							}
						}
						if($layer['aktivStatus'] == 1 AND $layer['Class'][0]['Name'] != ''){
							if($layer['requires'] == '' AND $layer['Layer_ID'] > 0){
								$legend .=  ' <a href="javascript:getlegend(\''.$group_id.'\', '.$layer['Layer_ID'].', document.GUI.nurFremdeLayer.value)" title="Klassen ein/ausblenden"><img border="0" src="graphics/';
								if($layer['showclasses']){
									$legend .=  'minus.gif';
								}
								else{
									$legend .=  'plus.gif';
								}
								$legend .=  '"></a>
								<input id="classes_'.$layer['Layer_ID'].'" name="classes_'.$layer['Layer_ID'].'" type="hidden" value="'.$layer['showclasses'].'">';
							}
							if($layer['showclasses'] != 0){
								if($layer['connectiontype'] == 7){      # WMS   
									$layersection = substr($layer['connection'], strpos(strtolower($layer['connection']), 'layers')+7);
									$layersection = substr($layersection, 0, strpos($layersection, '&'));
									$layers = explode(',', $layersection);
									for($l = 0; $l < count($layers); $l++){
									$legend .=  '<div style="display:inline" id="lg'.$j.'_'.$l.'"><br><img src="'.$layer['connection'].'&layer='.$layers[$l].'&service=wms&request=getlegendgraphic" onerror="ImageLoadFailed(\'lg'.$j.'_'.$l.'\')"></div>';
									}
								}
								else{
									$legend .= '<table border="0" cellspacing="2" cellpadding="0">';
									$maplayer = $this->map->getLayerByName($layer['alias']);
									for($k = 0; $k < $maplayer->numclasses; $k++){
										$class = $maplayer->getClass($k);
										for($s = 0; $s < $class->numstyles; $s++){
											$style = $class->getStyle($s);
											if($current_group[$j]->type > 0){
												$symbol = $this->map->getSymbolObjectById($style->symbol);
												if($symbol->type == 1006){ 	# 1006 == hatch
													$style->set('size', 2*$style->width);					# size und maxsize beim Typ Hatch auf die doppelte Linienbreite setzen, damit man was in der Legende erkennt 
													$style->set('maxsize', 2*$style->width);
												}
												else{
													$style->set('size', 2);					# size und maxsize bei Linien und Polygonlayern immer auf 2 setzen, damit man was in der Legende erkennt 
													$style->set('maxsize', 2);
												}
											}
											else{
												$style->set('maxsize', $style->size);		# maxsize auf size setzen bei Punktlayern, damit man was in der Legende erkennt
											}
											if (MAPSERVERVERSION > 500){
												if($current_group[$j]->opacity < 100 AND $current_group[$j]->opacity > 0){			# Layer-Transparenz auch in Legendenbildchen berücksichtigen
													$hsv = rgb2hsv($style->color->red,$style->color->green, $style->color->blue);
													$hsv[1] = $hsv[1]*$current_group[$j]->opacity/100;
													$rgb = hsv2rgb($hsv[0], $hsv[1], $hsv[2]);
													$style->color->setRGB($rgb[0],$rgb[1],$rgb[2]);
												}
											}
											else {
												if($current_group[$j]->transparency < 100 AND $current_group[$j]->transparency > 0){			# Layer-Transparenz auch in Legendenbildchen berücksichtigen
													$hsv = rgb2hsv($style->color->red,$style->color->green, $style->color->blue);
													$hsv[1] = $hsv[1]*$current_group[$j]->transparency/100;
													$rgb = hsv2rgb($hsv[0], $hsv[1], $hsv[2]);
													$style->color->setRGB($rgb[0],$rgb[1],$rgb[2]);
												}												
											}
										}
										$image = $class->createLegendIcon(18,12);
										$filename = $this->map_saveWebImage($image,'jpeg');
										$newname = $this->user->id.basename($filename);
										rename(IMAGEPATH.basename($filename), IMAGEPATH.$newname);
										#Anne										
										$classid = $layer['Class'][$k]['Class_ID'];
										if($this->mapDB->disabled_classes['status'][$classid] == '0'){
											$legend .= '<tr>
													<td><input type="hidden" size="2" name="class'.$classid.'" value="0"><a href="#" onmouseover="mouseOverClassStatus('.$classid.',\''.TEMPPATH_REL.$newname.'\')" onmouseout="mouseOutClassStatus('.$classid.',\''.TEMPPATH_REL.$newname.'\')" onclick="changeClassStatus('.$classid.',\''.TEMPPATH_REL.$newname.'\')"><img border="0" name="imgclass'.$classid.'" src="graphics/inactive.jpg"></a>&nbsp;<span class="px13">'.html_umlaute($class->name).'</span></td>
													</tr>';
										}
										elseif($this->mapDB->disabled_classes['status'][$classid] == 2){
											$legend .= '<tr>
													<td><input type="hidden" size="2" name="class'.$classid.'" value="2"><a href="#" onmouseover="mouseOverClassStatus('.$classid.',\''.TEMPPATH_REL.$newname.'\')" onmouseout="mouseOutClassStatus('.$classid.',\''.TEMPPATH_REL.$newname.'\')" onclick="changeClassStatus('.$classid.',\''.TEMPPATH_REL.$newname.'\')"><img border="0" name="imgclass'.$classid.'" src="'.TEMPPATH_REL.$newname.'"></a>&nbsp;<span class="px13">'.html_umlaute($class->name).'</span></td>
													</tr>';
										}
										else{
											$legend .= '<tr>
													<td><input type="hidden" size="2" name="class'.$classid.'" value="1"><a href="#" onmouseover="mouseOverClassStatus('.$classid.',\''.TEMPPATH_REL.$newname.'\')" onmouseout="mouseOutClassStatus('.$classid.',\''.TEMPPATH_REL.$newname.'\')" onclick="changeClassStatus('.$classid.',\''.TEMPPATH_REL.$newname.'\')"><img border="0" name="imgclass'.$classid.'" src="'.TEMPPATH_REL.$newname.'"></a>&nbsp;<span class="px13">'.html_umlaute($class->name).'</span></td>
													</tr>';
										}
									}
									$legend .= '</table>';
								}
							}
						}
						if($j+1 < $count AND $current_groupgetMetaData_off_requires != 1){		// todo
							$legend .= '</td></tr>';
						}
					}

					# unsichtbare Layer
					if($layer['requires'] == '' AND !$visible){
						$legend .=  '
									<tr>
										<td valign="top">';
						if($layer['queryable'] == 1){
							$legend .=  '<input ';
							if($layer['selectiontype'] == 'radio'){
								$legend .=  'type="radio" ';
							}
							else{
								$legend .=  'type="checkbox" ';
							}
							if($layer['queryStatus'] == 1){
								$legend .=  'checked="true"';
							}
							$legend .=' type="checkbox" name="pseudoqLayer'.$layer['Layer_ID'].'" disabled>';
						}
						$legend .=  '</td><td valign="top">';
						// die nicht sichtbaren Layer brauchen dieses Hiddenfeld mit dem gleichen Namen nur bei Radiolayern, damit sie beim Neuladen ausgeschaltet werden können, denn ein disabledtes input-Feld wird ja nicht übergeben
						$legend .=  '<input type="hidden" id="thema'.$layer['Layer_ID'].'" name="thema'.$layer['Layer_ID'].'" value="'.$layer['aktivStatus'].'">';
						$legend .=  '<input ';
						if($layer['selectiontype'] == 'radio'){
							$legend .=  'type="radio" ';
							$radiolayers[$group_id] .= $layer['Layer_ID'].'|';
						}
						else{
							$legend .=  'type="checkbox" ';
						}
						if($layer['aktivStatus'] == 1){
							$legend .=  'checked="true" ';
						}
						$legend .= 'id="thema_'.$layer['Layer_ID'].'" name="thema'.$layer['Layer_ID'].'" disabled="true"></td><td>
						<span class="legend_layer_hidden" ';
						if($layer['minscale'] != -1 AND $layer['maxscale'] != -1){
							$legend .= 'title="'.$layer['minscale'].' - '.$layer['maxscale'].'"';
						}
						$legend .= ' >'.html_umlaute($layer['alias']).'</span>';
						if($layer['status'] != ''){
							$legend .= '&nbsp;<img title="Thema nicht verfügbar: '.$layer['status'].'" src="'.GRAPHICSPATH.'warning.png">';
						}
						if($layer['queryable'] == 1){
							$legend .=  '<input type="hidden" name="qLayer'.$layer['Layer_ID'].'"';
							if($layer['queryStatus'] != 0){
								$legend .=  ' value="1"';
							}
							$legend .=  '>';
						}
						$legend .=  '</td>
								</tr>';
					}
				}
			}
	  }
    $legend .= '</table></div></td></tr></table>';
    $legend .= '<input type="hidden" name="radiolayers_'.$group_id.'" value="'.$radiolayers[$group_id].'">';
	  $legend .= '</div>';
    return $legend;
  }

		if($layer['status'] != '' OR ($this->map_scaledenom < $layer['minscale'] OR ($layer['maxscale'] > 0 AND $this->map_scaledenom > $layer['maxscale']))) {
			return false;
		}
		elseif($layer['Filter'] != ''){
			if(strpos($layer['Filter'], '&&')){
				$filterparts = explode(' ', $layer['Filter']);
				for($j = 0; $j < count($filterparts); $j++){
					if($filterparts[$j] == '&&'){
						if($this->BBoxinExtent($filterparts[$j+1]) == 'f'){
							return false;
						}
						break;
					}
				}
			}
		}
		return true;
	}

		if(MAPSERVERVERSION >= 600 ) {		
			return $image->saveWebImage();
		}
		else {
			return $image->saveWebImage($format, 1, 1, 0);
		}
	}	
}
    global $debug;
    $this->debug=$debug;
    $this->loglevel=LOG_LEVEL;
 		$this->defaultloglevel=LOG_LEVEL;
 		global $log_mysql;
    $this->logfile=$log_mysql;
 		$this->defaultlogfile=$log_mysql;
    $this->ist_Fortfuehrung=1;
    $this->type="MySQL";
    $this->commentsign='#';
    # Wenn dieser Parameter auf 1 gesetzt ist werden alle Anweisungen
    # BEGIN TRANSACTION, ROLLBACK und COMMIT unterdrückt, so daß alle anderen SQL
    # Anweisungen nicht in Transactionsblöcken ablaufen.
    # Kann zur Steigerung der Geschwindigkeit von großen Datenbeständen verwendet werden
    # Vorsicht: Wenn Fehler beim Einlesen passieren, ist der Datenbestand inkonsistent
    # und der Einlesevorgang muss wiederholt werden bis er fehlerfrei durchgelaufen ist.
    # Dazu Fehlerausschriften bearchten.
    $this->blocktransaction=0;
  }

    $this->debug->write("<br>MySQL Verbindung öffnen mit Host: ".$this->host." User: ".$this->user,4);
    $this->dbConn=mysql_connect($this->host,$this->user,$this->passwd);
    $this->debug->write("Datenbank mit ID: ".$this->dbConn." und Name: ".$this->dbName." auswählen.",4);
    return mysql_select_db($this->dbName,$this->dbConn);
  }

  	switch ($this->loglevel) {
  		case 0 : {
  			$logsql=0;
  		} break;
  		case 1 : {
  			$logsql=1;
  		} break;
  		case 2 : {
  			$logsql=$loglevel;
  		} break;
  	}
    # SQL-Statement wird nur ausgeführt, wenn DBWRITE gesetzt oder
    # wenn keine INSERT, UPDATE und DELETE Anweisungen in $sql stehen.
    # (lesend immer, aber schreibend nur mit DBWRITE=1)
    if (DBWRITE OR (!stristr($sql,'INSERT') AND !stristr($sql,'UPDATE') AND !stristr($sql,'DELETE'))) {
      $query=mysql_query($sql,$this->dbConn);
      #echo $sql;
      if ($query==0) {
        $ret[0]=1;
        $ret[1]="<b>Fehler bei SQL Anweisung:</b><br>".$sql."<br>".mysql_error($this->dbConn);
        $this->debug->write($ret[1],$debuglevel);
        if ($logsql) {
          $this->logfile->write("#".$ret[1]);
        }
      }
      else {
        $ret[0]=0;
        $ret[1]=$query;
        if ($logsql) {
          $this->logfile->write($sql.';');
        }
        $this->debug->write(date('H:i:s')."<br>".$sql,$debuglevel);
      }
      $ret[2]=$sql;
    }
    else {
    	if ($logsql) {
    		$this->logfile->write($sql.';');
    	}
    	$this->debug->write("<br>".$sql,$debuglevel);
    }
    return $ret;
  }

    $this->debug->write("<br>MySQL Verbindung mit ID: ".$this->dbConn." schließen.",4);
    if (LOG_LEVEL>0){
    	$this->logfile->close();
    }
    return mysql_close($this->dbConn);
  }
}
		global $debug;
		$this->debug=$debug;
		$this->database=$database;
		if($login_name){
			$this->login_name=$login_name;
			$this->readUserDaten(0,$login_name);
			$this->remote_addr=getenv('REMOTE_ADDR');
		}
		else{
			$this->id = $id;
			$this->readUserDaten($id,0);
		}
	}

    $sql ='SELECT * FROM user WHERE 1=1';
    if ($id>0) {
      $sql.=' AND ID='.$id;
    }
    if ($login_name!='') {
      $sql.=' AND login_name LIKE "'.$login_name.'"';
    }
    $this->debug->write("<p>file:users.php class:user->readUserDaten - Abfragen des Namens des Benutzers:<br>".$sql,4);
    $query=mysql_query($sql,$this->database->dbConn);
    if ($query==0) { $this->debug->write("<br>Abbruch Zeile: ".__LINE__,4); return 0; }
    $rs=mysql_fetch_array($query);
    $this->id=$rs['ID'];
    $this->login_name=$rs['login_name'];
    $this->Namenszusatz=$rs['Namenszusatz'];
    $this->Name=$rs['Name'];
    $this->Vorname=$rs['Vorname'];
    $this->stelle_id=$rs['stelle_id'];
    $this->phon=$rs['phon'];
    $this->email=$rs['email'];
    if (CHECK_CLIENT_IP) {
      $this->ips=$rs['ips'];
    }
    $this->password_setting_time=$rs['password_setting_time'];
  }

    $sql = 'SELECT stelle_id FROM user WHERE ID='.$this->id;
    $this->debug->write("<p>file:users.php class:user->getLastStelle - Abfragen der zuletzt genutzten Stelle:<br>".$sql,4);
    $query=mysql_query($sql,$this->database->dbConn);
    if ($query==0) { $this->debug->write("<br>Abbruch Zeile: ".__LINE__,4); return 0; }
    $rs=mysql_fetch_array($query);
    return $rs['stelle_id'];
  }

    # Prüfen ob die übergebene IP Adresse zu den für den Nutzer eingetragenen Adressen passt
    $ips=explode(';',$this->ips);
    foreach ($ips AS $ip) {
      if (trim($ip)!='') {
        $ip=trim($ip);
        if (in_subnet($remote_addr,$ip)) {
          $this->debug->write('<br>IP:'.$remote_addr.' paßt zu '.$ip,4);
          #echo '<br>IP:'.$remote_addr.' paßt zu '.$ip;
          return 1;
        }
      }
    }
    return 0;
  }

		# Abfragen und zuweisen der Einstellungen für die Rolle		
		$rolle=new rolle($this->id,$stelle_id,$this->database);		
		if ($rolle->readSettings()) {
			$this->rolle=$rolle;			
			return 1;
		}
		return 0;
	}
}
		global $debug;
		$this->debug=$debug;
		$this->id=$id;
		$this->database=$database;
		$this->Bezeichnung=$this->getName();
		$this->readDefaultValues();
	}

    $sql ='SELECT ';
    if ($this->language != 'german' AND $this->language != ''){
      $sql.='`Bezeichnung_'.$this->language.'` AS ';
    }
    $sql.='Bezeichnung FROM stelle WHERE ID='.$this->id;
    #echo $sql;
    $this->debug->write("<p>file:users.php class:stelle->getName - Abfragen des Namens der Stelle:<br>".$sql,4);
    $query=mysql_query($sql,$this->database->dbConn);
    if ($query==0) { $this->debug->write("<br>Abbruch Zeile: ".__LINE__,4); return 0; }
    $rs=mysql_fetch_array($query);
    $this->Bezeichnung=$rs['Bezeichnung'];
    return $rs['Bezeichnung'];
  }

    $sql ='SELECT * FROM stelle WHERE ID='.$this->id;
    $this->debug->write("<p>file:users.php class:stelle->readDefaultValues - Abfragen der Default Parameter der Karte zur Stelle:<br>".$sql,4);
    $query=mysql_query($sql,$this->database->dbConn);
    if ($query==0) { $this->debug->write("<br>Abbruch Zeile: ".__LINE__,4); return 0; }
    $rs=mysql_fetch_array($query);    
    $this->MaxGeorefExt=ms_newRectObj();
    $this->MaxGeorefExt->setextent($rs['minxmax'],$rs['minymax'],$rs['maxxmax'],$rs['maxymax']);
    $this->epsg_code=$rs["epsg_code"];
    $this->alb_raumbezug=$rs["alb_raumbezug"];
    $this->alb_raumbezug_wert=$rs["alb_raumbezug_wert"];
    $this->wasserzeichen=$rs["wasserzeichen"];
    $this->pgdbhost=$rs["pgdbhost"];
    $this->pgdbname=$rs["pgdbname"];
    $this->pgdbuser=$rs["pgdbuser"];
    $this->pgdbpasswd=$rs["pgdbpasswd"];
    $this->protected=$rs["protected"];
    //---------- OWS Metadaten ----------//
    $this->ows_title=$rs["ows_title"];
    $this->ows_abstract=$rs["ows_abstract"];
    $this->wms_accessconstraints=$rs["wms_accessconstraints"];
    $this->ows_contactperson=$rs["ows_contactperson"];
    $this->ows_contactorganization=$rs["ows_contactorganization"];
    $this->ows_contactelectronicmailaddress=$rs["ows_contactemailaddress"];
    $this->ows_contactposition=$rs["ows_contactposition"];
    $this->ows_fees=$rs["ows_fees"];
    $this->ows_srs=$rs["ows_srs"];
    $this->check_client_ip=$rs["check_client_ip"];
    $this->checkPasswordAge=$rs["check_password_age"];
    $this->allowedPasswordAge=$rs["allowed_password_age"];
    $this->useLayerAliases=$rs["use_layer_aliases"];
  }

    $sql ='SELECT check_client_ip FROM stelle WHERE ID = '.$this->id;
    $this->debug->write("<p>file:users.php class:stelle->checkClientIpIsOn- Abfragen ob IP's der Nutzer in der Stelle getestet werden sollen<br>".$sql,4);
    #echo '<br>'.$sql;
    $query=mysql_query($sql,$this->database->dbConn);
    if ($query==0) { $this->debug->write("<br>Abbruch Zeile: ".__LINE__,4); return 0; }
    $rs=mysql_fetch_array($query);
    if ($rs['check_client_ip']=='1') {
      return 1;
    }
    return 0;
  }
}
		global $debug;
		$this->debug=$debug;
		$this->user_id=$user_id;
		$this->stelle_id=$stelle_id;
		$this->database=$database;
		#$this->layerset=$this->getLayer('');
		#$this->groupset=$this->getGroups('');
		$this->loglevel = 0;
	}

    # Abfragen und Zuweisen der Einstellungen der Rolle
    $sql ='SELECT * FROM rolle WHERE user_id='.$this->user_id.' AND stelle_id='.$this->stelle_id;
    #echo $sql;
    $this->debug->write("<p>file:users.php class:rolle function:readSettings - Abfragen der Einstellungen der Rolle:<br>".$sql,4);
    $query=mysql_query($sql,$this->database->dbConn);
    if ($query==0) {
      $this->debug->write("<br>Abbruch Zeile: ".__LINE__,4);
      return 0;
    }
    $rs=mysql_fetch_array($query);
    $this->oGeorefExt=ms_newRectObj();
    $this->oGeorefExt->setextent($rs['minx'],$rs['miny'],$rs['maxx'],$rs['maxy']);
    $this->nImageWidth=$rs['nImageWidth'];
    $this->nImageHeight=$rs['nImageHeight'];
    $this->mapsize=$this->nImageWidth.'x'.$this->nImageHeight;
    @$this->pixwidth=($rs['maxx']-$rs['minx'])/$rs['nImageWidth'];
    @$this->pixheight=($rs['maxy']-$rs['miny'])/$rs['nImageHeight'];
    $this->pixsize=($this->pixwidth+$this->pixheight)/2;
    $this->nZoomFactor=$rs['nZoomFactor'];
    $this->epsg_code=$rs['epsg_code'];
    $this->epsg_code2=$rs['epsg_code2'];
    $this->coordtype=$rs['coordtype'];
    $this->last_time_id=$rs['last_time_id'];
    $this->gui=$rs['gui'];
    $this->language=$rs['language'];
		define(LANGUAGE, $this->language);
    $this->hideMenue=$rs['hidemenue'];
    $this->hideLegend=$rs['hidelegend'];
    $this->fontsize_gle=$rs['fontsize_gle'];
    $this->highlighting=$rs['highlighting'];
    $this->scrollposition=$rs['scrollposition'];
    $this->result_color=$rs['result_color'];
    $this->always_draw=$rs['always_draw'];
    $this->runningcoords=$rs['runningcoords'];
		$this->singlequery=$rs['singlequery'];
		$this->querymode=$rs['querymode'];
		$this->geom_edit_first=$rs['geom_edit_first'];		
		$this->overlayx=$rs['overlayx'];
		$this->overlayy=$rs['overlayy'];
		if($rs['hist_timestamp'] != ''){
			$this->hist_timestamp = DateTime::createFromFormat('Y-m-d H:i:s', $rs['hist_timestamp'])->format('d.m.Y H:i:s');
			define(HIST_TIMESTAMP, DateTime::createFromFormat('Y-m-d H:i:s', $rs['hist_timestamp'])->format('Y-m-d\TH:i:s\Z'));
		}
		else define(HIST_TIMESTAMP, '');
    $buttons = explode(',', $rs['buttons']);
    $this->back = in_array('back', $buttons);
    $this->forward = in_array('forward', $buttons);
    $this->zoomin = in_array('zoomin', $buttons);
    $this->zoomout = in_array('zoomout', $buttons);
    $this->zoomall = in_array('zoomall', $buttons);
    $this->recentre = in_array('recentre', $buttons);
    $this->jumpto = in_array('jumpto', $buttons);
    $this->query = in_array('query', $buttons);
    $this->queryradius = in_array('queryradius', $buttons);
    $this->polyquery = in_array('polyquery', $buttons);
    $this->touchquery = in_array('touchquery', $buttons);
    $this->measure = in_array('measure', $buttons);
    $this->freepolygon = in_array('freepolygon', $buttons);
    $this->freetext = in_array('freetext', $buttons);
    $this->freearrow = in_array('freearrow', $buttons);
    return 1;
  }

		$this->groupset=$this->getGroups('');
		# Eintragen des group_status=1 für Gruppen, die angezeigt werden sollen
		for ($i=0;$i<count($this->groupset);$i++) {
			if($formvars['group_'.$this->groupset[$i]['id']] !== NULL){
				if ($formvars['group_'.$this->groupset[$i]['id']] == 1) {
					$group_status=1;
				}
				else {
					$group_status=0;
				}
				$sql ='UPDATE u_groups2rolle set status="'.$group_status.'"';
				$sql.=' WHERE user_id='.$this->user_id.' AND stelle_id='.$this->stelle_id;
				$sql.=' AND id='.$this->groupset[$i]['id'];
				$this->debug->write("<p>file:users.php class:rolle->setGroupStatus - Speichern des Status der Gruppen zur Rolle:",4);
				$this->database->execSQL($sql,4, $this->loglevel);
			}
		}
		return $formvars;
	}

    # Abfragen der Gruppen in der Rolle
    $sql ='SELECT g2r.*, ';
		if(LANGUAGE != 'german') {
			$sql.='CASE WHEN `Gruppenname_'.LANGUAGE.'` != "" THEN `Gruppenname_'.LANGUAGE.'` ELSE `Gruppenname` END AS ';
		}
		$sql.='Gruppenname FROM u_groups AS g, u_groups2rolle AS g2r ';
    $sql.=' WHERE g2r.stelle_ID='.$this->stelle_id.' AND g2r.user_id='.$this->user_id;
    $sql.=' AND g2r.id = g.id';
    if ($GroupName!='') {
      $sql.=' AND Gruppenname LIKE "'.$GroupName.'"';
    }
    $this->debug->write("<p>file:users.php class:rolle->getGroups - Abfragen der Gruppen zur Rolle:<br>".$sql,4);
    $query=mysql_query($sql,$this->database->dbConn);
    if ($query==0) { $this->debug->write("<br>Abbruch in ".$PHP_SELF." Zeile: ".__LINE__,4); return 0; }
    while ($rs=mysql_fetch_array($query)) {
      $groups[]=$rs;
    }
    return $groups;
  }
}
    global $debug;
    $this->debug=$debug;
    $this->Stelle_ID=$Stelle_ID;
    $this->User_ID=$User_ID;
  }

    $sql ='SELECT r.* FROM referenzkarten AS r, stelle AS s WHERE r.ID=s.Referenzkarte_ID';
    $sql.=' AND s.ID='.$this->Stelle_ID;
    $this->debug->write("<p>file:kvwmap class:db_mapObj->read_ReferenceMap - Lesen der Referenzkartendaten:<br>".$sql,4);
    $query=mysql_query($sql);
    if ($query==0) { $this->debug->write("<br>Abbruch Zeile: ".__LINE__,4); return 0; }
    $rs=mysql_fetch_array($query);
    $this->referenceMap=$rs;
    return $rs;
  }  

    $sql ='SELECT g2r.*, ';
		if(LANGUAGE != 'german') {
			$sql.='CASE WHEN `Gruppenname_'.LANGUAGE.'` IS NOT NULL THEN `Gruppenname_'.LANGUAGE.'` ELSE `Gruppenname` END AS ';
		}
		$sql.='Gruppenname, obergruppe FROM u_groups AS g, u_groups2rolle AS g2r ';
    $sql.=' WHERE g2r.stelle_ID='.$this->Stelle_ID.' AND g2r.user_id='.$this->User_ID;
    $sql.=' AND g2r.id = g.id';
		$sql.=' ORDER BY `order`';
    $this->debug->write("<p>file:kvwmap class:db_mapObj->read_Groups - Lesen der Gruppen der Rolle:<br>".$sql,4);
    $query=mysql_query($sql);
    if ($query==0) { echo "<br>Abbruch in ".$PHP_SELF." Zeile: ".__LINE__."<br>wegen: ".$sql."<p>".INFO1; return 0; }
    while ($rs=mysql_fetch_array($query)) {
      $groups[$rs['id']]=$rs;
			if($rs['obergruppe'])$groups[$rs['obergruppe']]['untergruppen'][] = $rs['id'];
    }
    $this->anzGroups=count($groups);
    return $groups;
  }

    $sql ='SELECT DISTINCT rl.*,ul.*, l.Layer_ID, ';
		if(LANGUAGE != 'german') {
			$sql.='CASE WHEN `Name_'.LANGUAGE.'` != "" THEN `Name_'.LANGUAGE.'` ELSE `Name` END AS ';
		}
		$sql.='Name, l.alias, l.Datentyp, l.Gruppe, l.pfad, l.Data, l.tileindex, l.tileitem, l.labelangleitem, l.labelitem, l.labelmaxscale, l.labelminscale, l.labelrequires, l.connection, l.printconnection, l.connectiontype, l.classitem, l.filteritem, l.tolerance, l.toleranceunits, l.epsg_code, l.ows_srs, l.wms_name, l.wms_server_version, l.wms_format, l.wms_auth_username, l.wms_auth_password, l.wms_connectiontimeout, l.selectiontype, l.logconsume,l.metalink, l.status, g.*';
    $sql.=' FROM u_rolle2used_layer AS rl,used_layer AS ul,layer AS l, u_groups AS g, u_groups2rolle as gr';
    $sql.=' WHERE rl.stelle_id=ul.Stelle_ID AND rl.layer_id=ul.Layer_ID AND l.Layer_ID=ul.Layer_ID';
    $sql.=' AND (ul.minscale != -1 OR ul.minscale IS NULL) AND l.Gruppe = g.id AND rl.stelle_ID='.$this->Stelle_ID.' AND rl.user_id='.$this->User_ID;
    $sql.=' AND gr.id = g.id AND gr.stelle_id='.$this->Stelle_ID.' AND gr.user_id='.$this->User_ID;
		if($groups != NULL){
			$sql.=' AND g.id IN ('.$groups.')';
		}
    if ($this->nurAufgeklappteLayer) {
      $sql.=' AND (rl.aktivStatus != "0" OR gr.status != "0" OR requires != "")';
    }
    if ($this->nurAktiveLayerOhneRequires) {
      $sql.=' AND (rl.aktivStatus != "0")';
    }
    if ($this->nurFremdeLayer){			# entweder fremde (mit host=...) Postgis-Layer oder aktive nicht-Postgis-Layer
    	$sql.=' AND (l.connection like "%host=%" AND l.connection NOT like "%host=localhost%" OR l.connectiontype != 6 AND rl.aktivStatus != "0")';
    }
    $sql.=' ORDER BY ul.drawingorder';
    #echo $sql;
    $this->debug->write("<p>file:kvwmap class:db_mapObj->read_Layer - Lesen der Layer der Rolle:<br>".$sql,4);
    $query=mysql_query($sql);
    if ($query==0) { echo "<br>Abbruch in ".$PHP_SELF." Zeile: ".__LINE__."<br>wegen: ".$sql."<p>".INFO1; return 0; }
    $this->Layer = array();
    $this->disabled_classes = $this->read_disabled_classes();
		$i = 0;
    while ($rs=mysql_fetch_array($query)) {
      if($withClasses == 2 OR $rs['requires'] != '' OR ($withClasses == 1 AND $rs['aktivStatus'] != '0')){    # bei withclasses == 2 werden für alle Layer die Klassen geladen, bei withclasses == 1 werden die Klassen nur dann geladen, wenn der Layer aktiv ist
        $rs['Class']=$this->read_Classes($rs['Layer_ID'], $this->disabled_classes);
      }
      $this->Layer[$i]=$rs;
			$this->Layer['layer_ids'][$rs['Layer_ID']] =& $this->Layer[$i];		# damit man mit einer Layer-ID als Schlüssel auf dieses Array zugreifen kann
			$i++;
    }
    return $this->Layer;
  }

  	#Anne
    $sql_classes = 'SELECT class_id, status FROM u_rolle2used_class WHERE user_id='.$this->User_ID.' AND stelle_id='.$this->Stelle_ID.';';
    $query_classes=mysql_query($sql_classes);
    while($row = mysql_fetch_array($query_classes)){
  		$classarray['class_id'][] = $row['class_id'];
			$classarray['status'][$row['class_id']] = $row['status'];
		}
		return $classarray;
  }

    $sql ='SELECT ';
		if(!$all_languages AND LANGUAGE != 'german') {
			$sql.='CASE WHEN `Name_'.LANGUAGE.'` IS NOT NULL THEN `Name_'.LANGUAGE.'` ELSE `Name` END AS ';
		}
		$sql.='Name, `Name_low-german`, Name_english, Name_polish, Name_vietnamese, Class_ID, Layer_ID, Expression, drawingorder, text FROM classes';
    $sql.=' WHERE Layer_ID='.$Layer_ID.' ORDER BY drawingorder,Class_ID';
    #echo $sql.'<br>';
    $this->debug->write("<p>file:kvwmap class:db_mapObj->read_Class - Lesen der Classen eines Layers:<br>".$sql,4);
    $query=mysql_query($sql);
    if ($query==0) { echo "<br>Abbruch in ".$PHP_SELF." Zeile: ".__LINE__; return 0; }
    while($rs=mysql_fetch_array($query)) {
      $rs['Style']=$this->read_Styles($rs['Class_ID']);
      $rs['Label']=$this->read_Label($rs['Class_ID']);
      #Anne
      if($disabled_classes){
				if($disabled_classes['status'][$rs['Class_ID']] == 2){
					$rs['Status'] = 1;
					for($i = 0; $i < count($rs['Style']); $i++){
						if($rs['Style'][$i]['color'] != '' AND $rs['Style'][$i]['color'] != '-1 -1 -1'){
							$rs['Style'][$i]['outlinecolor'] = $rs['Style'][$i]['color'];
							$rs['Style'][$i]['color'] = '-1 -1 -1';
						}
					}
				}
				elseif($disabled_classes['status'][$rs['Class_ID']] == '0'){
					$rs['Status'] = 0;
				}
				else $rs['Status'] = 1;
      }
      else $rs['Status'] = 1;
			
      $Classes[]=$rs;
    }
    return $Classes;
  }

    $sql ='SELECT * FROM styles AS s,u_styles2classes AS s2c';
    $sql.=' WHERE s.Style_ID=s2c.style_id AND s2c.class_id='.$Class_ID;
    $sql.=' ORDER BY drawingorder';
    $this->debug->write("<p>file:kvwmap class:db_mapObj->read_Styles - Lesen der Styledaten:<br>".$sql,4);
    $query=mysql_query($sql);
    if ($query==0) { echo "<br>Abbruch in ".$PHP_SELF." Zeile: ".__LINE__; return 0; }
    while($rs=mysql_fetch_array($query)) {
      $Styles[]=$rs;
    }
    return $Styles;
  }

    $sql ='SELECT * FROM labels AS l,u_labels2classes AS l2c';
    $sql.=' WHERE l.Label_ID=l2c.label_id AND l2c.class_id='.$Class_ID;
    $this->debug->write("<p>file:kvwmap class:db_mapObj->read_Label - Lesen der Labels zur Classe eines Layers:<br>".$sql,4);
    $query=mysql_query($sql);
    if ($query==0) { echo "<br>Abbruch in ".$PHP_SELF." Zeile: ".__LINE__; return 0; }
    while ($rs=mysql_fetch_array($query)) {
      $Labels[]=$rs;
    }
    return $Labels;
  }

    //$sql = 'SELECT DISTINCT l.*, g.Gruppenname, gr.status, -l.id AS Layer_ID, 1 as showclasses from rollenlayer AS l, u_groups AS g, u_groups2rolle as gr';
    //$sql.= ' WHERE l.Gruppe = g.id AND l.stelle_id='.$this->Stelle_ID.' AND l.user_id='.$this->User_ID.' AND gr.id = g.id AND gr.stelle_id='.$this->Stelle_ID.' AND gr.user_id='.$this->User_ID;
		$sql = 'SELECT DISTINCT l.*, g.Gruppenname, -l.id AS Layer_ID, 1 as showclasses from rollenlayer AS l, u_groups AS g';
    $sql.= ' WHERE l.Gruppe = g.id AND l.stelle_id='.$this->Stelle_ID.' AND l.user_id='.$this->User_ID;
    if($id != NULL){
    	$sql .= ' AND l.id = '.$id;
    }
  	if($typ != NULL){
    	$sql .= ' AND l.Typ = \''.$typ.'\'';
    }
    $this->debug->write("<p>file:kvwmap class:db_mapObj->read_RollenLayer - Lesen der RollenLayer:<br>".$sql,4);
    $query=mysql_query($sql);
    if ($query==0) { echo "<br>Abbruch in ".$PHP_SELF." Zeile: ".__LINE__."<br>wegen: ".$sql."<p>".INFO1; return 0; }
    $Layer = array();
    while ($rs=mysql_fetch_array($query)) {
      $rs['Class']=$this->read_Classes(-$rs['id']);
      $Layer[]=$rs;
    }
    return $Layer;
  }
}