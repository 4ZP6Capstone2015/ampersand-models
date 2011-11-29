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
  require "Court.inc.php";
  require "connectToDataBase.inc.php";
  if(isset($_REQUEST['save'])) { // handle ajax save request (do not show the interface)
    $ID=@$_REQUEST['ID'];
    // we posted . characters, but something converts them to _ (HTTP 1.1 standard)
    $r=array();
    foreach($_REQUEST as $i=>$v){
      $r[join('.',explode('_',$i))]=$v; //convert _ back to .
    }
    $Sessions=array();
    for($i0=0;isset($r['0.'.$i0]);$i0++){
      $Sessions[$i0] = array( 'id' => @$r['0.'.$i0.'.0']
                            , 'nr' => @$r['0.'.$i0.'.0']
                            , 'panel' => @$r['0.'.$i0.'.1']
                            , 'clerk' => @$r['0.'.$i0.'.3']
                            , 'scheduled' => @$r['0.'.$i0.'.4']
                            );
      $Sessions[$i0]['judge']=array();
      for($i1=0;isset($r['0.'.$i0.'.2.'.$i1]);$i1++){
        $Sessions[$i0]['judge'][$i1] = @$r['0.'.$i0.'.2.'.$i1.''];
      }
      $Sessions[$i0]['schedule']=array();
      for($i1=0;isset($r['0.'.$i0.'.5.'.$i1]);$i1++){
        $Sessions[$i0]['schedule'][$i1] = array( 'id' => @$r['0.'.$i0.'.5.'.$i1.'.0']
                                               , 'nr' => @$r['0.'.$i0.'.5.'.$i1.'.0']
                                               , 'case' => @$r['0.'.$i0.'.5.'.$i1.'.1']
                                               );
        $Sessions[$i0]['schedule'][$i1]['type of case']=array();
        for($i2=0;isset($r['0.'.$i0.'.5.'.$i1.'.2.'.$i2]);$i2++){
          $Sessions[$i0]['schedule'][$i1]['type of case'][$i2] = @$r['0.'.$i0.'.5.'.$i1.'.2.'.$i2.''];
        }
      }
    }
    $panels=array();
    for($i0=0;isset($r['1.'.$i0]);$i0++){
      $panels[$i0] = @$r['1.'.$i0.''];
    }
    $members=array();
    for($i0=0;isset($r['2.'.$i0]);$i0++){
      $members[$i0] = @$r['2.'.$i0.''];
    }
    $cases=array();
    for($i0=0;isset($r['3.'.$i0]);$i0++){
      $cases[$i0] = @$r['3.'.$i0.''];
    }
    $mainoffice = @$r['4'];
    $localoffices=array();
    for($i0=0;isset($r['5.'.$i0]);$i0++){
      $localoffices[$i0] = @$r['5.'.$i0.''];
    }
    $courtofappeal = @$r['6'];
    $schedule=array();
    for($i0=0;isset($r['7.'.$i0]);$i0++){
      $schedule[$i0] = array( 'id' => @$r['7.'.$i0.'.0']
                            , 'casenr' => @$r['7.'.$i0.'.0']
                            );
      $schedule[$i0]['defendant']=array();
      for($i1=0;isset($r['7.'.$i0.'.1.'.$i1]);$i1++){
        $schedule[$i0]['defendant'][$i1] = @$r['7.'.$i0.'.1.'.$i1.''];
      }
      $schedule[$i0]['plaintiff']=array();
      for($i1=0;isset($r['7.'.$i0.'.2.'.$i1]);$i1++){
        $schedule[$i0]['plaintiff'][$i1] = @$r['7.'.$i0.'.2.'.$i1.''];
      }
    }
    $Court=new Court($ID,$Sessions, $panels, $members, $cases, $mainoffice, $localoffices, $courtofappeal, $schedule);
    if($Court->save()!==false) die('ok:'.$_SERVER['PHP_SELF'].'?Court='.urlencode($Court->getId())); else die('');
    exit(); // do not show the interface
  }
  $buttons="";
  if(isset($_REQUEST['new'])) $new=true; else $new=false;
  if(isset($_REQUEST['edit'])||$new) $edit=true; else $edit=false;
  $del=isset($_REQUEST['del']);
  if(isset($_REQUEST['Court'])){
    if(!$del || !delCourt($_REQUEST['Court']))
      $Court = readCourt($_REQUEST['Court']);
    else $Court = false; // delete was a succes!
  } else if($new) $Court = new Court();
  else $Court = false;
  if($Court){
    writeHead("<TITLE>Court - VIRO - ADL Prototype</TITLE>"
              .($edit?'<SCRIPT type="text/javascript" src="edit.js"></SCRIPT>':'<SCRIPT type="text/javascript" src="navigate.js"></SCRIPT>')."\n" );
    if($edit)
        echo '<FORM name="editForm" action="'
              .$_SERVER['PHP_SELF'].'" method="POST" class="Edit">';
    if($edit && $Court->isNew())
         echo '<P><INPUT TYPE="TEXT" NAME="ID" VALUE="'.addslashes($Court->getId()).'" /></P>';
    else echo '<H1>'.$Court->getId().'</H1>';
    ?>
    <DIV class="Floater Sessions">
      <DIV class="FloaterHeader">Sessions</DIV>
      <DIV class="FloaterContent"><?php
          $Sessions = $Court->get_Sessions();
          echo '
          <UL>';
          foreach($Sessions as $i0=>$v0){
            echo '
            <LI CLASS="item UI_Sessions" ID="0.'.$i0.'">';
              if(!$edit){
                echo '
              <A HREF="Session.php?Session='.urlencode($v0['id']).'">';
                echo '<DIV class="GotoArrow">&rarr;</DIV></A>';
              }
              echo '
              <DIV>';
                echo 'nr: ';
                echo '<SPAN CLASS="item UI_Sessions_nr" ID="0.'.$i0.'.0">';
                echo htmlspecialchars($v0['nr']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'panel: ';
                echo '<SPAN CLASS="item UI_Sessions_panel" ID="0.'.$i0.'.1">';
                if(!$edit) echo '
                <A HREF="Panel.php?Panel='.urlencode($v0['panel']).'">'.htmlspecialchars($v0['panel']).'</A>';
                else echo htmlspecialchars($v0['panel']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'judge: ';
                echo '
                <UL>';
                foreach($v0['judge'] as $i1=>$judge){
                  echo '
                  <LI CLASS="item UI_Sessions_judge" ID="0.'.$i0.'.2.'.$i1.'">';
                    if(!$edit){
                      echo '
                    <A class="GotoLink" id="To0.'.$i0.'.2.'.$i1.'">';
                      echo htmlspecialchars($judge).'</A>';
                      echo '<DIV class="Goto" id="GoTo0.'.$i0.'.2.'.$i1.'"><UL>';
                      echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($judge).'">Magistrate</A></LI>';
                      echo '<LI><A HREF="Party.php?Party='.urlencode($judge).'">Party</A></LI>';
                      echo '</UL></DIV>';
                    } else echo htmlspecialchars($judge);
                  echo '</LI>';
                }
                if($edit) echo '
                  <LI CLASS="new UI_Sessions_judge" ID="0.'.$i0.'.2.'.count($v0['judge']).'">new judge</LI>';
                echo '
                </UL>';
              echo '</DIV>
              <DIV>';
                echo 'clerk: ';
                echo '<SPAN CLASS="item UI_Sessions_clerk" ID="0.'.$i0.'.3">';
                if(!$edit){
                  echo '
                <A class="GotoLink" id="To0.'.$i0.'.3">';
                  echo htmlspecialchars($v0['clerk']).'</A>';
                  echo '<DIV class="Goto" id="GoTo0.'.$i0.'.3"><UL>';
                  echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0['clerk']).'">Magistrate</A></LI>';
                  echo '<LI><A HREF="Party.php?Party='.urlencode($v0['clerk']).'">Party</A></LI>';
                  echo '</UL></DIV>';
                } else echo htmlspecialchars($v0['clerk']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'scheduled: ';
                echo '<SPAN CLASS="item UI_Sessions_scheduled" ID="0.'.$i0.'.4">';
                echo htmlspecialchars($v0['scheduled']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                ?> 
                <DIV class ="Holder"><DIV class="HolderHeader">schedule</DIV>
                  <DIV class="HolderContent" name="schedule"><?php
                      echo '
                      <UL>';
                      foreach($v0['schedule'] as $i1=>$schedule){
                        echo '
                        <LI CLASS="item UI_Sessions_schedule" ID="0.'.$i0.'.5.'.$i1.'">';
                          if(!$edit){
                            echo '
                          <DIV class="GotoArrow" id="To0.'.$i0.'.5.'.$i1.'">&rArr;</DIV>';
                            echo '<DIV class="Goto" id="GoTo0.'.$i0.'.5.'.$i1.'"><UL>';
                            echo '<LI><A HREF="Process.php?Process='.urlencode($schedule['id']).'">Process</A></LI>';
                            echo '<LI><A HREF="ScheduleProcess.php?ScheduleProcess='.urlencode($schedule['id']).'">ScheduleProcess</A></LI>';
                            echo '</UL></DIV>';
                          }
                          echo '
                          <DIV>';
                            echo 'nr: ';
                            echo '<SPAN CLASS="item UI_Sessions_schedule_nr" ID="0.'.$i0.'.5.'.$i1.'.0">';
                            echo htmlspecialchars($schedule['nr']);
                            echo '</SPAN>';
                          echo '</DIV>
                          <DIV>';
                            echo 'case: ';
                            echo '<SPAN CLASS="item UI_Sessions_schedule_case" ID="0.'.$i0.'.5.'.$i1.'.1">';
                            if(!$edit) echo '
                            <A HREF="LegalCase.php?LegalCase='.urlencode($schedule['case']).'">'.htmlspecialchars($schedule['case']).'</A>';
                            else echo htmlspecialchars($schedule['case']);
                            echo '</SPAN>';
                          echo '</DIV>
                          <DIV>';
                            echo 'type of case: ';
                            echo '
                            <UL>';
                            foreach($schedule['type of case'] as $i2=>$typeofcase){
                              echo '
                              <LI CLASS="item UI_Sessions_schedule_typeofcase" ID="0.'.$i0.'.5.'.$i1.'.2.'.$i2.'">';
                                if(!$edit) echo '
                                <A HREF="CaseType.php?CaseType='.urlencode($typeofcase).'">'.htmlspecialchars($typeofcase).'</A>';
                                else echo htmlspecialchars($typeofcase);
                              echo '</LI>';
                            }
                            if($edit) echo '
                              <LI CLASS="new UI_Sessions_schedule_typeofcase" ID="0.'.$i0.'.5.'.$i1.'.2.'.count($schedule['type of case']).'">new type of case</LI>';
                            echo '
                            </UL>';
                          echo '
                          </DIV>';
                          if($edit) echo '
                          <INPUT TYPE="hidden" name="0.'.$i0.'.5.'.$i1.'.ID" VALUE="'.$schedule['id'].'" />';
                        echo '</LI>';
                      }
                      if($edit) echo '
                        <LI CLASS="new UI_Sessions_schedule" ID="0.'.$i0.'.5.'.count($v0['schedule']).'">new schedule</LI>';
                      echo '
                      </UL>';
                    ?> 
                  </DIV>
                </DIV>
                <?php
              echo '
              </DIV>';
              if($edit) echo '
              <INPUT TYPE="hidden" name="0.'.$i0.'.ID" VALUE="'.$v0['id'].'" />';
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_Sessions" ID="0.'.count($Sessions).'">new Sessions</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php if($edit){ ?>
    <SCRIPT type="text/javascript">
      // code for editing blocks in Sessions
      function UI_Sessions(id){
        return '<DIV>nr: <SPAN CLASS="item UI_Sessions_nr" ID="'+id+'.0"></SPAN></DIV>'
             + '<DIV>panel: <SPAN CLASS="item UI_Sessions_panel" ID="'+id+'.1"></SPAN></DIV>'
             + '<DIV>judge: <UL><LI CLASS="new UI_Sessions_judge" ID="'+id+'.2">new judge</LI></UL></DIV>'
             + '<DIV>clerk: <SPAN CLASS="item UI_Sessions_clerk" ID="'+id+'.3"></SPAN></DIV>'
             + '<DIV>scheduled: <SPAN CLASS="item UI_Sessions_scheduled" ID="'+id+'.4"></SPAN></DIV>'
             + '<DIV>schedule: <UL><LI CLASS="new UI_Sessions_schedule" ID="'+id+'.5">new schedule</LI></UL></DIV>'
              ;
      }
      function UI_Sessions_schedule(id){
        return '<DIV>nr: <SPAN CLASS="item UI_Sessions_schedule_nr" ID="'+id+'.0"></SPAN></DIV>'
             + '<DIV>case: <SPAN CLASS="item UI_Sessions_schedule_case" ID="'+id+'.1"></SPAN></DIV>'
             + '<DIV>type of case: <UL><LI CLASS="new UI_Sessions_schedule_typeofcase" ID="'+id+'.2">new type of case</LI></UL></DIV>'
              ;
      }
    </SCRIPT>
    <?php } ?>
    <DIV class="Floater panels">
      <DIV class="FloaterHeader">panels</DIV>
      <DIV class="FloaterContent"><?php
          $panels = $Court->get_panels();
          echo '
          <UL>';
          foreach($panels as $i0=>$v0){
            echo '
            <LI CLASS="item UI_panels" ID="1.'.$i0.'">';
              if(!$edit) echo '
              <A HREF="Panel.php?Panel='.urlencode($v0).'">'.htmlspecialchars($v0).'</A>';
              else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_panels" ID="1.'.count($panels).'">new panels</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater members">
      <DIV class="FloaterHeader">members</DIV>
      <DIV class="FloaterContent"><?php
          $members = $Court->get_members();
          echo '
          <UL>';
          foreach($members as $i0=>$v0){
            echo '
            <LI CLASS="item UI_members" ID="2.'.$i0.'">';
              if(!$edit){
                echo '
              <A class="GotoLink" id="To2.'.$i0.'">';
                echo htmlspecialchars($v0).'</A>';
                echo '<DIV class="Goto" id="GoTo2.'.$i0.'"><UL>';
                echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0).'">Magistrate</A></LI>';
                echo '<LI><A HREF="Party.php?Party='.urlencode($v0).'">Party</A></LI>';
                echo '</UL></DIV>';
              } else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_members" ID="2.'.count($members).'">new members</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater cases">
      <DIV class="FloaterHeader">cases</DIV>
      <DIV class="FloaterContent"><?php
          $cases = $Court->get_cases();
          echo '
          <UL>';
          foreach($cases as $i0=>$v0){
            echo '
            <LI CLASS="item UI_cases" ID="3.'.$i0.'">';
              if(!$edit) echo '
              <A HREF="LegalCase.php?LegalCase='.urlencode($v0).'">'.htmlspecialchars($v0).'</A>';
              else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_cases" ID="3.'.count($cases).'">new cases</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater main office">
      <DIV class="FloaterHeader">main office</DIV>
      <DIV class="FloaterContent"><?php
          $mainoffice = $Court->get_mainoffice();
          echo '<SPAN CLASS="item UI_mainoffice" ID="4">';
          if(!$edit) echo '
          <A HREF="City.php?City='.urlencode($mainoffice).'">'.htmlspecialchars($mainoffice).'</A>';
          else echo htmlspecialchars($mainoffice);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater local offices">
      <DIV class="FloaterHeader">local offices</DIV>
      <DIV class="FloaterContent"><?php
          $localoffices = $Court->get_localoffices();
          echo '
          <UL>';
          foreach($localoffices as $i0=>$v0){
            echo '
            <LI CLASS="item UI_localoffices" ID="5.'.$i0.'">';
              if(!$edit) echo '
              <A HREF="City.php?City='.urlencode($v0).'">'.htmlspecialchars($v0).'</A>';
              else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_localoffices" ID="5.'.count($localoffices).'">new local offices</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater court of appeal">
      <DIV class="FloaterHeader">court of appeal</DIV>
      <DIV class="FloaterContent"><?php
          $courtofappeal = $Court->get_courtofappeal();
          echo '<SPAN CLASS="item UI_courtofappeal" ID="6">';
          echo htmlspecialchars($courtofappeal);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater schedule">
      <DIV class="FloaterHeader">schedule</DIV>
      <DIV class="FloaterContent"><?php
          $schedule = $Court->get_schedule();
          echo '
          <UL>';
          foreach($schedule as $i0=>$v0){
            echo '
            <LI CLASS="item UI_schedule" ID="7.'.$i0.'">';
              if(!$edit){
                echo '
              <A HREF="LegalCase.php?LegalCase='.urlencode($v0['id']).'">';
                echo '<DIV class="GotoArrow">&rarr;</DIV></A>';
              }
              echo '
              <DIV>';
                echo 'casenr: ';
                echo '<SPAN CLASS="item UI_schedule_casenr" ID="7.'.$i0.'.0">';
                echo htmlspecialchars($v0['casenr']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'defendant: ';
                echo '
                <UL>';
                foreach($v0['defendant'] as $i1=>$defendant){
                  echo '
                  <LI CLASS="item UI_schedule_defendant" ID="7.'.$i0.'.1.'.$i1.'">';
                    if(!$edit){
                      echo '
                    <A class="GotoLink" id="To7.'.$i0.'.1.'.$i1.'">';
                      echo htmlspecialchars($defendant).'</A>';
                      echo '<DIV class="Goto" id="GoTo7.'.$i0.'.1.'.$i1.'"><UL>';
                      echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($defendant).'">Magistrate</A></LI>';
                      echo '<LI><A HREF="Party.php?Party='.urlencode($defendant).'">Party</A></LI>';
                      echo '</UL></DIV>';
                    } else echo htmlspecialchars($defendant);
                  echo '</LI>';
                }
                if($edit) echo '
                  <LI CLASS="new UI_schedule_defendant" ID="7.'.$i0.'.1.'.count($v0['defendant']).'">new defendant</LI>';
                echo '
                </UL>';
              echo '</DIV>
              <DIV>';
                echo 'plaintiff: ';
                echo '
                <UL>';
                foreach($v0['plaintiff'] as $i1=>$plaintiff){
                  echo '
                  <LI CLASS="item UI_schedule_plaintiff" ID="7.'.$i0.'.2.'.$i1.'">';
                    if(!$edit){
                      echo '
                    <A class="GotoLink" id="To7.'.$i0.'.2.'.$i1.'">';
                      echo htmlspecialchars($plaintiff).'</A>';
                      echo '<DIV class="Goto" id="GoTo7.'.$i0.'.2.'.$i1.'"><UL>';
                      echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($plaintiff).'">Magistrate</A></LI>';
                      echo '<LI><A HREF="Party.php?Party='.urlencode($plaintiff).'">Party</A></LI>';
                      echo '</UL></DIV>';
                    } else echo htmlspecialchars($plaintiff);
                  echo '</LI>';
                }
                if($edit) echo '
                  <LI CLASS="new UI_schedule_plaintiff" ID="7.'.$i0.'.2.'.count($v0['plaintiff']).'">new plaintiff</LI>';
                echo '
                </UL>';
              echo '
              </DIV>';
              if($edit) echo '
              <INPUT TYPE="hidden" name="7.'.$i0.'.ID" VALUE="'.$v0['id'].'" />';
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_schedule" ID="7.'.count($schedule).'">new schedule</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php if($edit){ ?>
    <SCRIPT type="text/javascript">
      // code for editing blocks in schedule
      function UI_schedule(id){
        return '<DIV>casenr: <SPAN CLASS="item UI_schedule_casenr" ID="'+id+'.0"></SPAN></DIV>'
             + '<DIV>defendant: <UL><LI CLASS="new UI_schedule_defendant" ID="'+id+'.1">new defendant</LI></UL></DIV>'
             + '<DIV>plaintiff: <UL><LI CLASS="new UI_schedule_plaintiff" ID="'+id+'.2">new plaintiff</LI></UL></DIV>'
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
       $buttons.=ifaceButton("JavaScript:save('".$_SERVER['PHP_SELF']."?save=1','".urlencode($Court->getId())."');","Save");
       $buttons.=ifaceButton($_SERVER['PHP_SELF']."?Court=".urlencode($Court->getId()),"Cancel");
     } 
  } else $buttons.=ifaceButton($_SERVER['PHP_SELF']."?edit=1&Court=".urlencode($Court->getId()),"Edit")
                 .ifaceButton($_SERVER['PHP_SELF']."?del=1&Court=".urlencode($Court->getId()),"Delete");
  }else{
    if($del){
      writeHead("<TITLE>Delete geslaagd</TITLE>");
      echo 'The Court is deleted';
    }else{  // deze pagina zou onbereikbaar moeten zijn
      writeHead("<TITLE>No Court object selected - VIRO - ADL Prototype</TITLE>");
      ?><i>No Court object selected</i><?php 
    }
    $buttons.=ifaceButton($_SERVER['PHP_SELF']."?new=1","New");
  }
  writeTail($buttons);
?>