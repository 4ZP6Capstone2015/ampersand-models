<?php // generated with ADL vs. 0.8.10-452
/***************************************\
*                                       *
*   Interface V1.3.1                    *
*   (c) Bas Joosten Jun 2005-Aug 2009   *
*                                       *
*   Using interfaceDef                  *
*                                       *
\***************************************/
  require "interfaceDef.inc.php";
  require "Cluster.inc.php";
  require "connectToDataBase.inc.php";
  if(isset($_REQUEST['save'])) { // handle ajax save request (do not show the interface)
    $ID=@$_REQUEST['ID'];
    // we posted . characters, but something converts them to _ (HTTP 1.1 standard)
    $r=array();
    foreach($_REQUEST as $i=>$v){
      $r[join('.',explode('_',$i))]=$v; //convert _ back to .
    }
    $name = @$r['0'];
    $base=array();
    for($i0=0;isset($r['1.'.$i0]);$i0++){
      $base[$i0] = @$r['1.'.$i0.''];
    }
    $case=array();
    for($i0=0;isset($r['2.'.$i0]);$i0++){
      $case[$i0] = array( 'id' => @$r['2.'.$i0.'']
                        , 'caretaker of case file' => @$r['2.'.$i0.'.0']
                        , 'area of law' => @$r['2.'.$i0.'.1']
                        , 'type of case' => @$r['2.'.$i0.'.2']
                        );
    }
    $Cluster=new Cluster($ID,$name, $base, $case);
    if($Cluster->save()!==false) die('ok:'.$_SERVER['PHP_SELF'].'?Cluster='.urlencode($Cluster->getId())); else die('');
    exit(); // do not show the interface
  }
  $buttons="";
  if(isset($_REQUEST['new'])) $new=true; else $new=false;
  if(isset($_REQUEST['edit'])||$new) $edit=true; else $edit=false;
  $del=isset($_REQUEST['del']);
  if(isset($_REQUEST['Cluster'])){
    if(!$del || !delCluster($_REQUEST['Cluster']))
      $Cluster = readCluster($_REQUEST['Cluster']);
    else $Cluster = false; // delete was a succes!
  } else if($new) $Cluster = new Cluster();
  else $Cluster = false;
  if($Cluster){
    writeHead("<TITLE>Cluster - VIRO - ADL Prototype</TITLE>"
              .($edit?'<SCRIPT type="text/javascript" src="edit.js"></SCRIPT>':'<SCRIPT type="text/javascript" src="navigate.js"></SCRIPT>')."\n" );
    if($edit)
        echo '<FORM name="editForm" action="'
              .$_SERVER['PHP_SELF'].'" method="POST" class="Edit">';
    if($edit && $Cluster->isNew())
         echo '<P><INPUT TYPE="TEXT" NAME="ID" VALUE="'.addslashes($Cluster->getId()).'" /></P>';
    else echo '<H1>'.$Cluster->getId().'</H1>';
    ?>
    <DIV class="Floater name">
      <DIV class="FloaterHeader">name</DIV>
      <DIV class="FloaterContent"><?php
          $name = $Cluster->get_name();
          echo '<SPAN CLASS="item UI_name" ID="0">';
          echo htmlspecialchars($name);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater base">
      <DIV class="FloaterHeader">base</DIV>
      <DIV class="FloaterContent"><?php
          $base = $Cluster->get_base();
          echo '
          <UL>';
          foreach($base as $i0=>$v0){
            echo '
            <LI CLASS="item UI_base" ID="1.'.$i0.'">';
              echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_base" ID="1.'.count($base).'">new base</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater case">
      <DIV class="FloaterHeader">case</DIV>
      <DIV class="FloaterContent"><?php
          $case = $Cluster->get_case();
          echo '
          <UL>';
          foreach($case as $i0=>$v0){
            echo '
            <LI CLASS="item UI_case" ID="2.'.$i0.'">';
              if(!$edit){
                echo '
              <DIV class="GotoArrow" id="To2.'.$i0.'">&rArr;</DIV>';
                echo '<DIV class="Goto" id="GoTo2.'.$i0.'"><UL>';
                echo '<LI><A HREF="CoreDataUC001.php?CoreDataUC001='.urlencode($v0['id']).'">CoreDataUC001</A></LI>';
                echo '<LI><A HREF="LegalCase.php?LegalCase='.urlencode($v0['id']).'">LegalCase</A></LI>';
                echo '<LI><A HREF="newCase.php?newCase='.urlencode($v0['id']).'">newCase</A></LI>';
                echo '</UL></DIV>';
              }
              echo '
              <DIV>';
                echo 'caretaker of case file: ';
                echo '<SPAN CLASS="item UI_case_caretakerofcasefile" ID="2.'.$i0.'.0">';
                if(!$edit) echo '
                <A HREF="Organ.php?Organ='.urlencode($v0['caretaker of case file']).'">'.htmlspecialchars($v0['caretaker of case file']).'</A>';
                else echo htmlspecialchars($v0['caretaker of case file']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'area of law: ';
                echo '<SPAN CLASS="item UI_case_areaoflaw" ID="2.'.$i0.'.1">';
                if(!$edit) echo '
                <A HREF="AreaOfLaw.php?AreaOfLaw='.urlencode($v0['area of law']).'">'.htmlspecialchars($v0['area of law']).'</A>';
                else echo htmlspecialchars($v0['area of law']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'type of case: ';
                echo '<SPAN CLASS="item UI_case_typeofcase" ID="2.'.$i0.'.2">';
                if(!$edit) echo '
                <A HREF="CaseType.php?CaseType='.urlencode($v0['type of case']).'">'.htmlspecialchars($v0['type of case']).'</A>';
                else echo htmlspecialchars($v0['type of case']);
                echo '</SPAN>';
              echo '
              </DIV>';
              if($edit) echo '
              <INPUT TYPE="hidden" name="2.'.$i0.'.ID" VALUE="'.$v0['id'].'" />';
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_case" ID="2.'.count($case).'">new case</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php if($edit){ ?>
    <SCRIPT type="text/javascript">
      // code for editing blocks in case
      function UI_case(id){
        return '<DIV>caretaker of case file: <SPAN CLASS="item UI_case_caretakerofcasefile" ID="'+id+'.0"></SPAN></DIV>'
             + '<DIV>area of law: <SPAN CLASS="item UI_case_areaoflaw" ID="'+id+'.1"></SPAN></DIV>'
             + '<DIV>type of case: <SPAN CLASS="item UI_case_typeofcase" ID="'+id+'.2"></SPAN></DIV>'
              ;
      }
    </SCRIPT>
    <?php } ?>
    <?php
    if($edit) echo '</FORM>';
   if($del) echo "<P><I>Delete failed</I></P>";
   if($edit){
     if($new) 
       $buttons.=ifaceButton("JavaScript:save('".$_SERVER['PHP_SELF']."?save=1',document.forms[0].ID.value);","Save");
     else { 
       $buttons.=ifaceButton("JavaScript:save('".$_SERVER['PHP_SELF']."?save=1','".urlencode($Cluster->getId())."');","Save");
       $buttons.=ifaceButton($_SERVER['PHP_SELF']."?Cluster=".urlencode($Cluster->getId()),"Cancel");
     } 
  } else $buttons.=ifaceButton($_SERVER['PHP_SELF']."?edit=1&Cluster=".urlencode($Cluster->getId()),"Edit")
                 .ifaceButton($_SERVER['PHP_SELF']."?del=1&Cluster=".urlencode($Cluster->getId()),"Delete");
  }else{
    if($del){
      writeHead("<TITLE>Delete geslaagd</TITLE>");
      echo 'The Cluster is deleted';
    }else{  // deze pagina zou onbereikbaar moeten zijn
      writeHead("<TITLE>No Cluster object selected - VIRO - ADL Prototype</TITLE>");
      ?><i>No Cluster object selected</i><?php 
    }
    $buttons.=ifaceButton($_SERVER['PHP_SELF']."?new=1","New");
  }
  writeTail($buttons);
?>