<?

	$this->goNotExecutedInPlugins = false;
	
	switch($this->go){
		case 'Antraege_Anzeigen' : {
			$this->checkCaseAllowed('Antraege_Anzeigen');
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->Antraege_Anzeigen();
	  } break;

	  case 'Antrag_loeschen' : {
			$this->checkCaseAllowed($this->go);
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->Antrag_Loeschen();
	  } break;

	  case 'Antraganzeige_Festpunkte_in_Karte_Anzeigen' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->festpunkteZuAntragZeigen();
	  } break;

	  case 'Antraganzeige_Festpunkte_in_Liste_Anzeigen' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->festpunkteSuchen();
	  } break;

	  case 'Antraganzeige_Festpunkte_in_KVZ_schreiben' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->festpunkteInKVZschreiben();
			$this->Antraege_Anzeigen();
	  } break;

	  case 'Antraganzeige_Zugeordnete_Dokumente_Anzeigen' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->checkCaseAllowed($this->go);
			$this->DokumenteZuAntraegeAnzeigen();
	  } break;

	  case 'Antraganzeige_Uebergabeprotokoll_Erzeugen' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->erzeugenUebergabeprotokollNachweise($this->formvars['antr_selected']);
	  }break;
	  
	  case 'Antraganzeige_Uebergabeprotokoll_Erzeugen_PDF' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->erzeugenUebergabeprotokollNachweise_PDF();
	  }break;
	  
	  case 'Antraganzeige_Uebergabeprotokoll_Erzeugen_CSV' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->erzeugenUebergabeprotokollNachweise_CSV();
	  }break;

	  case 'Antraganzeige_Zusammenstellen_Zippen' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$ret=$this->DokumenteZumAntragInOrdnerZusammenstellen();
			if($ret != '')showAlert($ret);
			$filename = $this->DokumenteOrdnerPacken();
			$this->Datei_Download($filename);
	  } break;

	  case 'Nachweisloeschen':{
			$this->checkCaseAllowed($this->go);
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->nachweisLoeschen();
	  } break;


		#Documente die in der Ergebnisliste ausgewählt wurden sollen weiterverarbeitet werden!
		# 2006-01-26 pk
	  case 'Nachweisanzeige_zum_Auftrag_hinzufuegen' : {
			$this->checkCaseAllowed($this->go);
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->nachweiseZuAuftrag();
	  } break;

	  case 'Nachweisanzeige_aus_Auftrag_entfernen':{
			$this->checkCaseAllowed($this->go);
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->nachweiseZuAuftragEntfernen();
	  } break;

	  # Rechercheanfrage an die Datenbank senden / mit prüfen der Eingabedaten
	  case 'Nachweisanzeige' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			if($this->formvars['art_markieren'] AND $this->formvars['art_einblenden']){
				$this->formvars['showffr']=substr($this->formvars['art_einblenden'],0,1);
				$this->formvars['showkvz']=substr($this->formvars['art_einblenden'],1,1);
				$this->formvars['showgn']=substr($this->formvars['art_einblenden'],2,1);
				$this->formvars['showan']=substr($this->formvars['art_einblenden'],3,1);
				$this->formvars['markffr']=substr($this->formvars['art_markieren'],0,1);
				$this->formvars['markkvz']=substr($this->formvars['art_markieren'],1,1);
				$this->formvars['markgn']=substr($this->formvars['art_markieren'],2,1);
				$this->setNachweisAnzeigeparameter($this->user->rolle->stelle_id, $this->user->rolle->user_id, $this->formvars['showffr'],$this->formvars['showkvz'],$this->formvars['showgn'],$this->formvars['showan'],$this->formvars['markffr'],$this->formvars['markkvz'],$this->formvars['markgn']);
			}
			# Abfragen aller aktuellen Such- und Anzeigeparameter aus der Datenbank
			$this->savedformvars=$this->getNachweisParameter($this->user->rolle->stelle_id, $this->user->rolle->user_id);
			$this->formvars=array_merge($this->savedformvars,$this->formvars);
			$this->nachweis = new Nachweis($this->pgdatabase, $this->user->rolle->epsg_code);
			$ret=$this->nachweis->getNachweise(0,$this->formvars['suchpolygon'],$this->formvars['suchgemarkung'],$this->formvars['suchstammnr'],$this->formvars['suchrissnr'],$this->formvars['suchfortf'],$this->formvars['art_einblenden'],$this->formvars['richtung'],$this->formvars['abfrageart'], $this->formvars['order'],$this->formvars['suchantrnr'],$this->formvars['sdatum'], $this->formvars['sVermStelle'], $this->formvars['gueltigkeit'], $this->formvars['sdatum2'], $this->formvars['suchflur'], $this->formvars['flur_thematisch'], $this->formvars['such_andere_art']);
			if($ret!=''){
				$this->nachweisAnzeige();
				showAlert($ret);
			}
			else {
				$this->nachweisAnzeige();
			}
	  } break;

	  case 'document_anzeigen' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			$this->nachweisDokumentAnzeigen();
	  } break;
	  
	  case 'document_vorschau' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			$this->nachweisDokumentVorschau();
	  } break;

	  case 'Nachweisformular' : {
			$this->checkCaseAllowed($this->go);		
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			# Unterscheidung ob vorhandene Dokumente geändert werden sollen oder neu eingegeben
			if ($this->formvars['id']!='') {
				# Ein Nachweis soll geändert werden
				$this->nachweisAenderungsformular();
			}
			else {
				# Eingabe von Daten zu einem neuen Nachweisdokument
				# Anzeige des Neueingabeformulars
				$this->nachweisFormAnzeige();
			}
	  } break;
		
	  case 'Nachweisformular_Senden' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
	  	$_files = $_FILES;
			$this->formvars['Bilddatei']=$_files['Bilddatei']['tmp_name'];
			$this->formvars['Bilddatei_name']=$_files['Bilddatei']['name'];
			$this->formvars['Bilddatei_size']=$_files['Bilddatei']['size'];
			$this->formvars['Bilddatei_type']=$_files['Bilddatei']['type'];
			$this->nachweisFormSenden();
	  } break;
	  
	  case 'Nachweisformular_Vorlage' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden		
			$this->nachweisFormAnzeigeVorlage();
	  } break;

		case 'check_nachweis_poly' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			$this->check_nachweis_poly();
	  } break;

	  case 'Antrag_Aendern' : {
			$this->checkCaseAllowed($this->go);
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->vermessungsantragAendern();
	  } break;

	  case 'Nachweis_antragsnr_form_aufrufen' : {
			$this->checkCaseAllowed($this->go);
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->vermessungsantragsFormular();
	  } break;

	  case 'Nachweis_antragsnummer_Senden' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->vermessungsantragAnlegen();
	  } break;

	  case 'Nachweisrechercheformular':{
			$this->checkCaseAllowed($this->go);
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->rechercheFormAnzeigen();
	  } break;
		
		case 'Nachweisrechercheformular_Dokumentauswahl_speichern':{
			$this->checkCaseAllowed('Nachweisrechercheformular');
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->rechercheFormAnzeigen();
	  } break;
		
		case 'Nachweisrechercheformular_Dokumentauswahl_löschen':{
			$this->checkCaseAllowed('Nachweisrechercheformular');
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->rechercheFormAnzeigen();
	  } break;		

	  # Rechercheanfrage an die Datenbank senden/ mit prüfen der Eingabedaten
	  case 'Nachweisrechercheformular_Senden':{
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->nachweiseRecherchieren();
	  } break;
				
		case 'Sachdaten_Festpunkte Anzeigen' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			$this->festpunkteZeigen();
	  } break;

	  case 'Festpunkte Anzeigen' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			$this->festpunkteZeigen();
	  } break;

	  case 'Festpunkte in Liste Anzeigen' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->festpunkteSuchen();
	  } break;

	  case 'Festpunkte_Auswaehlen' : {
			$this->checkCaseAllowed($this->go);
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->festpunkteWahl();
	  } break;

	  case 'Festpunkte_Auswaehlen_Suchen' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->festpunkteSuchen();
	  } break;
		
		case 'Sachdaten_Festpunkte zu Auftrag Hinzufügen' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->festpunkteZuAuftragFormular();
	  } break;
	  
	  case 'Sachdaten_KVZ-Datei erzeugen' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			$this->formvars['antr_selected'] = 'ohne';
			$this->festpunkteInKVZschreiben();
			ob_end_clean();
			header("Content-type: text/kvz");
			header("Content-Disposition: attachment; filename=".basename($this->datei));
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			readfile($this->datei);
	  } break;

	  case 'Festpunkte zum Antrag Hinzufügen_Senden' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->festpunkteZuAuftragSenden();
	  } break;

	  case 'sendImage' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			if ($this->formvars['format'] == '') {
				$this->formvars['format']='png';
			}
			$this->sendImage($this->formvars['name'],'png');
	  } break;

	  case 'sendeFestpunktskizze' : {
			$this->checkCaseAllowed($this->go);
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			$this->sendeFestpunktskizze($this->formvars['name'],PUNKTDATEIPATH);
	  } break;

	  case 'FestpunktDateiUebernehmen' : {
			$this->checkCaseAllowed($this->go);
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->uebernehmeFestpunkte();
	  } break;

	  case 'FestpunktDateiAktualisieren' : {
			$this->checkCaseAllowed($this->go);
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->aktualisiereFestpunkte();
	  } break;

	  case 'Sachdaten_FestpunkteSkizzenZuordnung' : {
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->showFestpunkteSkizze();
	  } break;

	  case 'FestpunkteSkizzenZuordnung_Senden' : {
			$this->checkCaseAllowed($this->go);
			include (PLUGINS.'nachweisverwaltung/model/kvwmap.php');						# GUI-Objekt erweitern
			include (PLUGINS.'nachweisverwaltung/model/nachweis.php');					# nachweis-Klasse einbinden
			include (PLUGINS.'nachweisverwaltung/model/antrag.php');						# antrag-Klasse einbinden
			$this->ordneFestpunktSkizzen();
	  } break;
		
		default : {
			$this->goNotExecutedInPlugins = true;		// in diesem Plugin wurde go nicht ausgeführt
		}
	}
	
?>