<?php // generated with ADL vs. 0.8.10-478
/***************************************\
*                                       *
*   Interface V1.3.1                    *
*   (c) Bas Joosten Jun 2005-Aug 2009   *
*                                       *
*   Using interfaceDef                  *
*                                       *
\***************************************/
  error_reporting(E_ALL); 
  ini_set("display_errors", 1);
  require "interfaceDef.inc.php";
  require "Concept.inc.php";
  require "connectToDataBase.inc.php";
  if(isset($_REQUEST['save'])) { // handle ajax save request (do not show the interface)
    $ID=@$_REQUEST['ID'];
    // we posted . characters, but something converts them to _ (HTTP 1.1 standard)
    $r=array();
    foreach($_REQUEST as $i=>$v){
      $r[join('.',explode('_',$i))]=$v; //convert _ back to .
    }
    $population=array();
    for($i0=0;isset($r['0.'.$i0]);$i0++){
      $population[$i0] = @$r['0.'.$i0.''];
    }
    $Concept=new Concept($ID,$population);
    if($Concept->save()!==false) die('ok:'.$_SERVER['PHP_SELF'].'?Concept='.urlencode($Concept->getId())); else die('Please fix errors!');
    exit(); // do not show the interface
  }
  $buttons="";
  if(isset($_REQUEST['new'])) $new=true; else $new=false;
  if(isset($_REQUEST['edit'])||$new) $edit=true; else $edit=false;
  $del=isset($_REQUEST['del']);
  if(isset($_REQUEST['Concept'])){
    if(!$del || !delConcept($_REQUEST['Concept']))
      $Concept = readConcept($_REQUEST['Concept']);
    else $Concept = false; // delete was a succes!
  } else if($new) $Concept = new Concept();
  else $Concept = false;
  if($Concept){
    writeHead("<TITLE>Concept - Atlas - ADL Prototype</TITLE>"
              .($edit?'<SCRIPT type="text/javascript" src="edit.js"></SCRIPT>':'<SCRIPT type="text/javascript" src="navigate.js"></SCRIPT>')."\n" );
    if($edit)
        echo '<FORM name="editForm" action="'
              .$_SERVER['PHP_SELF'].'" method="POST" class="Edit">';
    if($edit && $Concept->isNew())
         echo '<P><INPUT TYPE="TEXT" NAME="ID" VALUE="'.addslashes($Concept->getId()).'" /></P>';
    else echo '<H1>'.$Concept->getId().'</H1>';
    ?>
    <DIV class="Floater population">
      <DIV class="FloaterHeader">population</DIV>
      <DIV class="FloaterContent"><?php
          $population = $Concept->get_population();
          echo '
          <UL>';
          foreach($population as $i0=>$v0){
            echo '
            <LI CLASS="item UI" ID="0.'.$i0.'">';
              echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI" ID="0.'.count($population).'">new population</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php
    if($edit) echo '</FORM>';
   if($del) echo "<P><I>Delete failed</I></P>";
   if($edit){
     if($new) 
       $buttons.=ifaceButton("JavaScript:save('".$_SERVER['PHP_SELF']."?save=1',document.forms[0].ID.value);","Save");
     else { 
       $buttons.=ifaceButton("JavaScript:save('".$_SERVER['PHP_SELF']."?save=1','".urlencode($Concept->getId())."');","Save");
       $buttons.=ifaceButton($_SERVER['PHP_SELF']."?Concept=".urlencode($Concept->getId()),"Cancel");
     } 
  } else $buttons.=ifaceButton($_SERVER['PHP_SELF']."?edit=1&Concept=".urlencode($Concept->getId()),"Edit")
                 .ifaceButton($_SERVER['PHP_SELF']."?del=1&Concept=".urlencode($Concept->getId()),"Delete");
  }else{
    if($del){
      writeHead("<TITLE>Delete geslaagd</TITLE>");
      echo 'The Concept is deleted';
    }else{  // deze pagina zou onbereikbaar moeten zijn
      writeHead("<TITLE>No Concept object selected - Atlas - ADL Prototype</TITLE>");
      ?><i>No Concept object selected</i><?php 
    }
    $buttons.=ifaceButton($_SERVER['PHP_SELF']."?new=1","New");
  }
  writeTail($buttons);
?>