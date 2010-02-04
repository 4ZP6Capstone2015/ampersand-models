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
  $DB_slct = mysql_select_db('VIRO',$DB_link);
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
    // No violations should occur in (I |- eiser~;eiser)
    //            rule':: I/\-(eiser~;eiser)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `procedur` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`procedur`, F1.`procedur` AS `procedur1`
                                        FROM `eiser` AS F0, `eiser` AS F1
                                       WHERE F0.`persoon`=F1.`persoon`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`procedur` AND isect0.`i`=cp.`procedur1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Procedur '.$v[0][0].',Procedur '.$v[0][1].')
reden: \"Voor elke procedure moet er tenminste een eisende partij zijn.\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule2(){
    // No violations should occur in (griffier |- locatie;griffier)
    //            rule':: griffier/\-(locatie;griffier)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`griffier`
                     FROM `zitting` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`Persoon`
                                        FROM `zitting` AS F0, `griffier` AS F1
                                       WHERE F0.`locatie`=F1.`gerecht`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`griffier`=cp.`Persoon`) AND isect0.`i` IS NOT NULL AND isect0.`griffier` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Zitting '.$v[0][0].',Persoon '.$v[0][1].')
reden: \"De griffier in een zaak moet benoemd zijn bij de rechtbank waar deze zaak dient.\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule3(){
    // No violations should occur in (plaatsgevonden |- geagendeerd)
    //            rule':: plaatsgevonden/\-geagendeerd
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`plaatsgevonden`
                     FROM `zitting` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM `zitting` AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`plaatsgevonden`=cp.`geagendeerd`) AND isect0.`i` IS NOT NULL AND isect0.`plaatsgevonden` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Zitting '.$v[0][0].',Datum '.$v[0][1].')
reden: \"Alle zittingen worden geagendeerd\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule4(){
    // No violations should occur in (plaats~;locatie |- hoofdplaats~\/neven)
    //            rule':: plaats~;locatie/\-hoofdplaats~/\-neven
    // sqlExprSrc fSpec rule':: plaats
     $v=DB_doquer('SELECT DISTINCT isect0.`plaats`, isect0.`locatie`
                     FROM 
                        ( SELECT DISTINCT F0.`plaats`, F1.`locatie`
                            FROM `zitting` AS F0, `zitting` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM `gerecht` AS cp
                                WHERE isect0.`plaats`=cp.`hoofdplaats` AND isect0.`locatie`=cp.`i`) AND NOT EXISTS (SELECT *
                                 FROM `plaats` AS cp
                                WHERE isect0.`plaats`=cp.`i` AND isect0.`locatie`=cp.`neven`) AND isect0.`plaats` IS NOT NULL AND isect0.`locatie` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Plaats '.$v[0][0].',Gerecht '.$v[0][1].')
reden: \"Elke zitting vindt plaats in de hoofdvestigingsplaats van een gerecht of een van de nevenvestigingsplaatsen (tekst checken, Artikel 47 lid 2 RO)\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule5(){
    // No violations should occur in (zaak~;zitting;locatie |- bevoegd)
    //            rule':: zaak~;zitting;locatie/\-bevoegd
    // sqlExprSrc fSpec rule':: zaak
     $v=DB_doquer('SELECT DISTINCT isect0.`zaak`, isect0.`locatie`
                     FROM 
                        ( SELECT DISTINCT F0.`zaak`, F2.`locatie`
                            FROM `behandeling` AS F0, `behandeling` AS F1, `zitting` AS F2
                           WHERE F0.`i`=F1.`i`
                             AND F1.`zitting`=F2.`i`
                        ) AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM `bevoegd` AS cp
                                WHERE isect0.`zaak`=cp.`procedur` AND isect0.`locatie`=cp.`Gerecht`) AND isect0.`zaak` IS NOT NULL AND isect0.`locatie` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Procedur '.$v[0][0].',Gerecht '.$v[0][1].')
reden: \"Een bestuurszaak dient bij de rechter die bij de zetel van de gemeente, provincie, waterschap of politieregio hoort, waar tegen bezwaar was ingesteld (voorafgaande aan de procedure bij de bestuursrechter) (art. 8:7 Awb.)\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule6(){
    // No violations should occur in (rechter |- kamer;bezetting~)
    //            rule':: rechter/\-(kamer;bezetting~)
    // sqlExprSrc fSpec rule':: zitting
     $v=DB_doquer('SELECT DISTINCT isect0.`zitting`, isect0.`persoon`
                     FROM `rechter` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`persoon`
                                        FROM `zitting` AS F0, `bezetting` AS F1
                                       WHERE F0.`kamer`=F1.`Kamer`
                                    ) AS cp
                                WHERE isect0.`zitting`=cp.`i` AND isect0.`persoon`=cp.`persoon`) AND isect0.`zitting` IS NOT NULL AND isect0.`persoon` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Zitting '.$v[0][0].',Persoon '.$v[0][1].')
reden: \"De rechter ter zitting maakt deel uit van de bezetting van de kamer die de zitting houdt\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule7(){
    // No violations should occur in (type;handeling~;orgaan |- als)
    //            rule':: type;handeling~;orgaan/\-als
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`Orgaan`
                     FROM 
                        ( SELECT DISTINCT F0.`i`, F2.`Orgaan`
                            FROM `actie` AS F0, `handelingartikel` AS F1, `orgaanartikel` AS F2
                           WHERE F0.`type`=F1.`Handeling`
                             AND F1.`artikel`=F2.`artikel`
                        ) AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM `als` AS cp
                                WHERE isect0.`i`=cp.`actie` AND isect0.`Orgaan`=cp.`Orgaan`) AND isect0.`i` IS NOT NULL AND isect0.`Orgaan` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Actie '.$v[0][0].',Orgaan '.$v[0][1].')
reden: \"De persoon die een actie uitvoert doet dat als vertegenwoordiger van het orgaan dat de handeling uitvoert\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule8(){
    // No violations should occur in (login;digid |- digid~)
    //            rule':: login;digid/\-digid~
    // sqlExprSrc fSpec rule':: login
     $v=DB_doquer('SELECT DISTINCT isect0.`login`, isect0.`digid`
                     FROM 
                        ( SELECT DISTINCT F0.`login`, F1.`digid`
                            FROM `sessie` AS F0, `sessie` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM `digid` AS cp
                                WHERE isect0.`login`=cp.`digid` AND isect0.`digid`=cp.`i`) AND isect0.`login` IS NOT NULL AND isect0.`digid` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Persoon '.$v[0][0].',DigID '.$v[0][1].')
reden: \"Elke sessie behoort geautoriseerd te zijn op basis van de juiste DigID\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule9(){
    // No violations should occur in (login;rol |- vervult)
    //            rule':: login;rol/\-vervult
    // sqlExprSrc fSpec rule':: login
     $v=DB_doquer('SELECT DISTINCT isect0.`login`, isect0.`rol`
                     FROM 
                        ( SELECT DISTINCT F0.`login`, F1.`rol`
                            FROM `sessie` AS F0, `sessie` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM `vervult` AS cp
                                WHERE isect0.`login`=cp.`persoon` AND isect0.`rol`=cp.`Rol`) AND isect0.`login` IS NOT NULL AND isect0.`rol` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Persoon '.$v[0][0].',Rol '.$v[0][1].')
reden: \"De gebruiker in deze sessie dient in te loggen met een van de rollen die hij of zij vervult.\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule10(){
    // No violations should occur in (vervult |- aut)
    //            rule':: vervult/\-aut
    // sqlExprSrc fSpec rule':: persoon
     $v=DB_doquer('SELECT DISTINCT isect0.`persoon`, isect0.`Rol`
                     FROM `vervult` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM `aut` AS cp
                                WHERE isect0.`persoon`=cp.`persoon` AND isect0.`Rol`=cp.`Rol`) AND isect0.`persoon` IS NOT NULL AND isect0.`Rol` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Persoon '.$v[0][0].',Rol '.$v[0][1].')
reden: \"Elke persoon die een rol vervult moet daarvoor geautoriseerd zijn.\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule11(){
    // No violations should occur in (type~;type |- I)
    //            rule':: type~;type/\-I
    // sqlExprSrc fSpec rule':: type
     $v=DB_doquer('SELECT DISTINCT isect0.`type`, isect0.`type1`
                     FROM 
                        ( SELECT DISTINCT F0.`type`, F1.`type` AS `type1`
                            FROM `document` AS F0, `document` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`type` <> isect0.`type1` AND isect0.`type` IS NOT NULL AND isect0.`type1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Documenttype '.$v[0][0].',Documenttype '.$v[0][1].')
