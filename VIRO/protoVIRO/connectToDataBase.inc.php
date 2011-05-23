<?php // generated with ADL vs. 0.8.10-452
  require "dbsettings.php";
  
  function stripslashes_deep(&$value) 
  { $value = is_array($value) ? 
             array_map('stripslashes_deep', $value) : 
             stripslashes($value); 
      return $value; 
  } 
  if((function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) 
      || (ini_get('magic_quotes_sybase') && (strtolower(ini_get('magic_quotes_sybase'))!="off")) ){ 
      stripslashes_deep($_GET); 
      stripslashes_deep($_POST); 
      stripslashes_deep($_REQUEST); 
      stripslashes_deep($_COOKIE); 
  } 
  $DB_slct = mysql_select_db('VIRO453ENG',$DB_link);
  function firstRow($rows){ return $rows[0]; }
  function firstCol($rows){ foreach ($rows as $i=>&$v) $v=$v[0]; return $rows; }
  function DB_debug($txt,$lvl=0){
    global $DB_debug;
    if($lvl<=$DB_debug) {
      echo "<i title=\"debug level $lvl\">$txt</i>\n<P />\n";
      return true;
    }else return false;
  }
  
  $DB_errs = array();
  // wrapper function for MySQL
  function DB_doquer($quer,$debug=5)
  {
    global $DB_link,$DB_errs;
    DB_debug($quer,$debug);
    $result=mysql_query($quer,$DB_link);
    if(!$result){
      DB_debug('Error '.($ernr=mysql_errno($DB_link)).' in query "'.$quer.'": '.mysql_error(),2);
      $DB_errs[]='Error '.($ernr=mysql_errno($DB_link)).' in query "'.$quer.'"';
      return false;
    }
    if($result===true) return true; // succes.. but no contents..
    $rows=Array();
    while (($row = @mysql_fetch_array($result))!==false) {
      $rows[]=$row;
      unset($row);
    }
    return $rows;
  }
  function DB_plainquer($quer,&$errno,$debug=5)
  {
    global $DB_link,$DB_errs,$DB_lastquer;
    $DB_lastquer=$quer;
    DB_debug($quer,$debug);
    $result=mysql_query($quer,$DB_link);
    if(!$result){
      $errno=mysql_errno($DB_link);
      return false;
    }else{
      if(($p=stripos($quer,'INSERT'))!==false
         && (($q=stripos($quer,'UPDATE'))==false || $p<$q)
         && (($q=stripos($quer,'DELETE'))==false || $p<$q)
        )
      {
        return mysql_insert_id();
      } else return mysql_affected_rows();
    }
  }
  
  
  function checkRule1(){
    // No violations should occur in (I |- plaintiff~;plaintiff)
    //            rule':: I/\-(plaintiff~;plaintiff)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `case` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`case`, F1.`case` AS `case1`
                                        FROM `plaintiff` AS F0, `plaintiff` AS F1
                                       WHERE F0.`party`=F1.`party`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`case` AND isect0.`i`=cp.`case1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Case '.$v[0][0].',Case '.$v[0][1].')
reden: \"Voor elke procedure moet er tenminste een eisende partij zijn.\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule2(){
    // No violations should occur in (clerk |- location;clerk)
    //            rule':: clerk/\-(location;clerk)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`clerk`
                     FROM `session` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`Party`
                                        FROM `session` AS F0, `clerk` AS F1
                                       WHERE F0.`location`=F1.`court`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`clerk`=cp.`Party`) AND isect0.`i` IS NOT NULL AND isect0.`clerk` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Session '.$v[0][0].',Party '.$v[0][1].')
reden: \"De clerk in een case moet benoemd zijn bij de rechtbank waar deze case dient.\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule3(){
    // No violations should occur in (occured |- scheduled)
    //            rule':: occured/\-scheduled
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`occured`
                     FROM `session` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM `session` AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`occured`=cp.`scheduled`) AND isect0.`i` IS NOT NULL AND isect0.`occured` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Session '.$v[0][0].',Date '.$v[0][1].')
reden: \"Alle sessionen worden scheduled\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule4(){
    // No violations should occur in (city~;location |- mainCity~\/localCities)
    //            rule':: city~;location/\-mainCity~/\-localCities
    // sqlExprSrc fSpec rule':: city
     $v=DB_doquer('SELECT DISTINCT isect0.`city`, isect0.`location`
                     FROM 
                        ( SELECT DISTINCT F0.`city`, F1.`location`
                            FROM `session` AS F0, `session` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM `court` AS cp
                                WHERE isect0.`city`=cp.`maincity` AND isect0.`location`=cp.`i`) AND NOT EXISTS (SELECT *
                                 FROM `city` AS cp
                                WHERE isect0.`city`=cp.`i` AND isect0.`location`=cp.`localcities`) AND isect0.`city` IS NOT NULL AND isect0.`location` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (City '.$v[0][0].',Court '.$v[0][1].')
reden: \"Elke session vindt city in de hoofdvestigingscity van een court of een van de localCitiesvestigingscityen (tekst checken, Article 47 lid 2 RO)\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule5(){
    // No violations should occur in (case~;session;location |- authorized)
    //            rule':: case~;session;location/\-authorized
    // sqlExprSrc fSpec rule':: case
     $v=DB_doquer('SELECT DISTINCT isect0.`case`, isect0.`location`
                     FROM 
                        ( SELECT DISTINCT F0.`case`, F2.`location`
                            FROM `process` AS F0, `process` AS F1, `session` AS F2
                           WHERE F0.`i`=F1.`i`
                             AND F1.`session`=F2.`i`
                        ) AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM `authorized` AS cp
                                WHERE isect0.`case`=cp.`case` AND isect0.`location`=cp.`Court`) AND isect0.`case` IS NOT NULL AND isect0.`location` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Case '.$v[0][0].',Court '.$v[0][1].')
reden: \"An appeal lodged against a decision of an administrative authority of a province or municipality, or a water management board, or a region as referred to in article 21 of the 1993 Police Act, or of a joint body or public body established under the Joint Arrangements Act, falls within the jurisdiction of the district court within whose district the administrative authority has its seat. (art. 8:7 par.1 Awb.)\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule6(){
    // No violations should occur in (judge |- panel;members~)
    //            rule':: judge/\-(panel;members~)
    // sqlExprSrc fSpec rule':: session
     $v=DB_doquer('SELECT DISTINCT isect0.`session`, isect0.`party`
                     FROM `judge` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`party`
                                        FROM `session` AS F0, `members` AS F1
                                       WHERE F0.`panel`=F1.`Panel`
                                    ) AS cp
                                WHERE isect0.`session`=cp.`i` AND isect0.`party`=cp.`party`) AND isect0.`session` IS NOT NULL AND isect0.`party` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Session '.$v[0][0].',Party '.$v[0][1].')
reden: \"De judge ter session maakt deel uit from de members from de panel die de session houdt\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule7(){
    // No violations should occur in (actionType;act~;organ |- as)
    //            rule':: actionType;act~;organ/\-as
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`Organ`
                     FROM 
                        ( SELECT DISTINCT F0.`i`, F2.`Organ`
                            FROM `action` AS F0, `actarticle` AS F1, `organarticle` AS F2
                           WHERE F0.`actiontype`=F1.`Act`
                             AND F1.`article`=F2.`article`
                        ) AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM `as` AS cp
                                WHERE isect0.`i`=cp.`action` AND isect0.`Organ`=cp.`Organ`) AND isect0.`i` IS NOT NULL AND isect0.`Organ` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Action '.$v[0][0].',Organ '.$v[0][1].')
reden: \"De persoon die een actie uitvoert doet dat as vertegenwoordiger from het organ dat de act uitvoert\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule8(){
    // No violations should occur in (documentType~;documentType |- I)
    //            rule':: documentType~;documentType/\-I
    // sqlExprSrc fSpec rule':: documenttype
     $v=DB_doquer('SELECT DISTINCT isect0.`documenttype`, isect0.`documenttype1`
                     FROM 
                        ( SELECT DISTINCT F0.`documenttype`, F1.`documenttype` AS `documenttype1`
                            FROM `document` AS F0, `document` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`documenttype` <> isect0.`documenttype1` AND isect0.`documenttype` IS NOT NULL AND isect0.`documenttype1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (DocumentType '.$v[0][0].',DocumentType '.$v[0][1].')
reden: \"Artificial explanation: documentType~;documentType |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule9(){
    // No violations should occur in (I |- documentType;documentType~)
    //            rule':: I/\-(documentType;documentType~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `document` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `document` AS F0, `document` AS F1
                                       WHERE F0.`documenttype`=F1.`documenttype`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Document '.$v[0][0].',Document '.$v[0][1].')
