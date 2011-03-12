<?php
  // ctxELMtest.php
  // Generated with Prototype vs. 1.1.0.899(core vs. 2.0.0.25)
  // Prototype interface design by Milan van Bruggen and Sebastiaan J.C. Joosten
  
  
  error_reporting(E_ALL); 
  ini_set("display_errors", 1);
  
  $content = $_REQUEST['content'];
  $ctxenv = array();
  include "$content.php";
  
  $myerrors = array();
  //TODO create and destroy transaction (with rules to check before close) in session or something
  function starttransaction(){
     // if(!isset(session transaction)){ //if there is no transaction
       //session transaction = array("check"=>array()); //check=> rules to be checked before commit
       DB_doquer('START TRANSACTION');
     // } //transaction already running
  }
  
  function closetransaction(){
     global $myerrors;
     if(isset($_REQUEST['save'])){ //if there is a transaction to be closed
       $rls=array(); //TODO get rules from transaction and check them somehow
       $check = true;
       foreach ($rls as $rl){
         if ($check) {$check=$rl;}
       }
       if($check){ // all rules are met
         DB_doquer('COMMIT');
         //unset(session transaction);
         return true;
       }
       $myerrors[] = 'There are rule violations';
       DB_doquer('ROLLBACK');
       //unset(session transaction);
       return false;
     } else return true; //there is no transaction
  }
  
  function rollbacktransaction(){
     if(isset($_REQUEST['save'])){ //if there is a transaction to be closed
       DB_doquer('ROLLBACK');
       //unset(session transaction);
     }
  }
  
  ?>