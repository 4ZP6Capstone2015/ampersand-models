<?php // generated with ADL vs. 0.8.10-558
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
  require "UserRule.inc.php";
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
    $relations=array();
    for($i0=0;isset($r['2.'.$i0]);$i0++){
      $relations[$i0] = @$r['2.'.$i0.''];
    }
    $subexpressions=array();
    for($i0=0;isset($r['3.'.$i0]);$i0++){
      $subexpressions[$i0] = @$r['3.'.$i0.''];
    }
    $violations=array();
    for($i0=0;isset($r['4.'.$i0]);$i0++){
      $violations[$i0] = @$r['4.'.$i0.''];
    }
    $explanation = @$r['5'];
    $previous = @$r['6'];
    $next = @$r['7'];
    $pattern = @$r['8'];
    $Conceptualdiagram = @$r['9'];
    $UserRule=new UserRule($ID,$source, $target, $relations, $subexpressions, $violations, $explanation, $previous, $next, $pattern, $Conceptualdiagram);
    if($UserRule->save()!==false) die('ok:'.serviceref($_REQUEST['content']).'&UserRule='.urlencode($UserRule->getId())); else die('Please fix errors!');
    exit(); // do not show the interface
  }
  $buttons="";
  if(isset($_REQUEST['new'])) $new=true; else $new=false;
  if(isset($_REQUEST['edit'])||$new) $edit=true; else $edit=false;
  $del=isset($_REQUEST['del']);
  if(isset($_REQUEST['UserRule'])){
    if(!$del || !delUserRule($_REQUEST['UserRule']))
      $UserRule = readUserRule($_REQUEST['UserRule']);
    else $UserRule = false; // delete was a succes!
  } else if($new) $UserRule = new UserRule();
  else $UserRule = false;
  if($UserRule){
    writeHead("<TITLE>UserRule - Atlas - ADL Prototype</TITLE>"
              .($edit?'<SCRIPT type="text/javascript" src="edit.js"></SCRIPT>':'<SCRIPT type="text/javascript" src="navigate.js"></SCRIPT>')."\n" );
    if($edit)
        echo '<FORM name="editForm" action="'
              .$_SERVER['PHP_SELF'].'" method="POST" class="Edit">';
    if($edit && $UserRule->isNew())
         echo '<P><INPUT TYPE="TEXT" NAME="ID" VALUE="'.addslashes($UserRule->getId()).'" /></P>';
    else echo '<H1>'.display('UserRule','display',$UserRule->getId()).'</H1>';
    ?>
    <DIV class="Floater source">
      <DIV class="FloaterHeader">source</DIV>
      <DIV class="FloaterContent"><?php
          $source = $UserRule->get_source();
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
          $target = $UserRule->get_target();
          echo '<SPAN CLASS="item UI_target" ID="1">';
            $displaytarget=display('Concept','display',$target);
          if(!$edit) echo '
          <A HREF="'.serviceref('Concept', array('Concept'=>urlencode($target))).'">'.htmlspecialchars($displaytarget).'</A>';
          else echo htmlspecialchars($displaytarget);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater relations">
      <DIV class="FloaterHeader">relations</DIV>
      <DIV class="FloaterContent"><?php
          $relations = $UserRule->get_relations();
          echo '
          <UL>';
          foreach($relations as $i0=>$idv0){
            $v0=display('Relation','display',$idv0);
            echo '
            <LI CLASS="item UI_relations" ID="2.'.$i0.'">';
          
              if(!$edit){
                echo '
              <A class="GotoLink" id="To2.'.$i0.'">';
                echo htmlspecialchars($v0).'</A>';
                echo '<DIV class="Goto" id="GoTo2.'.$i0.'"><UL>';
                echo '<LI><A HREF="'.serviceref('RelationDetails', array('RelationDetails'=>urlencode($idv0))).'">RelationDetails</A></LI>';
                echo '<LI><A HREF="'.serviceref('Population', array('Population'=>urlencode($idv0))).'">Population</A></LI>';
                echo '</UL></DIV>';
              } else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_relations" ID="2.'.count($relations).'">new relations</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater subexpressions">
      <DIV class="FloaterHeader">subexpressions</DIV>
      <DIV class="FloaterContent"><?php
          $subexpressions = $UserRule->get_subexpressions();
          echo '
          <UL>';
          foreach($subexpressions as $i0=>$idv0){
            $v0=display('SubExpression','display',$idv0);
            echo '
            <LI CLASS="item UI_subexpressions" ID="3.'.$i0.'">';
          
              if(!$edit) echo '
              <A HREF="'.serviceref('Population2', array('Population2'=>urlencode($idv0))).'">'.htmlspecialchars($v0).'</A>';
              else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_subexpressions" ID="3.'.count($subexpressions).'">new subexpressions</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater violations">
      <DIV class="FloaterHeader">violations</DIV>
      <DIV class="FloaterContent"><?php
          $violations = $UserRule->get_violations();
          echo '
          <UL>';
          foreach($violations as $i0=>$idv0){
            $v0=$idv0;
            echo '
            <LI CLASS="item UI_violations" ID="4.'.$i0.'">';
          
              echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_violations" ID="4.'.count($violations).'">new violations</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater explanation">
      <DIV class="FloaterHeader">explanation</DIV>
      <DIV class="FloaterContent"><?php
          $explanation = $UserRule->get_explanation();
          echo '<SPAN CLASS="item UI_explanation" ID="5">';
            $explanation=$explanation;
          echo htmlspecialchars($explanation);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater previous">
      <DIV class="FloaterHeader">previous</DIV>
      <DIV class="FloaterContent"><?php
          $previous = $UserRule->get_previous();
          echo '<SPAN CLASS="item UI_previous" ID="6">';
            $displayprevious=display('UserRule','display',$previous);
          if(!$edit) echo '
          <A HREF="'.serviceref('UserRule', array('UserRule'=>urlencode($previous))).'">'.htmlspecialchars($displayprevious).'</A>';
          else echo htmlspecialchars($displayprevious);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater next">
      <DIV class="FloaterHeader">next</DIV>
      <DIV class="FloaterContent"><?php
          $next = $UserRule->get_next();
          echo '<SPAN CLASS="item UI_next" ID="7">';
            $displaynext=display('UserRule','display',$next);
          if(!$edit) echo '
          <A HREF="'.serviceref('UserRule', array('UserRule'=>urlencode($next))).'">'.htmlspecialchars($displaynext).'</A>';
          else echo htmlspecialchars($displaynext);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater pattern">
      <DIV class="FloaterHeader">pattern</DIV>
      <DIV class="FloaterContent"><?php
          $pattern = $UserRule->get_pattern();
          echo '<SPAN CLASS="item UI_pattern" ID="8">';
            $displaypattern=display('Pattern','display',$pattern);
          if(!$edit) echo '
          <A HREF="'.serviceref('Pattern', array('Pattern'=>urlencode($pattern))).'">'.htmlspecialchars($displaypattern).'</A>';
          else echo htmlspecialchars($displaypattern);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <?php
          $Conceptualdiagram = $UserRule->get_Conceptualdiagram();
          echo '<IMG src="'.$Conceptualdiagram.'"/>';
        ?> 
    <?php
    if($edit) echo '</FORM>';
   if($del) echo "<P><I>Delete failed</I></P>";
   if($edit){
     if($new) 
       $buttons.=ifaceButton("JavaScript:save('".serviceref($_REQUEST['content'])."&save=1',document.forms[0].ID.value);","Save");
     else { 
       $buttons.=ifaceButton("JavaScript:save('".serviceref($_REQUEST['content'])."&save=1','".urlencode($UserRule->getId())."');","Save");
       $buttons.=ifaceButton(serviceref($_REQUEST['content'], array('UserRule'=>urlencode($UserRule->getId()) )),"Cancel");
     } 
  } else {
          $buttons=$buttons;
          $buttons=$buttons;
         }
  }else{
    if($del){
      writeHead("<TITLE>Delete geslaagd</TITLE>");
      echo 'The UserRule is deleted';
    }else{  // deze pagina zou onbereikbaar moeten zijn
      writeHead("<TITLE>No UserRule object selected - Atlas - ADL Prototype</TITLE>");
      ?><i>No UserRule object selected</i><?php 
    }
    $buttons.=ifaceButton($_SERVER['PHP_SELF']."?new=1","New");
  }
  writeTail($buttons);
?>