reden: \"Artificial explanation: type~;type |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule12(){
    // No violations should occur in (I |- type;type~)
    //            rule':: I/\-(type;type~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `document` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `document` AS F0, `document` AS F1
                                       WHERE F0.`type`=F1.`type`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Document '.$v[0][0].',Document '.$v[0][1].')
reden: \"Artificial explanation: I |- type;type~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule13(){
    // No violations should occur in (zorgdrager~;zorgdrager |- I)
    //            rule':: zorgdrager~;zorgdrager/\-I
    // sqlExprSrc fSpec rule':: zorgdrager
     $v=DB_doquer('SELECT DISTINCT isect0.`zorgdrager`, isect0.`zorgdrager1`
                     FROM 
                        ( SELECT DISTINCT F0.`zorgdrager`, F1.`zorgdrager` AS `zorgdrager1`
                            FROM `procedur` AS F0, `procedur` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`zorgdrager` <> isect0.`zorgdrager1` AND isect0.`zorgdrager` IS NOT NULL AND isect0.`zorgdrager1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Orgaan '.$v[0][0].',Orgaan '.$v[0][1].')
reden: \"Artificial explanation: zorgdrager~;zorgdrager |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule14(){
    // No violations should occur in (I |- zorgdrager;zorgdrager~)
    //            rule':: I/\-(zorgdrager;zorgdrager~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `procedur` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `procedur` AS F0, `procedur` AS F1
                                       WHERE F0.`zorgdrager`=F1.`zorgdrager`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Procedur '.$v[0][0].',Procedur '.$v[0][1].')
reden: \"Artificial explanation: I |- zorgdrager;zorgdrager~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule15(){
    // No violations should occur in (I |- eiser~;eiser)
    //            rule':: I/\-(eiser~;eiser)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `procedur` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`procedur`, F1.`procedur` AS `procedur1`
                                        FROM `eiser` AS F0, `eiser` AS F1
                                       WHERE F0.`persoon`=F1.`persoon`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`procedur` AND isect0.`i`=cp.`procedur1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Procedur '.$v[0][0].',Procedur '.$v[0][1].')
reden: \"Artificial explanation: I |- eiser~;eiser\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule16(){
    // No violations should occur in (I |- gemachtigde~;gemachtigde)
    //            rule':: I/\-(gemachtigde~;gemachtigde)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `machtiging` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`machtiging`, F1.`machtiging` AS `machtiging1`
                                        FROM `gemachtigde` AS F0, `gemachtigde` AS F1
                                       WHERE F0.`persoon`=F1.`persoon`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`machtiging` AND isect0.`i`=cp.`machtiging1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Machtiging '.$v[0][0].',Machtiging '.$v[0][1].')
reden: \"Artificial explanation: I |- gemachtigde~;gemachtigde\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule17(){
    // No violations should occur in (door~;door |- I)
    //            rule':: door~;door/\-I
    // sqlExprSrc fSpec rule':: door
     $v=DB_doquer('SELECT DISTINCT isect0.`door`, isect0.`door1`
                     FROM 
                        ( SELECT DISTINCT F0.`door`, F1.`door` AS `door1`
                            FROM `machtiging` AS F0, `machtiging` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`door` <> isect0.`door1` AND isect0.`door` IS NOT NULL AND isect0.`door1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Persoon '.$v[0][0].',Persoon '.$v[0][1].')
reden: \"Artificial explanation: door~;door |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule18(){
    // No violations should occur in (I |- door;door~)
    //            rule':: I/\-(door;door~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `machtiging` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `machtiging` AS F0, `machtiging` AS F1
                                       WHERE F0.`door`=F1.`door`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Machtiging '.$v[0][0].',Machtiging '.$v[0][1].')
