<?php // generated with ADL vs. 0.8.10-547
/**********************\
*                      *
*   Interface V1.3.1   *
*                      *
*                      *
*   Using interfaceDef *
*                      *
\**********************/
  error_reporting(E_ALL); 
  ini_set("display_errors", 1);
  require "interfaceDef.inc.php";
  require "Population.inc.php";
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
    $Population=new Population($ID,$population);
    if($Population->save()!==false) die('ok:'.serviceref($_REQUEST['content']).'&Population='.urlencode($Population->getId())); else die('Please fix errors!');
    exit(); // do not show the interface
  }
  $buttons="";
  if(isset($_REQUEST['new'])) $new=true; else $new=false;
  if(isset($_REQUEST['edit'])||$new) $edit=true; else $edit=false;
  $del=isset($_REQUEST['del']);
  if(isset($_REQUEST['Population'])){
    if(!$del || !delPopulation($_REQUEST['Population']))
      $Population = readPopulation($_REQUEST['Population']);
    else $Population = false; // delete was a succes!
  } else if($new) $Population = new Population();
  else $Population = false;
  if($Population){
    writeHead("<TITLE>Population - Atlas - ADL Prototype</TITLE>"
              .($edit?'<SCRIPT type="text/javascript" src="edit.js"></SCRIPT>':'<SCRIPT type="text/javascript" src="navigate.js"></SCRIPT>')."\n" );
    if($edit)
        echo '<FORM name="editForm" action="'
              .$_SERVER['PHP_SELF'].'" method="POST" class="Edit">';
    if($edit && $Population->isNew())
         echo '<P><INPUT TYPE="TEXT" NAME="ID" VALUE="'.addslashes($Population->getId()).'" /></P>';
    else echo '<H1>'.display('Relation','display',$Population->getId()).'</H1>';
    ?>
    <DIV class="Floater population">
      <DIV class="FloaterHeader">population</DIV>
      <DIV class="FloaterContent"><?php
          $population = $Population->get_population();
          echo '
          <UL>';
          foreach($population as $i0=>$idv0){
            $v0=$idv0;
          
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
       $buttons.=ifaceButton("JavaScript:save('".serviceref($_REQUEST['content'])."&save=1',document.forms[0].ID.value);","Save");
     else { 
       $buttons.=ifaceButton("JavaScript:save('".serviceref($_REQUEST['content'])."&save=1','".urlencode($Population->getId())."');","Save");
       $buttons.=ifaceButton(serviceref($_REQUEST['content'], array('Population'=>urlencode($Population->getId()) )),"Cancel");
     } 
  } else $buttons.=ifaceButton(serviceref($_REQUEST['content'], array('Population'=>urlencode($Population->getId()),'edit'=>1)),"Edit")
                 .ifaceButton(serviceref($_REQUEST['content'], array('Population'=>urlencode($Population->getId()),'del'=>1)),"Delete");
  }else{
    if($del){
      writeHead("<TITLE>Delete geslaagd</TITLE>");
      echo 'The Population is deleted';
    }else{  // deze pagina zou onbereikbaar moeten zijn
      writeHead("<TITLE>No Population object selected - Atlas - ADL Prototype</TITLE>");
      ?><i>No Population object selected</i><?php 
    }
    $buttons.=ifaceButton($_SERVER['PHP_SELF']."?new=1","New");
  }
  writeTail($buttons);
?>