reden: \"Artificial explanation: I |- documentType;documentType~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule10(){
    // No violations should occur in (caretaker~;caretaker |- I)
    //            rule':: caretaker~;caretaker/\-I
    // sqlExprSrc fSpec rule':: caretaker
     $v=DB_doquer('SELECT DISTINCT isect0.`caretaker`, isect0.`caretaker1`
                     FROM 
                        ( SELECT DISTINCT F0.`caretaker`, F1.`caretaker` AS `caretaker1`
                            FROM `case` AS F0, `case` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`caretaker` <> isect0.`caretaker1` AND isect0.`caretaker` IS NOT NULL AND isect0.`caretaker1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Organ '.$v[0][0].',Organ '.$v[0][1].')
reden: \"Artificial explanation: caretaker~;caretaker |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule11(){
    // No violations should occur in (I |- caretaker;caretaker~)
    //            rule':: I/\-(caretaker;caretaker~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `case` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `case` AS F0, `case` AS F1
                                       WHERE F0.`caretaker`=F1.`caretaker`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Case '.$v[0][0].',Case '.$v[0][1].')
reden: \"Artificial explanation: I |- caretaker;caretaker~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule12(){
    // No violations should occur in (I |- plaintiff~;plaintiff)
    //            rule':: I/\-(plaintiff~;plaintiff)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `case` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`case`, F1.`case` AS `case1`
                                        FROM `plaintiff` AS F0, `plaintiff` AS F1
                                       WHERE F0.`party`=F1.`party`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`case` AND isect0.`i`=cp.`case1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Case '.$v[0][0].',Case '.$v[0][1].')
reden: \"Artificial explanation: I |- plaintiff~;plaintiff\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule13(){
    // No violations should occur in (I |- authorizedRepresentative~;authorizedRepresentative)
    //            rule':: I/\-(authorizedRepresentative~;authorizedRepresentative)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `authorization` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`authorization`, F1.`authorization` AS `authorization1`
                                        FROM `authorizedrepresentative` AS F0, `authorizedrepresentative` AS F1
                                       WHERE F0.`party`=F1.`party`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`authorization` AND isect0.`i`=cp.`authorization1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Authorization '.$v[0][0].',Authorization '.$v[0][1].')
reden: \"Artificial explanation: I |- authorizedRepresentative~;authorizedRepresentative\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule14(){
    // No violations should occur in (by~;by |- I)
    //            rule':: by~;by/\-I
    // sqlExprSrc fSpec rule':: by
     $v=DB_doquer('SELECT DISTINCT isect0.`by`, isect0.`by1`
                     FROM 
                        ( SELECT DISTINCT F0.`by`, F1.`by` AS `by1`
                            FROM `authorization` AS F0, `authorization` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`by` <> isect0.`by1` AND isect0.`by` IS NOT NULL AND isect0.`by1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Party '.$v[0][0].',Party '.$v[0][1].')