reden: \"Artificial explanation: I |- door;door~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule19(){
    // No violations should occur in (rechtsgebied~;rechtsgebied |- I)
    //            rule':: rechtsgebied~;rechtsgebied/\-I
    // sqlExprSrc fSpec rule':: rechtsgebied
     $v=DB_doquer('SELECT DISTINCT isect0.`rechtsgebied`, isect0.`rechtsgebied1`
                     FROM 
                        ( SELECT DISTINCT F0.`rechtsgebied`, F1.`rechtsgebied` AS `rechtsgebied1`
                            FROM `procedur` AS F0, `procedur` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`rechtsgebied` <> isect0.`rechtsgebied1` AND isect0.`rechtsgebied` IS NOT NULL AND isect0.`rechtsgebied1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Rechtsgebied '.$v[0][0].',Rechtsgebied '.$v[0][1].')
reden: \"Artificial explanation: rechtsgebied~;rechtsgebied |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule20(){
    // No violations should occur in (I |- rechtsgebied;rechtsgebied~)
    //            rule':: I/\-(rechtsgebied;rechtsgebied~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `procedur` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `procedur` AS F0, `procedur` AS F1
                                       WHERE F0.`rechtsgebied`=F1.`rechtsgebied`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Procedur '.$v[0][0].',Procedur '.$v[0][1].')
reden: \"Artificial explanation: I |- rechtsgebied;rechtsgebied~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule21(){
    // No violations should occur in (proceduresoort~;proceduresoort |- I)
    //            rule':: proceduresoort~;proceduresoort/\-I
    // sqlExprSrc fSpec rule':: proceduresoort
     $v=DB_doquer('SELECT DISTINCT isect0.`proceduresoort`, isect0.`proceduresoort1`
                     FROM 
                        ( SELECT DISTINCT F0.`proceduresoort`, F1.`proceduresoort` AS `proceduresoort1`
                            FROM `procedur` AS F0, `procedur` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`proceduresoort` <> isect0.`proceduresoort1` AND isect0.`proceduresoort` IS NOT NULL AND isect0.`proceduresoort1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Proceduresoort '.$v[0][0].',Proceduresoort '.$v[0][1].')
reden: \"Artificial explanation: proceduresoort~;proceduresoort |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule22(){
    // No violations should occur in (I |- proceduresoort;proceduresoort~)
    //            rule':: I/\-(proceduresoort;proceduresoort~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `procedur` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `procedur` AS F0, `procedur` AS F1
                                       WHERE F0.`proceduresoort`=F1.`proceduresoort`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Procedur '.$v[0][0].',Procedur '.$v[0][1].')
reden: \"Artificial explanation: I |- proceduresoort;proceduresoort~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule23(){
    // No violations should occur in (I |- grond;grond~)
    //            rule':: I/\-(grond;grond~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `cluster` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`cluster`, F1.`cluster` AS `cluster1`
                                        FROM `grond` AS F0, `grond` AS F1
                                       WHERE F0.`tekst`=F1.`tekst`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`cluster` AND isect0.`i`=cp.`cluster1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Cluster '.$v[0][0].',Cluster '.$v[0][1].')
reden: \"Artificial explanation: I |- grond;grond~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule24(){
    // No violations should occur in (naam~;naam |- I)
    //            rule':: naam~;naam/\-I
    // sqlExprSrc fSpec rule':: naam
     $v=DB_doquer('SELECT DISTINCT isect0.`naam`, isect0.`naam1`
                     FROM 
                        ( SELECT DISTINCT F0.`naam`, F1.`naam` AS `naam1`
                            FROM `cluster` AS F0, `cluster` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`naam` <> isect0.`naam1` AND isect0.`naam` IS NOT NULL AND isect0.`naam1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Tekst '.$v[0][0].',Tekst '.$v[0][1].')
reden: \"Artificial explanation: naam~;naam |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule25(){
    // No violations should occur in (I |- naam;naam~)
    //            rule':: I/\-(naam;naam~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `cluster` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `cluster` AS F0, `cluster` AS F1
                                       WHERE F0.`naam`=F1.`naam`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Cluster '.$v[0][0].',Cluster '.$v[0][1].')
reden: \"Artificial explanation: I |- naam;naam~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule26(){
    // No violations should occur in (zitting~;zitting |- I)
    //            rule':: zitting~;zitting/\-I
    // sqlExprSrc fSpec rule':: zitting
     $v=DB_doquer('SELECT DISTINCT isect0.`zitting`, isect0.`zitting1`
                     FROM 
                        ( SELECT DISTINCT F0.`zitting`, F1.`zitting` AS `zitting1`
                            FROM `behandeling` AS F0, `behandeling` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`zitting` <> isect0.`zitting1` AND isect0.`zitting` IS NOT NULL AND isect0.`zitting1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Zitting '.$v[0][0].',Zitting '.$v[0][1].')
reden: \"Artificial explanation: zitting~;zitting |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule27(){
    // No violations should occur in (I |- zitting;zitting~)
    //            rule':: I/\-(zitting;zitting~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `behandeling` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `behandeling` AS F0, `behandeling` AS F1
                                       WHERE F0.`zitting`=F1.`zitting`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Behandeling '.$v[0][0].',Behandeling '.$v[0][1].')
reden: \"Artificial explanation: I |- zitting;zitting~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule28(){
    // No violations should occur in (zaak~;zaak |- I)
    //            rule':: zaak~;zaak/\-I
    // sqlExprSrc fSpec rule':: zaak
     $v=DB_doquer('SELECT DISTINCT isect0.`zaak`, isect0.`zaak1`
                     FROM 
                        ( SELECT DISTINCT F0.`zaak`, F1.`zaak` AS `zaak1`
                            FROM `behandeling` AS F0, `behandeling` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`zaak` <> isect0.`zaak1` AND isect0.`zaak` IS NOT NULL AND isect0.`zaak1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Procedur '.$v[0][0].',Procedur '.$v[0][1].')
reden: \"Artificial explanation: zaak~;zaak |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule29(){
    // No violations should occur in (I |- zaak;zaak~)
    //            rule':: I/\-(zaak;zaak~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `behandeling` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `behandeling` AS F0, `behandeling` AS F1
                                       WHERE F0.`zaak`=F1.`zaak`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Behandeling '.$v[0][0].',Behandeling '.$v[0][1].')
