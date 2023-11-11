<?php

if( ! class_exists( "millMachDrvTmpl" ) ) include( "../lib/cltmpl/millMachDrv.cltmpl.php" );

class modDrvMachTNC530_drvLib_vlsv2HH extends millMachDrvTmpl{

  var $_MACHID = 0;

  var $_VLSV2LIB = false;
  
  var $_HASMUPLOAD  = false;
  var $_HASNCSTART  = false;
  var $_HASNCSTATUS = false;
  var $_HASWPC = false;
  var $_HAS_USEROPT_DELJOB = false;
  var $_HAS_USEROPT_RESTARTJOB = false;
  
  var $_HASOPTSLEEP = true; // 04 2010
  var $_HASOPTAUTOSLEEP = true;
  
  var $_MINREFRESHPAUSE = 8;
  
  var $_AUTOMODE_MAX_RETRIES = 3;
  
  var $_SCRDUMP_LOCK_FH = false;

  function modDrvMachTNC530_drvLib_vlsv2HH( $MachId ){
    global $CFG;
    $this->_MACHID = cId( $MachId );
    $_M = mysql_rs0( "SELECT * FROM ".$CFG["MySql"]["tblModMillHouse_Machines"]." WHERE Id = '".cId( $MachId )."';" );
    if( $_M[ "Id" ] == $MachId ){
      if( strlen( trim( $_M["remoIp"] ) ) > 0 ){
        // Wir haben eine passende Lib, eine Remote-IP, also können wir eischentlich alles ;)
        $this->_HASMUPLOAD  = true;
        $this->_HASNCSTART  = true;
        $this->_HASNCSTATUS = true;
        $this->_HASWPC      = true;
        $this->_HAS_USEROPT_DELJOB     = true;
        $this->_HAS_USEROPT_RESTARTJOB = true;
        if( strpos( strtolower( ";".$_M["opts"].";" ), ";mikronrobotecve;" ) !== false ) $this->_MIKRON_ROBOTEC_VE = true;
        
      }
    }
    
  }

  function checkConn(){
    
  }
  
  /* Schnelle Ansichten ;) */

  function getMachInfoIPane( $altMachRS = false ){
    global $GUI, $MODS;
    $mod = $MODS->getModByName( "modDrvMachTNC530" );
    $t = "";
    if( $altMachRS ){
      $_M = $altMachRS;
    }else{
    
    }
    
    $_DEBUG = false; /*TODO WIEDER RAUS!!!!*/
    
    $_INFOURI = $mod->myUrl()."machInfos&machId=".$this->_MACHID;
    $_CONNECTED = true;//mErrCount mErrClassNo mErrClassStr mErrGrp mErrNo mErrStr
    if( $_M["mConnState"] == 0 AND $_DEBUG == false /*TODO WIEDER RAUS!!!!*/ ){
      $_MSG = "<span style=\"margin-left:40px;font-weight:bold;color:#FF0000;\">Keine Verbindung zur Maschine</span>";
      $img = $mod->_PATH."/pub/modIco.noConn.ani.32x32.gif";
    }else{
      if( $_M["mErrNo"] > -1 ){
        switch( $_M["mErrClassNo"] * 1 ){
          
          case 8:  $img = "pub/img/sys/info.32x32.gif";       $_ERMSG = "<span style=\"color:#828200;\"><b>INFO: ".$_M["mErrStr"]."</b></span>";  break;
          case 6:  $img = "pub/img/sys/info.32x32.gif";       $_ERMSG = "<span style=\"color:#828200;\"><b>RESET: ".$_M["mErrStr"]."</b></span>";  break;
          case 1:  $img = "pub/img/sys/debugWarn.32x32.gif";  $_ERMSG = "<span style=\"color:#c66319;\"><b>WARNING: ".$_M["mErrStr"]."</b></span>";  break;
          case 2:  $img = "pub/img/sys/debugWarn.32x32.gif";  $_ERMSG = "<span style=\"color:#c66319;\"><b>FEEDHOLD: ".$_M["mErrStr"]."</b></span>";  break;
          case 3:  $img = "pub/img/sys/debugWarn.32x32.gif";  $_ERMSG = "<span style=\"color:#c66319;\"><b>PROGRAMHOLD: ".$_M["mErrStr"]."</b></span>";  break;
          case 4:  $img = "pub/img/sys/debugWarn.32x32.gif";  $_ERMSG = "<span style=\"color:#c66319;\"><b>PROGRAMABORT: ".$_M["mErrStr"]."</b></span>";  break;
          case 5:  $img = "pub/img/sys/debugErr.32x32.gif";   $_ERMSG = "<span style=\"color:#cd0000;\"><b>EMERGENCYSTOP: ".$_M["mErrStr"]."</b></span>";  break;
          case 7:  $img = "pub/img/sys/critical.32x32.gif";   $_ERMSG = "<span style=\"color:#cd0000;\"><b>ERROR: ".$_M["mErrStr"]."</b></span>";  break;
          case 0:  $img = "pub/img/sys/help.32x32.gif";       $_ERMSG = "<span style=\"color:#000000;\"><b>NONE: ".$_M["mErrStr"]."</b></span>";  break;
          default: $img = "pub/img/sys/help.32x32.gif";       $_ERMSG = "<span style=\"color:#000000;\"><b>????: ".$_M["mErrStr"]."</b></span>";  break;
        }
      }else{
        $_ERMSG = "";
        $img = $mod->_PATH."/pub/modIco.32x32.gif";
      }
      $_MSG =  "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"font-family:sans-serif;font-size:11px;\">"
            .   "<tr><td style=\"\">POS: ".$_M["mStateActPos"]."</td>"
            .       "<td style=\"padding-left:25px;\">Tool:</td>"
            .       "<td style=\"padding-left:15px;\">".$_M["toolCurName"]."</td>"
            .       "<td style=\"padding-left:25px;\">SEL: ".$_M["mStateSelFile"]."</td>"
            .   "</tr>"
            .   "<tr><td>DNC: ".$_M["mDNCState"]."</td>"
            .       "<td style=\"padding-left:25px;\">ToolState:</td>"
            .       "<td style=\"padding-left:15px;".( $_M["toolState0OK1Broken"] == 1 ? "font-weight:bold;color:#FF0000;" : "" )."\">".$_M["toolStateString"]."</td>"
            .       "<td style=\"padding-left:25px;\">ACT: ".$_M["mStateRunFile"]."</td>"
            .   "</tr>";
      

      $_M2 = v7()->db()->getRow("SELECT * FROM ".v7()->tbl("tblModMillHouse_Machines")." WHERE Id ='".$this->_MACHID."';");
      if( strpos( ";".strtolower( $_M2["opts"] ).";", "mikronrobotecve" ) !== false ){
        $_MSG .= "<tr><td>Roboter:</td>"
              .       "<td style=\"padding-left:25px;\" colspan=\"3\">";
        $_MSG .= v7()->db()->getField( "SELECT CONCAT( '<span style=\"', IF( criticalFlag = 0, '', 'color:#FF0000;' ), '\">', fktName ) AS msg "
                                     . "  FROM ".v7()->tbl("tblFktLogs")
                                     . " WHERE fktContext = 'modCFMRVE' "
                                     . "   AND optRefId = '".$this->_MACHID."' "
                                     . " ORDER BY Id DESC "
                                     . " LIMIT 1;" );
        
        $_MSG .= "</td></tr>";
      }


      if( strlen( $_ERMSG ) > 0 ) $_MSG .= "<tr><td colspan=\"4\">".$_ERMSG."</td></tr>";
      $_MSG .= "</table>";
    }
    
    $iP = $GUI->getPane( "inner" );
    $t .= $iP->sprintCnt( "open" );
    
    $t .= "<div id=\"modCFMillMachInfoDiv_".$MachId."\">"
       .   "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"\">"
       .    "<tr>"
       .     "<td valign=\"top\" style=\"padding-right:10px;\">"
       .      "<div style=\"background-image:url(".$mod->_PATH."/pub/btBgr.40x40.gif);width:40px;height:40px;oberflow:hidden;text-align:center;\" "
       .           "onmouseover=\"this.style.backgroundPosition='0px 40px';\" "
       .           "onmouseout=\"this.style.backgroundPosition='0px 0px';\">"
       .       "<img style=\"margin-top:4px;\" src=\"".$img."\" border=\"0\" onclick=\"V7.modalPane.addPane( '".$_INFOURI."' );\" />"
       .      "</div>"
       .     "</td>"
       .     "<td valign=\"top\" style=\"padding-right:10px;\">"
       .      "<div style=\"background-image:url(".$mod->_PATH."/pub/btBgr.40x40.gif);width:40px;height:40px;oberflow:hidden;text-align:center;\" "
       .           "onmouseover=\"this.style.backgroundPosition='0px 40px';\" "
       .           "onmouseout=\"this.style.backgroundPosition='0px 0px';\">"
       .       "<img style=\"margin-top:4px;\" src=\"".$mod->_PATH."/pub/tool.ico.32x32.png\" border=\"0\" onclick=\"V7.modalPane.addPane( '".$mod->myUrl()."toolInfos&machId=".$this->_MACHID."' );\" />"
       .      "</div>"
       .     "</td>"
       
       .     "<td valign=\"top\" style=\"padding-right:10px;\">"
       .      "<div style=\"background-image:url(".$mod->_PATH."/pub/btBgr.40x40.gif);width:40px;height:40px;oberflow:hidden;text-align:center;\" "
       .           "onmouseover=\"this.style.backgroundPosition='0px 40px';\" "
       .           "onmouseout=\"this.style.backgroundPosition='0px 0px';\">"
       .       "<img style=\"margin-top:4px;\" src=\"".$mod->_PATH."/pub/term.32x32.gif\" border=\"0\" onclick=\"V7.modalPane.addPane( '".$mod->myUrl()."scrdmp&machId=".$this->_MACHID."' );\" />"
       .      "</div>"
       .     "</td>";
    if( $this->_MIKRON_ROBOTEC_VE && isDev() ){
      //vUdpDebug( "_MIKRON_ROBOTEC_VE" );
      if( $modMH = v7()->mod( "modMillHouse" ) ){
        //vUdpDebug( "modMillHouse" );
        $t .=    "<td valign=\"top\" style=\"padding-right:10px;\">"
           .      "<div style=\"background-image:url(".$mod->_PATH."/pub/btBgr.40x40.gif);width:40px;height:40px;oberflow:hidden;text-align:center;\" "
           .           "onmouseover=\"this.style.backgroundPosition='0px 40px';\" "
           .           "onmouseout=\"this.style.backgroundPosition='0px 0px';\">"
           .       "<img style=\"margin-top:4px;\" src=\"".$mod->_PATH."/pub/roboMIRT.32x32.png\" border=\"0\" "
           ."onclick=\"V7.modalPane.addPane( '".$modMH->myUrl( "mikronrobotecveadm&MachId=".$this->_MACHID )."' );\" />"
           .      "</div>"
           .     "</td>";
      }else{
        //vUdpDebug( "!modMillHouse" );
      }
    }else{
      //vUdpDebug( "no _MIKRON_ROBOTEC_VE" );
    }
    $t .=    "<td style=\"padding-right:10px;\">";
    
    $t .= $_MSG;
    $t .=    "</td></tr>"
       .    "</table>"
       .   "</div>";
    
    $t .= $iP->sprintCnt( "close" )."";
    return $t;
  }