reden: \"Artificial explanation: by~;by |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule15(){
    // No violations should occur in (I |- by;by~)
    //            rule':: I/\-(by;by~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `authorization` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `authorization` AS F0, `authorization` AS F1
                                       WHERE F0.`by`=F1.`by`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Authorization '.$v[0][0].',Authorization '.$v[0][1].')
reden: \"Artificial explanation: I |- by;by~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule16(){
    // No violations should occur in (areaOfLaw~;areaOfLaw |- I)
    //            rule':: areaOfLaw~;areaOfLaw/\-I
    // sqlExprSrc fSpec rule':: areaoflaw
     $v=DB_doquer('SELECT DISTINCT isect0.`areaoflaw`, isect0.`areaoflaw1`
                     FROM 
                        ( SELECT DISTINCT F0.`areaoflaw`, F1.`areaoflaw` AS `areaoflaw1`
                            FROM `case` AS F0, `case` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`areaoflaw` <> isect0.`areaoflaw1` AND isect0.`areaoflaw` IS NOT NULL AND isect0.`areaoflaw1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (AreaOfLaw '.$v[0][0].',AreaOfLaw '.$v[0][1].')
reden: \"Artificial explanation: areaOfLaw~;areaOfLaw |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule17(){
    // No violations should occur in (I |- areaOfLaw;areaOfLaw~)
    //            rule':: I/\-(areaOfLaw;areaOfLaw~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `case` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `case` AS F0, `case` AS F1
                                       WHERE F0.`areaoflaw`=F1.`areaoflaw`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Case '.$v[0][0].',Case '.$v[0][1].')
reden: \"Artificial explanation: I |- areaOfLaw;areaOfLaw~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule18(){
    // No violations should occur in (caseType~;caseType |- I)
    //            rule':: caseType~;caseType/\-I
    // sqlExprSrc fSpec rule':: casetype
     $v=DB_doquer('SELECT DISTINCT isect0.`casetype`, isect0.`casetype1`
                     FROM 
                        ( SELECT DISTINCT F0.`casetype`, F1.`casetype` AS `casetype1`
                            FROM `case` AS F0, `case` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`casetype` <> isect0.`casetype1` AND isect0.`casetype` IS NOT NULL AND isect0.`casetype1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (CaseType '.$v[0][0].',CaseType '.$v[0][1].')
reden: \"Artificial explanation: caseType~;caseType |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule19(){
    // No violations should occur in (I |- caseType;caseType~)
    //            rule':: I/\-(caseType;caseType~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `case` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `case` AS F0, `case` AS F1
                                       WHERE F0.`casetype`=F1.`casetype`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Case '.$v[0][0].',Case '.$v[0][1].')
reden: \"Artificial explanation: I |- caseType;caseType~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule20(){
    // No violations should occur in (I |- base;base~)
    //            rule':: I/\-(base;base~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `cluster` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`cluster`, F1.`cluster` AS `cluster1`
                                        FROM `base` AS F0, `base` AS F1
                                       WHERE F0.`text`=F1.`text`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`cluster` AND isect0.`i`=cp.`cluster1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Cluster '.$v[0][0].',Cluster '.$v[0][1].')
reden: \"Artificial explanation: I |- base;base~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule21(){
    // No violations should occur in (name~;name |- I)
    //            rule':: name~;name/\-I
    // sqlExprSrc fSpec rule':: name
     $v=DB_doquer('SELECT DISTINCT isect0.`name`, isect0.`name1`
                     FROM 
                        ( SELECT DISTINCT F0.`name`, F1.`name` AS `name1`
                            FROM `cluster` AS F0, `cluster` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`name` <> isect0.`name1` AND isect0.`name` IS NOT NULL AND isect0.`name1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Text '.$v[0][0].',Text '.$v[0][1].')
