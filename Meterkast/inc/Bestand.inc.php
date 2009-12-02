<?php // generated with ADL vs. 0.8.10-408
  
  /********* on line 66, file "Meterkast.adl"
    SERVICE Bestand : I[Bestand]
   = [ path : path
     , session : session
     , compilations : object~
        = [ id : [Actie]
          , operatie : type
          ]
     ]
   *********/
  
  class Bestand {
    protected $_id=false;
    protected $_new=true;
    private $_path;
    private $_session;
    private $_compilations;
    function Bestand($id=null, $path=null, $session=null, $compilations=null){
      $this->_id=$id;
      $this->_path=$path;
      $this->_session=$session;
      $this->_compilations=$compilations;
      if(!isset($path) && isset($id)){
        // get a Bestand based on its identifier
        // check if it exists:
        $ctx = DB_doquer('SELECT DISTINCT fst.`AttBestand` AS `Id`
                           FROM 
                              ( SELECT DISTINCT Id AS `AttBestand`, Id
                                  FROM BestandTbl
                              ) AS fst
                          WHERE fst.`AttBestand` = \''.addSlashes($id).'\'');
        if(count($ctx)==0) $this->_new=true; else
        {
          $this->_new=false;
          // fill the attributes
          $me=firstRow(DB_doquer("SELECT DISTINCT `BestandTbl`.`path`
                                       , `SessieTbl`.`Id` AS `session`
                                       , '".addslashes($id)."' AS `id`
                                    FROM `BestandTbl`
                                    LEFT JOIN `SessieTbl` ON `SessieTbl`.`bestand`='".addslashes($id)."'
                                   WHERE `BestandTbl`.`Id`='".addslashes($id)."'"));
          $me['compilations']=(DB_doquer("SELECT DISTINCT `ActieTbl`.`Id` AS `id`
                                            FROM `ActieTbl`
                                           WHERE `ActieTbl`.`object`='".addslashes($id)."'"));
          foreach($me['compilations'] as $i0=>&$v0){
            $v0=firstRow(DB_doquer("SELECT DISTINCT '".addslashes($v0['id'])."' AS `id`
                                         , '".addslashes($v0['id'])."' AS `id`
                                         , `f3`.`type` AS `operatie`
                                      FROM `ActieTbl`
                                      LEFT JOIN `ActieTbl` AS f3 ON `f3`.`Id`='".addslashes($v0['id'])."'
                                     WHERE `ActieTbl`.`Id`='".addslashes($v0['id'])."'"));
          }
          unset($v0);
          $this->set_path($me['path']);
          $this->set_session($me['session']);
          $this->set_compilations($me['compilations']);
        }
      }
      else if(isset($id)){ // just check if it exists
        $ctx = DB_doquer('SELECT DISTINCT fst.`AttBestand` AS `Id`
                           FROM 
                              ( SELECT DISTINCT Id AS `AttBestand`, Id
                                  FROM BestandTbl
                              ) AS fst
                          WHERE fst.`AttBestand` = \''.addSlashes($id).'\'');
        $this->_new=(count($ctx)==0);
      }
    }

    function save(){
      DB_doquer('START TRANSACTION');
      /****************************************\
      * Attributes that will not be saved are: *
      * -------------------------------------- *
      * id                                     *
      \****************************************/
      $newID = ($this->getId()===false);
      $me=array("id"=>$this->getId(), "path" => $this->_path, "session" => $this->_session, "compilations" => $this->_compilations);
      // no code for operatie,Id in OperationTbl
      foreach($me['compilations'] as $i0=>$v0){
        if(isset($v0['id']))
          DB_doquer("UPDATE `ActieTbl` SET `type`='".addslashes($v0['operatie'])."' WHERE `Id`='".addslashes($v0['id'])."'", 5);
      }
      foreach  ($me['compilations'] as $compilations){
        if(isset($me['id']))
          DB_doquer("UPDATE `ActieTbl` SET `object`='".addslashes($me['id'])."' WHERE `Id`='".addslashes($compilations['id'])."'", 5);
      }
      DB_doquer("DELETE FROM `BestandTbl` WHERE `Id`='".addslashes($me['id'])."'",5);
      $res=DB_doquer("INSERT IGNORE INTO `BestandTbl` (`path`,`Id`) VALUES ('".addslashes($me['path'])."', ".(!$newID?"'".addslashes($me['id'])."'":"NULL").")", 5);
      if($newID) $this->setId($me['id']=mysql_insert_id());
      // no code for session,Id in SessieTbl
      if(isset($me['id'])) DB_doquer("UPDATE `SessieTbl` SET `bestand`=NULL WHERE `bestand`='".addslashes($me['id'])."'",5);
      if(isset($me['id']))
        DB_doquer("UPDATE `SessieTbl` SET `bestand`='".addslashes($me['id'])."' WHERE `Id`='".addslashes($me['session'])."'", 5);
      DB_doquer("DELETE FROM `text` WHERE `text`='".addslashes($me['path'])."'",5);
      $res=DB_doquer("INSERT IGNORE INTO `text` (`text`) VALUES ('".addslashes($me['path'])."')", 5);
      if($res!==false && !isset($me['path']['id']))
        $me['path']['id']=mysql_insert_id();
      if (!checkRule1()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule2()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule3()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule4()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule5()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule6()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule7()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule8()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule9()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule10()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule11()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule12()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule13()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule14()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule15()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule16()){
        $DB_err=$preErr.'\"\"';
      } else
      if(true){ // all rules are met
        DB_doquer('COMMIT');
        return $this->getId();
      }
      DB_doquer('ROLLBACK');
      return false;
    }
    function del(){
      DB_doquer('START TRANSACTION');
      $me=array("id"=>$this->getId(), "path" => $this->_path, "session" => $this->_session, "compilations" => $this->_compilations);
      DB_doquer("DELETE FROM `BestandTbl` WHERE `Id`='".addslashes($me['id'])."'",5);
      DB_doquer("DELETE FROM `text` WHERE `text`='".addslashes($me['path'])."'",5);
      if (!checkRule1()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule2()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule3()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule4()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule5()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule6()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule7()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule8()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule9()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule10()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule11()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule12()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule13()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule14()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule15()){
        $DB_err=$preErr.'\"\"';
      } else
      if (!checkRule16()){
        $DB_err=$preErr.'\"\"';
      } else
      if(true){ // all rules are met
        DB_doquer('COMMIT');
        return true;
      }
      DB_doquer('ROLLBACK');
      return false;
    }
    function set_path($val){
      $this->_path=$val;
    }
    function get_path(){
      return $this->_path;
    }
    function set_session($val){
      $this->_session=$val;
    }
    function get_session(){
      return $this->_session;
    }
    function set_compilations($val){
      $this->_compilations=$val;
    }
    function get_compilations(){
      return $this->_compilations;
    }
    function setId($id){
      $this->_id=$id;
      return $this->_id;
    }
    function getId(){
      if($this->_id==null) return false;
      return $this->_id;
    }
    function isNew(){
      return $this->_new;
    }
  }

  function getEachBestand(){
    return firstCol(DB_doquer('SELECT DISTINCT Id
                                 FROM BestandTbl'));
  }

  function readBestand($id){
      // check existence of $id
      $obj = new Bestand($id);
      if($obj->isNew()) return false; else return $obj;
  }

  function delBestand($id){
    $tobeDeleted = new Bestand($id);
    if($tobeDeleted->isNew()) return true; // item never existed in the first place
    if($tobeDeleted->del()) return true; else return $tobeDeleted;
  }

?>