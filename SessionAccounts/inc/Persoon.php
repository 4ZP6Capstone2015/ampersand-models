<?php // generated with ADL vs. 1.1.0.801
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
  require "Persoon.inc.php";
  require "connectToDataBase.inc.php";
  if(isset($_REQUEST['save'])) { // handle ajax save request (do not show the interface)
    // we posted . characters, but something converts them to _ (HTTP 1.1 standard)
    $r=array();
    foreach($_REQUEST as $i=>$v){
      $r[join('.',explode('_',$i))]=$v; //convert _ back to .
    }
    $Id = @$r['0'];
    $name=array();
    for($i0=0;isset($r['1.'.$i0]);$i0++){
      $name[$i0] = @$r['1.'.$i0.''];
    }
    $emailaddr=array();
    for($i0=0;isset($r['2.'.$i0]);$i0++){
      $emailaddr[$i0] = @$r['2.'.$i0.''];
    }
    $telefoonnr=array();
    for($i0=0;isset($r['3.'.$i0]);$i0++){
      $telefoonnr[$i0] = @$r['3.'.$i0.''];
    }
    $Persoon=new Persoon(@$_REQUEST['ID'],$Id, $name, $emailaddr, $telefoonnr);
    if($Persoon->save()!==false) die('ok:'.serviceref($_REQUEST['content']).'&Persoon='.urlencode($Persoon->getId()));
    else die('Please fix errors!');
    exit(); // do not show the interface
  }
  $buttons="";
  if(isset($_REQUEST['new'])) $new=true; else $new=false;
  if(isset($_REQUEST['edit'])||$new) $edit=true; else $edit=false;
  $del=isset($_REQUEST['del']);
  if(isset($_REQUEST['Persoon'])){
    if(!$del || !delPersoon($_REQUEST['Persoon']))
      $Persoon = readPersoon($_REQUEST['Persoon']);
    else $Persoon = false; // delete was a succes!
  } else if($new) $Persoon = new Persoon();
  else $Persoon = false;
  if($Persoon){
    writeHead("<TITLE>Persoon - ctxSessionAccounts - ADL Prototype</TITLE>"
              .($edit?'<SCRIPT type="text/javascript" src="js/edit.js"></SCRIPT>':'').'<SCRIPT type="text/javascript" src="js/navigate.js"></SCRIPT>'."\n" );
    if($edit)
        echo '<FORM name="editForm" action="'.$_SERVER['PHP_SELF'].'" method="POST" class="Edit">';
    if($edit && $Persoon->isNew())
         echo '<P><INPUT TYPE="TEXT" NAME="ID" VALUE="'.addslashes($Persoon->getId()).'" /></P>';
    else echo '<H1>'.$Persoon->getId().'</H1>';
    ?>
    <DIV class="Floater Id">
      <DIV class="FloaterHeader">Id</DIV>
      <DIV class="FloaterContent"><?php
          $Id = $Persoon->get_Id();
          //PICK an existing item0. Creating instances should at most be possible for simple Concepts.
          if(isset($Id)){
            echo '<DIV CLASS="item UI_Id" ID="0">';
          }else{
            echo '<DIV CLASS="new UI_Id" ID="0">';
          }
              if(isset($Id) && $Id!=''){
                echo htmlspecialchars($Id);
              } else {echo '<I>Nothing</I>';}
          echo '</DIV>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater name">
      <DIV class="FloaterHeader">name</DIV>
      <DIV class="FloaterContent"><?php
          $name = $Persoon->get_name();
          echo '
          <UL>';
          foreach($name as $i0=>$idv0){
            $v0=$idv0;
            echo '
            <LI CLASS="item UI_name" ID="1.'.$i0.'">';
          
              if($v0==''){echo '<I>Nothing</I>';}
              else{
              echo htmlspecialchars($v0);
              }
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_name" ID="1.'.count($name).'">enter instance of name</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater email addr">
      <DIV class="FloaterHeader">email addr</DIV>
      <DIV class="FloaterContent"><?php
          $emailaddr = $Persoon->get_emailaddr();
          echo '
          <UL>';
          foreach($emailaddr as $i0=>$idv0){
            $v0=$idv0;
            echo '
            <LI CLASS="item UI_emailaddr" ID="2.'.$i0.'">';
          
              if($v0==''){echo '<I>Nothing</I>';}
              else{
              echo htmlspecialchars($v0);
              }
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_emailaddr" ID="2.'.count($emailaddr).'">enter instance of email addr</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater telefoonnr">
      <DIV class="FloaterHeader">telefoonnr</DIV>
      <DIV class="FloaterContent"><?php
          $telefoonnr = $Persoon->get_telefoonnr();
          echo '
          <UL>';
          foreach($telefoonnr as $i0=>$idv0){
            $v0=$idv0;
            echo '
            <LI CLASS="item UI_telefoonnr" ID="3.'.$i0.'">';
          
              if($v0==''){echo '<I>Nothing</I>';}
              else{
              echo htmlspecialchars($v0);
              }
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_telefoonnr" ID="3.'.count($telefoonnr).'">enter instance of telefoonnr</LI>';
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
       $buttons.=ifaceButton("JavaScript:save('".serviceref($_REQUEST['content'])."&save=1', document.forms[0].ID.value);","Save");
     else { 
       $buttons.=ifaceButton("JavaScript:save('".serviceref($_REQUEST['content'])."&save=1','".urlencode($Persoon->getId())."');","Save");
       $buttons.=ifaceButton(serviceref($_REQUEST['content'],false,false, array('Persoon'=>urlencode($Persoon->getId()) )),"Cancel");
     } 
  } else {
          $buttons.=ifaceButton(serviceref($_REQUEST['content'],false,false, array('Persoon'=>urlencode($Persoon->getId()),'edit'=>1)),"Edit");
          $buttons.=ifaceButton(serviceref($_REQUEST['content'],false,false, array('Persoon'=>urlencode($Persoon->getId()),'del'=>1)),"Delete");
         }
  }else{
    if($del){
      writeHead("<TITLE>Delete geslaagd</TITLE>");
      echo 'The Persoon is deleted';
    }else{  // deze pagina zou onbereikbaar moeten zijn
      writeHead("<TITLE>No Persoon object selected - ctxSessionAccounts - ADL Prototype</TITLE>");
      ?><i>No Persoon object selected</i><?php 
    }
    $buttons.=ifaceButton($_SERVER['PHP_SELF']."?new=1","New");
  }
  writeTail($buttons);
?>