reden: \"Artificial explanation: name~;name |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule22(){
    // No violations should occur in (I |- name;name~)
    //            rule':: I/\-(name;name~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `cluster` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `cluster` AS F0, `cluster` AS F1
                                       WHERE F0.`name`=F1.`name`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Cluster '.$v[0][0].',Cluster '.$v[0][1].')
reden: \"Artificial explanation: I |- name;name~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule23(){
    // No violations should occur in (session~;session |- I)
    //            rule':: session~;session/\-I
    // sqlExprSrc fSpec rule':: session
     $v=DB_doquer('SELECT DISTINCT isect0.`session`, isect0.`session1`
                     FROM 
                        ( SELECT DISTINCT F0.`session`, F1.`session` AS `session1`
                            FROM `process` AS F0, `process` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`session` <> isect0.`session1` AND isect0.`session` IS NOT NULL AND isect0.`session1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Session '.$v[0][0].',Session '.$v[0][1].')
reden: \"Artificial explanation: session~;session |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule24(){
    // No violations should occur in (I |- session;session~)
    //            rule':: I/\-(session;session~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `process` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `process` AS F0, `process` AS F1
                                       WHERE F0.`session`=F1.`session`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Process '.$v[0][0].',Process '.$v[0][1].')
reden: \"Artificial explanation: I |- session;session~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule25(){
    // No violations should occur in (case~;case |- I)
    //            rule':: case~;case/\-I
    // sqlExprSrc fSpec rule':: case
     $v=DB_doquer('SELECT DISTINCT isect0.`case`, isect0.`case1`
                     FROM 
                        ( SELECT DISTINCT F0.`case`, F1.`case` AS `case1`
                            FROM `process` AS F0, `process` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`case` <> isect0.`case1` AND isect0.`case` IS NOT NULL AND isect0.`case1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Case '.$v[0][0].',Case '.$v[0][1].')
reden: \"Artificial explanation: case~;case |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule26(){
    // No violations should occur in (I |- case;case~)
    //            rule':: I/\-(case;case~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `process` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `process` AS F0, `process` AS F1
                                       WHERE F0.`case`=F1.`case`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Process '.$v[0][0].',Process '.$v[0][1].')
