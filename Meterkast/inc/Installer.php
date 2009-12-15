<?php
  // Try to connect to the database

  if(isset($DB_host)&&!isset($_REQUEST['DB_host'])){
    $included = true; // this means user/pass are probably correct
    $DB_link = @mysql_connect(@$DB_host,@$DB_user,@$DB_pass);
  }else{
    $included = false; // get user/pass elsewhere
    if(file_exists("dbsettings.php")) include "dbsettings.php";
    else { // no settings found.. try some default settings
      if(!( $DB_link=@mysql_connect($DB_host='localhost',$DB_user='root',$DB_pass='')))
      { // we still have no working settings.. ask the user!
        die("Install failed: cannot connect to MySQL"); // todo
      }
    } 
  }
  if($DB_slct = @mysql_select_db('meterkast')){
    $existing=true;
  }else{
    $existing = false; // db does not exist, so try to create it
    @mysql_query("CREATE DATABASE `meterkast` DEFAULT CHARACTER SET latin1 COLLATE latin1_bin");
    $DB_slct = @mysql_select_db('meterkast');
  }
  if(!$DB_slct){
    echo die("Install failed: cannot connect to MySQL or error selecting database");
  }else{
    if(!$included && !file_exists("dbsettings.php")){ // we have a link now; try to write the dbsettings.php file
       if($fh = @fopen("dbsettings.php", 'w')){
         fwrite($fh, '<'.'?php $DB_link=mysql_connect($DB_host="'.$DB_host.'", $DB_user="'.$DB_user.'", $DB_pass="'.$DB_pass.'"); $DB_debug = 3; ?'.'>');
         fclose($fh);
       }else die('<P>Error: could not write dbsettings.php, make sure that the directory of Installer.php is writable
                  or create dbsettings.php in the same directory as Installer.php
                  and paste the following code into it:</P><code>'.
                 '&lt;'.'?php $DB_link=mysql_connect($DB_host="'.$DB_host.'", $DB_user="'.$DB_user.'", $DB_pass="'.$DB_pass.'"); $DB_debug = 3; ?'.'&gt;</code>');
    }

    $error=false;
    /*** Create new SQL tables ***/
    //// Number of plugs: 7
    if($existing==true){
      if($columns = mysql_query("SHOW COLUMNS FROM `operationtbl`")){
        mysql_query("DROP TABLE `operationtbl`");
      }
      if($columns = mysql_query("SHOW COLUMNS FROM `actietbl`")){
        mysql_query("DROP TABLE `actietbl`");
      }
      if($columns = mysql_query("SHOW COLUMNS FROM `bestandtbl`")){
        mysql_query("DROP TABLE `bestandtbl`");
      }
      if($columns = mysql_query("SHOW COLUMNS FROM `sessietbl`")){
        mysql_query("DROP TABLE `sessietbl`");
      }
      if($columns = mysql_query("SHOW COLUMNS FROM `text`")){
        mysql_query("DROP TABLE `text`");
      }
      if($columns = mysql_query("SHOW COLUMNS FROM `outputtype`")){
        mysql_query("DROP TABLE `outputtype`");
      }
      if($columns = mysql_query("SHOW COLUMNS FROM `id`")){
        mysql_query("DROP TABLE `id`");
      }
    }
    /**************************************\
    * Plug operationtbl                    *
    *                                      *
    * fields:                              *
    * I  [INJ,SUR,UNI,TOT,SYM,ASY,TRN,RFX] *
    * name  [INJ,UNI,TOT]                  *
    * call  [UNI,TOT]                      *
    * outputtype  [UNI,TOT]                *
    \**************************************/
    mysql_query("CREATE TABLE `operationtbl`
                     ( `id` INT AUTO_INCREMENT NOT NULL
                     , `name` VARCHAR(255) NOT NULL
                     , `call` VARCHAR(255) NOT NULL
                     , `outputtype` VARCHAR(255) NOT NULL
                     , UNIQUE KEY (`id`)
                     , UNIQUE KEY (`name`)
                      ) TYPE=InnoDB DEFAULT CHARACTER SET latin1 COLLATE latin1_bin");
    if($err=mysql_error()) { $error=true; echo $err.'<br />'; }
    /**************************************\
    * Plug actietbl                        *
    *                                      *
    * fields:                              *
    * I  [INJ,SUR,UNI,TOT,SYM,ASY,TRN,RFX] *
    * object  [UNI,TOT]                    *
    * type  [UNI,TOT]                      *
    \**************************************/
    mysql_query("CREATE TABLE `actietbl`
                     ( `id` INT AUTO_INCREMENT NOT NULL
                     , `object` INT NOT NULL
                     , `type` INT NOT NULL
                     , UNIQUE KEY (`id`)
                      ) TYPE=InnoDB DEFAULT CHARACTER SET latin1 COLLATE latin1_bin");
    if($err=mysql_error()) { $error=true; echo $err.'<br />'; }
    /**************************************\
    * Plug bestandtbl                      *
    *                                      *
    * fields:                              *
    * I  [INJ,SUR,UNI,TOT,SYM,ASY,TRN,RFX] *
    * path  [UNI,TOT]                      *
    \**************************************/
    mysql_query("CREATE TABLE `bestandtbl`
                     ( `id` INT AUTO_INCREMENT NOT NULL
                     , `path` VARCHAR(255) NOT NULL
                     , UNIQUE KEY (`id`)
                      ) TYPE=InnoDB DEFAULT CHARACTER SET latin1 COLLATE latin1_bin");
    if($err=mysql_error()) { $error=true; echo $err.'<br />'; }
    /**************************************\
    * Plug sessietbl                       *
    *                                      *
    * fields:                              *
    * I  [INJ,SUR,UNI,TOT,SYM,ASY,TRN,RFX] *
    * ip  [UNI,TOT]                        *
    * session~  [UNI,INJ,SUR]              *
    \**************************************/
    mysql_query("CREATE TABLE `sessietbl`
                     ( `id` VARCHAR(255) NOT NULL
                     , `ip` VARCHAR(255) NOT NULL
                     , `bestand` INT
                     , UNIQUE KEY (`id`)
                      ) TYPE=InnoDB DEFAULT CHARACTER SET latin1 COLLATE latin1_bin");
    if($err=mysql_error()) { $error=true; echo $err.'<br />'; }
    /**************************************\
    * Plug text                            *
    *                                      *
    * fields:                              *
    * I  [INJ,SUR,UNI,TOT,SYM,ASY,TRN,RFX] *
    \**************************************/
    mysql_query("CREATE TABLE `text`
                     ( `i` VARCHAR(255) NOT NULL
                     , UNIQUE KEY (`i`)
                      ) TYPE=InnoDB DEFAULT CHARACTER SET latin1 COLLATE latin1_bin");
    if($err=mysql_error()) { $error=true; echo $err.'<br />'; }
    /**************************************\
    * Plug outputtype                      *
    *                                      *
    * fields:                              *
    * I  [INJ,SUR,UNI,TOT,SYM,ASY,TRN,RFX] *
    \**************************************/
    mysql_query("CREATE TABLE `outputtype`
                     ( `i` VARCHAR(255) NOT NULL
                     , UNIQUE KEY (`i`)
                      ) TYPE=InnoDB DEFAULT CHARACTER SET latin1 COLLATE latin1_bin");
    if($err=mysql_error()) { $error=true; echo $err.'<br />'; }
    else
    mysql_query("INSERT IGNORE INTO `outputtype` (`i` )
                VALUES ('Atlas')
                      , ('Nothing')
                      , ('Local')
                ");
    if($err=mysql_error()) { $error=true; echo $err.'<br />'; }
    /***************\
    * Plug id       *
    *               *
    * fields:       *
    * I/\id;id~  [] *
    * id  [RFX]     *
    \***************/
    mysql_query("CREATE TABLE `id`
                     ( `outputtype` VARCHAR(255)
                     , `outputtype1` VARCHAR(255)
                      ) TYPE=InnoDB DEFAULT CHARACTER SET latin1 COLLATE latin1_bin");
    if($err=mysql_error()) { $error=true; echo $err.'<br />'; }
    else
    mysql_query("INSERT IGNORE INTO `id` (`outputtype` ,`outputtype1` )
                VALUES ('Atlas', 'Atlas')
                      , ('Nothing', 'Nothing')
                      , ('Local', 'Local')
                ");
    if($err=mysql_error()) { $error=true; echo $err.'<br />'; }
    mysql_query('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
  }
?>
