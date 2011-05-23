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
  require "Magistrate.inc.php";
  require "connectToDataBase.inc.php";
  if(isset($_REQUEST['save'])) { // handle ajax save request (do not show the interface)
    $ID=@$_REQUEST['ID'];
    // we posted . characters, but something converts them to _ (HTTP 1.1 standard)
    $r=array();
    foreach($_REQUEST as $i=>$v){
      $r[join('.',explode('_',$i))]=$v; //convert _ back to .
    }
    $court=array();
    for($i0=0;isset($r['0.'.$i0]);$i0++){
      $court[$i0] = @$r['0.'.$i0.''];
    }
    $panel=array();
    for($i0=0;isset($r['1.'.$i0]);$i0++){
      $panel[$i0] = array( 'id' => @$r['1.'.$i0.'.0']
                         , 'panel' => @$r['1.'.$i0.'.0']
                         , 'sector' => @$r['1.'.$i0.'.1']
                         );
    }
    $role=array();
    for($i0=0;isset($r['2.'.$i0]);$i0++){
      $role[$i0] = @$r['2.'.$i0.''];
    }
    $Sessions=array();
    for($i0=0;isset($r['3.'.$i0]);$i0++){
      $Sessions[$i0] = array( 'id' => @$r['3.'.$i0.'']
                            , 'clerk' => @$r['3.'.$i0.'.1']
                            , 'scheduled' => @$r['3.'.$i0.'.2']
                            , 'city' => @$r['3.'.$i0.'.3']
                            , 'location' => @$r['3.'.$i0.'.4']
                            , 'panel' => array( 'id' => @$r['3.'.$i0.'.5'], 'court' => @$r['3.'.$i0.'.5.0'], 'sector' => @$r['3.'.$i0.'.5.1'])
                            );
      $Sessions[$i0]['judge']=array();
      for($i1=0;isset($r['3.'.$i0.'.0.'.$i1]);$i1++){
        $Sessions[$i0]['judge'][$i1] = @$r['3.'.$i0.'.0.'.$i1.''];
      }
    }
    $received=array();
    for($i0=0;isset($r['4.'.$i0]);$i0++){
      $received[$i0] = array( 'id' => @$r['4.'.$i0.'.0']
                            , 'message' => @$r['4.'.$i0.'.0']
                            , 'from' => @$r['4.'.$i0.'.1']
                            , 'sent' => @$r['4.'.$i0.'.2']
                            , 'received' => @$r['4.'.$i0.'.3']
                            );
    }
    $sent=array();
    for($i0=0;isset($r['5.'.$i0]);$i0++){
      $sent[$i0] = array( 'id' => @$r['5.'.$i0.'.0']
                        , 'message' => @$r['5.'.$i0.'.0']
                        , 'from' => @$r['5.'.$i0.'.1']
                        , 'sent' => @$r['5.'.$i0.'.2']
                        );
    }
    $Magistrate=new Magistrate($ID,$court, $panel, $role, $Sessions, $received, $sent);
    if($Magistrate->save()!==false) die('ok:'.$_SERVER['PHP_SELF'].'?Magistrate='.urlencode($Magistrate->getId())); else die('');
    exit(); // do not show the interface
  }
  $buttons="";
  if(isset($_REQUEST['new'])) $new=true; else $new=false;
  if(isset($_REQUEST['edit'])||$new) $edit=true; else $edit=false;
  $del=isset($_REQUEST['del']);
  if(isset($_REQUEST['Magistrate'])){
    if(!$del || !delMagistrate($_REQUEST['Magistrate']))
      $Magistrate = readMagistrate($_REQUEST['Magistrate']);
    else $Magistrate = false; // delete was a succes!
  } else if($new) $Magistrate = new Magistrate();
  else $Magistrate = false;
  if($Magistrate){
    writeHead("<TITLE>Magistrate - VIRO - ADL Prototype</TITLE>"
              .($edit?'<SCRIPT type="text/javascript" src="edit.js"></SCRIPT>':'<SCRIPT type="text/javascript" src="navigate.js"></SCRIPT>')."\n" );
    if($edit)
        echo '<FORM name="editForm" action="'
              .$_SERVER['PHP_SELF'].'" method="POST" class="Edit">';
    if($edit && $Magistrate->isNew())
         echo '<P><INPUT TYPE="TEXT" NAME="ID" VALUE="'.addslashes($Magistrate->getId()).'" /></P>';
    else echo '<H1>'.$Magistrate->getId().'</H1>';
    ?>
    <DIV class="Floater court">
      <DIV class="FloaterHeader">court</DIV>
      <DIV class="FloaterContent"><?php
          $court = $Magistrate->get_court();
          echo '
          <UL>';
          foreach($court as $i0=>$v0){
            echo '
            <LI CLASS="item UI_court" ID="0.'.$i0.'">';
              if(!$edit) echo '
              <A HREF="Court.php?Court='.urlencode($v0).'">'.htmlspecialchars($v0).'</A>';
              else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_court" ID="0.'.count($court).'">new court</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater panel">
      <DIV class="FloaterHeader">panel</DIV>
      <DIV class="FloaterContent"><?php
          $panel = $Magistrate->get_panel();
          echo '
          <UL>';
          foreach($panel as $i0=>$v0){
            echo '
            <LI CLASS="item UI_panel" ID="1.'.$i0.'">';
              if(!$edit){
                echo '
              <A HREF="Panel.php?Panel='.urlencode($v0['id']).'">';
                echo '<DIV class="GotoArrow">&rarr;</DIV></A>';
              }
              echo '
              <DIV>';
                echo 'panel: ';
                echo '<SPAN CLASS="item UI_panel_panel" ID="1.'.$i0.'.0">';
                echo htmlspecialchars($v0['panel']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'sector: ';
                echo '<SPAN CLASS="item UI_panel_sector" ID="1.'.$i0.'.1">';
                if(!$edit) echo '
                <A HREF="Sector.php?Sector='.urlencode($v0['sector']).'">'.htmlspecialchars($v0['sector']).'</A>';
                else echo htmlspecialchars($v0['sector']);
                echo '</SPAN>';
              echo '
              </DIV>';
              if($edit) echo '
              <INPUT TYPE="hidden" name="1.'.$i0.'.ID" VALUE="'.$v0['id'].'" />';
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_panel" ID="1.'.count($panel).'">new panel</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php if($edit){ ?>
    <SCRIPT type="text/javascript">
      // code for editing blocks in panel
      function UI_panel(id){
        return '<DIV>panel: <SPAN CLASS="item UI_panel_panel" ID="'+id+'.0"></SPAN></DIV>'
             + '<DIV>sector: <SPAN CLASS="item UI_panel_sector" ID="'+id+'.1"></SPAN></DIV>'
              ;
      }
    </SCRIPT>
    <?php } ?>
    <DIV class="Floater role">
      <DIV class="FloaterHeader">role</DIV>
      <DIV class="FloaterContent"><?php
          $role = $Magistrate->get_role();
          echo '
          <UL>';
          foreach($role as $i0=>$v0){
            echo '
            <LI CLASS="item UI_role" ID="2.'.$i0.'">';
              echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_role" ID="2.'.count($role).'">new role</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater Sessions">
      <DIV class="FloaterHeader">Sessions</DIV>
      <DIV class="FloaterContent"><?php
          $Sessions = $Magistrate->get_Sessions();
          echo '
          <UL>';
          foreach($Sessions as $i0=>$v0){
            echo '
            <LI CLASS="item UI_Sessions" ID="3.'.$i0.'">';
              if(!$edit){
                echo '
              <A HREF="Session.php?Session='.urlencode($v0['id']).'">';
                echo '<DIV class="GotoArrow">&rarr;</DIV></A>';
              }
              echo '
              <DIV>';
                echo 'judge: ';
                echo '
                <UL>';
                foreach($v0['judge'] as $i1=>$judge){
                  echo '
                  <LI CLASS="item UI_Sessions_judge" ID="3.'.$i0.'.0.'.$i1.'">';
                    if(!$edit){
                      echo '
                    <A class="GotoLink" id="To3.'.$i0.'.0.'.$i1.'">';
                      echo htmlspecialchars($judge).'</A>';
                      echo '<DIV class="Goto" id="GoTo3.'.$i0.'.0.'.$i1.'"><UL>';
                      echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($judge).'">Magistrate</A></LI>';
                      echo '<LI><A HREF="Party.php?Party='.urlencode($judge).'">Party</A></LI>';
                      echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($judge).'">InterestedParty</A></LI>';
                      echo '</UL></DIV>';
                    } else echo htmlspecialchars($judge);
                  echo '</LI>';
                }
                if($edit) echo '
                  <LI CLASS="new UI_Sessions_judge" ID="3.'.$i0.'.0.'.count($v0['judge']).'">new judge</LI>';
                echo '
                </UL>';
              echo '</DIV>
              <DIV>';
                echo 'clerk: ';
                echo '<SPAN CLASS="item UI_Sessions_clerk" ID="3.'.$i0.'.1">';
                if(!$edit){
                  echo '
                <A class="GotoLink" id="To3.'.$i0.'.1">';
                  echo htmlspecialchars($v0['clerk']).'</A>';
                  echo '<DIV class="Goto" id="GoTo3.'.$i0.'.1"><UL>';
                  echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0['clerk']).'">Magistrate</A></LI>';
                  echo '<LI><A HREF="Party.php?Party='.urlencode($v0['clerk']).'">Party</A></LI>';
                  echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($v0['clerk']).'">InterestedParty</A></LI>';
                  echo '</UL></DIV>';
                } else echo htmlspecialchars($v0['clerk']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'scheduled: ';
                echo '<SPAN CLASS="item UI_Sessions_scheduled" ID="3.'.$i0.'.2">';
                echo htmlspecialchars($v0['scheduled']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'city: ';
                echo '<SPAN CLASS="item UI_Sessions_city" ID="3.'.$i0.'.3">';
                if(!$edit) echo '
                <A HREF="City.php?City='.urlencode($v0['city']).'">'.htmlspecialchars($v0['city']).'</A>';
                else echo htmlspecialchars($v0['city']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'location: ';
                echo '<SPAN CLASS="item UI_Sessions_location" ID="3.'.$i0.'.4">';
                if(!$edit) echo '
                <A HREF="Court.php?Court='.urlencode($v0['location']).'">'.htmlspecialchars($v0['location']).'</A>';
                else echo htmlspecialchars($v0['location']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                ?> 
                <DIV class ="Holder"><DIV class="HolderHeader">panel</DIV>
                  <DIV class="HolderContent" name="panel"><?php
                      echo '<DIV CLASS="UI_Sessions_panel" ID="3.'.$i0.'.5">';
                        if(!$edit){
                          echo '
                        <A HREF="Panel.php?Panel='.urlencode($v0['panel']['id']).'">';
                          echo '<DIV class="GotoArrow">&rarr;</DIV></A>';
                        }
                        echo '
                        <DIV>';
                          echo 'court: ';
                          echo '<SPAN CLASS="item UI_Sessions_panel_court" ID="3.'.$i0.'.5.0">';
                          if(!$edit) echo '
                          <A HREF="Court.php?Court='.urlencode($v0['panel']['court']).'">'.htmlspecialchars($v0['panel']['court']).'</A>';
                          else echo htmlspecialchars($v0['panel']['court']);
                          echo '</SPAN>';
                        echo '</DIV>
                        <DIV>';
                          echo 'sector: ';
                          echo '<SPAN CLASS="item UI_Sessions_panel_sector" ID="3.'.$i0.'.5.1">';
                          if(!$edit) echo '
                          <A HREF="Sector.php?Sector='.urlencode($v0['panel']['sector']).'">'.htmlspecialchars($v0['panel']['sector']).'</A>';
                          else echo htmlspecialchars($v0['panel']['sector']);
                          echo '</SPAN>';
                        echo '
                        </DIV>';
                        if($edit) echo '
                        <INPUT TYPE="hidden" name="3.'.$i0.'.5.ID" VALUE="'.$v0['panel']['id'].'" />';
                      echo '</DIV>';
                    ?> 
                  </DIV>
                </DIV>
                <?php
              echo '
              </DIV>';
              if($edit) echo '
              <INPUT TYPE="hidden" name="3.'.$i0.'.ID" VALUE="'.$v0['id'].'" />';
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_Sessions" ID="3.'.count($Sessions).'">new Sessions</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php if($edit){ ?>
    <SCRIPT type="text/javascript">
      // code for editing blocks in Sessions
      function UI_Sessions(id){
        return '<DIV>judge: <UL><LI CLASS="new UI_Sessions_judge" ID="'+id+'.0">new judge</LI></UL></DIV>'
             + '<DIV>clerk: <SPAN CLASS="item UI_Sessions_clerk" ID="'+id+'.1"></SPAN></DIV>'
             + '<DIV>scheduled: <SPAN CLASS="item UI_Sessions_scheduled" ID="'+id+'.2"></SPAN></DIV>'
             + '<DIV>city: <SPAN CLASS="item UI_Sessions_city" ID="'+id+'.3"></SPAN></DIV>'
             + '<DIV>location: <SPAN CLASS="item UI_Sessions_location" ID="'+id+'.4"></SPAN></DIV>'
             + '<DIV>panel: <SPAN CLASS="item UI_Sessions_panel" ID="'+id+'.5"><DIV>court: <SPAN CLASS="item UI_Sessions_panel_court" ID="'+id+'.50"></SPAN></DIV><DIV>sector: <SPAN CLASS="item UI_Sessions_panel_sector" ID="'+id+'.51"></SPAN></DIV></SPAN></DIV>'
              ;
      }
    </SCRIPT>
    <?php } ?>
    <DIV class="Floater received">
      <DIV class="FloaterHeader">received</DIV>
      <DIV class="FloaterContent"><?php
          $received = $Magistrate->get_received();
          echo '
          <UL>';
          foreach($received as $i0=>$v0){
            echo '
            <LI CLASS="item UI_received" ID="4.'.$i0.'">';
              if(!$edit){
                echo '
              <DIV class="GotoArrow" id="To4.'.$i0.'">&rArr;</DIV>';
                echo '<DIV class="Goto" id="GoTo4.'.$i0.'"><UL>';
                echo '<LI><A HREF="Letter.php?Letter='.urlencode($v0['id']).'">Letter</A></LI>';
                echo '<LI><A HREF="Document.php?Document='.urlencode($v0['id']).'">Document</A></LI>';
                echo '</UL></DIV>';
              }
              echo '
              <DIV>';
                echo 'message: ';
                echo '<SPAN CLASS="item UI_received_message" ID="4.'.$i0.'.0">';
                echo htmlspecialchars($v0['message']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'from: ';
                echo '<SPAN CLASS="item UI_received_from" ID="4.'.$i0.'.1">';
                if(!$edit){
                  echo '
                <A class="GotoLink" id="To4.'.$i0.'.1">';
                  echo htmlspecialchars($v0['from']).'</A>';
                  echo '<DIV class="Goto" id="GoTo4.'.$i0.'.1"><UL>';
                  echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0['from']).'">Magistrate</A></LI>';
                  echo '<LI><A HREF="Party.php?Party='.urlencode($v0['from']).'">Party</A></LI>';
                  echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($v0['from']).'">InterestedParty</A></LI>';
                  echo '</UL></DIV>';
                } else echo htmlspecialchars($v0['from']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'sent: ';
                echo '<SPAN CLASS="item UI_received_sent" ID="4.'.$i0.'.2">';
                echo htmlspecialchars($v0['sent']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'received: ';
                echo '<SPAN CLASS="item UI_received_received" ID="4.'.$i0.'.3">';
                echo htmlspecialchars($v0['received']);
                echo '</SPAN>';
              echo '
              </DIV>';
              if($edit) echo '
              <INPUT TYPE="hidden" name="4.'.$i0.'.ID" VALUE="'.$v0['id'].'" />';
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_received" ID="4.'.count($received).'">new received</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php if($edit){ ?>
    <SCRIPT type="text/javascript">
      // code for editing blocks in received
      function UI_received(id){
        return '<DIV>message: <SPAN CLASS="item UI_received_message" ID="'+id+'.0"></SPAN></DIV>'
             + '<DIV>from: <SPAN CLASS="item UI_received_from" ID="'+id+'.1"></SPAN></DIV>'
             + '<DIV>sent: <SPAN CLASS="item UI_received_sent" ID="'+id+'.2"></SPAN></DIV>'
             + '<DIV>received: <SPAN CLASS="item UI_received_received" ID="'+id+'.3"></SPAN></DIV>'
              ;
      }
    </SCRIPT>
    <?php } ?>
    <DIV class="Floater sent">
      <DIV class="FloaterHeader">sent</DIV>
      <DIV class="FloaterContent"><?php
          $sent = $Magistrate->get_sent();
          echo '
          <UL>';
          foreach($sent as $i0=>$v0){
            echo '
            <LI CLASS="item UI_sent" ID="5.'.$i0.'">';
              if(!$edit){
                echo '
              <DIV class="GotoArrow" id="To5.'.$i0.'">&rArr;</DIV>';
                echo '<DIV class="Goto" id="GoTo5.'.$i0.'"><UL>';
                echo '<LI><A HREF="Letter.php?Letter='.urlencode($v0['id']).'">Letter</A></LI>';
                echo '<LI><A HREF="Document.php?Document='.urlencode($v0['id']).'">Document</A></LI>';
                echo '</UL></DIV>';
              }
              echo '
              <DIV>';
                echo 'message: ';
                echo '<SPAN CLASS="item UI_sent_message" ID="5.'.$i0.'.0">';
                echo htmlspecialchars($v0['message']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'from: ';
                echo '<SPAN CLASS="item UI_sent_from" ID="5.'.$i0.'.1">';
                if(!$edit){
                  echo '
                <A class="GotoLink" id="To5.'.$i0.'.1">';
                  echo htmlspecialchars($v0['from']).'</A>';
                  echo '<DIV class="Goto" id="GoTo5.'.$i0.'.1"><UL>';
                  echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0['from']).'">Magistrate</A></LI>';
                  echo '<LI><A HREF="Party.php?Party='.urlencode($v0['from']).'">Party</A></LI>';
                  echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($v0['from']).'">InterestedParty</A></LI>';
                  echo '</UL></DIV>';
                } else echo htmlspecialchars($v0['from']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'sent: ';
                echo '<SPAN CLASS="item UI_sent_sent" ID="5.'.$i0.'.2">';
                echo htmlspecialchars($v0['sent']);
                echo '</SPAN>';
              echo '
              </DIV>';
              if($edit) echo '
              <INPUT TYPE="hidden" name="5.'.$i0.'.ID" VALUE="'.$v0['id'].'" />';
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_sent" ID="5.'.count($sent).'">new sent</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php if($edit){ ?>
    <SCRIPT type="text/javascript">
      // code for editing blocks in sent
      function UI_sent(id){
        return '<DIV>message: <SPAN CLASS="item UI_sent_message" ID="'+id+'.0"></SPAN></DIV>'
             + '<DIV>from: <SPAN CLASS="item UI_sent_from" ID="'+id+'.1"></SPAN></DIV>'
             + '<DIV>sent: <SPAN CLASS="item UI_sent_sent" ID="'+id+'.2"></SPAN></DIV>'
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
       $buttons.=ifaceButton("JavaScript:save('".$_SERVER['PHP_SELF']."?save=1','".urlencode($Magistrate->getId())."');","Save");
       $buttons.=ifaceButton($_SERVER['PHP_SELF']."?Magistrate=".urlencode($Magistrate->getId()),"Cancel");
     } 
  } else $buttons.=ifaceButton($_SERVER['PHP_SELF']."?edit=1&Magistrate=".urlencode($Magistrate->getId()),"Edit")
                 .ifaceButton($_SERVER['PHP_SELF']."?del=1&Magistrate=".urlencode($Magistrate->getId()),"Delete");
  }else{
    if($del){
      writeHead("<TITLE>Delete geslaagd</TITLE>");
      echo 'The Magistrate is deleted';
    }else{  // deze pagina zou onbereikbaar moeten zijn
      writeHead("<TITLE>No Magistrate object selected - VIRO - ADL Prototype</TITLE>");
      ?><i>No Magistrate object selected</i><?php 
    }
    $buttons.=ifaceButton($_SERVER['PHP_SELF']."?new=1","New");
  }
  writeTail($buttons);
?>