<?

	$this->goNotExecutedInPlugins = false;
	
	switch($this->go){  
		case 'delete_bplan' : {
			include(PLUGINS.'bauleitplanung/model/rok.php');
			$mapdb = new db_mapObj($this->Stelle->id,$this->user->id);
			$layerdb = $mapdb->getlayerdatabase($this->formvars['selected_layer_id'], $this->Stelle->pgdbhost);
			$rok = new rok($layerdb);
			$rok->delete_bplan($this->formvars['plan_id']);
			if($this->formvars['value_b_plan_stammdaten_oid'] == ''){
				$this->GenerischeSuche_Suchen();		# Trefferliste wieder anzeigen
			}
			else{
				$this->loadMap('DataBase');					# Karte anzeigen
				$currenttime=date('Y-m-d H:i:s',time());
				$this->user->rolle->setConsumeActivity($currenttime,'getMap',$this->user->rolle->last_time_id);
				$this->drawMap();
				$this->saveMap('');
				$this->output();
			}
		} break;
		
		case 'delete_fplan' : {
			include(PLUGINS.'bauleitplanung/model/rok.php');
			$mapdb = new db_mapObj($this->Stelle->id,$this->user->id);
			$layerdb = $mapdb->getlayerdatabase($this->formvars['selected_layer_id'], $this->Stelle->pgdbhost);
			$rok = new rok($layerdb);
			$rok->delete_fplan($this->formvars['plan_id']);
			if($this->formvars['value_f_plan_stammdaten_oid'] == ''){
				$this->GenerischeSuche_Suchen();		# Trefferliste wieder anzeigen
			}
			else{
				$this->loadMap('DataBase');					# Karte anzeigen
				$currenttime=date('Y-m-d H:i:s',time());
				$this->user->rolle->setConsumeActivity($currenttime,'getMap',$this->user->rolle->last_time_id);
				$this->drawMap();
				$this->saveMap('');
				$this->output();
			}
		} break;
		
		case 'copy_bplan' : {
			include(PLUGINS.'bauleitplanung/model/rok.php');
			$mapdb = new db_mapObj($this->Stelle->id,$this->user->id);
			$layerdb = $mapdb->getlayerdatabase($this->formvars['selected_layer_id'], $this->Stelle->pgdbhost);
			$rok = new rok($layerdb);
			$this->formvars['value_b_plan_stammdaten_oid'] = $rok->copy_bplan($this->formvars['plan_id']);
			$this->GenerischeSuche_Suchen();
		} break;
		
		case 'copy_fplan' : {
			include(PLUGINS.'bauleitplanung/model/rok.php');
			$mapdb = new db_mapObj($this->Stelle->id,$this->user->id);
			$layerdb = $mapdb->getlayerdatabase($this->formvars['selected_layer_id'], $this->Stelle->pgdbhost);
			$rok = new rok($layerdb);
			$this->formvars['value_b_plan_stammdaten_oid'] = $rok->copy_fplan($this->formvars['plan_id']);
			$this->GenerischeSuche_Suchen();
		} break;
		
		case 'update_bplan_from_rok' : {
			include(PLUGINS.'bauleitplanung/model/rok.php');
			$mapdb = new db_mapObj($this->Stelle->id,$this->user->id);
			$layerdb = $mapdb->getlayerdatabase($this->formvars['selected_layer_id'], $this->Stelle->pgdbhost);
			$rok = new rok($layerdb);
			$rok->update_bplan_from_rok($this->formvars['plan_id']);
			$this->GenerischeSuche_Suchen();
		} break;
		
		case 'update_fplan_from_rok' : {
			include(PLUGINS.'bauleitplanung/model/rok.php');
			$mapdb = new db_mapObj($this->Stelle->id,$this->user->id);
			$layerdb = $mapdb->getlayerdatabase($this->formvars['selected_layer_id'], $this->Stelle->pgdbhost);
			$rok = new rok($layerdb);
			$rok->update_fplan_from_rok($this->formvars['plan_id']);
			$this->GenerischeSuche_Suchen();
		} break;
		
		case 'zoomtobplan' : {
			include(PLUGINS.'bauleitplanung/model/rok.php');
			$rok = new rok($this->pgdatabase);
			$rect = $rok->getExtentFromRokNrBplan($this->formvars['roknr'], $this->formvars['art'], 10, $this->user->rolle->epsg_code);
			$this->loadMap('DataBase');
			if ($rect->minx!=0 and $rect->miny!=0 and $rect->maxx!=0 and $rect->maxy!=0) {
				$this->map->setextent($rect->minx,$rect->miny,$rect->maxx,$rect->maxy);
				if (MAPSERVERVERSION > 600) {
					$this->map_scaledenom = $this->map->scaledenom;
				}
				else {
					$this->map_scaledenom = $this->map->scale;
				}
			}
			else {
				$this->Fehlermeldung='Es konnte kein Geltungsbereich mit ROK-Nr. = '.$this->formvars['roknr'].' gefunden werden.';
			}
			$currenttime=date('Y-m-d H:i:s',time());
			$this->user->rolle->setConsumeActivity($currenttime,'getMap',$this->user->rolle->last_time_id);
			$this->drawMap();
			$this->saveMap('');
			$this->output();
		} break;
		
		case 'zoomtofplan' : {
			include(PLUGINS.'bauleitplanung/model/rok.php');
			$rok = new rok($this->pgdatabase);
			$rect = $rok->getExtentFromRokNrFplan($this->formvars['gkz'], 10, $this->user->rolle->epsg_code);
			$this->loadMap('DataBase');
			if ($rect->minx!=0 and $rect->miny!=0 and $rect->maxx!=0 and $rect->maxy!=0) {
				$this->map->setextent($rect->minx,$rect->miny,$rect->maxx,$rect->maxy);	  
			}
			else {
				$this->Fehlermeldung='Es konnte keine Gemeinde mit GKZ = '.$this->formvars['gkz'].' gefunden werden.';
			}
			$currenttime=date('Y-m-d H:i:s',time());
			$this->user->rolle->setConsumeActivity($currenttime,'getMap',$this->user->rolle->last_time_id);
			$this->drawMap();
			$this->saveMap('');
			$this->output();
		} break;
		
		default : {
			$this->goNotExecutedInPlugins = true;		// in diesem Plugin wurde go nicht ausgeführt
		}
	}
	
?>