reden: \"Artificial explanation: I |- case;case~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule27(){
    // No violations should occur in (I |- judge;judge~)
    //            rule':: I/\-(judge;judge~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `session` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`session`, F1.`session` AS `session1`
                                        FROM `judge` AS F0, `judge` AS F1
                                       WHERE F0.`party`=F1.`party`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`session` AND isect0.`i`=cp.`session1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Session '.$v[0][0].',Session '.$v[0][1].')
reden: \"Artificial explanation: I |- judge;judge~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule28(){
    // No violations should occur in (I |- clerk;clerk~)
    //            rule':: I/\-(clerk;clerk~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `session` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `session` AS F0, `session` AS F1
                                       WHERE F0.`clerk`=F1.`clerk`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Session '.$v[0][0].',Session '.$v[0][1].')
reden: \"Artificial explanation: I |- clerk;clerk~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule29(){
    // No violations should occur in (clerk~;clerk |- I)
    //            rule':: clerk~;clerk/\-I
    // sqlExprSrc fSpec rule':: clerk
     $v=DB_doquer('SELECT DISTINCT isect0.`clerk`, isect0.`clerk1`
                     FROM 
                        ( SELECT DISTINCT F0.`clerk`, F1.`clerk` AS `clerk1`
                            FROM `session` AS F0, `session` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`clerk` <> isect0.`clerk1` AND isect0.`clerk` IS NOT NULL AND isect0.`clerk1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Party '.$v[0][0].',Party '.$v[0][1].')
