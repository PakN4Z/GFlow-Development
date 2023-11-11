<?php

/*
 * Standard Maschinenteriber
 */

class millMachDrvTmpl{
  
  var $_HASMUPLOAD = false;
  var $_HASNCSTART = false;
  var $_HASNCSTATUS = false;
  var $_HASAUTOMODE = false;
  var $_HASOPTSLEEP = false;
  var $_HAS_USEROPT_RESTARTJOB = false;
  var $_HASOPTAUTOSLEEP = false;


  function millMachDrvTmpl(){
  
  }
  
  function getMachInfoIPane(){ return ""; }
  
  
  function getMachInfoURi(){ return ""; }
  function getMachScrDumpURi(){ return ""; }
  
  function setAutoMode( $w ){}
  
  function hasMUpload(){   return $this->_HASMUPLOAD;  }
  function hasNCStart(){   return $this->_HASNCSTART;  }
  function hasNCStatus(){  return $this->_HASNCSTATUS; }
  function hasWPC(){ return $this->_HASWPC; }
  
  function hasAutoMode(){ return $this->_HASAUTOMODE; }
  function hasOptSleep(){ return $this->_HASOPTSLEEP; }
  function hasOptAutoStandBy(){ return $this->_HASOPTAUTOSLEEP; }
  function setAutoStandBy( $w = true ){  }
  function getAutoStandBy(){ return false; }

  
  function tryStartUpload( $MJId ){
    return $this->setErr( "tryStartUpload( $"."MJId ) nicht implementiert" );;
  }
  
  function tryStartMillJob( $MJId ){
    return $this->setErr( "tryStartMillJob( $"."MJId ) nicht implementiert" );;
  }
  
  function _syncToolTbl(){
    vUdpDebug( "_syncToolTbl nicht verfügbar" );
  }

  function tryRedoJob( $MJId, $initialStateStr = "", $ignoreState = false ){
    $MJId = cId( $MJId );
    mquery( "LOCK TABLES ".v7()->tbl("tblModMillHouse_MillJobs")." WRITE;" );
    $MJ = mysql_rs0( "SELECT * FROM ".v7()->tbl("tblModMillHouse_MillJobs")." WHERE Id = '".$MJId."';" );
    if( $MJ["stateStr"] != "runnc" || $ignoreState == true ){
      mquery( "UPDATE ".v7()->tbl("tblModMillHouse_MillJobs")
            . "   SET stateStr = '".$initialStateStr."', "
            . "       statePrgr = 0, "
            . "       jMsg = '', jStarted = '', jDone = 0, jDoneDT = '', jDur = 0, "
            . "       LastMod = '', Editor = 0 WHERE Id = '".$MJId."';" );
      mquery( "UNLOCK TABLES;" );
      return "ok";
    }else{
      mquery( "UNLOCK TABLES;" );
      return "ERR: Job wird scheinbar noch abgearbeitet. Bitte an der Maschine entpannen.";
    }
    mquery( "UNLOCK TABLES;" );
    return "ERR: Disabled (".$MJId.")!";
    
  }
  
  /* millJobs sollte gesperrt sein .. selbstredend
   */
  function getNextJobId(){
    $Id = cId( mysql_singlers( "SELECT MIN(Id) "
                             . "  FROM ".v7()->tbl("tblModMillHouse_MillJobs")
                             . " WHERE machId = '".cId( $this->_MACHID )."' "
                             . "   AND jDone != 1 "
                             . "   AND stateStr = 'uploaded' "
                             . "   AND DATEDIFF( NOW(), Created ) < 14;", false, true ) );
    //echo "nextJob: ".$Id;
    return $Id;
  }
  function setCurJobId( $Id ){
    
    mquery( "UPDATE ".v7()->tbl( "tblModMillHouse_Machines" )
          . "   SET mCurJobId = '".cId( $Id )."'"
          . " WHERE Id = '".cId( $this->_MACHID )."';" );
          
  }

}

?>