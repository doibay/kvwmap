<?

##########################
# Klasse f�r Debug-Datei #
##########################
# Klasse debugfile #
####################

class debugfile {
  var $filename;
  var $fp;

  ###################### Liste der Funktionen ####################################
  #
  # function debugfile($filename) - Construktor
  # function write($msg,$level)
  # function close()
  #
  ################################################################################

  function debugfile($filename) {
    $this->filename=$filename;
    $this->fp=fopen($filename,'w');
    fwrite($this->fp,"<html>\n<head>\n  <title>kvwmap Debug-Datei</title>\n</head>\n<body>");
    fwrite($this->fp,"<h2>Debug Datei</h2>");
  }

  function write($msg,$level) {
    if ($level>=DEBUG_LEVEL) {
      $ret=@fwrite($this->fp,"\n<br>".$msg);
      if (!$ret) {
        $this->Fehlermeldung ='In die Debugdatei '.$this->filename.' l��t sich nicht schreiben.';
        $this->Fehlermeldung.='<br>Das kann daran liegen, dass f�r den WebServer, in dem kvwmap l�uft, keine Schreibrechte gesetzt sind.';
        $this->Fehlermeldung.='<br>Pr�fen Sie die Rechte der Datei!';
        include(LAYOUTPATH."snippets/Fehlermeldung.php");
        exit;
      }
    }
  }

  function close() {
    fwrite($this->fp,"\n</body>\n</html>");
    fclose($this->fp);
  }
}
############################################
# Klasse f�r das Loggen von SQL-Statements #
############################################
# Klasse LogFile #
##################

class LogFile {
  var $filename; # Dateiname in der gelogt wird
  var $fp; # filepointer
  var $format; # Ausgabeformat

  ###################### Liste der Funktionen ####################################
  #
  # function LogFile($filename,$format,$title,$headline) - Construktor
  # function write($msg)
  # function close()
  #
  ################################################################################

  # �ffnet die Logdatei
  function LogFile($filename,$format,$title,$headline) {
    $this->name=$filename;
    $this->fp=fopen($filename,"a");
    $this->format=$format;
    if ($format=="html") {
      # f�gt HEML header ein zum loggen in einer HTML-Datei
      # Wenn title gesetzt ist wird er als Titel im header gesetzt, sonst default.
      if ($title=="") { $title=="Logdatei"; }
      fwrite($this->fp,"<html>\n<head>\n<title>".$title."</title>\n</head>\n<body>");
      if ($headline!="") {
        $ret=@fwrite($this->fp,"<h1>".$headline."</h2>");
      }
    }
    if ($format=="text") {
      if ($headline!="") {
        $ret=@fwrite($this->fp,"\n".$headline);
      }
    }
    if (!$ret) {
      $this->Fehlermeldung ='In die Logdatei '.$this->name.' l��t sich nicht schreiben.';
      $this->Fehlermeldung.='<br>Das kann daran liegen, dass f�r den WebServer, in dem kvwmap l�uft, keine Schreibrechte gesetzt sind.';
      $this->Fehlermeldung.='<br>Pr�fen Sie die Rechte der Datei!';
      include(LAYOUTPATH."snippets/Fehlermeldung.php");
      exit;
    }
  }

  function write($msg) {
    if ($this->format=="html") {
      fwrite($this->fp,"\n<br>".$msg);
    }
    if ($this->format=="text") {
      fwrite($this->fp,"\n".$msg);
    }
  }

  function close() {
    if ($this->format=="html") {
      fwrite($this->fp,"\n</body>\n</html>");
    }
    fclose($this->fp);
  }

  function delete() {
    unlink($this->name);
  }
}

?>