  function getMachInfoURi(){
    global $MODS;
    $mod = $MODS->getModByName( "modDrvMachTNC530" );
    if( $mod ) return $mod->myUrl()."machInfos&machId=".$this->_MACHID;
    return "";
  }
  function getMachScrDumpURi(){
    global $MODS;
    $mod = $MODS->getModByName( "modDrvMachTNC530" );
    if( $mod ) return $mod->myUrl()."scrdmp&machId=".$this->_MACHID;
    return "";
  }
  
  function hasAutoMode(){ return true; }
  
  function setAutoMode( $w ){
    global $CFG;
    if( $this->_MACHID == 0 ) return false;
    if( $w == true ){
      mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_Machines"]
             ."   SET man0auto1mode = 1, autoModeErrC = 0, autoSleep = IF( autoSleepCfg > 0, 1, 0 ) WHERE Id = '".$this->_MACHID."';" );
    }else{
      mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_Machines"]
             ."   SET man0auto1mode = 0, autoModeErrC = 0, autoSleep = 0 WHERE Id = '".$this->_MACHID."';" );
    }
    return true;
  }


  // TODO: MachId nur class->_MACHID !!!!!!!!!!!!!!!!!!!
  
  /*
   *  Aktualisiert den Status der Maschine in der Datenbank ( tblModMillHouse_Machines )
   *  Verhindert mehr als eine Aktualisierung in $this->_MINREFRESHPAUSE Sekunden
   * 
   *  PRIVATE !!!!!!!!!!!!!!!!!!!!
   */
  
  function _refreshState( $altConn = false ){
    global $CFG;
    $MachId = cId( $this->_MACHID );//cId( $MachId );
    if( $MachId == 0 ) return false;
    if( ! $altConn ){
      $_M = mysql_rs0( "SELECT remoIp, mStateLastRefresh, mStateSelFile, mStateRunFile, mStateActPos, "
                     . "       mErrClassNo, mErrClassStr, mErrGrp, mErrNo, mErrStr, "
                     . "       IF( mStateLastRefresh + ".cId( $this->_MINREFRESHPAUSE )." < UNIX_TIMESTAMP( NOW() ), 1, 0 ) AS doRefresh "
                     . "  FROM ".$CFG["MySql"]["tblModMillHouse_Machines"]
                     . " WHERE Id = '".$MachId."';" );
    }
    if( $_M[ "doRefresh" ] == 1 OR $altConn ){
      //dependsOn( "v7sHHLsv2" );
      if( ! $altConn ){
//        $shhl = new v7sHHLsv2( $_M[ "remoIp" ] );
        $shhl = v7()->io()->cnc()->lsv2( $_M[ "remoIp" ], $forceReConn = true );
      }else{
        $shhl = $altConn;
      }
      if( ! $shhl ) return $this->setErr( "noConnClass" );
      
      
      if( ! ( $res = $shhl->getDNCExecStatus() ) ){
        vUdpDebug( "getDNCExecStatus() failed on '".$MachId."'");
        mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_Machines"]
              . "   SET mErrClassNo = 0, mErrClassStr = '', mErrGrp = '', mErrNo = -1, mErrStr = '', mConnState = 0 "
              . " WHERE Id = '".$MachId."';" );
        return false;
      }
      if( ! ( $_DNCSTATE = $shhl->getDNCStatus2string() ) ) $_DNCSTATE = "";
      $this->_FIRST_ERROR = Array( "errCl" => 0, "errClStr" => "", "errGrpStr" => "", "errNo" => -1, "errStr" => "" ); 
      if( $tErr = $shhl->getDNCFirstError() ) $this->_FIRST_ERROR = $tErr;
      // mErrCount mErrClassNo mErrClassStr mErrGrp mErrNo mErrStr mConnState
      if( strtoupper( trim( $tErr[ "errStr" ], "\n\r\t\0 " ) ) == "75 ITC WURDE NEU INITIALISIERT" ){
        // TODO: Abklären, was der sch. bei der dingensdrive soll!!!!
        $tErr = Array( "errCl" => 0, "errClStr" => "", "errGrpStr" => "", "errNo" => -1, "errStr" => "" ); 
      }
      
      mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_Machines"]
            . "   SET mStateLastRefresh = UNIX_TIMESTAMP( NOW() ), "
            . "       mStateSelFile = '".addslashes( $res["selFileName"] )."', "
            . "       mStateRunFile = '".addslashes( $res["actFileName"] )."', "
            . "       mStateActPos  = '".cId( $res["pos"] )."', "
            . "       mDNCState     = '".addslashes( $_DNCSTATE )."', "
            . "       mErrClassNo   = '".( $this->_FIRST_ERROR[ "errCl" ] * 1 )."', "
            . "       mErrClassStr  = '".addslashes( $this->_FIRST_ERROR[ "errClStr" ] )."', "
            . "       mErrGrp       = '".addslashes( $this->_FIRST_ERROR[ "errGrpStr" ] )."', "
            . "       mErrNo        = ".( $this->_FIRST_ERROR[ "errNo" ] * 1 ).", "
            . "       mErrStr       = '".addslashes( $this->_FIRST_ERROR[ "errStr" ] )."', "
            . "       mConnState = 1 "
            . " WHERE Id = '".$MachId."';" );
      $this->_STATE_ACTFILE = $res["actFileName"];
      $this->_STATE_SELFILE = $res["selFileName"];
      $this->_STATE_ACTPOS  = cId( $res["pos"] );
      $this->_STATE_DNC = $_DNCSTATE;
      $this->_DID_REFRESH_STATE = true;
      return true;
    }else{
      $this->_FIRST_ERROR = Array( "errCl" => $_M["mErrClassNo"], "errClStr" => $_M["mErrClassStr"], 
                                   "errGrpStr" => $_M["mErrGrp"], "errNo" => $_M["mErrNo"], "errStr" => $_M["mErrStr"] );
      $this->_STATE_ACTFILE = $_M["mStateRunFile"];
      $this->_STATE_SELFILE = $_M["mStateSelFile"];
      $this->_STATE_ACTPOS  = $_M["mStateActPos"];
      $this->_STATE_DNC     = $_M["mDNCState"];
    }
    return false;
  }
  
  /*
   *  Synchronisiert V7 + Maschine
   *  Ist ein Job
   *    erfolgreich fertiggestellt ( = 100% + nicht mehr aktiv)
   *      1. wird der status der Elemente incrementiert 
   *      2. wird wenn nicht mehr selected die Datei von der Maschine gelöscht und der Dateiname MachNC bei Erfolg geleert
   *    fehlerhaft beendet ( nicht mehr aktiv und < 100% )
   *      1. werdem die Elemente als Fehlerhaft markiert
   *      2. wird wenn nicht mehr selected die Datei von der Maschine gelöscht und der Dateiname MachNC bei Erfolg geleert
   *
   */
   // TODO LOCKS !!!!!!!!
  function syncJobs(){
    global $CFG, $MODS;
    $_DEBUG = false;
    $MachId = $this->_MACHID;//cId( $MachId );
    if( $MachId == 0 ) return  $this->setErr( "MachId == 0" );
    
    $this->checkAutoStandby();
    
    $_M = mysql_rs0( "SELECT remoIp, IF( mStateLastRefresh + ".cId( $this->_MINREFRESHPAUSE )." < UNIX_TIMESTAMP( NOW() ), 1, 0 ) AS doRefresh, "
                   . "       opts "
                   . "  FROM ".$CFG["MySql"]["tblModMillHouse_Machines"]
                   . " WHERE Id = '".$MachId."';" );
    if( $_M["doRefresh"] != 1 ){
      if( $_DEBUG ) echo "\nzum warten verdammt\n";
      return 0;
    }
    $remoIp = $_M["remoIp"];
    if( strlen( $remoIp ) == 0 ) return $this->setErr( "Keine remoIp" );
    //dependsOn( "v7sHHLsv2" );
//    $shhl = new v7sHHLsv2( $remoIp );
    if( ! ( $shhl = v7()->io()->cnc()->lsv2( $remoIp, $forceReConn = true ) ) ) return $this->setErr( "noConnClass" );
//    if( ! $shhl->checkReConn() ) return $this->setErr( "noConn" );
    $shhl->chDirCreate( "tnc:\camflow" );
    
    if( ! $this->_refreshState( $shhl ) ){
      if( $_DEBUG ) echo "\nzum warten verdammt\n";
      unset( $shhl );
      // checkAutoStandby
      mquery( "UPDATE ".v7()->tbl("tblModMillHouse_Machines")." SET autoSleep = 0 WHERE Id = '".$MachId."';" );
      return 0;
    }
    
    // Jobs synchronisieren >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    
    $_FO_SEL = explode( "\\", strrev( strtolower( $this->_STATE_SELFILE ) ), 2 );
    $_FO_SEL = strrev( $_FO_SEL[ 0 ] );
    $_FO_RUN = explode( "\\", strrev( strtolower( $this->_STATE_ACTFILE ) ), 2 );
    $_FO_RUN = strrev( $_FO_RUN[ 0 ] );
    if( strlen( $_FO_SEL ) == 0 ) return $this->setErr( "strlen( _FO_SEL == 0" );
    
    $_LIBE = $MODS->getModByName( "modMillHouse" )->lib( "elements" );
    
    if( $_DEBUG ) echo "Versuche Sync<br />aktuelle Datei=".$_FO_SEL;
    $_JOBS = mysql_toAsArray( "SELECT * FROM ".$CFG["MySql"]["tblModMillHouse_MillJobs"]
                            . " WHERE stateStr = 'runnc'"
                            . "   AND machId = '".$MachId."';", false, $_DEBUG );
    $_JC = COUNT( $_JOBS );
    for( $i = 0 ; $i < $_JC ; $i++ ){
      $_T_SEL = strtolower( $_JOBS[ $i ]["ncFileMach"] ) == $_FO_SEL;
      $_T_RUN = strtolower( $_JOBS[ $i ]["ncFileMach"] ) == $_FO_RUN;
      $_T_ERR = $this->_FIRST_ERROR[ "errNo" ] != -1;
      $_T_PRGR = $_JOBS[ $i ][ "statePrgr" ] * 1;
        $perc = 0;
        if( $_JOBS[ $i ][ "ncFileLineCount" ] > 0 ) $perc = round( ( $this->_STATE_ACTPOS * 100 ) / ( $_JOBS[ $i ][ "ncFileLineCount" ] * 1 ) );
        if( $perc > 100 ) $perc = 100;
        if( $perc < 0 ) $perc = 0;
        //echo $_JOBS[ $i ][ "ncFileLineCount" ]."-".$this->_STATE_ACTPOS."-".$perc;
        if( $perc > $_T_PRGR ) $_T_PRGR = $perc;
      
      if( $_T_SEL AND $_T_RUN ){ //                       Progress
      
        mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_MillJobs"]
              . "   SET statePrgr = '".$perc."' "
              . " WHERE '".$perc."' > statePrgr "
              . "   AND Id = '".$_JOBS[ $i ]["Id"]."';", false, false );
      
      }elseif( $_T_SEL == true AND $_T_RUN == false ){ // Idle ..
        if( $_T_ERR == 0 AND $_T_PRGR > 99 ){ //          Habe Fertig
          
          $_ELIds = mysql_toAsArray( "SELECT ElId FROM ".$CFG["MySql"]["tblModMillHouse_MillJobs_Els"]
                                   . " WHERE JId = '".cId( $_JOBS[ $i ][ "Id"] )."';", false, $_DEBUG );
          for( $ii = 0 ; $ii < COUNT( $_ELIds ) ; $ii++ ) $_LIBE->stateNext( $_ELIds[ $ii ][ "ElId" ], $ifStateIs = 300, true );
          if( strlen( $_JOBS[ $i ]["ncFileMach"] ) > 0 ){
            if( $shhl->delNC( $_JOBS[ $i ]["ncFileMach"] ) ){
              vUdpDebug( "'".$_JOBS[ $i ]["ncFileMach"]."' gelöscht." );
              $tDelRes = "";
            }else{
              vUdpDebug( "Löschen von '".$_JOBS[ $i ]["ncFileMach"]."' fehlgeschlagen.", "critical" );
              $tDelRes = ": DelErr (".$_JOBS[ $i ]["ncFileMach"].")";
            }
            // ACHTUNG: Vermutlich schlägt das HIER fehl, weil die Maschine die Datei noch geladen hat. Also zusätzlichen "Aufräumlauf" initiieren
          }
          mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_MillJobs"]
                . "   SET jDone = 1, "
                . "       stateStr = 'ncdone', "
                . "       jDoneDT = NOW(), "
                . "       jMsg = CONCAT('NC fertiggestellt".addslashes( $tDelRes )."'), "
                . "       jDur = SEC_TO_TIME( UNIX_TIMESTAMP( NOW() ) - UNIX_TIMESTAMP( jStarted ) ) "
                . " WHERE Id = '".cId( $_JOBS[ $i ][ "Id"] )."';", false, $_DEBUG );
          //// Automatisierung von Mikron, Robotec + Millhouse
          vUdpDebug( "----- MillJob '".cId( $_JOBS[ $i ][ "Id"] )."' erfolgreich erledigt -----" );
          if( $this->_MIKRON_ROBOTEC_VE ){
            vUdpDebug( "----- Initialisiere Vereinzelung -----" );
            $robo = v7()->io()->cnc()->mikronVERobo( $this->_MACHID );
            $robo->ve( cId( $_JOBS[ $i ][ "Id"] ) );
            vUdpDebug( "----- Beende Vereinzelung (MId ".$this->_MACHID.") -----" );
          }else{
            vUdpDebug( "----- Keine Vereinzelung (MId ".$this->_MACHID.") -----" );
          }
          
        }elseif( $_T_PRGR > 99 ){ //                      Unverzeilicher Fehler
          
          // addLog( "vITNC530", "FEHLER, ActPos : ".$this->_STATE_ACTPOS.", LF: ".$_FO_SEL.", RF: ".$_FO_RUN."" );
          
          if( $_DEBUG ) echo "<br />Fehlerhaft";
          mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_MillJobs"]
                . "   SET jDone = 1, "
                . "       stateStr = 'runncerr', "
                . "       jDoneDT = NOW(), "
                . "       jMsg = CONCAT('FEHLER: ".addslashes( $this->_FIRST_ERROR[ "errStr"] )."'), "
                . "       jDur = SEC_TO_TIME( UNIX_TIMESTAMP( NOW() ) - UNIX_TIMESTAMP( jStarted ) ) "
                . " WHERE Id = '".cId( $_JOBS[ $i ][ "Id"] )."';", false, $_DEBUG );
          
          if( strlen( $_JOBS[ $i ]["ncFileMach"] ) > 0 ){
            if( $shhl->delNC( $_JOBS[ $i ]["ncFileMach"] ) ){
              vUdpDebug( "'".$_JOBS[ $i ]["ncFileMach"]."' gelöscht." );
            }else{
              vUdpDebug( "Löschen von '".$_JOBS[ $i ]["ncFileMach"]."' fehlgeschlagen.", "critical" );
            }
          }
          
        }else{ //                                         Hier steht was faul rum
          // Um Hilfe rufen
          // 28022010 addLog( "vITNC530", "GETHELP!, ActPos : ".$this->_STATE_ACTPOS.", LF: ".$_FO_SEL.", RF: ".$_FO_RUN.", ERR: ".$this->_FIRST_ERROR[ "errStr"] );
        }
      }elseif( $_T_SEL == false AND $_T_RUN == false AND $_T_PRGR > 0 ){
        // Abbruch durch Benutzer an der Maschine
          mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_MillJobs"]
                . "   SET jDone = 1, "
                . "       stateStr = 'runncerr', "
                . "       jDoneDT = NOW(), "
                . "       jMsg = CONCAT('FEHLER: Manual User-Abbort @ TNC'), "
                . "       jDur = SEC_TO_TIME( UNIX_TIMESTAMP( NOW() ) - UNIX_TIMESTAMP( jStarted ) ) "
                . " WHERE Id = '".cId( $_JOBS[ $i ][ "Id"] )."';", false, $_DEBUG );
          
          if( strlen( $_JOBS[ $i ]["ncFileMach"] ) > 0 ){
            if( $shhl->delNC( $_JOBS[ $i ]["ncFileMach"] ) ){
              vUdpDebug( "'".$_JOBS[ $i ]["ncFileMach"]."' gelöscht." );
            }else{
              vUdpDebug( "Löschen von '".$_JOBS[ $i ]["ncFileMach"]."' fehlgeschlagen.", "critical" );
            }
          }
      }
    }
    // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Jobs synchronisieren
   

 
    // Werkzeugtabellen synchronisieren >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    
    $this->_syncToolTbl( $shhl ); // Vorsicht: setzt Blocksize der Verbindung auf 1024 !!

    $_curTool = mysql_rs0( "SELECT toolPl.Id, toolPl.currTool, toolPl.L, toolPl.tName, "
                         . "       toolT.tLocked, toolT.reserveTool, toolT.timeToRun, toolT.timeElapsed, "
                         . "       IF( toolT.tLocked = 1, "
                         . "           CONCAT( 'gebrochen' ), "
                         . "           CONCAT( IF( toolT.timeToRun < toolT.timeElapsed, CONCAT( '-' ), CONCAT( '' ) ), "
                         . "                   TIME_FORMAT( SEC_TO_TIME( ROUND( toolT.timeToRun - toolT.timeElapsed ) ), '%H:%i:%s verbleibend' ) ) "
                         . "         ) AS tmpToolStateStr "
                         . "  FROM prod_modDrvMachTNC530_ToolPl AS toolPl "
                         . " CROSS JOIN prod_modDrvMachTNC530_Tools AS toolT "
                         . "    ON ( toolT.tNumber = toolPl.currTool ) "
                         . " WHERE toolPl.pNo = 0;" );
    mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_Machines"]
          . "   SET toolCurIndex = '".( $_curTool[ "currTool"] * 1 )."', "
          . "       toolCurName  = '".addslashes( $_curTool[ "tName" ] )."', "
          . "       toolState0OK1Broken = '".( $_curTool["tLocked"] * 1 )."', "
          . "       toolStateString = '".addslashes( $_curTool["tmpToolStateStr"] )."' "
          . " WHERE Id = '".$MachId."';" );
    // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Werkzeugtabellen synchronisieren
    
    unset( $shhl );
    return $_JC;
  }
  

