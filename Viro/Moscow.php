<?php // generated with ADL vs. 0.8.10-451
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
  require "Moscow.inc.php";
  require "connectToDataBase.inc.php";
  if(isset($_REQUEST['save'])) { // handle ajax save request (do not show the interface)
    $ID=@$_REQUEST['ID'];
    // we posted . characters, but something converts them to _ (HTTP 1.1 standard)
    $r=array();
    foreach($_REQUEST as $i=>$v){
      $r[join('.',explode('_',$i))]=$v; //convert _ back to .
    }
    $prioHandeling=array();
    for($i0=0;isset($r['0.'.$i0]);$i0++){
      $prioHandeling[$i0] = array( 'id' => @$r['0.'.$i0.'.0']
                                 , 'Handeling' => @$r['0.'.$i0.'.0']
                                 );
      $prioHandeling[$i0]['door']=array();
      for($i1=0;isset($r['0.'.$i0.'.1.'.$i1]);$i1++){
        $prioHandeling[$i0]['door'][$i1] = @$r['0.'.$i0.'.1.'.$i1.''];
      }
      $prioHandeling[$i0]['usecase']=array();
      for($i1=0;isset($r['0.'.$i0.'.2.'.$i1]);$i1++){
        $prioHandeling[$i0]['usecase'][$i1] = @$r['0.'.$i0.'.2.'.$i1.''];
      }
      $prioHandeling[$i0]['rol']=array();
      for($i1=0;isset($r['0.'.$i0.'.3.'.$i1]);$i1++){
        $prioHandeling[$i0]['rol'][$i1] = @$r['0.'.$i0.'.3.'.$i1.''];
      }
    }
    $Moscow=new Moscow($ID,$prioHandeling);
    if($Moscow->save()!==false) die('ok:'.$_SERVER['PHP_SELF'].'?Moscow='.urlencode($Moscow->getId())); else die('Please fix errors!');
    exit(); // do not show the interface
  }
  $buttons="";
  if(isset($_REQUEST['new'])) $new=true; else $new=false;
  if(isset($_REQUEST['edit'])||$new) $edit=true; else $edit=false;
  $del=isset($_REQUEST['del']);
  if(isset($_REQUEST['Moscow'])){
    if(!$del || !delMoscow($_REQUEST['Moscow']))
      $Moscow = readMoscow($_REQUEST['Moscow']);
    else $Moscow = false; // delete was a succes!
  } else if($new) $Moscow = new Moscow();
  else $Moscow = false;
  if($Moscow){
    writeHead("<TITLE>Moscow - VIRO - ADL Prototype</TITLE>"
              .($edit?'<SCRIPT type="text/javascript" src="edit.js"></SCRIPT>':'<SCRIPT type="text/javascript" src="navigate.js"></SCRIPT>')."\n" );
    if($edit)
        echo '<FORM name="editForm" action="'
              .$_SERVER['PHP_SELF'].'" method="POST" class="Edit">';
    if($edit && $Moscow->isNew())
         echo '<P><INPUT TYPE="TEXT" NAME="ID" VALUE="'.addslashes($Moscow->getId()).'" /></P>';
    else echo '<H1>'.$Moscow->getId().'</H1>';
    ?>
    <DIV class="Floater prio Handeling">
      <DIV class="FloaterHeader">prio Handeling</DIV>
      <DIV class="FloaterContent"><?php
          $prioHandeling = $Moscow->get_prioHandeling();
          echo '
          <UL>';
          foreach($prioHandeling as $i0=>$v0){
            echo '
            <LI CLASS="item UI" ID="0.'.$i0.'">';
              if(!$edit){
                echo '
              <DIV class="GotoArrow" id="To0.'.$i0.'">&rArr;</DIV>';
                echo '<DIV class="Goto" id="GoTo0.'.$i0.'"><UL>';
                echo '<LI><A HREF="HandelingCompact.php?HandelingCompact='.urlencode($v0['id']).'">HandelingCompact</A></LI>';
                echo '<LI><A HREF="Handeling.php?Handeling='.urlencode($v0['id']).'">Handeling</A></LI>';
                echo '</UL></DIV>';
              }
              echo '
              <DIV>';
                echo 'Handeling: ';
                echo '<SPAN CLASS="item UIHandeling" ID="0.'.$i0.'.0">';
                echo htmlspecialchars($v0['Handeling']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'door: ';
                echo '
                <UL>';
                foreach($v0['door'] as $i1=>$door){
                  echo '
                  <LI CLASS="item UIdoor" ID="0.'.$i0.'.1.'.$i1.'">';
                    if(!$edit) echo '
                    <A HREF="Orgaan.php?Orgaan='.urlencode($door).'">'.htmlspecialchars($door).'</A>';
                    else echo htmlspecialchars($door);
                  echo '</LI>';
                }
                if($edit) echo '
                  <LI CLASS="new UIdoor" ID="0.'.$i0.'.1.'.count($v0['door']).'">new door</LI>';
                echo '
                </UL>';
              echo '</DIV>
              <DIV>';
                echo 'usecase: ';
                echo '
                <UL>';
                foreach($v0['usecase'] as $i1=>$usecase){
                  echo '
                  <LI CLASS="item UIusecase" ID="0.'.$i0.'.2.'.$i1.'">';
                    echo htmlspecialchars($usecase);
                  echo '</LI>';
                }
                if($edit) echo '
                  <LI CLASS="new UIusecase" ID="0.'.$i0.'.2.'.count($v0['usecase']).'">new usecase</LI>';
                echo '
                </UL>';
              echo '</DIV>
              <DIV>';
                echo 'rol: ';
                echo '
                <UL>';
                foreach($v0['rol'] as $i1=>$rol){
                  echo '
                  <LI CLASS="item UIrol" ID="0.'.$i0.'.3.'.$i1.'">';
                    if(!$edit) echo '
                    <A HREF="Rol.php?Rol='.urlencode($rol).'">'.htmlspecialchars($rol).'</A>';
                    else echo htmlspecialchars($rol);
                  echo '</LI>';
                }
                if($edit) echo '
                  <LI CLASS="new UIrol" ID="0.'.$i0.'.3.'.count($v0['rol']).'">new rol</LI>';
                echo '
                </UL>';
              echo '
              </DIV>';
              if($edit) echo '
              <INPUT TYPE="hidden" name="0.'.$i0.'.ID" VALUE="'.$v0['id'].'" />';
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI" ID="0.'.count($prioHandeling).'">new prio Handeling</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php if($edit){ ?>
    <SCRIPT type="text/javascript">
      // code for editing blocks in prio Handeling
      function UI(id){
        return '<DIV>Handeling: <SPAN CLASS="item UI_Handeling" ID="'+id+'.0"></SPAN></DIV>'
             + '<DIV>door: <UL><LI CLASS="new UI_door" ID="'+id+'.1">new door</LI></UL></DIV>'
             + '<DIV>usecase: <UL><LI CLASS="new UI_usecase" ID="'+id+'.2">new usecase</LI></UL></DIV>'
             + '<DIV>rol: <UL><LI CLASS="new UI_rol" ID="'+id+'.3">new rol</LI></UL></DIV>'
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
       $buttons.=ifaceButton("JavaScript:save('".$_SERVER['PHP_SELF']."?save=1','".urlencode($Moscow->getId())."');","Save");
       $buttons.=ifaceButton($_SERVER['PHP_SELF']."?Moscow=".urlencode($Moscow->getId()),"Cancel");
     } 
  } else $buttons.=ifaceButton($_SERVER['PHP_SELF']."?edit=1&Moscow=".urlencode($Moscow->getId()),"Edit")
                 .ifaceButton($_SERVER['PHP_SELF']."?del=1&Moscow=".urlencode($Moscow->getId()),"Delete");
  }else{
    if($del){
      writeHead("<TITLE>Delete geslaagd</TITLE>");
      echo 'The Moscow is deleted';
    }else{  // deze pagina zou onbereikbaar moeten zijn
      writeHead("<TITLE>No Moscow object selected - VIRO - ADL Prototype</TITLE>");
      ?><i>No Moscow object selected</i><?php 
    }
    $buttons.=ifaceButton($_SERVER['PHP_SELF']."?new=1","New");
  }
  writeTail($buttons);
?>