reden: \"Artificial explanation: I |- zaak;zaak~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule30(){
    // No violations should occur in (I |- rechter;rechter~)
    //            rule':: I/\-(rechter;rechter~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `zitting` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`zitting`, F1.`zitting` AS `zitting1`
                                        FROM `rechter` AS F0, `rechter` AS F1
                                       WHERE F0.`persoon`=F1.`persoon`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`zitting` AND isect0.`i`=cp.`zitting1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Zitting '.$v[0][0].',Zitting '.$v[0][1].')
reden: \"Artificial explanation: I |- rechter;rechter~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule31(){
    // No violations should occur in (I |- griffier;griffier~)
    //            rule':: I/\-(griffier;griffier~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `zitting` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `zitting` AS F0, `zitting` AS F1
                                       WHERE F0.`griffier`=F1.`griffier`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Zitting '.$v[0][0].',Zitting '.$v[0][1].')
reden: \"Artificial explanation: I |- griffier;griffier~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule32(){
    // No violations should occur in (griffier~;griffier |- I)
    //            rule':: griffier~;griffier/\-I
    // sqlExprSrc fSpec rule':: griffier
     $v=DB_doquer('SELECT DISTINCT isect0.`griffier`, isect0.`griffier1`
                     FROM 
                        ( SELECT DISTINCT F0.`griffier`, F1.`griffier` AS `griffier1`
                            FROM `zitting` AS F0, `zitting` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`griffier` <> isect0.`griffier1` AND isect0.`griffier` IS NOT NULL AND isect0.`griffier1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Persoon '.$v[0][0].',Persoon '.$v[0][1].')
