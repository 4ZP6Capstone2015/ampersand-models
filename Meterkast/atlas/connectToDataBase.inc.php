<?php // generated with ADL vs. 0.8.10-547
  require "dbsettings.php";
  
  function display($tbl,$col,$id){
     return firstRow(firstCol(DB_doquer("SELECT DISTINCT `".$col."` FROM `".$tbl."` WHERE `i`='".addslashes($id)."'")));
  }
  
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
  $DB_slct = mysql_select_db('Atlas',$DB_link);
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
  
  
  if($DB_debug>=3){
  }
?>