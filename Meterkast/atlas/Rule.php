<?php // generated with ADL vs. 1.1-646
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
  require "Rule.inc.php";
  require "connectToDataBase.inc.php";
  if(isset($_REQUEST['save'])) { // handle ajax save request (do not show the interface)
    $ID=@$_REQUEST['ID'];
    // we posted . characters, but something converts them to _ (HTTP 1.1 standard)
    $r=array();
    foreach($_REQUEST as $i=>$v){
      $r[join('.',explode('_',$i))]=$v; //convert _ back to .
    }
    $source = @$r['0'];
    $target = @$r['1'];
    $overtredingen=array();
    for($i0=0;isset($r['2.'.$i0]);$i0++){
      $overtredingen[$i0] = @$r['2.'.$i0.''];
    }
    $uitleg = @$r['3'];
    $pattern = @$r['4'];
    $Rule=new Rule($ID,$source, $target, $overtredingen, $uitleg, $pattern);
    if($Rule->save()!==false) die('ok:'.serviceref($_REQUEST['content']).'&Rule='.urlencode($Rule->getId())); else die('Please fix errors!');
    exit(); // do not show the interface
  }
  $buttons="";
  if(isset($_REQUEST['new'])) $new=true; else $new=false;
  if(isset($_REQUEST['edit'])||$new) $edit=true; else $edit=false;
  $del=isset($_REQUEST['del']);
  if(isset($_REQUEST['Rule'])){
    if(!$del || !delRule($_REQUEST['Rule']))
      $Rule = readRule($_REQUEST['Rule']);
    else $Rule = false; // delete was a succes!
  } else if($new) $Rule = new Rule();
  else $Rule = false;
  if($Rule){
    writeHead("<TITLE>Rule - Atlas - ADL Prototype</TITLE>"
              .($edit?'<SCRIPT type="text/javascript" src="js/edit.js"></SCRIPT>':'<SCRIPT type="text/javascript" src="js/navigate.js"></SCRIPT>')."\n" );
    if($edit)
        echo '<FORM name="editForm" action="'
              .$_SERVER['PHP_SELF'].'" method="POST" class="Edit">';
    if($edit && $Rule->isNew())
         echo '<P><INPUT TYPE="TEXT" NAME="ID" VALUE="'.addslashes($Rule->getId()).'" /></P>';
    else echo '<H1>'.display('Rule','display',$Rule->getId()).'</H1>';
    ?>
    <DIV class="Floater source">
      <DIV class="FloaterHeader">source</DIV>
      <DIV class="FloaterContent"><?php
          $source = $Rule->get_source();
          echo '<SPAN CLASS="item UI_source" ID="0">';
            $displaysource=display('Concept','display',$source);
          if(!$edit) echo '
          <A HREF="'.serviceref('Concept', array('Concept'=>urlencode($source))).'">'.htmlspecialchars($displaysource).'</A>';
          else echo htmlspecialchars($displaysource);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater target">
      <DIV class="FloaterHeader">target</DIV>
      <DIV class="FloaterContent"><?php
          $target = $Rule->get_target();
          echo '<SPAN CLASS="item UI_target" ID="1">';
            $displaytarget=display('Concept','display',$target);
          if(!$edit) echo '
          <A HREF="'.serviceref('Concept', array('Concept'=>urlencode($target))).'">'.htmlspecialchars($displaytarget).'</A>';
          else echo htmlspecialchars($displaytarget);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater overtredingen">
      <DIV class="FloaterHeader">overtredingen</DIV>
      <DIV class="FloaterContent"><?php
          $overtredingen = $Rule->get_overtredingen();
          echo '
          <UL>';
          foreach($overtredingen as $i0=>$idv0){
            $v0=$idv0;
            echo '
            <LI CLASS="item UI_overtredingen" ID="2.'.$i0.'">';
          
              echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_overtredingen" ID="2.'.count($overtredingen).'">new overtredingen</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater uitleg">
      <DIV class="FloaterHeader">uitleg</DIV>
      <DIV class="FloaterContent"><?php
          $uitleg = $Rule->get_uitleg();
          echo '<SPAN CLASS="item UI_uitleg" ID="3">';
            $uitleg=$uitleg;
          echo htmlspecialchars($uitleg);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater pattern">
      <DIV class="FloaterHeader">pattern</DIV>
      <DIV class="FloaterContent"><?php
          $pattern = $Rule->get_pattern();
          echo '<SPAN CLASS="item UI_pattern" ID="4">';
            $displaypattern=display('Pattern','display',$pattern);
          if(!$edit) echo '
          <A HREF="'.serviceref('Pattern', array('Pattern'=>urlencode($pattern))).'">'.htmlspecialchars($displaypattern).'</A>';
          else echo htmlspecialchars($displaypattern);
          echo '</SPAN>';
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
       $buttons.=ifaceButton("JavaScript:save('".serviceref($_REQUEST['content'])."&save=1','".urlencode($Rule->getId())."');","Save");
       $buttons.=ifaceButton(serviceref($_REQUEST['content'], array('Rule'=>urlencode($Rule->getId()) )),"Cancel");
     } 
  } else {
          $buttons=$buttons;
          $buttons=$buttons;
         }
  }else{
    if($del){
      writeHead("<TITLE>Delete geslaagd</TITLE>");
      echo 'The Rule is deleted';
    }else{  // deze pagina zou onbereikbaar moeten zijn
      writeHead("<TITLE>No Rule object selected - Atlas - ADL Prototype</TITLE>");
      ?><i>No Rule object selected</i><?php 
    }
    $buttons.=ifaceButton($_SERVER['PHP_SELF']."?new=1","New");
  }
  writeTail($buttons);
?>