reden: \"Artificial explanation: clerk~;clerk |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule30(){
    // No violations should occur in (scheduled~;scheduled |- I)
    //            rule':: scheduled~;scheduled/\-I
    // sqlExprSrc fSpec rule':: scheduled
     $v=DB_doquer('SELECT DISTINCT isect0.`scheduled`, isect0.`scheduled1`
                     FROM 
                        ( SELECT DISTINCT F0.`scheduled`, F1.`scheduled` AS `scheduled1`
                            FROM `session` AS F0, `session` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`scheduled` <> isect0.`scheduled1` AND isect0.`scheduled` IS NOT NULL AND isect0.`scheduled1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Date '.$v[0][0].',Date '.$v[0][1].')
reden: \"Artificial explanation: scheduled~;scheduled |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule31(){
    // No violations should occur in (I |- scheduled;scheduled~)
    //            rule':: I/\-(scheduled;scheduled~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `session` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `session` AS F0, `session` AS F1
                                       WHERE F0.`scheduled`=F1.`scheduled`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Session '.$v[0][0].',Session '.$v[0][1].')
reden: \"Artificial explanation: I |- scheduled;scheduled~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule32(){
    // No violations should occur in (occured~;occured |- I)
    //            rule':: occured~;occured/\-I
    // sqlExprSrc fSpec rule':: occured
     $v=DB_doquer('SELECT DISTINCT isect0.`occured`, isect0.`occured1`
                     FROM 
                        ( SELECT DISTINCT F0.`occured`, F1.`occured` AS `occured1`
                            FROM `session` AS F0, `session` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`occured` <> isect0.`occured1` AND isect0.`occured` IS NOT NULL AND isect0.`occured1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Date '.$v[0][0].',Date '.$v[0][1].')
reden: \"Artificial explanation: occured~;occured |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule33(){
    // No violations should occur in (city~;city |- I)
    //            rule':: city~;city/\-I
    // sqlExprSrc fSpec rule':: city
     $v=DB_doquer('SELECT DISTINCT isect0.`city`, isect0.`city1`
                     FROM 
                        ( SELECT DISTINCT F0.`city`, F1.`city` AS `city1`
                            FROM `session` AS F0, `session` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`city` <> isect0.`city1` AND isect0.`city` IS NOT NULL AND isect0.`city1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (City '.$v[0][0].',City '.$v[0][1].')
reden: \"Artificial explanation: city~;city |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule34(){
    // No violations should occur in (I |- city;city~)
    //            rule':: I/\-(city;city~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `session` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `session` AS F0, `session` AS F1
                                       WHERE F0.`city`=F1.`city`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Session '.$v[0][0].',Session '.$v[0][1].')
reden: \"Artificial explanation: I |- city;city~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule35(){
    // No violations should occur in (location~;location |- I)
    //            rule':: location~;location/\-I
    // sqlExprSrc fSpec rule':: location
     $v=DB_doquer('SELECT DISTINCT isect0.`location`, isect0.`location1`
                     FROM 
                        ( SELECT DISTINCT F0.`location`, F1.`location` AS `location1`
                            FROM `session` AS F0, `session` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`location` <> isect0.`location1` AND isect0.`location` IS NOT NULL AND isect0.`location1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Court '.$v[0][0].',Court '.$v[0][1].')
reden: \"Artificial explanation: location~;location |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule36(){
    // No violations should occur in (I |- location;location~)
    //            rule':: I/\-(location;location~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `session` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `session` AS F0, `session` AS F1
                                       WHERE F0.`location`=F1.`location`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Session '.$v[0][0].',Session '.$v[0][1].')
reden: \"Artificial explanation: I |- location;location~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule37(){
    // No violations should occur in (court~;court |- I)
    //            rule':: court~;court/\-I
    // sqlExprSrc fSpec rule':: court
     $v=DB_doquer('SELECT DISTINCT isect0.`court`, isect0.`court1`
                     FROM 
                        ( SELECT DISTINCT F0.`court`, F1.`court` AS `court1`
                            FROM `panel` AS F0, `panel` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`court` <> isect0.`court1` AND isect0.`court` IS NOT NULL AND isect0.`court1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Court '.$v[0][0].',Court '.$v[0][1].')
reden: \"Artificial explanation: court~;court |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule38(){
    // No violations should occur in (I |- court;court~)
    //            rule':: I/\-(court;court~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `panel` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `panel` AS F0, `panel` AS F1
                                       WHERE F0.`court`=F1.`court`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Panel '.$v[0][0].',Panel '.$v[0][1].')
reden: \"Artificial explanation: I |- court;court~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule39(){
    // No violations should occur in (sector~;sector |- I)
    //            rule':: sector~;sector/\-I
    // sqlExprSrc fSpec rule':: sector
     $v=DB_doquer('SELECT DISTINCT isect0.`sector`, isect0.`sector1`
                     FROM 
                        ( SELECT DISTINCT F0.`sector`, F1.`sector` AS `sector1`
                            FROM `panel` AS F0, `panel` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`sector` <> isect0.`sector1` AND isect0.`sector` IS NOT NULL AND isect0.`sector1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Sector '.$v[0][0].',Sector '.$v[0][1].')
reden: \"Artificial explanation: sector~;sector |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule40(){
    // No violations should occur in (I |- sector;sector~)
    //            rule':: I/\-(sector;sector~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `panel` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `panel` AS F0, `panel` AS F1
                                       WHERE F0.`sector`=F1.`sector`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Panel '.$v[0][0].',Panel '.$v[0][1].')
reden: \"Artificial explanation: I |- sector;sector~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule41(){
    // No violations should occur in (panel~;panel |- I)
    //            rule':: panel~;panel/\-I
    // sqlExprSrc fSpec rule':: panel
     $v=DB_doquer('SELECT DISTINCT isect0.`panel`, isect0.`panel1`
                     FROM 
                        ( SELECT DISTINCT F0.`panel`, F1.`panel` AS `panel1`
                            FROM `session` AS F0, `session` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`panel` <> isect0.`panel1` AND isect0.`panel` IS NOT NULL AND isect0.`panel1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Panel '.$v[0][0].',Panel '.$v[0][1].')
reden: \"Artificial explanation: panel~;panel |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule42(){
    // No violations should occur in (I |- panel;panel~)
    //            rule':: I/\-(panel;panel~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `session` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `session` AS F0, `session` AS F1
                                       WHERE F0.`panel`=F1.`panel`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Session '.$v[0][0].',Session '.$v[0][1].')
reden: \"Artificial explanation: I |- panel;panel~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule43(){
    // No violations should occur in (district~;district |- I)
    //            rule':: district~;district/\-I
    // sqlExprSrc fSpec rule':: district
     $v=DB_doquer('SELECT DISTINCT isect0.`district`, isect0.`district1`
                     FROM 
                        ( SELECT DISTINCT F0.`district`, F1.`district` AS `district1`
                            FROM `court` AS F0, `court` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`district` <> isect0.`district1` AND isect0.`district` IS NOT NULL AND isect0.`district1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (CourtOfAppeal '.$v[0][0].',CourtOfAppeal '.$v[0][1].')
reden: \"Artificial explanation: district~;district |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule44(){
    // No violations should occur in (I |- district;district~)
    //            rule':: I/\-(district;district~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `court` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `court` AS F0, `court` AS F1
                                       WHERE F0.`district`=F1.`district`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Court '.$v[0][0].',Court '.$v[0][1].')
reden: \"Artificial explanation: I |- district;district~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule45(){
    // No violations should occur in (mainCity~;mainCity |- I)
    //            rule':: mainCity~;mainCity/\-I
    // sqlExprSrc fSpec rule':: maincity
     $v=DB_doquer('SELECT DISTINCT isect0.`maincity`, isect0.`maincity1`
                     FROM 
                        ( SELECT DISTINCT F0.`maincity`, F1.`maincity` AS `maincity1`
                            FROM `court` AS F0, `court` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`maincity` <> isect0.`maincity1` AND isect0.`maincity` IS NOT NULL AND isect0.`maincity1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (City '.$v[0][0].',City '.$v[0][1].')
reden: \"Artificial explanation: mainCity~;mainCity |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule46(){
    // No violations should occur in (I |- mainCity;mainCity~)
    //            rule':: I/\-(mainCity;mainCity~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `court` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `court` AS F0, `court` AS F1
                                       WHERE F0.`maincity`=F1.`maincity`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Court '.$v[0][0].',Court '.$v[0][1].')
reden: \"Artificial explanation: I |- mainCity;mainCity~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule47(){
    // No violations should occur in (localCities~;localCities |- I)
    //            rule':: localCities~;localCities/\-I
    // sqlExprSrc fSpec rule':: localcities
     $v=DB_doquer('SELECT DISTINCT isect0.`localcities`, isect0.`localcities1`
                     FROM 
                        ( SELECT DISTINCT F0.`localcities`, F1.`localcities` AS `localcities1`
                            FROM `city` AS F0, `city` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`localcities` <> isect0.`localcities1` AND isect0.`localcities` IS NOT NULL AND isect0.`localcities1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Court '.$v[0][0].',Court '.$v[0][1].')
reden: \"Artificial explanation: localCities~;localCities |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule48(){
    // No violations should occur in (subject~;subject |- I)
    //            rule':: subject~;subject/\-I
    // sqlExprSrc fSpec rule':: subject
     $v=DB_doquer('SELECT DISTINCT isect0.`subject`, isect0.`subject1`
                     FROM 
                        ( SELECT DISTINCT F0.`subject`, F1.`subject` AS `subject1`
                            FROM `action` AS F0, `action` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`subject` <> isect0.`subject1` AND isect0.`subject` IS NOT NULL AND isect0.`subject1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Party '.$v[0][0].',Party '.$v[0][1].')
reden: \"Artificial explanation: subject~;subject |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule49(){
    // No violations should occur in (I |- subject;subject~)
    //            rule':: I/\-(subject;subject~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `action` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `action` AS F0, `action` AS F1
                                       WHERE F0.`subject`=F1.`subject`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Action '.$v[0][0].',Action '.$v[0][1].')
reden: \"Artificial explanation: I |- subject;subject~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule50(){
    // No violations should occur in (actionType~;actionType |- I)
    //            rule':: actionType~;actionType/\-I
    // sqlExprSrc fSpec rule':: actiontype
     $v=DB_doquer('SELECT DISTINCT isect0.`actiontype`, isect0.`actiontype1`
                     FROM 
                        ( SELECT DISTINCT F0.`actiontype`, F1.`actiontype` AS `actiontype1`
                            FROM `action` AS F0, `action` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`actiontype` <> isect0.`actiontype1` AND isect0.`actiontype` IS NOT NULL AND isect0.`actiontype1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Act '.$v[0][0].',Act '.$v[0][1].')
reden: \"Artificial explanation: actionType~;actionType |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule51(){
    // No violations should occur in (I |- actionType;actionType~)
    //            rule':: I/\-(actionType;actionType~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `action` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `action` AS F0, `action` AS F1
                                       WHERE F0.`actiontype`=F1.`actiontype`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Action '.$v[0][0].',Action '.$v[0][1].')
reden: \"Artificial explanation: I |- actionType;actionType~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule52(){
    // No violations should occur in (prio~;prio |- I)
    //            rule':: prio~;prio/\-I
    // sqlExprSrc fSpec rule':: prio
     $v=DB_doquer('SELECT DISTINCT isect0.`prio`, isect0.`prio1`
                     FROM 
                        ( SELECT DISTINCT F0.`prio`, F1.`prio` AS `prio1`
                            FROM `usecase` AS F0, `usecase` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`prio` <> isect0.`prio1` AND isect0.`prio` IS NOT NULL AND isect0.`prio1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Moscow '.$v[0][0].',Moscow '.$v[0][1].')
reden: \"Artificial explanation: prio~;prio |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule53(){
    // No violations should occur in (from~;from |- I)
    //            rule':: from~;from/\-I
    // sqlExprSrc fSpec rule':: from
     $v=DB_doquer('SELECT DISTINCT isect0.`from`, isect0.`from1`
                     FROM 
                        ( SELECT DISTINCT F0.`from`, F1.`from` AS `from1`
                            FROM `document` AS F0, `document` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`from` <> isect0.`from1` AND isect0.`from` IS NOT NULL AND isect0.`from1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Party '.$v[0][0].',Party '.$v[0][1].')
reden: \"Artificial explanation: from~;from |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule54(){
    // No violations should occur in (I |- from;from~)
    //            rule':: I/\-(from;from~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `document` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `document` AS F0, `document` AS F1
                                       WHERE F0.`from`=F1.`from`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Document '.$v[0][0].',Document '.$v[0][1].')
reden: \"Artificial explanation: I |- from;from~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule55(){
    // No violations should occur in (I |- to;to~)
    //            rule':: I/\-(to;to~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `document` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`document`, F1.`document` AS `document1`
                                        FROM `to` AS F0, `to` AS F1
                                       WHERE F0.`party`=F1.`party`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`document` AND isect0.`i`=cp.`document1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Document '.$v[0][0].',Document '.$v[0][1].')
reden: \"Artificial explanation: I |- to;to~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule56(){
    // No violations should occur in (sent~;sent |- I)
    //            rule':: sent~;sent/\-I
    // sqlExprSrc fSpec rule':: sent
     $v=DB_doquer('SELECT DISTINCT isect0.`sent`, isect0.`sent1`
                     FROM 
                        ( SELECT DISTINCT F0.`sent`, F1.`sent` AS `sent1`
                            FROM `document` AS F0, `document` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`sent` <> isect0.`sent1` AND isect0.`sent` IS NOT NULL AND isect0.`sent1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (TimeStamp '.$v[0][0].',TimeStamp '.$v[0][1].')
reden: \"Artificial explanation: sent~;sent |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule57(){
    // No violations should occur in (I |- sent;sent~)
    //            rule':: I/\-(sent;sent~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `document` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `document` AS F0, `document` AS F1
                                       WHERE F0.`sent`=F1.`sent`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Document '.$v[0][0].',Document '.$v[0][1].')
reden: \"Artificial explanation: I |- sent;sent~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule58(){
    // No violations should occur in (received~;received |- I)
    //            rule':: received~;received/\-I
    // sqlExprSrc fSpec rule':: received
     $v=DB_doquer('SELECT DISTINCT isect0.`received`, isect0.`received1`
                     FROM 
                        ( SELECT DISTINCT F0.`received`, F1.`received` AS `received1`
                            FROM `document` AS F0, `document` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`received` <> isect0.`received1` AND isect0.`received` IS NOT NULL AND isect0.`received1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (TimeStamp '.$v[0][0].',TimeStamp '.$v[0][1].')
reden: \"Artificial explanation: received~;received |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule59(){
    // No violations should occur in (propertyOf~;propertyOf |- I)
    //            rule':: propertyOf~;propertyOf/\-I
    // sqlExprSrc fSpec rule':: propertyof
     $v=DB_doquer('SELECT DISTINCT isect0.`propertyof`, isect0.`propertyof1`
                     FROM 
                        ( SELECT DISTINCT F0.`propertyof`, F1.`propertyof` AS `propertyof1`
                            FROM `document` AS F0, `document` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`propertyof` <> isect0.`propertyof1` AND isect0.`propertyof` IS NOT NULL AND isect0.`propertyof1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Text '.$v[0][0].',Text '.$v[0][1].')
reden: \"Artificial explanation: propertyOf~;propertyOf |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule60(){
    // No violations should occur in (I |- propertyOf;propertyOf~)
    //            rule':: I/\-(propertyOf;propertyOf~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `document` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `document` AS F0, `document` AS F1
                                       WHERE F0.`propertyof`=F1.`propertyof`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Document '.$v[0][0].',Document '.$v[0][1].')
reden: \"Artificial explanation: I |- propertyOf;propertyOf~\"<BR>',3);
      return false;
    }return true;
  }
  
  if($DB_debug>=3){
    checkRule1();
    checkRule2();
    checkRule3();
    checkRule4();
    checkRule5();
    checkRule6();
    checkRule7();
    checkRule8();
    checkRule9();
    checkRule10();
    checkRule11();
    checkRule12();
    checkRule13();
    checkRule14();
    checkRule15();
    checkRule16();
    checkRule17();
    checkRule18();
    checkRule19();
    checkRule20();
    checkRule21();
    checkRule22();
    checkRule23();
    checkRule24();
    checkRule25();
    checkRule26();
    checkRule27();
    checkRule28();
    checkRule29();
    checkRule30();
    checkRule31();
    checkRule32();
    checkRule33();
    checkRule34();
    checkRule35();
    checkRule36();
    checkRule37();
    checkRule38();
    checkRule39();
    checkRule40();
    checkRule41();
    checkRule42();
    checkRule43();
    checkRule44();
    checkRule45();
    checkRule46();
    checkRule47();
    checkRule48();
    checkRule49();
    checkRule50();
    checkRule51();
    checkRule52();
    checkRule53();
    checkRule54();
    checkRule55();
    checkRule56();
    checkRule57();
    checkRule58();
    checkRule59();
    checkRule60();
  }
?>