// TODO: Logging in writelog oder raus, LOCK an!!
  function checkAuto( $wLock = false ){
    global $CFG, $SYS;
    $_DOLOG = false;
    $_DEBUG = false;
    $MachId = cId( $this->_MACHID );
    if( $MachId * 1 == 0 ) return;
    //autoSleep
    if( cId( mysql_singlers( "SELECT man0auto1mode FROM ".$CFG["MySql"]["tblModMillHouse_Machines"]
                            ." WHERE Id = '".$MachId."';", false, $_DEBUG ) ) == 0 ) return; // Nix zu tun
    if( $wLock ) mquery( "LOCK TABLES ".$CFG["MySql"]["tblModMillHouse_MillJobs"]." WRITE, "
                                       .$CFG["MySql"]["tblModMillHouse_Machines"]." WRITE;", false, $_DEBUG );
    
    // Fehlversuche ( Maschine will nicht und wir wissen nichts anderes zu tun als abzuwarten und ggf aufzugeben
    $AMERRC = cId( mysql_singlers( "SELECT autoModeErrC FROM ".$CFG["MySql"]["tblModMillHouse_Machines"]
                                 . " WHERE Id = '".$MachId."';", false, $_DEBUG ) );
    
    if( $AMERRC > $this->_AUTOMODE_MAX_RETRIES ){
    
      if( $_DOLOG == true ) addLog( "vITNC530", "maximale Anzahl Autostartversuche für Maschine ".$MachId." überschritten" );
      mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_Machines"]
            . "   SET man0auto1mode = 0 "
            . " WHERE Id = '".$MachId."' -- $"."AMERRC was ".$AMERRC.";", false, $_DEBUG );
    
    }else{
      
      $RCC = cId( mysql_singlers( "SELECT COUNT(*) FROM ".$CFG["MySql"]["tblModMillHouse_MillJobs"]
                                . " WHERE stateStr = 'runnc' "
                                . "   AND machId = '".$MachId."';", false, $_DEBUG ) ) * 1;
      if( $RCC == 0 ){
        $MJId = cId( mysql_singlers( "SELECT MIN( Id ) FROM ".$CFG["MySql"]["tblModMillHouse_MillJobs"]
                                   . " WHERE stateStr = 'uploaded'"
                                   . "   AND machId = '".$MachId."';", false, $_DEBUG ) );
        if( $MJId > 0 ){
        
          if( $this->tryStartMillJob( $MJId ) ){
            if( $wLock ) mquery( "UNLOCK TABLES;", false, $_DEBUG );
            if( $_DOLOG == true ) addLog( "vITNC530", "".$MJId." auf ".$MachId." autogestartet :)" );
            return true;
          }else{
            mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_Machines"]
                  . "   SET autoModeErrC = autoModeErrC + 1 "
                  . " WHERE Id = '".$MachId."';", false, $_DEBUG );
            if( $_DOLOG == true ) addLog( "vITNC530", "trying to start next ".( $res == true ? "done" : "failed ".$AMERRC." times (".$this->_LAST_ERR.")" ) );
          }
        
        }else{ // Nix mehr zu tun
          
          if( cId( mysql_singlers( "SELECT autoSleep FROM ".$CFG["MySql"]["tblModMillHouse_Machines"]
                                 . " WHERE Id = '".$this->_MACHID."';", false, $_DEBUG ) ) > 0 ){
            
            vUdpDebug( "Schicke Maschine Id ".$this->_MACHID." schlafen, nix mehr zu tun :) n8" );
////            $this->tryInitStandby();// Und weg
          
          }
         /* 
          mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_Machines"]
                . "   SET man0auto1mode = 0 "
                . " WHERE Id = '".$this->_MACHID."';", false, $_DEBUG );
          */
        }
        
      }
    
    }
    
    // Autoupload >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    // Verlässt sich auf die Sperren des Uploadprozesses sowie dessen Uploadmethode
    $uploadMJId = 0;
    if( mysql_singlers( "SELECT COUNT( * ) FROM ".$CFG["MySql"]["tblModMillHouse_MillJobs"]
                      . " WHERE stateStr = 'preupload' "
                      . "    OR stateStr = 'uploading'"
                      . "   AND machId = '".$MachId."';", false, $_DEBUG ) * 1 == 0 ){
    
    $uploadMJId = cId( mysql_singlers( "SELECT MIN( Id ) FROM ".$CFG["MySql"]["tblModMillHouse_MillJobs"]
                                     . " WHERE machId = '".$this->_MACHID."' "
                                     . "   AND jDone = 0 "
                                     . "   AND ( stateStr = '' "
                                     . "       OR stateStr = 'uploaderr' )"
                                     . "   AND DATEDIFF( NOW(), Created ) < 14;", false, $_DEBUG ) ); 
 
    }
    // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Autoupload
    
    if( $wLock ) mquery( "UNLOCK TABLES;", false, $_DEBUG );
    
    if( $uploadMJId > 0 ) $SYS->vchp()->initProcess( "modDrvMachTNC530", "modNUpload", Array( "mjId" => $uploadMJId ) );
    
    return false;
  }
  
  
  function tryStartUpload( $MJId ){
    global $SYS;
    $SYS->vchp()->initProcess( "modDrvMachTNC530", "modNUpload", Array( "mjId" => cId( $MJId ) ) );
  }
  
  function tryStartMillJob( $MJId ){// Achtung, Sperren von startNext beachten
    global $CFG;
    $MJId = cId( $MJId );
    if( $MJId == 0 ) return $this->setErr( "MJId == 0" );
    $_MJ = mysql_rs0( "SELECT machId, ncFileMach, blankId FROM ".$CFG["MySql"]["tblModMillHouse_MillJobs"]." WHERE Id = '".$MJId."';" );
    $MachId = cId( $_MJ["machId"] );
    $NCFileM = $_MJ["ncFileMach"];
    if( $MachId == 0 ) return $this->setErr( "MachId == 0" );
    if( strlen( $NCFileM ) == 0 ) return $this->setErr( "Kein NCFileM" );
    $remoIp = mysql_singlers( "SELECT remoIp FROM ".$CFG["MySql"]["tblModMillHouse_Machines"]." WHERE Id = '".$MachId."';" );
    if( strlen( $remoIp ) == 0 ) return $this->setErr( "Keine remoIp" );
    //dependsOn( "v7sHHLsv2" );
//    $shhl = new v7sHHLsv2( $remoIp );
    if( ! ( $shhl = v7()->io()->cnc()->lsv2( $remoIp ) ) ) return $this->setErr( "ERR: sHHLsv2 0:".$shhl->getLastErr() );
    
    //if( ! ( $res = $shhl->getDNCExecStatus() ) ) return $this->setErr( "ERR: sHHLsv2 0:".$shhl->getLastErr() );
      
      // Muss das nich eigentlich erst nach tatsächlich gestartet passieren?? :
      // -->> Nein, weil der Neue Status so auch erst - korrekter weise - nach dem start mit dem nächsten refresh gesetzt wird !!
      
      mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_Machines"]
            . "   SET mStateLastRefresh = UNIX_TIMESTAMP( NOW() ), "
            . "       mStateSelFile = '".$res["selFileName"]."', "
            . "       mStateRunFile = '".$res["actFileName"]."', "
            . "       mStateActPos  = '".$res["pos"]."' "
            . " WHERE Id = '".$MachId."';" );
    
    if( strlen( trim( $res[ "actFileName" ] ) ) > 0 ) return $this->setErr( "Es läuft bereits eine NC (".$res["actFileName"].")" );//läuft bereits eine
    
    // Vereinzelung MiRtMh ?? 
    $veMiRtMh = ";".mysql_singlers( "SELECT opts FROM ".$CFG["MySql"]["tblModMillHouse_Machines"]." WHERE Id = '".$MachId."';" ).";";
    $ERR_STOP = "";
    if( $this->_MIKRON_ROBOTEC_VE ){// Hier muss erst ein Palettenwechsel durch den Robo erfolgen
      vUdpDebug( "txPalChange 2 Robo (MId ".$this->_MACHID.")" );
      $blankId = cId( $_MJ["blankId"] );
      if( $blankId == 0 ){
        $ERR_STOP = "BlankId == 0 !!";
      }else{
        $palPl = cId( mysql_singlers( "SELECT PalWPlatz FROM ".v7()->tbl("tblModMillHouse_RohlVw" )." WHERE Id = '".$blankId."';" ) );
        if( $palPl == 0 ){
          $ERR_STOP = "PanWPC == 0!!";
        }else{
          $robo = v7()->io()->cnc()->mikronVERobo($MachId);
          if( $robo->loadPal( $palPl ) == false ) $ERR_STOP = "Robofehler..";
        }
      }
    }else{
      vUdpDebug( "keine VE (MId ".$this->_MACHID.")" );
    }
    if( strlen( $ERR_STOP ) == 0 ){
      vUdpDebug( "Fange an mit ".$NCFileM );
      if( ! $shhl->chDirCreate( "tnc:\camflow" ) ) return $this->setErr( "ERR: sHHLsv2 1: ".$shhl->getLastErr() );
      if( ! $shhl->activateNRun( $NCFileM ) ){
        $xxxx = $shhl->getDNCFirstError();
        vUdpDebug( "ERR: ".print_r( $xxxx, true ) );
        return $this->setErr( "ERR: sHHLsv2 2: ".$shhl->getLastErr()."('".$NCFileM."')" );
      }
      // scheinbar gestartet, also mal millJobs aktualisieren
      
      mquery( "UPDATE ".$CFG["MySql"]["tblModMillHouse_MillJobs"]
            . "   SET stateStr = 'runnc', statePrgr = 0, jStarted = NOW() WHERE Id = '".$MJId."';" );
      return true;
    }else{
      vUdpDebug( "Habe Vereinzelung, kann diese aber aus folgendem Grund nicht ausf.: '".$ERR_STOP."'", "critical" );
      return false;
    }
  }
  
  /* GUI-Called */
  function tryDelJob( $MJId ){
    global $CFG;
    echo "tryDelJob ".$MJId;
    $MJId = cId( $MJId );
    if( $MJId == 0 ) return "MJId=0";
    $_MJ = mysql_rs0( "SELECT ncFileMach FROM ".$CFG["MySql"]["tblModMillHouse_MillJobs"]." WHERE Id = '".$MJId."';" );
    $MachId = $this->_MACHID * 1;
    if( $MachId == 0 ) return "MachId == 0";
    $_M = mysql_rs0( "SELECT remoIp, mStateRunFile, mStateSelFile "
                   . "  FROM ".$CFG["MySql"]["tblModMillHouse_Machines"]
                   . " WHERE Id = '".$MachId."';" );    
    //dependsOn( "v7sHHLsv2" );
//    $shhl = new v7sHHLsv2( $_M["remoIp"] );
    if( ! ( $shhl = v7()->io()->cnc()->lsv2( $_M["remoIp"] ) ) ) return "ERR: No conn!";;
//    if( ! $shhl->getDNCExecStatus() ) return "ERR: No conn!";
    
    $cRunF = strtolower( trim( removeNonAlNumAscii( $_M["mStateRunFile"] ) ) );
    $cSelF = strtolower( trim( removeNonAlNumAscii( $_M["mStateSelFile"] ) ) );
    $MJNCF = strtolower( trim( removeNonAlNumAscii( $_MJ["ncFileMach"] ) ) );
    if( strpos( $cRunF, $MJNCF ) !== false ) return "ERR: Still executing!";
    if( strpos( $cSelF, $MJNCF ) !== false ) return "ERR: Still selected!";
    mquery( "DELETE FROM ".$CFG["MySql"]["tblModMillHouse_MillJobs"]." WHERE Id = '".$MJId."' LIMIT 1;" );
    mquery( "DELETE FROM ".$CFG["MySql"]["tblModMillHouse_MillJobs_Els"]." WHERE JId = '".$MJId."';" );
    
    $shhl->chDirCreate( "tnc:\camflow" );
    if( ! $shhl->delNC( $_MJ["ncFileMach"] ) ) return "ERR: Could not delete NC-File!";
    return "OK";
  }



  function hasOptAutoStandBy(){
    return $this->_HASOPTAUTOSLEEP;
  }

  function setAutoStandBy( $w = true ){
    v7()->db()->query( "UPDATE ".v7()->tbl("tblModMillHouse_Machines")." SET autoSleep = ".($w == true ? 1 : 0)." WHERE Id = '".$this->_MACHID."';" );
  }
  
  function getAutoStandBy(){
    return v7()->db()->getField( "SELECT autoSleep FROM ".v7()->tbl("tblModMillHouse_Machines")." WHERE Id = '".$this->_MACHID."';" ) == 0 
      ? false : true;
  }


  // prüft, ob die Maschine automatisch in den Standby soll (Zeit letzer Aktivität + Wartezeit < NOW() )
  function checkAutoStandby(){
    $_DEBUG = true;
    if( $_DEBUG ) vUdpDebug( "Prüfe Autostandby machId '".$this->_MACHID."'" );
    if( ( $MachId = cId( $this->_MACHID ) ) == 0 ) return false;
    if( cId( mysql_singlers( "SELECT autoSleep FROM ".v7()->tbl("tblModMillHouse_Machines")." WHERE Id = '".$MachId."';" ) ) == 0 ){
      if( $_DEBUG ) vUdpDebug( "autoSleep für machId '".$this->_MACHID."' nicht aktiviert" );
      return false;//Nix zu tun
    }
    if( $_DEBUG ) vUdpDebug( "Autostandby aktiv" );
    
    // Anzahl noch laufender Fräsberechnungen
    $CRunC = mysql_singlers( "SELECT COUNT(Id) FROM ".v7()->tbl("tblModMillHouse_millCalc")." "
                           . " WHERE Deleted = 0 "
                           . "   AND StateCr0St10Ok40Err50 = 10 "
                           . "   AND MachId = '".$MachId."';" );
    
    // Anzahl noch laufender Jobs (bzw. startbereite wenn Automatik ..)
    $CRunJ = mysql_singlers( "SELECT COUNT(Id) FROM ".v7()->tbl("tblModMillHouse_MillJobs")." "
                           . " WHERE machId = '".$MachId."' "
                           . "  ".( $autoMode ? "AND ( stateStr = 'uploaded' OR stateStr = 'runnc' ) " : " AND stateStr = 'runnc' " ).";" );
    if( $_DEBUG ) vUdpDebug( "autoSleep( ".$this->_MACHID." ) Anz Laufender Ber. = ".$CRunC.", Anz. laufender Jobs = ".$CRunJ.";" );
    if( $CRunC > 0 OR $CRunJ > 0 ){
      if( $_DEBUG ) vUdpDebug( "Setze autoSleepLastActUT, da CRunC = ".$CRunC.", CRunJ = ".$CRunJ.";" );
      mquery( "UPDATE ".v7()->tbl("tblModMillHouse_Machines")
            . "   SET autoSleepLastActUT = UNIX_TIMESTAMP( NOW() ) "
            . " WHERE Id = '".$MachId."';" );
    }else{
      $standby = mysql_singlers( "SELECT IF( autoSleepLastActUT != 0 AND autoSleepLastActUT + 600 < UNIX_TIMESTAMP( NOW() ), 1, 0 ) "
                               . "  FROM ".v7()->tbl("tblModMillHouse_Machines")
                               . " WHERE Id = '".$MachId."';" );
      if( $standby * 1 == 1 ){
        if( $_DEBUG ) vUdpDebug( "Soll autostandby ausführen" );
        $res = $this->tryInitStandby();
        if( $_DEBUG ) vUdpDebug( "Geh(".$MachId.") dann mal schlafen :)" );
        //$res = "Würde hier nun schlafen gehen :)";
        if( $res !== 0 ) vUdpDebug( "Autostandby-Fehler: ".$res, "critical" );
        // Korrenkt, oder vorsichtshalber beenden ..
        mquery( "UPDATE ".v7()->tbl("tblModMillHouse_Machines")
              . "   SET autoSleep = 0, autoSleepLastActUT = 0, man0auto1mode = 0 "
              . " WHERE Id = '".$MachId."';" );
      }else{
        if( $_DEBUG ) vUdpDebug( "IF( autoSleepLastActUT != 0 AND autoSleepLastActUT + 600 < UNIX_TIMESTAMP( NOW() ), 1, 0 ) == 0" );
      }
    }
    
  } 
 
  /* TODO: ACHTUNG (19.01.2011):
   * Kann ToolTbls nicht einfach truncaten, da es mehrere Maschinen gibt!! machId noch prüfen und beherzigen!!
   */
  
  function tryInitStandby(){
    global $CFG;
    $MachId = cId( $this->_MACHID );
    if( $MachId == 0 ) return "ERR: 0 tryInitStandby(): Keine MachId";
    if( ! is_dir( "../tmp/modDrvMachTNC530" ) ) mkdir( "../tmp/modDrvMachTNC530", 0777 );
    file_put_contents( "../tmp/modDrvMachTNC530/CFStandby.h", 
                       "0 BEGIN PGM Standby MM"
                     . "\n1 ; created ".date( "d.m.Y" )." by CamFlow"
                     . "\n2 M77"
                     . "\n3 END PGM Standby MM" );
    $_M = mysql_rs0( "SELECT remoIp, mStateRunFile, mStateSelFile "
                   . "  FROM ".$CFG["MySql"]["tblModMillHouse_Machines"]
                   . " WHERE Id = '".$MachId."';" );    
    //dependsOn( "v7sHHLsv2" );
//    $shhl = new v7sHHLsv2( $_M["remoIp"] );
    if( ! ( $shhl = v7()->io()->cnc()->lsv2( $_M["remoIp"] ) ) )  return "ERR: 1 tryInitStandby(): No conn!";;
    if( ! $shhl->getDNCExecStatus() ) return "ERR: 1 tryInitStandby(): No conn!";
    if( ! $shhl->chDirCreate( "tnc:\camflow" ) ) return "ERR: 2 tryInitStandby(): ".$shhl->getLastErr();
    if( ! $shhl->uploadNC( "../tmp/modDrvMachTNC530/CFStandby.h", "CFStandby.h" ) ){
      // des bassd scho .. 
      //return "ERR: 3 tryInitStandby(): ".$shhl->getLastErr();
    }
    if( ! $shhl->activateNRun( "CFStandby.h" ) ) return "ERR: 4 tryInitStandby(): ".$shhl->getLastErr();
    return 0;
  }
  
  function _syncToolTbl( $commDrv = false ){
    global $CFG, $SYS;
    $_DEBUG_SQL = false;
    //return "ok";// Kurzfristig mal raus ..
    vUdpDebug( "_syncToolTbl() called" );
    
    if( ! $SYS->getLock( "iTNC530Id".$MachId ) ) return $this->_syncToolTbl_cleanUp( "ERR: _syncToolTbl(): got no Lock 4 MId ".$MachId );
    
    $MachId = cId( $this->_MACHID );
    if( $MachId == 0 ) return $this->_syncToolTbl_cleanUp( "ERR: 0 tryInitStandby(): Keine MachId" );
    if( ! $commDrv ){
      $_M = mysql_rs0( "SELECT remoIp, mStateRunFile, mStateSelFile "
                     . "  FROM ".$CFG["MySql"]["tblModMillHouse_Machines"]
                     . " WHERE Id = '".$MachId."';" );    
//      dependsOn( "v7sHHLsv2" );
//      $shhl = new v7sHHLsv2( $_M["remoIp"] );
      $shhl = v7()->io()->cnc()->lsv2( $_M["remoIp"] );
    }else{
      $shhl = $commDrv;
    }
    if( ! $shhl ) return  $this->_syncToolTbl_cleanUp( "ERR: noConn" );
    if( ! $shhl->getTNCTyp() )  return $this->_syncToolTbl_cleanUp( "ERR: _syncToolTbl(): typ verweigert" );
    if( ! is_dir( "../tmp/modDrvMachTNC530" ) ) mkdir( "../tmp/modDrvMachTNC530", 0777 );
    $this->_T_TOOLTTNAME = "tool.".md5( date( "dmyHis" ) ).".t";
    $this->_T_TOOLTPNAME = "tool_p.".md5( date( "dmyHis" ) ).".tch";
    if( ! $shhl->getConnStatus() ) return $this->_syncToolTbl_cleanUp( "ERR: _syncToolTbl(): no conn" );
    if( ! $shhl->sysSetBUF1024() ) return $this->_syncToolTbl_cleanUp( "ERR: _syncToolTbl(): setBuff1024 verweigert :(" );
    if( ! $shhl->download( $src = "TNC:\\TOOL.T", $dst = "../tmp/modDrvMachTNC530/".$this->_T_TOOLTTNAME ) ) return $this->_syncToolTbl_cleanUp( "ERR: _syncToolTbl(): Datei TOOL.T verweigert :(" );
    if( ! $shhl->download( $src = "TNC:\\TOOL_P.TCH", $dst = "../tmp/modDrvMachTNC530/".$this->_T_TOOLTPNAME ) ) return $this->_syncToolTbl_cleanUp( "ERR: _syncToolTbl(): Datei TOOL.T verweigert :(" );
    
    // Wir haben Daten, also ab damit in die DB

    // Tool-Plätze >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
      $d = fixedColWidthTblFile2Array( file_get_contents( "../tmp/modDrvMachTNC530/".$this->_T_TOOLTPNAME ), 
                                      $doTrim = " ", $initialLineMustContain = "BEGIN", $lastLineMustContain = "END" );
      $vgl = Array( "P", "T", "L", "TNAME", "DOC" );
    
      $dC = COUNT( $d[ "data" ] );
      $MyQuery = "INSERT INTO prod_modDrvMachTNC530_ToolPl_tmp ( "
              . "       machId, pNo, currTool, L, TNAME, DOC ) VALUES ";
      for( $i = 0 ; $i < $dC ; $i++ ){
        $ii = 0;
        if( $i > 0 ) $MyQuery .= ", ";
        $MyQuery .= "\n('".$MachId."', '".$d["data"][ $i ][ $ii++ ]."', '".$d["data"][ $i ][ $ii++ ]."', '".$d["data"][ $i ][ $ii++ ]."', '".$d["data"][ $i ][ $ii++ ]."', '".$d["data"][ $i ][ $ii++ ]."' )";
      }
      $MyQuery .= ";";
      //mquery( "LOCK TABLES prod_modDrvMachTNC530_Tools_tmp WRITE;", false, true );
      mquery( "TRUNCATE prod_modDrvMachTNC530_ToolPl_tmp;", false, $_DEBUG_SQL  );
      mquery( $MyQuery, false, $_DEBUG_SQL );
      mquery( "TRUNCATE prod_modDrvMachTNC530_ToolPl;", false, $_DEBUG_SQL  );
      mquery( "INSERT INTO prod_modDrvMachTNC530_ToolPl SELECT * FROM prod_modDrvMachTNC530_ToolPl_tmp;", false, $_DEBUG_SQL );
      unset( $d );
    // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Tool-Plätze
    
    // Tools >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
      $d = fixedColWidthTblFile2Array( file_get_contents( "../tmp/modDrvMachTNC530/".$this->_T_TOOLTTNAME ), 
                                        $doTrim = " ", $initialLineMustContain = "BEGIN", $lastLineMustContain = "END" );


      $vgl = Array( "T", "NAME", "L", "R", "R2", "DL", "DR", "DR2", "TL", "RT", "TIME2", "CUR.TIME", 
                    "DOC", "CUT.", "LTOL", "RTOL", "DIRECT.", "TT:L-OFFS", "TT:R-OFFS", "LBREAK",
                    "RBREAK", "LCUTS", "ANGLE", "TYP", "TMAT", "AFC", "CDT", "CAL-OF1", "CAL-OF2",
                    "CAL-ANG", "NMAX", "LIFTOFF", "KINEMATIC", "T-ANGLE", "PITCH" );

      $dC = COUNT( $d[ "data" ] );
      $MyQuery = "INSERT INTO prod_modDrvMachTNC530_Tools_tmp ( "
                . "       machId, tNumber, tName, tLength, tRadius, tR2, "
                . "       tDL, tDR, tDR2, tLocked, reserveTool, "
                . "       timeToRun, timeElapsed, tDescr, noCutEdges, tolLength, "
                . "       tolRadius, tDirection, ttOffsetLengt, ttOffsetRadius, lengthBreak, "
                . "       radiusBreak, tLCuts, tAngle, tTyp, tMaterial, "
                . "       tAFC, tCDT, tCalOf1, tCalOf2, tCalAng, "
                . "       nMax, liftOff, tKindematic, tAngle2, tPitch ) VALUES ";

      for( $i = 0 ; $i < $dC ; $i++ ){
        $ii = 0;
        if( $i > 0 ) $MyQuery .= ", ";
        $MyQuery .= "\n('".$MachId."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."',"
                  .  "   '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".($d["data"][ $i ][ $ii++ ] == "L" ? 1 : 0 )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."',"
                  .  "   '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."',"
                  .  "   '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."',"
                  .  "   '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."',"
                  .  "   '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."',"
                  .  "   '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."', '".addslashes( $d["data"][ $i ][ $ii++ ] )."' )";
      }
      $MyQuery .= ";";
      //mquery( "LOCK TABLES prod_modDrvMachTNC530_Tools_tmp WRITE;", false, true );
      mquery( "TRUNCATE prod_modDrvMachTNC530_Tools_tmp;", false, $_DEBUG_SQL );
      mquery( $MyQuery, false, $_DEBUG_SQL );
      mquery( "TRUNCATE prod_modDrvMachTNC530_Tools;", false, $_DEBUG_SQL );
      mquery( "INSERT INTO prod_modDrvMachTNC530_Tools SELECT * FROM prod_modDrvMachTNC530_Tools_tmp;", false, $_DEBUG_SQL );
    // <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Tools      


    
    $this->_syncToolTbl_cleanUp();
    return "OK";
  }
    function _syncToolTbl_cleanUp( $err = "" ){
      unlink( "../tmp/modDrvMachTNC530/".$this->_T_TOOLTTNAME );
      unlink( "../tmp/modDrvMachTNC530/".$this->_T_TOOLTPNAME );
      if( strlen( $err ) > 0 ) vUdpDebug( $err );
      return $err;
    }
  
  function tryRedoJob( $MJId ){
    return "ERR: Disabled!";
  }
  
  function setErr( $err ){
    vUdpDebug( "LSV2ERR: ".$err, "critical" ); 
    $this->_LAST_ERR = $err;
    return false;
  }
  
  function getLastErr(){
    return $this->_LAST_ERR;
  }
  
  /* Erzeugt ein Bildschirmfoto der Maschine und sperrt dieses.
   * Im Erfolgsfall wird der Pfad des Bildes, im Fehlerfall false zurück geliefert.
   */
   
  // Noch nicht verwendet, da erstmal direkt über die lsv2-lib angebunden
   
