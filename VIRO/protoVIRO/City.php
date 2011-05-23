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
  require "City.inc.php";
  require "connectToDataBase.inc.php";
  if(isset($_REQUEST['save'])) { // handle ajax save request (do not show the interface)
    $ID=@$_REQUEST['ID'];
    // we posted . characters, but something converts them to _ (HTTP 1.1 standard)
    $r=array();
    foreach($_REQUEST as $i=>$v){
      $r[join('.',explode('_',$i))]=$v; //convert _ back to .
    }
    if(@$r['0']!=''){
      $courts = @$r['0'];
    }else $courts=null;
    if(@$r['1']!=''){
      $district = @$r['1'];
    }else $district=null;
    $sessions=array();
    for($i0=0;isset($r['2.'.$i0]);$i0++){
      $sessions[$i0] = array( 'id' => @$r['2.'.$i0.'.0']
                            , 'Session' => @$r['2.'.$i0.'.0']
                            , 'scheduled' => @$r['2.'.$i0.'.2']
                            , 'panel' => @$r['2.'.$i0.'.3']
                            );
      $sessions[$i0]['judge']=array();
      for($i1=0;isset($r['2.'.$i0.'.1.'.$i1]);$i1++){
        $sessions[$i0]['judge'][$i1] = @$r['2.'.$i0.'.1.'.$i1.''];
      }
    }
    $maincity=array();
    for($i0=0;isset($r['3.'.$i0]);$i0++){
      $maincity[$i0] = @$r['3.'.$i0.''];
    }
    $City=new City($ID,$courts, $district, $sessions, $maincity);
    if($City->save()!==false) die('ok:'.$_SERVER['PHP_SELF'].'?City='.urlencode($City->getId())); else die('');
    exit(); // do not show the interface
  }
  $buttons="";
  if(isset($_REQUEST['new'])) $new=true; else $new=false;
  if(isset($_REQUEST['edit'])||$new) $edit=true; else $edit=false;
  $del=isset($_REQUEST['del']);
  if(isset($_REQUEST['City'])){
    if(!$del || !delCity($_REQUEST['City']))
      $City = readCity($_REQUEST['City']);
    else $City = false; // delete was a succes!
  } else if($new) $City = new City();
  else $City = false;
  if($City){
    writeHead("<TITLE>City - VIRO - ADL Prototype</TITLE>"
              .($edit?'<SCRIPT type="text/javascript" src="edit.js"></SCRIPT>':'<SCRIPT type="text/javascript" src="navigate.js"></SCRIPT>')."\n" );
    if($edit)
        echo '<FORM name="editForm" action="'
              .$_SERVER['PHP_SELF'].'" method="POST" class="Edit">';
    if($edit && $City->isNew())
         echo '<P><INPUT TYPE="TEXT" NAME="ID" VALUE="'.addslashes($City->getId()).'" /></P>';
    else echo '<H1>'.$City->getId().'</H1>';
    ?>
    <DIV class="Floater courts">
      <DIV class="FloaterHeader">courts</DIV>
      <DIV class="FloaterContent"><?php
          $courts = $City->get_courts();
          echo '<SPAN CLASS="item UI_courts" ID="0">';
          if(isset($courts)){
            if(!$edit) echo '
            <A HREF="Court.php?Court='.urlencode($courts).'">'.htmlspecialchars($courts).'</A>';
            else echo htmlspecialchars($courts);
          }
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater district">
      <DIV class="FloaterHeader">district</DIV>
      <DIV class="FloaterContent"><?php
          $district = $City->get_district();
          echo '<SPAN CLASS="item UI_district" ID="1">';
          if(isset($district)){
            echo htmlspecialchars($district);
          }
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater sessions">
      <DIV class="FloaterHeader">sessions</DIV>
      <DIV class="FloaterContent"><?php
          $sessions = $City->get_sessions();
          echo '
          <UL>';
          foreach($sessions as $i0=>$v0){
            echo '
            <LI CLASS="item UI_sessions" ID="2.'.$i0.'">';
              if(!$edit){
                echo '
              <A HREF="Session.php?Session='.urlencode($v0['id']).'">';
                echo '<DIV class="GotoArrow">&rarr;</DIV></A>';
              }
              echo '
              <DIV>';
                echo 'Session: ';
                echo '<SPAN CLASS="item UI_sessions_Session" ID="2.'.$i0.'.0">';
                echo htmlspecialchars($v0['Session']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'judge: ';
                echo '
                <UL>';
                foreach($v0['judge'] as $i1=>$judge){
                  echo '
                  <LI CLASS="item UI_sessions_judge" ID="2.'.$i0.'.1.'.$i1.'">';
                    if(!$edit){
                      echo '
                    <A class="GotoLink" id="To2.'.$i0.'.1.'.$i1.'">';
                      echo htmlspecialchars($judge).'</A>';
                      echo '<DIV class="Goto" id="GoTo2.'.$i0.'.1.'.$i1.'"><UL>';
                      echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($judge).'">Magistrate</A></LI>';
                      echo '<LI><A HREF="Party.php?Party='.urlencode($judge).'">Party</A></LI>';
                      echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($judge).'">InterestedParty</A></LI>';
                      echo '</UL></DIV>';
                    } else echo htmlspecialchars($judge);
                  echo '</LI>';
                }
                if($edit) echo '
                  <LI CLASS="new UI_sessions_judge" ID="2.'.$i0.'.1.'.count($v0['judge']).'">new judge</LI>';
                echo '
                </UL>';
              echo '</DIV>
              <DIV>';
                echo 'scheduled: ';
                echo '<SPAN CLASS="item UI_sessions_scheduled" ID="2.'.$i0.'.2">';
                echo htmlspecialchars($v0['scheduled']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'panel: ';
                echo '<SPAN CLASS="item UI_sessions_panel" ID="2.'.$i0.'.3">';
                if(!$edit) echo '
                <A HREF="Panel.php?Panel='.urlencode($v0['panel']).'">'.htmlspecialchars($v0['panel']).'</A>';
                else echo htmlspecialchars($v0['panel']);
                echo '</SPAN>';
              echo '
              </DIV>';
              if($edit) echo '
              <INPUT TYPE="hidden" name="2.'.$i0.'.ID" VALUE="'.$v0['id'].'" />';
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_sessions" ID="2.'.count($sessions).'">new sessions</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php if($edit){ ?>
    <SCRIPT type="text/javascript">
      // code for editing blocks in sessions
      function UI_sessions(id){
        return '<DIV>Session: <SPAN CLASS="item UI_sessions_Session" ID="'+id+'.0"></SPAN></DIV>'
             + '<DIV>judge: <UL><LI CLASS="new UI_sessions_judge" ID="'+id+'.1">new judge</LI></UL></DIV>'
             + '<DIV>scheduled: <SPAN CLASS="item UI_sessions_scheduled" ID="'+id+'.2"></SPAN></DIV>'
             + '<DIV>panel: <SPAN CLASS="item UI_sessions_panel" ID="'+id+'.3"></SPAN></DIV>'
              ;
      }
    </SCRIPT>
    <?php } ?>
    <DIV class="Floater main city">
      <DIV class="FloaterHeader">main city</DIV>
      <DIV class="FloaterContent"><?php
          $maincity = $City->get_maincity();
          echo '
          <UL>';
          foreach($maincity as $i0=>$v0){
            echo '
            <LI CLASS="item UI_maincity" ID="3.'.$i0.'">';
              if(!$edit) echo '
              <A HREF="Court.php?Court='.urlencode($v0).'">'.htmlspecialchars($v0).'</A>';
              else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_maincity" ID="3.'.count($maincity).'">new main city</LI>';
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
       $buttons.=ifaceButton("JavaScript:save('".$_SERVER['PHP_SELF']."?save=1','".urlencode($City->getId())."');","Save");
       $buttons.=ifaceButton($_SERVER['PHP_SELF']."?City=".urlencode($City->getId()),"Cancel");
     } 
  } else $buttons.=ifaceButton($_SERVER['PHP_SELF']."?edit=1&City=".urlencode($City->getId()),"Edit")
                 .ifaceButton($_SERVER['PHP_SELF']."?del=1&City=".urlencode($City->getId()),"Delete");
  }else{
    if($del){
      writeHead("<TITLE>Delete geslaagd</TITLE>");
      echo 'The City is deleted';
    }else{  // deze pagina zou onbereikbaar moeten zijn
      writeHead("<TITLE>No City object selected - VIRO - ADL Prototype</TITLE>");
      ?><i>No City object selected</i><?php 
    }
    $buttons.=ifaceButton($_SERVER['PHP_SELF']."?new=1","New");
  }
  writeTail($buttons);
?>