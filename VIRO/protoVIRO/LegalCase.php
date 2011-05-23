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
  require "LegalCase.inc.php";
  require "connectToDataBase.inc.php";
  if(isset($_REQUEST['save'])) { // handle ajax save request (do not show the interface)
    $ID=@$_REQUEST['ID'];
    // we posted . characters, but something converts them to _ (HTTP 1.1 standard)
    $r=array();
    foreach($_REQUEST as $i=>$v){
      $r[join('.',explode('_',$i))]=$v; //convert _ back to .
    }
    $plaintiff=array();
    for($i0=0;isset($r['0.'.$i0]);$i0++){
      $plaintiff[$i0] = @$r['0.'.$i0.''];
    }
    $representativeofplaintiff=array();
    for($i0=0;isset($r['1.'.$i0]);$i0++){
      $representativeofplaintiff[$i0] = @$r['1.'.$i0.''];
    }
    $defendant=array();
    for($i0=0;isset($r['2.'.$i0]);$i0++){
      $defendant[$i0] = @$r['2.'.$i0.''];
    }
    $representativeofdefendant=array();
    for($i0=0;isset($r['3.'.$i0]);$i0++){
      $representativeofdefendant[$i0] = @$r['3.'.$i0.''];
    }
    $joinedparty=array();
    for($i0=0;isset($r['4.'.$i0]);$i0++){
      $joinedparty[$i0] = @$r['4.'.$i0.''];
    }
    $representativeofjoinedparty=array();
    for($i0=0;isset($r['5.'.$i0]);$i0++){
      $representativeofjoinedparty[$i0] = @$r['5.'.$i0.''];
    }
    $areaoflaw = @$r['6'];
    $typeofcase = @$r['7'];
    $process=array();
    for($i0=0;isset($r['8.'.$i0]);$i0++){
      $process[$i0] = array( 'id' => @$r['8.'.$i0.'.0']
                           , 'nr' => @$r['8.'.$i0.'.0']
                           , 'session' => @$r['8.'.$i0.'.1']
                           , 'panel' => @$r['8.'.$i0.'.2']
                           , 'scheduled' => @$r['8.'.$i0.'.3']
                           , 'clerk' => @$r['8.'.$i0.'.5']
                           );
      $process[$i0]['judge']=array();
      for($i1=0;isset($r['8.'.$i0.'.4.'.$i1]);$i1++){
        $process[$i0]['judge'][$i1] = @$r['8.'.$i0.'.4.'.$i1.''];
      }
    }
    $authorized=array();
    for($i0=0;isset($r['9.'.$i0]);$i0++){
      $authorized[$i0] = @$r['9.'.$i0.''];
    }
    $caretakerofcasefile = @$r['10'];
    $casefile=array();
    for($i0=0;isset($r['11.'.$i0]);$i0++){
      $casefile[$i0] = array( 'id' => @$r['11.'.$i0.'.0']
                            , 'Document' => @$r['11.'.$i0.'.0']
                            , 'type' => @$r['11.'.$i0.'.1']
                            );
    }
    $authorizationdocuments=array();
    for($i0=0;isset($r['12.'.$i0]);$i0++){
      $authorizationdocuments[$i0] = array( 'id' => @$r['12.'.$i0.'.0']
                                          , 'authorization' => @$r['12.'.$i0.'.0']
                                          , 'party' => @$r['12.'.$i0.'.1']
                                          );
      $authorizationdocuments[$i0]['representative']=array();
      for($i1=0;isset($r['12.'.$i0.'.2.'.$i1]);$i1++){
        $authorizationdocuments[$i0]['representative'][$i1] = @$r['12.'.$i0.'.2.'.$i1.''];
      }
    }
    $LegalCase=new LegalCase($ID,$plaintiff, $representativeofplaintiff, $defendant, $representativeofdefendant, $joinedparty, $representativeofjoinedparty, $areaoflaw, $typeofcase, $process, $authorized, $caretakerofcasefile, $casefile, $authorizationdocuments);
    if($LegalCase->save()!==false) die('ok:'.$_SERVER['PHP_SELF'].'?LegalCase='.urlencode($LegalCase->getId())); else die('');
    exit(); // do not show the interface
  }
  $buttons="";
  if(isset($_REQUEST['new'])) $new=true; else $new=false;
  if(isset($_REQUEST['edit'])||$new) $edit=true; else $edit=false;
  $del=isset($_REQUEST['del']);
  if(isset($_REQUEST['LegalCase'])){
    if(!$del || !delLegalCase($_REQUEST['LegalCase']))
      $LegalCase = readLegalCase($_REQUEST['LegalCase']);
    else $LegalCase = false; // delete was a succes!
  } else if($new) $LegalCase = new LegalCase();
  else $LegalCase = false;
  if($LegalCase){
    writeHead("<TITLE>LegalCase - VIRO - ADL Prototype</TITLE>"
              .($edit?'<SCRIPT type="text/javascript" src="edit.js"></SCRIPT>':'<SCRIPT type="text/javascript" src="navigate.js"></SCRIPT>')."\n" );
    if($edit)
        echo '<FORM name="editForm" action="'
              .$_SERVER['PHP_SELF'].'" method="POST" class="Edit">';
    if($edit && $LegalCase->isNew())
         echo '<P><INPUT TYPE="TEXT" NAME="ID" VALUE="'.addslashes($LegalCase->getId()).'" /></P>';
    else echo '<H1>'.$LegalCase->getId().'</H1>';
    ?>
    <DIV class="Floater plaintiff">
      <DIV class="FloaterHeader">plaintiff</DIV>
      <DIV class="FloaterContent"><?php
          $plaintiff = $LegalCase->get_plaintiff();
          echo '
          <UL>';
          foreach($plaintiff as $i0=>$v0){
            echo '
            <LI CLASS="item UI_plaintiff" ID="0.'.$i0.'">';
              if(!$edit){
                echo '
              <A class="GotoLink" id="To0.'.$i0.'">';
                echo htmlspecialchars($v0).'</A>';
                echo '<DIV class="Goto" id="GoTo0.'.$i0.'"><UL>';
                echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0).'">Magistrate</A></LI>';
                echo '<LI><A HREF="Party.php?Party='.urlencode($v0).'">Party</A></LI>';
                echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($v0).'">InterestedParty</A></LI>';
                echo '</UL></DIV>';
              } else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_plaintiff" ID="0.'.count($plaintiff).'">new plaintiff</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater representative of plaintiff">
      <DIV class="FloaterHeader">representative of plaintiff</DIV>
      <DIV class="FloaterContent"><?php
          $representativeofplaintiff = $LegalCase->get_representativeofplaintiff();
          echo '
          <UL>';
          foreach($representativeofplaintiff as $i0=>$v0){
            echo '
            <LI CLASS="item UI_representativeofplaintiff" ID="1.'.$i0.'">';
              if(!$edit){
                echo '
              <A class="GotoLink" id="To1.'.$i0.'">';
                echo htmlspecialchars($v0).'</A>';
                echo '<DIV class="Goto" id="GoTo1.'.$i0.'"><UL>';
                echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0).'">Magistrate</A></LI>';
                echo '<LI><A HREF="Party.php?Party='.urlencode($v0).'">Party</A></LI>';
                echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($v0).'">InterestedParty</A></LI>';
                echo '</UL></DIV>';
              } else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_representativeofplaintiff" ID="1.'.count($representativeofplaintiff).'">new representative of plaintiff</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater defendant">
      <DIV class="FloaterHeader">defendant</DIV>
      <DIV class="FloaterContent"><?php
          $defendant = $LegalCase->get_defendant();
          echo '
          <UL>';
          foreach($defendant as $i0=>$v0){
            echo '
            <LI CLASS="item UI_defendant" ID="2.'.$i0.'">';
              if(!$edit){
                echo '
              <A class="GotoLink" id="To2.'.$i0.'">';
                echo htmlspecialchars($v0).'</A>';
                echo '<DIV class="Goto" id="GoTo2.'.$i0.'"><UL>';
                echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0).'">Magistrate</A></LI>';
                echo '<LI><A HREF="Party.php?Party='.urlencode($v0).'">Party</A></LI>';
                echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($v0).'">InterestedParty</A></LI>';
                echo '</UL></DIV>';
              } else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_defendant" ID="2.'.count($defendant).'">new defendant</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater representative of defendant">
      <DIV class="FloaterHeader">representative of defendant</DIV>
      <DIV class="FloaterContent"><?php
          $representativeofdefendant = $LegalCase->get_representativeofdefendant();
          echo '
          <UL>';
          foreach($representativeofdefendant as $i0=>$v0){
            echo '
            <LI CLASS="item UI_representativeofdefendant" ID="3.'.$i0.'">';
              if(!$edit){
                echo '
              <A class="GotoLink" id="To3.'.$i0.'">';
                echo htmlspecialchars($v0).'</A>';
                echo '<DIV class="Goto" id="GoTo3.'.$i0.'"><UL>';
                echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0).'">Magistrate</A></LI>';
                echo '<LI><A HREF="Party.php?Party='.urlencode($v0).'">Party</A></LI>';
                echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($v0).'">InterestedParty</A></LI>';
                echo '</UL></DIV>';
              } else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_representativeofdefendant" ID="3.'.count($representativeofdefendant).'">new representative of defendant</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater joined party">
      <DIV class="FloaterHeader">joined party</DIV>
      <DIV class="FloaterContent"><?php
          $joinedparty = $LegalCase->get_joinedparty();
          echo '
          <UL>';
          foreach($joinedparty as $i0=>$v0){
            echo '
            <LI CLASS="item UI_joinedparty" ID="4.'.$i0.'">';
              if(!$edit){
                echo '
              <A class="GotoLink" id="To4.'.$i0.'">';
                echo htmlspecialchars($v0).'</A>';
                echo '<DIV class="Goto" id="GoTo4.'.$i0.'"><UL>';
                echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0).'">Magistrate</A></LI>';
                echo '<LI><A HREF="Party.php?Party='.urlencode($v0).'">Party</A></LI>';
                echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($v0).'">InterestedParty</A></LI>';
                echo '</UL></DIV>';
              } else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_joinedparty" ID="4.'.count($joinedparty).'">new joined party</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater representative of joined party">
      <DIV class="FloaterHeader">representative of joined party</DIV>
      <DIV class="FloaterContent"><?php
          $representativeofjoinedparty = $LegalCase->get_representativeofjoinedparty();
          echo '
          <UL>';
          foreach($representativeofjoinedparty as $i0=>$v0){
            echo '
            <LI CLASS="item UI_representativeofjoinedparty" ID="5.'.$i0.'">';
              if(!$edit){
                echo '
              <A class="GotoLink" id="To5.'.$i0.'">';
                echo htmlspecialchars($v0).'</A>';
                echo '<DIV class="Goto" id="GoTo5.'.$i0.'"><UL>';
                echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0).'">Magistrate</A></LI>';
                echo '<LI><A HREF="Party.php?Party='.urlencode($v0).'">Party</A></LI>';
                echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($v0).'">InterestedParty</A></LI>';
                echo '</UL></DIV>';
              } else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_representativeofjoinedparty" ID="5.'.count($representativeofjoinedparty).'">new representative of joined party</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater area of law">
      <DIV class="FloaterHeader">area of law</DIV>
      <DIV class="FloaterContent"><?php
          $areaoflaw = $LegalCase->get_areaoflaw();
          echo '<SPAN CLASS="item UI_areaoflaw" ID="6">';
          if(!$edit) echo '
          <A HREF="AreaOfLaw.php?AreaOfLaw='.urlencode($areaoflaw).'">'.htmlspecialchars($areaoflaw).'</A>';
          else echo htmlspecialchars($areaoflaw);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater type of case">
      <DIV class="FloaterHeader">type of case</DIV>
      <DIV class="FloaterContent"><?php
          $typeofcase = $LegalCase->get_typeofcase();
          echo '<SPAN CLASS="item UI_typeofcase" ID="7">';
          if(!$edit) echo '
          <A HREF="CaseType.php?CaseType='.urlencode($typeofcase).'">'.htmlspecialchars($typeofcase).'</A>';
          else echo htmlspecialchars($typeofcase);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater process">
      <DIV class="FloaterHeader">process</DIV>
      <DIV class="FloaterContent"><?php
          $process = $LegalCase->get_process();
          echo '
          <UL>';
          foreach($process as $i0=>$v0){
            echo '
            <LI CLASS="item UI_process" ID="8.'.$i0.'">';
              if(!$edit){
                echo '
              <DIV class="GotoArrow" id="To8.'.$i0.'">&rArr;</DIV>';
                echo '<DIV class="Goto" id="GoTo8.'.$i0.'"><UL>';
                echo '<LI><A HREF="Process.php?Process='.urlencode($v0['id']).'">Process</A></LI>';
                echo '<LI><A HREF="ScheduleProcess.php?ScheduleProcess='.urlencode($v0['id']).'">ScheduleProcess</A></LI>';
                echo '</UL></DIV>';
              }
              echo '
              <DIV>';
                echo 'nr: ';
                echo '<SPAN CLASS="item UI_process_nr" ID="8.'.$i0.'.0">';
                echo htmlspecialchars($v0['nr']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'session: ';
                echo '<SPAN CLASS="item UI_process_session" ID="8.'.$i0.'.1">';
                if(!$edit) echo '
                <A HREF="Session.php?Session='.urlencode($v0['session']).'">'.htmlspecialchars($v0['session']).'</A>';
                else echo htmlspecialchars($v0['session']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'panel: ';
                echo '<SPAN CLASS="item UI_process_panel" ID="8.'.$i0.'.2">';
                if(!$edit) echo '
                <A HREF="Panel.php?Panel='.urlencode($v0['panel']).'">'.htmlspecialchars($v0['panel']).'</A>';
                else echo htmlspecialchars($v0['panel']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'scheduled: ';
                echo '<SPAN CLASS="item UI_process_scheduled" ID="8.'.$i0.'.3">';
                echo htmlspecialchars($v0['scheduled']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'judge: ';
                echo '
                <UL>';
                foreach($v0['judge'] as $i1=>$judge){
                  echo '
                  <LI CLASS="item UI_process_judge" ID="8.'.$i0.'.4.'.$i1.'">';
                    if(!$edit){
                      echo '
                    <A class="GotoLink" id="To8.'.$i0.'.4.'.$i1.'">';
                      echo htmlspecialchars($judge).'</A>';
                      echo '<DIV class="Goto" id="GoTo8.'.$i0.'.4.'.$i1.'"><UL>';
                      echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($judge).'">Magistrate</A></LI>';
                      echo '<LI><A HREF="Party.php?Party='.urlencode($judge).'">Party</A></LI>';
                      echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($judge).'">InterestedParty</A></LI>';
                      echo '</UL></DIV>';
                    } else echo htmlspecialchars($judge);
                  echo '</LI>';
                }
                if($edit) echo '
                  <LI CLASS="new UI_process_judge" ID="8.'.$i0.'.4.'.count($v0['judge']).'">new judge</LI>';
                echo '
                </UL>';
              echo '</DIV>
              <DIV>';
                echo 'clerk: ';
                echo '<SPAN CLASS="item UI_process_clerk" ID="8.'.$i0.'.5">';
                if(!$edit){
                  echo '
                <A class="GotoLink" id="To8.'.$i0.'.5">';
                  echo htmlspecialchars($v0['clerk']).'</A>';
                  echo '<DIV class="Goto" id="GoTo8.'.$i0.'.5"><UL>';
                  echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0['clerk']).'">Magistrate</A></LI>';
                  echo '<LI><A HREF="Party.php?Party='.urlencode($v0['clerk']).'">Party</A></LI>';
                  echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($v0['clerk']).'">InterestedParty</A></LI>';
                  echo '</UL></DIV>';
                } else echo htmlspecialchars($v0['clerk']);
                echo '</SPAN>';
              echo '
              </DIV>';
              if($edit) echo '
              <INPUT TYPE="hidden" name="8.'.$i0.'.ID" VALUE="'.$v0['id'].'" />';
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_process" ID="8.'.count($process).'">new process</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php if($edit){ ?>
    <SCRIPT type="text/javascript">
      // code for editing blocks in process
      function UI_process(id){
        return '<DIV>nr: <SPAN CLASS="item UI_process_nr" ID="'+id+'.0"></SPAN></DIV>'
             + '<DIV>session: <SPAN CLASS="item UI_process_session" ID="'+id+'.1"></SPAN></DIV>'
             + '<DIV>panel: <SPAN CLASS="item UI_process_panel" ID="'+id+'.2"></SPAN></DIV>'
             + '<DIV>scheduled: <SPAN CLASS="item UI_process_scheduled" ID="'+id+'.3"></SPAN></DIV>'
             + '<DIV>judge: <UL><LI CLASS="new UI_process_judge" ID="'+id+'.4">new judge</LI></UL></DIV>'
             + '<DIV>clerk: <SPAN CLASS="item UI_process_clerk" ID="'+id+'.5"></SPAN></DIV>'
              ;
      }
    </SCRIPT>
    <?php } ?>
    <DIV class="Floater authorized">
      <DIV class="FloaterHeader">authorized</DIV>
      <DIV class="FloaterContent"><?php
          $authorized = $LegalCase->get_authorized();
          echo '
          <UL>';
          foreach($authorized as $i0=>$v0){
            echo '
            <LI CLASS="item UI_authorized" ID="9.'.$i0.'">';
              if(!$edit) echo '
              <A HREF="Court.php?Court='.urlencode($v0).'">'.htmlspecialchars($v0).'</A>';
              else echo htmlspecialchars($v0);
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_authorized" ID="9.'.count($authorized).'">new authorized</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater caretaker of case file">
      <DIV class="FloaterHeader">caretaker of case file</DIV>
      <DIV class="FloaterContent"><?php
          $caretakerofcasefile = $LegalCase->get_caretakerofcasefile();
          echo '<SPAN CLASS="item UI_caretakerofcasefile" ID="10">';
          if(!$edit) echo '
          <A HREF="Organ.php?Organ='.urlencode($caretakerofcasefile).'">'.htmlspecialchars($caretakerofcasefile).'</A>';
          else echo htmlspecialchars($caretakerofcasefile);
          echo '</SPAN>';
        ?> 
      </DIV>
    </DIV>
    <DIV class="Floater case file">
      <DIV class="FloaterHeader">case file</DIV>
      <DIV class="FloaterContent"><?php
          $casefile = $LegalCase->get_casefile();
          echo '
          <UL>';
          foreach($casefile as $i0=>$v0){
            echo '
            <LI CLASS="item UI_casefile" ID="11.'.$i0.'">';
              if(!$edit){
                echo '
              <DIV class="GotoArrow" id="To11.'.$i0.'">&rArr;</DIV>';
                echo '<DIV class="Goto" id="GoTo11.'.$i0.'"><UL>';
                echo '<LI><A HREF="Letter.php?Letter='.urlencode($v0['id']).'">Letter</A></LI>';
                echo '<LI><A HREF="Document.php?Document='.urlencode($v0['id']).'">Document</A></LI>';
                echo '</UL></DIV>';
              }
              echo '
              <DIV>';
                echo 'Document: ';
                echo '<SPAN CLASS="item UI_casefile_Document" ID="11.'.$i0.'.0">';
                echo htmlspecialchars($v0['Document']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'type: ';
                echo '<SPAN CLASS="item UI_casefile_type" ID="11.'.$i0.'.1">';
                if(!$edit) echo '
                <A HREF="DocumentType.php?DocumentType='.urlencode($v0['type']).'">'.htmlspecialchars($v0['type']).'</A>';
                else echo htmlspecialchars($v0['type']);
                echo '</SPAN>';
              echo '
              </DIV>';
              if($edit) echo '
              <INPUT TYPE="hidden" name="11.'.$i0.'.ID" VALUE="'.$v0['id'].'" />';
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_casefile" ID="11.'.count($casefile).'">new case file</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php if($edit){ ?>
    <SCRIPT type="text/javascript">
      // code for editing blocks in case file
      function UI_casefile(id){
        return '<DIV>Document: <SPAN CLASS="item UI_casefile_Document" ID="'+id+'.0"></SPAN></DIV>'
             + '<DIV>type: <SPAN CLASS="item UI_casefile_type" ID="'+id+'.1"></SPAN></DIV>'
              ;
      }
    </SCRIPT>
    <?php } ?>
    <DIV class="Floater authorization documents">
      <DIV class="FloaterHeader">authorization documents</DIV>
      <DIV class="FloaterContent"><?php
          $authorizationdocuments = $LegalCase->get_authorizationdocuments();
          echo '
          <UL>';
          foreach($authorizationdocuments as $i0=>$v0){
            echo '
            <LI CLASS="item UI_authorizationdocuments" ID="12.'.$i0.'">';
              if(!$edit){
                echo '
              <A HREF="Authorization.php?Authorization='.urlencode($v0['id']).'">';
                echo '<DIV class="GotoArrow">&rarr;</DIV></A>';
              }
              echo '
              <DIV>';
                echo 'authorization: ';
                echo '<SPAN CLASS="item UI_authorizationdocuments_authorization" ID="12.'.$i0.'.0">';
                echo htmlspecialchars($v0['authorization']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'party: ';
                echo '<SPAN CLASS="item UI_authorizationdocuments_party" ID="12.'.$i0.'.1">';
                if(!$edit){
                  echo '
                <A class="GotoLink" id="To12.'.$i0.'.1">';
                  echo htmlspecialchars($v0['party']).'</A>';
                  echo '<DIV class="Goto" id="GoTo12.'.$i0.'.1"><UL>';
                  echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($v0['party']).'">Magistrate</A></LI>';
                  echo '<LI><A HREF="Party.php?Party='.urlencode($v0['party']).'">Party</A></LI>';
                  echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($v0['party']).'">InterestedParty</A></LI>';
                  echo '</UL></DIV>';
                } else echo htmlspecialchars($v0['party']);
                echo '</SPAN>';
              echo '</DIV>
              <DIV>';
                echo 'representative: ';
                echo '
                <UL>';
                foreach($v0['representative'] as $i1=>$representative){
                  echo '
                  <LI CLASS="item UI_authorizationdocuments_representative" ID="12.'.$i0.'.2.'.$i1.'">';
                    if(!$edit){
                      echo '
                    <A class="GotoLink" id="To12.'.$i0.'.2.'.$i1.'">';
                      echo htmlspecialchars($representative).'</A>';
                      echo '<DIV class="Goto" id="GoTo12.'.$i0.'.2.'.$i1.'"><UL>';
                      echo '<LI><A HREF="Magistrate.php?Magistrate='.urlencode($representative).'">Magistrate</A></LI>';
                      echo '<LI><A HREF="Party.php?Party='.urlencode($representative).'">Party</A></LI>';
                      echo '<LI><A HREF="InterestedParty.php?InterestedParty='.urlencode($representative).'">InterestedParty</A></LI>';
                      echo '</UL></DIV>';
                    } else echo htmlspecialchars($representative);
                  echo '</LI>';
                }
                if($edit) echo '
                  <LI CLASS="new UI_authorizationdocuments_representative" ID="12.'.$i0.'.2.'.count($v0['representative']).'">new representative</LI>';
                echo '
                </UL>';
              echo '
              </DIV>';
              if($edit) echo '
              <INPUT TYPE="hidden" name="12.'.$i0.'.ID" VALUE="'.$v0['id'].'" />';
            echo '</LI>';
          }
          if($edit) echo '
            <LI CLASS="new UI_authorizationdocuments" ID="12.'.count($authorizationdocuments).'">new authorization documents</LI>';
          echo '
          </UL>';
        ?> 
      </DIV>
    </DIV>
    <?php if($edit){ ?>
    <SCRIPT type="text/javascript">
      // code for editing blocks in authorization documents
      function UI_authorizationdocuments(id){
        return '<DIV>authorization: <SPAN CLASS="item UI_authorizationdocuments_authorization" ID="'+id+'.0"></SPAN></DIV>'
             + '<DIV>party: <SPAN CLASS="item UI_authorizationdocuments_party" ID="'+id+'.1"></SPAN></DIV>'
             + '<DIV>representative: <UL><LI CLASS="new UI_authorizationdocuments_representative" ID="'+id+'.2">new representative</LI></UL></DIV>'
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
       $buttons.=ifaceButton("JavaScript:save('".$_SERVER['PHP_SELF']."?save=1','".urlencode($LegalCase->getId())."');","Save");
       $buttons.=ifaceButton($_SERVER['PHP_SELF']."?LegalCase=".urlencode($LegalCase->getId()),"Cancel");
     } 
  } else $buttons.=ifaceButton($_SERVER['PHP_SELF']."?edit=1&LegalCase=".urlencode($LegalCase->getId()),"Edit")
                 .ifaceButton($_SERVER['PHP_SELF']."?del=1&LegalCase=".urlencode($LegalCase->getId()),"Delete");
  }else{
    if($del){
      writeHead("<TITLE>Delete geslaagd</TITLE>");
      echo 'The LegalCase is deleted';
    }else{  // deze pagina zou onbereikbaar moeten zijn
      writeHead("<TITLE>No LegalCase object selected - VIRO - ADL Prototype</TITLE>");
      ?><i>No LegalCase object selected</i><?php 
    }
    $buttons.=ifaceButton($_SERVER['PHP_SELF']."?new=1","New");
  }
  writeTail($buttons);
?>