//   function getScreenDump_Lock(){
//     global $CFG;
//     $MachId = $this->_MACHID * 1;
//     $_M = mysql_rs0( "SELECT remoIp, mStateRunFile, mStateSelFile "
//                    . "  FROM ".$CFG["MySql"]["tblModMillHouse_Machines"]
//                    . " WHERE Id = '".$MachId."';" );    
//     dependsOn( "v7sHHLsv2" );
//     $shhl = new v7sHHLsv2( $_M["remoIp"] );
//     
//     if( ! $shhl->getDNCExecStatus() ) return false;
//     
//     if( ! is_dir( "../tmp/modDrvMachTNC530" ) ){
//       mkdir( "../tmp/modDrvMachTNC530", 0777 );
//     }
//     $fh = $shhl->getScreenDump( "../tmp/modDrvMachTNC530/scrdmp.".$MachId.".bmp", 
//                                 $lockFile = true )
//     if( ! $fh ) return false;
//     if( $this->_SCRDUMP_LOCK_FH ) fclose( $this->_SCRDUMP_LOCK_FH );
//     $this->_SCRDUMP_LOCK_FH = $fh;
//     return "../tmp/modDrvMachTNC530/scrdmp.".$MachId.".bmp";
//   }
  
  /* Hebt die Sperre auf ein erzeigtes Bildschirmfoto wieder auf
//    */
//   function unlockScreenDump(){
//     if( $this->_SCRDUMP_LOCK_FH ) fclose( $this->_SCRDUMP_LOCK_FH );
//     $this->_SCRDUMP_LOCK_FH = false;
//   }

// mStateLastRefresh
// mStateSelFile
// mStateRunFile
// mStateActPos

}


/* INFOS ....

NICHT GANZ RICHTIG: Wenn CurrentTime < LastCurrentTime -> Werkzeugwechsel passiert

TOOL_P: 0 = Spindel (aktuelles Werkzeug)

TOOL.T:

T       Toolnumber ( Ref. aus der onner )
NAME    Bezeichnung
L       Länge
R       Radius
R2      
DL
DR
DR2
TL        Tool-Lock (Gesperrt wenn L)
RT        Reserve-Tool (Schwesterwerkzeug)
TIME2     Time to run (Maximale "Lebensdauer")
CUR.TIME  Aktuell verbratene Zeit
DOC       Bemerkung
CUT.      Anzahl Schneiden
LTOL      Toleranz Länge
RTOL      Toleranz Radius
DIRECT.   
TT:L-OFFS
TT:R-OFFS
LBREAK
RBREAK
LCUTS
ANGLE
TYP
TMAT
AFC
CDT
CAL-OF1
CAL-OF2
CAL-ANG
NMAX
LIFTOFF
KINEMATIC
T-ANGLE
PITCH

*/




?>