reden: \"Artificial explanation: griffier~;griffier |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule33(){
    // No violations should occur in (geagendeerd~;geagendeerd |- I)
    //            rule':: geagendeerd~;geagendeerd/\-I
    // sqlExprSrc fSpec rule':: geagendeerd
     $v=DB_doquer('SELECT DISTINCT isect0.`geagendeerd`, isect0.`geagendeerd1`
                     FROM 
                        ( SELECT DISTINCT F0.`geagendeerd`, F1.`geagendeerd` AS `geagendeerd1`
                            FROM `zitting` AS F0, `zitting` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`geagendeerd` <> isect0.`geagendeerd1` AND isect0.`geagendeerd` IS NOT NULL AND isect0.`geagendeerd1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Datum '.$v[0][0].',Datum '.$v[0][1].')
reden: \"Artificial explanation: geagendeerd~;geagendeerd |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule34(){
    // No violations should occur in (I |- geagendeerd;geagendeerd~)
    //            rule':: I/\-(geagendeerd;geagendeerd~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `zitting` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `zitting` AS F0, `zitting` AS F1
                                       WHERE F0.`geagendeerd`=F1.`geagendeerd`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Zitting '.$v[0][0].',Zitting '.$v[0][1].')
reden: \"Artificial explanation: I |- geagendeerd;geagendeerd~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule35(){
    // No violations should occur in (plaatsgevonden~;plaatsgevonden |- I)
    //            rule':: plaatsgevonden~;plaatsgevonden/\-I
    // sqlExprSrc fSpec rule':: plaatsgevonden
     $v=DB_doquer('SELECT DISTINCT isect0.`plaatsgevonden`, isect0.`plaatsgevonden1`
                     FROM 
                        ( SELECT DISTINCT F0.`plaatsgevonden`, F1.`plaatsgevonden` AS `plaatsgevonden1`
                            FROM `zitting` AS F0, `zitting` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`plaatsgevonden` <> isect0.`plaatsgevonden1` AND isect0.`plaatsgevonden` IS NOT NULL AND isect0.`plaatsgevonden1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Datum '.$v[0][0].',Datum '.$v[0][1].')
reden: \"Artificial explanation: plaatsgevonden~;plaatsgevonden |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule36(){
    // No violations should occur in (plaats~;plaats |- I)
    //            rule':: plaats~;plaats/\-I
    // sqlExprSrc fSpec rule':: plaats
     $v=DB_doquer('SELECT DISTINCT isect0.`plaats`, isect0.`plaats1`
                     FROM 
                        ( SELECT DISTINCT F0.`plaats`, F1.`plaats` AS `plaats1`
                            FROM `zitting` AS F0, `zitting` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`plaats` <> isect0.`plaats1` AND isect0.`plaats` IS NOT NULL AND isect0.`plaats1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Plaats '.$v[0][0].',Plaats '.$v[0][1].')
reden: \"Artificial explanation: plaats~;plaats |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule37(){
    // No violations should occur in (I |- plaats;plaats~)
    //            rule':: I/\-(plaats;plaats~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `zitting` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `zitting` AS F0, `zitting` AS F1
                                       WHERE F0.`plaats`=F1.`plaats`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Zitting '.$v[0][0].',Zitting '.$v[0][1].')
reden: \"Artificial explanation: I |- plaats;plaats~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule38(){
    // No violations should occur in (locatie~;locatie |- I)
    //            rule':: locatie~;locatie/\-I
    // sqlExprSrc fSpec rule':: locatie
     $v=DB_doquer('SELECT DISTINCT isect0.`locatie`, isect0.`locatie1`
                     FROM 
                        ( SELECT DISTINCT F0.`locatie`, F1.`locatie` AS `locatie1`
                            FROM `zitting` AS F0, `zitting` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`locatie` <> isect0.`locatie1` AND isect0.`locatie` IS NOT NULL AND isect0.`locatie1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Gerecht '.$v[0][0].',Gerecht '.$v[0][1].')
reden: \"Artificial explanation: locatie~;locatie |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule39(){
    // No violations should occur in (I |- locatie;locatie~)
    //            rule':: I/\-(locatie;locatie~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `zitting` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `zitting` AS F0, `zitting` AS F1
                                       WHERE F0.`locatie`=F1.`locatie`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Zitting '.$v[0][0].',Zitting '.$v[0][1].')
reden: \"Artificial explanation: I |- locatie;locatie~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule40(){
    // No violations should occur in (gerecht~;gerecht |- I)
    //            rule':: gerecht~;gerecht/\-I
    // sqlExprSrc fSpec rule':: gerecht
     $v=DB_doquer('SELECT DISTINCT isect0.`gerecht`, isect0.`gerecht1`
                     FROM 
                        ( SELECT DISTINCT F0.`gerecht`, F1.`gerecht` AS `gerecht1`
                            FROM `kamer` AS F0, `kamer` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`gerecht` <> isect0.`gerecht1` AND isect0.`gerecht` IS NOT NULL AND isect0.`gerecht1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Gerecht '.$v[0][0].',Gerecht '.$v[0][1].')
reden: \"Artificial explanation: gerecht~;gerecht |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule41(){
    // No violations should occur in (I |- gerecht;gerecht~)
    //            rule':: I/\-(gerecht;gerecht~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `kamer` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `kamer` AS F0, `kamer` AS F1
                                       WHERE F0.`gerecht`=F1.`gerecht`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Kamer '.$v[0][0].',Kamer '.$v[0][1].')
reden: \"Artificial explanation: I |- gerecht;gerecht~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule42(){
    // No violations should occur in (sector~;sector |- I)
    //            rule':: sector~;sector/\-I
    // sqlExprSrc fSpec rule':: sector
     $v=DB_doquer('SELECT DISTINCT isect0.`sector`, isect0.`sector1`
                     FROM 
                        ( SELECT DISTINCT F0.`sector`, F1.`sector` AS `sector1`
                            FROM `kamer` AS F0, `kamer` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`sector` <> isect0.`sector1` AND isect0.`sector` IS NOT NULL AND isect0.`sector1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Sector '.$v[0][0].',Sector '.$v[0][1].')
reden: \"Artificial explanation: sector~;sector |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule43(){
    // No violations should occur in (I |- sector;sector~)
    //            rule':: I/\-(sector;sector~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `kamer` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `kamer` AS F0, `kamer` AS F1
                                       WHERE F0.`sector`=F1.`sector`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Kamer '.$v[0][0].',Kamer '.$v[0][1].')
reden: \"Artificial explanation: I |- sector;sector~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule44(){
    // No violations should occur in (kamer~;kamer |- I)
    //            rule':: kamer~;kamer/\-I
    // sqlExprSrc fSpec rule':: kamer
     $v=DB_doquer('SELECT DISTINCT isect0.`kamer`, isect0.`kamer1`
                     FROM 
                        ( SELECT DISTINCT F0.`kamer`, F1.`kamer` AS `kamer1`
                            FROM `zitting` AS F0, `zitting` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`kamer` <> isect0.`kamer1` AND isect0.`kamer` IS NOT NULL AND isect0.`kamer1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Kamer '.$v[0][0].',Kamer '.$v[0][1].')
reden: \"Artificial explanation: kamer~;kamer |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule45(){
    // No violations should occur in (I |- kamer;kamer~)
    //            rule':: I/\-(kamer;kamer~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `zitting` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `zitting` AS F0, `zitting` AS F1
                                       WHERE F0.`kamer`=F1.`kamer`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Zitting '.$v[0][0].',Zitting '.$v[0][1].')
reden: \"Artificial explanation: I |- kamer;kamer~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule46(){
    // No violations should occur in (ressort~;ressort |- I)
    //            rule':: ressort~;ressort/\-I
    // sqlExprSrc fSpec rule':: ressort
     $v=DB_doquer('SELECT DISTINCT isect0.`ressort`, isect0.`ressort1`
                     FROM 
                        ( SELECT DISTINCT F0.`ressort`, F1.`ressort` AS `ressort1`
                            FROM `gerecht` AS F0, `gerecht` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`ressort` <> isect0.`ressort1` AND isect0.`ressort` IS NOT NULL AND isect0.`ressort1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Gerechtshof '.$v[0][0].',Gerechtshof '.$v[0][1].')
reden: \"Artificial explanation: ressort~;ressort |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule47(){
    // No violations should occur in (I |- ressort;ressort~)
    //            rule':: I/\-(ressort;ressort~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `gerecht` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `gerecht` AS F0, `gerecht` AS F1
                                       WHERE F0.`ressort`=F1.`ressort`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Gerecht '.$v[0][0].',Gerecht '.$v[0][1].')
reden: \"Artificial explanation: I |- ressort;ressort~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule48(){
    // No violations should occur in (hoofdplaats~;hoofdplaats |- I)
    //            rule':: hoofdplaats~;hoofdplaats/\-I
    // sqlExprSrc fSpec rule':: hoofdplaats
     $v=DB_doquer('SELECT DISTINCT isect0.`hoofdplaats`, isect0.`hoofdplaats1`
                     FROM 
                        ( SELECT DISTINCT F0.`hoofdplaats`, F1.`hoofdplaats` AS `hoofdplaats1`
                            FROM `gerecht` AS F0, `gerecht` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`hoofdplaats` <> isect0.`hoofdplaats1` AND isect0.`hoofdplaats` IS NOT NULL AND isect0.`hoofdplaats1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Plaats '.$v[0][0].',Plaats '.$v[0][1].')
reden: \"Artificial explanation: hoofdplaats~;hoofdplaats |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule49(){
    // No violations should occur in (I |- hoofdplaats;hoofdplaats~)
    //            rule':: I/\-(hoofdplaats;hoofdplaats~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `gerecht` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `gerecht` AS F0, `gerecht` AS F1
                                       WHERE F0.`hoofdplaats`=F1.`hoofdplaats`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Gerecht '.$v[0][0].',Gerecht '.$v[0][1].')
reden: \"Artificial explanation: I |- hoofdplaats;hoofdplaats~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule50(){
    // No violations should occur in (neven~;neven |- I)
    //            rule':: neven~;neven/\-I
    // sqlExprSrc fSpec rule':: neven
     $v=DB_doquer('SELECT DISTINCT isect0.`neven`, isect0.`neven1`
                     FROM 
                        ( SELECT DISTINCT F0.`neven`, F1.`neven` AS `neven1`
                            FROM `plaats` AS F0, `plaats` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`neven` <> isect0.`neven1` AND isect0.`neven` IS NOT NULL AND isect0.`neven1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Gerecht '.$v[0][0].',Gerecht '.$v[0][1].')
reden: \"Artificial explanation: neven~;neven |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule51(){
    // No violations should occur in (subject~;subject |- I)
    //            rule':: subject~;subject/\-I
    // sqlExprSrc fSpec rule':: subject
     $v=DB_doquer('SELECT DISTINCT isect0.`subject`, isect0.`subject1`
                     FROM 
                        ( SELECT DISTINCT F0.`subject`, F1.`subject` AS `subject1`
                            FROM `actie` AS F0, `actie` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`subject` <> isect0.`subject1` AND isect0.`subject` IS NOT NULL AND isect0.`subject1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Persoon '.$v[0][0].',Persoon '.$v[0][1].')
reden: \"Artificial explanation: subject~;subject |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule52(){
    // No violations should occur in (I |- subject;subject~)
    //            rule':: I/\-(subject;subject~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `actie` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `actie` AS F0, `actie` AS F1
                                       WHERE F0.`subject`=F1.`subject`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Actie '.$v[0][0].',Actie '.$v[0][1].')
reden: \"Artificial explanation: I |- subject;subject~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule53(){
    // No violations should occur in (type~;type |- I)
    //            rule':: type~;type/\-I
    // sqlExprSrc fSpec rule':: type
     $v=DB_doquer('SELECT DISTINCT isect0.`type`, isect0.`type1`
                     FROM 
                        ( SELECT DISTINCT F0.`type`, F1.`type` AS `type1`
                            FROM `actie` AS F0, `actie` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`type` <> isect0.`type1` AND isect0.`type` IS NOT NULL AND isect0.`type1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Handeling '.$v[0][0].',Handeling '.$v[0][1].')
reden: \"Artificial explanation: type~;type |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule54(){
    // No violations should occur in (I |- type;type~)
    //            rule':: I/\-(type;type~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `actie` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `actie` AS F0, `actie` AS F1
                                       WHERE F0.`type`=F1.`type`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Actie '.$v[0][0].',Actie '.$v[0][1].')
reden: \"Artificial explanation: I |- type;type~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule55(){
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
  
  function checkRule56(){
    // No violations should occur in (sub~/\sub |- I)
    //            rule':: sub~/\sub/\-I
    // sqlExprSrc fSpec rule':: usecase1
     $v=DB_doquer('SELECT DISTINCT isect0.`usecase1`, isect0.`usecase`
                     FROM `sub` AS isect0, `sub` AS isect1
                    WHERE (isect0.`usecase1` = isect1.`usecase` AND isect0.`usecase` = isect1.`usecase1`) AND isect0.`usecase1` <> isect0.`usecase` AND isect0.`usecase1` IS NOT NULL AND isect0.`usecase` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Usecase '.$v[0][0].',Usecase '.$v[0][1].')
reden: \"Artificial explanation: sub~/\\sub |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule57(){
    // No violations should occur in (categorie~;categorie |- I)
    //            rule':: categorie~;categorie/\-I
    // sqlExprSrc fSpec rule':: categorie
     $v=DB_doquer('SELECT DISTINCT isect0.`categorie`, isect0.`categorie1`
                     FROM 
                        ( SELECT DISTINCT F0.`categorie`, F1.`categorie` AS `categorie1`
                            FROM `usecase` AS F0, `usecase` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`categorie` <> isect0.`categorie1` AND isect0.`categorie` IS NOT NULL AND isect0.`categorie1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Gpstap '.$v[0][0].',Gpstap '.$v[0][1].')
reden: \"Artificial explanation: categorie~;categorie |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule58(){
    // No violations should occur in (omschrijving~;omschrijving |- I)
    //            rule':: omschrijving~;omschrijving/\-I
    // sqlExprSrc fSpec rule':: omschrijving
     $v=DB_doquer('SELECT DISTINCT isect0.`omschrijving`, isect0.`omschrijving1`
                     FROM 
                        ( SELECT DISTINCT F0.`omschrijving`, F1.`omschrijving` AS `omschrijving1`
                            FROM `usecase` AS F0, `usecase` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`omschrijving` <> isect0.`omschrijving1` AND isect0.`omschrijving` IS NOT NULL AND isect0.`omschrijving1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Tekst '.$v[0][0].',Tekst '.$v[0][1].')
reden: \"Artificial explanation: omschrijving~;omschrijving |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule59(){
    // No violations should occur in (iS_component_objecttype~;iS_component_objecttype |- I)
    //            rule':: iS_component_objecttype~;iS_component_objecttype/\-I
    // sqlExprSrc fSpec rule':: is_component_objecttype
     $v=DB_doquer('SELECT DISTINCT isect0.`is_component_objecttype`, isect0.`is_component_objecttype1`
                     FROM 
                        ( SELECT DISTINCT F0.`is_component_objecttype`, F1.`is_component_objecttype` AS `is_component_objecttype1`
                            FROM `usecase` AS F0, `usecase` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`is_component_objecttype` <> isect0.`is_component_objecttype1` AND isect0.`is_component_objecttype` IS NOT NULL AND isect0.`is_component_objecttype1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Component '.$v[0][0].',Component '.$v[0][1].')
reden: \"Artificial explanation: iS_component_objecttype~;iS_component_objecttype |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule60(){
    // No violations should occur in (opmerkingen~;opmerkingen |- I)
    //            rule':: opmerkingen~;opmerkingen/\-I
    // sqlExprSrc fSpec rule':: opmerkingen
     $v=DB_doquer('SELECT DISTINCT isect0.`opmerkingen`, isect0.`opmerkingen1`
                     FROM 
                        ( SELECT DISTINCT F0.`opmerkingen`, F1.`opmerkingen` AS `opmerkingen1`
                            FROM `usecase` AS F0, `usecase` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`opmerkingen` <> isect0.`opmerkingen1` AND isect0.`opmerkingen` IS NOT NULL AND isect0.`opmerkingen1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Tekst '.$v[0][0].',Tekst '.$v[0][1].')
reden: \"Artificial explanation: opmerkingen~;opmerkingen |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule61(){
    // No violations should occur in (form~;form |- I)
    //            rule':: form~;form/\-I
    // sqlExprSrc fSpec rule':: form
     $v=DB_doquer('SELECT DISTINCT isect0.`form`, isect0.`form1`
                     FROM 
                        ( SELECT DISTINCT F0.`form`, F1.`form` AS `form1`
                            FROM `usecase` AS F0, `usecase` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`form` <> isect0.`form1` AND isect0.`form` IS NOT NULL AND isect0.`form1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (FormCodes '.$v[0][0].',FormCodes '.$v[0][1].')
reden: \"Artificial explanation: form~;form |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule62(){
    // No violations should occur in (bron~;bron |- I)
    //            rule':: bron~;bron/\-I
    // sqlExprSrc fSpec rule':: bron
     $v=DB_doquer('SELECT DISTINCT isect0.`bron`, isect0.`bron1`
                     FROM 
                        ( SELECT DISTINCT F0.`bron`, F1.`bron` AS `bron1`
                            FROM `usecase` AS F0, `usecase` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`bron` <> isect0.`bron1` AND isect0.`bron` IS NOT NULL AND isect0.`bron1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Referentie '.$v[0][0].',Referentie '.$v[0][1].')
reden: \"Artificial explanation: bron~;bron |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule63(){
    // No violations should occur in (I |- login~;login)
    //            rule':: I/\-(login~;login)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `sessie` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `sessie` AS F0, `sessie` AS F1
                                       WHERE F0.`login`=F1.`login`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Sessie '.$v[0][0].',Sessie '.$v[0][1].')
reden: \"Artificial explanation: I |- login~;login\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule64(){
    // No violations should occur in (login;login~ |- I)
    //            rule':: login;login~/\-I
    // sqlExprSrc fSpec rule':: login
     $v=DB_doquer('SELECT DISTINCT isect0.`login`, isect0.`login1`
                     FROM 
                        ( SELECT DISTINCT F0.`login`, F1.`login` AS `login1`
                            FROM `sessie` AS F0, `sessie` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`login` <> isect0.`login1` AND isect0.`login` IS NOT NULL AND isect0.`login1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Persoon '.$v[0][0].',Persoon '.$v[0][1].')
reden: \"Artificial explanation: login;login~ |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule65(){
    // No violations should occur in (digid~;digid |- I)
    //            rule':: digid~;digid/\-I
    // sqlExprSrc fSpec rule':: digid
     $v=DB_doquer('SELECT DISTINCT isect0.`digid`, isect0.`digid1`
                     FROM 
                        ( SELECT DISTINCT F0.`digid`, F1.`digid` AS `digid1`
                            FROM `sessie` AS F0, `sessie` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`digid` <> isect0.`digid1` AND isect0.`digid` IS NOT NULL AND isect0.`digid1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (DigID '.$v[0][0].',DigID '.$v[0][1].')
reden: \"Artificial explanation: digid~;digid |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule66(){
    // No violations should occur in (I |- digid;digid~)
    //            rule':: I/\-(digid;digid~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `sessie` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `sessie` AS F0, `sessie` AS F1
                                       WHERE F0.`digid`=F1.`digid`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Sessie '.$v[0][0].',Sessie '.$v[0][1].')
reden: \"Artificial explanation: I |- digid;digid~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule67(){
    // No violations should occur in (digid~;digid |- I)
    //            rule':: digid~;digid/\-I
    // sqlExprSrc fSpec rule':: digid
     $v=DB_doquer('SELECT DISTINCT isect0.`digid`, isect0.`digid1`
                     FROM 
                        ( SELECT DISTINCT F0.`digid`, F1.`digid` AS `digid1`
                            FROM `digid` AS F0, `digid` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`digid` <> isect0.`digid1` AND isect0.`digid` IS NOT NULL AND isect0.`digid1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Persoon '.$v[0][0].',Persoon '.$v[0][1].')
reden: \"Artificial explanation: digid~;digid |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule68(){
    // No violations should occur in (I |- digid;digid~)
    //            rule':: I/\-(digid;digid~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `digid` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `digid` AS F0, `digid` AS F1
                                       WHERE F0.`digid`=F1.`digid`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (DigID '.$v[0][0].',DigID '.$v[0][1].')
reden: \"Artificial explanation: I |- digid;digid~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule69(){
    // No violations should occur in (rol~;rol |- I)
    //            rule':: rol~;rol/\-I
    // sqlExprSrc fSpec rule':: rol
     $v=DB_doquer('SELECT DISTINCT isect0.`rol`, isect0.`rol1`
                     FROM 
                        ( SELECT DISTINCT F0.`rol`, F1.`rol` AS `rol1`
                            FROM `sessie` AS F0, `sessie` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`rol` <> isect0.`rol1` AND isect0.`rol` IS NOT NULL AND isect0.`rol1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Rol '.$v[0][0].',Rol '.$v[0][1].')
reden: \"Artificial explanation: rol~;rol |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule70(){
    // No violations should occur in (I |- rol;rol~)
    //            rule':: I/\-(rol;rol~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `sessie` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `sessie` AS F0, `sessie` AS F1
                                       WHERE F0.`rol`=F1.`rol`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Sessie '.$v[0][0].',Sessie '.$v[0][1].')
reden: \"Artificial explanation: I |- rol;rol~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule71(){
    // No violations should occur in (start~;start |- I)
    //            rule':: start~;start/\-I
    // sqlExprSrc fSpec rule':: start
     $v=DB_doquer('SELECT DISTINCT isect0.`start`, isect0.`start1`
                     FROM 
                        ( SELECT DISTINCT F0.`start`, F1.`start` AS `start1`
                            FROM `sessie` AS F0, `sessie` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`start` <> isect0.`start1` AND isect0.`start` IS NOT NULL AND isect0.`start1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Tijdstip '.$v[0][0].',Tijdstip '.$v[0][1].')
reden: \"Artificial explanation: start~;start |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule72(){
    // No violations should occur in (I |- start;start~)
    //            rule':: I/\-(start;start~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `sessie` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `sessie` AS F0, `sessie` AS F1
                                       WHERE F0.`start`=F1.`start`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Sessie '.$v[0][0].',Sessie '.$v[0][1].')
reden: \"Artificial explanation: I |- start;start~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule73(){
    // No violations should occur in (einde~;einde |- I)
    //            rule':: einde~;einde/\-I
    // sqlExprSrc fSpec rule':: einde
     $v=DB_doquer('SELECT DISTINCT isect0.`einde`, isect0.`einde1`
                     FROM 
                        ( SELECT DISTINCT F0.`einde`, F1.`einde` AS `einde1`
                            FROM `sessie` AS F0, `sessie` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`einde` <> isect0.`einde1` AND isect0.`einde` IS NOT NULL AND isect0.`einde1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Tijdstip '.$v[0][0].',Tijdstip '.$v[0][1].')
reden: \"Artificial explanation: einde~;einde |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule74(){
    // No violations should occur in (van~;van |- I)
    //            rule':: van~;van/\-I
    // sqlExprSrc fSpec rule':: van
     $v=DB_doquer('SELECT DISTINCT isect0.`van`, isect0.`van1`
                     FROM 
                        ( SELECT DISTINCT F0.`van`, F1.`van` AS `van1`
                            FROM `document` AS F0, `document` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`van` <> isect0.`van1` AND isect0.`van` IS NOT NULL AND isect0.`van1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Persoon '.$v[0][0].',Persoon '.$v[0][1].')
reden: \"Artificial explanation: van~;van |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule75(){
    // No violations should occur in (I |- van;van~)
    //            rule':: I/\-(van;van~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `document` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `document` AS F0, `document` AS F1
                                       WHERE F0.`van`=F1.`van`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Document '.$v[0][0].',Document '.$v[0][1].')
reden: \"Artificial explanation: I |- van;van~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule76(){
    // No violations should occur in (I |- aan;aan~)
    //            rule':: I/\-(aan;aan~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `document` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`document`, F1.`document` AS `document1`
                                        FROM `aan` AS F0, `aan` AS F1
                                       WHERE F0.`persoon`=F1.`persoon`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`document` AND isect0.`i`=cp.`document1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Document '.$v[0][0].',Document '.$v[0][1].')
reden: \"Artificial explanation: I |- aan;aan~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule77(){
    // No violations should occur in (verzonden~;verzonden |- I)
    //            rule':: verzonden~;verzonden/\-I
    // sqlExprSrc fSpec rule':: verzonden
     $v=DB_doquer('SELECT DISTINCT isect0.`verzonden`, isect0.`verzonden1`
                     FROM 
                        ( SELECT DISTINCT F0.`verzonden`, F1.`verzonden` AS `verzonden1`
                            FROM `document` AS F0, `document` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`verzonden` <> isect0.`verzonden1` AND isect0.`verzonden` IS NOT NULL AND isect0.`verzonden1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Tijdstip '.$v[0][0].',Tijdstip '.$v[0][1].')
reden: \"Artificial explanation: verzonden~;verzonden |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule78(){
    // No violations should occur in (I |- verzonden;verzonden~)
    //            rule':: I/\-(verzonden;verzonden~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `document` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `document` AS F0, `document` AS F1
                                       WHERE F0.`verzonden`=F1.`verzonden`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Document '.$v[0][0].',Document '.$v[0][1].')
reden: \"Artificial explanation: I |- verzonden;verzonden~\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule79(){
    // No violations should occur in (ontvangen~;ontvangen |- I)
    //            rule':: ontvangen~;ontvangen/\-I
    // sqlExprSrc fSpec rule':: ontvangen
     $v=DB_doquer('SELECT DISTINCT isect0.`ontvangen`, isect0.`ontvangen1`
                     FROM 
                        ( SELECT DISTINCT F0.`ontvangen`, F1.`ontvangen` AS `ontvangen1`
                            FROM `document` AS F0, `document` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`ontvangen` <> isect0.`ontvangen1` AND isect0.`ontvangen` IS NOT NULL AND isect0.`ontvangen1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Tijdstip '.$v[0][0].',Tijdstip '.$v[0][1].')
reden: \"Artificial explanation: ontvangen~;ontvangen |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule80(){
    // No violations should occur in (kenmerkVan~;kenmerkVan |- I)
    //            rule':: kenmerkVan~;kenmerkVan/\-I
    // sqlExprSrc fSpec rule':: kenmerkvan
     $v=DB_doquer('SELECT DISTINCT isect0.`kenmerkvan`, isect0.`kenmerkvan1`
                     FROM 
                        ( SELECT DISTINCT F0.`kenmerkvan`, F1.`kenmerkvan` AS `kenmerkvan1`
                            FROM `document` AS F0, `document` AS F1
                           WHERE F0.`i`=F1.`i`
                        ) AS isect0
                    WHERE isect0.`kenmerkvan` <> isect0.`kenmerkvan1` AND isect0.`kenmerkvan` IS NOT NULL AND isect0.`kenmerkvan1` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Tekst '.$v[0][0].',Tekst '.$v[0][1].')
reden: \"Artificial explanation: kenmerkVan~;kenmerkVan |- I\"<BR>',3);
      return false;
    }return true;
  }
  
  function checkRule81(){
    // No violations should occur in (I |- kenmerkVan;kenmerkVan~)
    //            rule':: I/\-(kenmerkVan;kenmerkVan~)
    // sqlExprSrc fSpec rule':: i
     $v=DB_doquer('SELECT DISTINCT isect0.`i`, isect0.`i` AS `i1`
                     FROM `document` AS isect0
                    WHERE NOT EXISTS (SELECT *
                                 FROM 
                                    ( SELECT DISTINCT F0.`i`, F1.`i` AS `i1`
                                        FROM `document` AS F0, `document` AS F1
                                       WHERE F0.`kenmerkvan`=F1.`kenmerkvan`
                                    ) AS cp
                                WHERE isect0.`i`=cp.`i` AND isect0.`i`=cp.`i1`) AND isect0.`i` IS NOT NULL AND isect0.`i` IS NOT NULL');
     if(count($v)) {
      DB_debug('Overtreding (Document '.$v[0][0].',Document '.$v[0][1].')
reden: \"Artificial explanation: I |- kenmerkVan;kenmerkVan~\"<BR>',3);
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
    checkRule61();
    checkRule62();
    checkRule63();
    checkRule64();
    checkRule65();
    checkRule66();
    checkRule67();
    checkRule68();
    checkRule69();
    checkRule70();
    checkRule71();
    checkRule72();
    checkRule73();
    checkRule74();
    checkRule75();
    checkRule76();
    checkRule77();
    checkRule78();
    checkRule79();
    checkRule80();
    checkRule81();
  }
?>