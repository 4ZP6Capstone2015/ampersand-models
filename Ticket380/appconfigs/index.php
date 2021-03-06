<?php
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set("display_errors", 1);

$debug = false;
//$debug = true;

require_once __DIR__.'/Generics.php'; // loading the Ampersand model
require_once __DIR__.'/dbSettings.php'; // Get username/password for the database
require_once __DIR__.'/pluginsettings.php'; // configuration for ExecEngine and plugins
require_once __DIR__.'/php/DatabaseUtils.php'; // Make all sorts of DB functions available

SetSMF('pause', true); // Stop background view sync from using up the DB

initSession(); // initialize both a PHP session and an Ampersand session as soon as we can...

if ($debug) {echo 'a'; flush(); die();}
require_once __DIR__.'/php/Database.php'; // Handles requests: ?resetSession, ?getTimestamp, ?testRule=.., ?commands=..
require_once __DIR__.'/php/InstallMysqlProcedures.php'; // Load 'stored procedures' (rules for role 'DATABASE')
require_once __DIR__.'/php/loadplugins.php'; // make ExecEngine plugins available

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">
<!-- Generated by <?php echo $versionInfo ?> -->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>

<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="no-cache">
<meta http-equiv="Expires" content="-1">
<meta http-equiv="cache-Control" content="no-cache"> 

<link rel="apple-touch-icon-precomposed" href="images/AmpersandIcon.png">

<link href="css/Ampersand.css" rel="stylesheet" type="text/css"/>
<link href="css/Custom.css" rel="stylesheet" type="text/css"/>

<link href="css/smoothness/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css"/>
<script src="js/jquery-1.6.2.min.js"></script>
<script src="js/jquery-ui-1.8.16.custom.min.js"></script>

<script src="js/Ampersand.js"></script>
<script type="text/javascript">

function init() {
        initialize();

// het onderstaande maakt het mogelijk om blokjes te kleuren. Hiervan wordt bijvoorbeeld gebruik gemaakt door de app 'ISTAR'
  $(".AtomName:contains('Green')").closest(".AtomList[concept='Status']").closest(".InterfaceList").css("background-color", "green").find("div").css("color", "white");
  $(".AtomName:contains('Red')").closest(".AtomList[concept='Status']").closest(".InterfaceList").css("background-color", "red").find("div").css("color", "white");
  $(".AtomName:contains('Yellow')").closest(".AtomList[concept='Status']").closest(".InterfaceList").css("background-color", "yellow").find("div").css("color", "black");
  $(".AtomName:contains('Orange')").closest(".AtomList[concept='Status']").closest(".InterfaceList").css("background-color", "orange").find("div").css("color", "black");
  $(".AtomName:contains('White')").closest(".AtomList[concept='Status']").closest(".InterfaceList").css("background-color", "white").find("div").css("color", "black");
  $(".AtomName:contains('Black')").closest(".AtomList[concept='Status']").closest(".InterfaceList").css("background-color", "black").find("div").css("color", "white");
  $(".AtomName:contains('Grey')").closest(".AtomList[concept='Status']").closest(".InterfaceList").css("background-color", "grey").find("div").css("color", "white");
  $(".AtomName:contains('Blue')").closest(".AtomList[concept='Status']").closest(".InterfaceList").css("background-color", "blue").find("div").css("color", "white");
  
  $(".AtomName:contains('Green'), .AtomName:contains('Red'), .AtomName:contains('Yellow'), .AtomName:contains('Orange'), .AtomName:contains('White'), .AtomName:contains('Black'), .AtomName:contains('Grey'), .AtomName:contains('Blue')").closest(".AtomList[concept='Status']").closest(".Interface").hide();
// Einde van de code om blokjes te kleuren.

	var label;
	var value;
	label = $('div[label~="Ingelogde"]').attr('label');
	value = $('div[label~="Ingelogde"]').find('.Atom').first().attr('atom');
	if(label != undefined){
  		$('div[label~="Ingelogde"]').remove();
		$('<div id="LoginInfo">' + label + ':<b> ' + value + '</b></div>').insertBefore('#ScrollPane');
	}
	// alert(value);
}

<?php 
$selectedRoleNr = isset($_REQUEST['role']) ? $_REQUEST['role'] : -1; // role=-1 (or not specified) means no role is selected

generateInterfaceMap();
if (isset($_REQUEST['interface'])) 
  genEditableConceptInfo($_REQUEST['interface']);
?>

</script>
</head>
<body onload="init()">
<div id="Header"><div id="Logo"></div><div id="Decoration"></div></div>

<?php
echo '<div id="TopLevelInterfaces">';
echo '<div class="MenuBar">';
//echo '<ul>';

// TODO: until there is more time to design a nice user interface, we put the role selector as a list item in the top-level interfaces list
echo '<div class="MenuItem" id="LinkToMain"><a href="index.php'.($selectedRoleNr>=0? '?role='.$selectedRoleNr : '').'"><span class=TextContent>Main</span></a></div>';
topLevelInterfaceLinks();

echo '<select id=RoleSelector onchange="changeRole()">';
echo '<option value="-1"'.($selectedRoleNr==-1 ? ' selected=yes' : '').'>Algemeen</option>'; // selected if role==0 or role is not specified
for ($i=0; $i<count($allRoles); $i++)
{ $roleNm = $allRoles[$i]['name'];
//if ($roleNm != 'ExecEngine' && $roleNm != 'DATABASE')
  if ($roleNm != 'DATABASE')
  { echo '<option value="'.$i.'"'.($selectedRoleNr==$i ? ' selected=yes' : '').'>'.$roleNm.'</option>';
  }
}
echo '</select>'; // the select is in front of the rest, so it floats to the right before the reset item does.
echo '<div class="MenuItem" id="MenuBarNew"><span class=TextContent>New</span></div>';
if (isset($conceptTableInfo['SESSION'])) // only show login/logout buttons when concept SESSION is used by adl script
{ if (!isset($sessionAtom) || !isAtomInConcept($sessionAtom, 'SESSION')) // if there is an Ampersand session, show the logout button
  { echo '<div class="MenuItem" id="MenuBarLogout"><a href="index.php?Login"><span class=TextContent>Loginx</span></a></div>';
  } else // if there is already an Ampersand session, show the logout button
  { echo '<div class="MenuItem" id="MenuBarLogout"><a href="javascript:resetSession()"><span class=TextContent>Logout</span></a></div>';
  }
}
if ($isDev) // with --dev on, we show the reset-database link in the menu bar
{ echo '<div class="MenuItem" id="MenuBarReset"><a href="javascript:resetDatabase()"><span class=TextContent>Reset</span></a></div>';
}

echo '</div>'; // .MenuBar
echo '</div>'; // #TopLevelInterfaces
genNewAtomDropDownMenu();

$timeStamp = getTimestamp($err); // access database to see if it is there
if ($err)
{ echo '<br/>Cannot access database. Make sure the MySQL server is running, or <a href="javascript:resetDatabase()">create a new database</span></a>.';  
} else if (isset ($_REQUEST['Login'])) 
{      echo '<div id=AmpersandRoot>';
       Login();
       echo '</dev>';
} else if (isset ($_REQUEST['CheckLogin'])) 
{      echo '<div id=AmpersandRoot>';
       CheckLogin();
       echo '</dev>';
} else if (!isset($_REQUEST['interface']) || !isset($_REQUEST['atom'])) 
{
  // Add dummy AmpersandRoot with just the refresh interval and timestamp to auto update signals.
  // This will be obsolete once these and other properties are in a separate div. 
  echo "<div id=AmpersandRoot refresh=$autoRefreshInterval timestamp=\"".$timeStamp."\">"; 
  echo "</div>";
  
  echo '<div id=SignalAndPhpLogs>';
  genSignalLogWindow($selectedRoleNr);
  echo '</div>';
  
  echo '<ul id="Maintenance">';
  echo '<li id="Reset"><a href="javascript:resetDatabase()"><span class=TextContent>Reset database</span></a></li>';
  echo '</ul>';
  
  echo '<h3 id="CreateHeader"><span class=TextContent>Create</span></h3>';

  genNewAtomLinks();
} else
{
  $interface= $_REQUEST['interface'];
  $atom = $_REQUEST['atom'];
	
  $concept = $allInterfaceObjects[$interface]['srcConcept'];
  
  $isNew = $concept!='ONE' && !isAtomInConcept($atom,$concept);

  echo '<div id=AmpersandRoot interface='.showHtmlAttrStr($interface).' atom='.showHtmlAttrStr($atom).
       ' concept='.showHtmlAttrStr($allInterfaceObjects[$interface]['srcConcept']).
       ' editing='.($isNew?'true':'false').' isNew='.($isNew?'true':'false').
       " refresh=$autoRefreshInterval dev=".($isDev?'true':'false').' role='.showHtmlAttrStr(getRoleName($selectedRoleNr)).
       ' timestamp="'.$timeStamp.'">';
  
  echo '<div class=LogWindow id=EditLog minimized=false><div class=MinMaxButton></div><div class=Title>Edit commands</div></div>';
  echo '<div class=LogWindow id=ErrorLog minimized=false><div class=MinMaxButton></div><div class=Title>Errors</div></div>';
  
  echo '<div id=SignalAndPhpLogs>';
  echo '<div class=LogWindow id=PhpLog minimized=false><div class=MinMaxButton></div><div class=Title>Php log </div></div>';
  genSignalLogWindow($selectedRoleNr);
  echo '</div>';

  if  (!empty($allInterfaceObjects[$interface]['editableConcepts'])){
       echo '<button class="Button EditButton" onclick="startEditing()">Edit</button>';
       echo '<button class="Button SaveButton" onclick="commitEditing()">Save</button>';
       echo '<button class="Button CancelButton" onclick="cancelEditing()">Cancel</button>';
  }

  // If the atom is not in the concept, this means that a new atom was be created (and $atom is a time-based unique name).
  // We cannot use a url-encoded command for Create new, since such a url causes problems in the browser history. (pressing back
  // could cause the creation of another atom) With the current method, going back or refreshing the url simply shows the new atom.
  // TODO: Once navigation is not done with urls anymore, we can employ a more elegant solution here.
  //
  // We add the atom to its concept in a temporary transaction, so we can generate the interface in the normal way (by querying
  // the database). When the interface is done, the transaction is rolled back. On save, the atom is added to the concept table
  // again.
  // TODO: with multiple users, this mechanism may lead to non-unique new atom names, until we enocode a session number
  //       in the unique atom name. But since the atom names are based on microseconds, the chances of a problem are pretty slim.
  if ($isNew)
  { DB_doquer('START TRANSACTION');
    addAtomToConcept($atom, $concept);
  }
  
  // we need an extra ScrollPane div because the log windows need to be outside scroll area but inside ampersand root
  // (since their css depends on the 'editing' attribute)
  echo '<div id=ScrollPane>';
  echo generateAtomInterfaces($allInterfaceObjects[$interface], $atom, true); 
  echo '</div>';
  
  echo '</div>';
  echo '<div id=Rollback></div>'; // needs to be outside AmpersandRoot, so it's easy to address all interface elements not in the Rollback
  
  if ($isNew) {
    DB_doquer('ROLLBACK');
  }
} ?>
</body>
</html>

<?php 
SetSMF('pause', false);


function topLevelInterfaceLinks() {
  global $allInterfaceObjects;
  global $selectedRoleNr;
  
  foreach($allInterfaceObjects as $interface) {
    if ($interface['srcConcept']=='ONE' && isInterfaceForRole($interface, $selectedRoleNr)) 
      echo '<div class="MenuItem" interface="'.escapeHtmlAttrStr(escapeURI($interface['name'])) // the interface attribute is there so we can style specific menu items with css
          .'"><a href="index.php?interface='.escapeHtmlAttrStr(escapeURI($interface['name'])).'&atom=1'.($selectedRoleNr>=0? '&role='.$selectedRoleNr : '')
          .'"><span class=TextContent>'.htmlSpecialChars($interface['name']).'</span></a></div>';
  }
}

function genNewAtomLinks() {
  global $allInterfaceObjects;
  global $selectedRoleNr;
  
  echo '<ul id=CreateList>';
  foreach($allInterfaceObjects as $interface) {
    if ($interface['srcConcept']!='ONE' && isInterfaceForRole($interface, $selectedRoleNr)) {
      $interfaceStr = escapeHtmlAttrStr(escapeURI($interface['name']));
      $conceptStr = escapeHtmlAttrStr(escapeURI($interface['srcConcept']));
      echo "\n<li interface='$interfaceStr'><a href=\"javascript:navigateToNew('$interfaceStr','$conceptStr')\">"
           .'<span class=TextContent>Create new '.htmlSpecialChars($interface['srcConcept'])
           .' ('.htmlSpecialChars($interface['name']).')</span></a></li>';
    }
  }
  echo '</ul>';
}

function genNewAtomDropDownMenu() {
  global $allInterfaceObjects;
  global $selectedRoleNr;

  // unlike the menu bar, we don't use <a>'s here for navigation, but real click events. This is because the vertical layout may cause a lot of whitespace
  // which would not be clickable, since <a>'s don't easily stretch. The click events are initialized in initCreateNewMenu (in Ampersand.js).
  echo '<div id=CreateMenu>';
  foreach($allInterfaceObjects as $interface) {
    if ($interface['srcConcept']!='ONE' && isInterfaceForRole($interface, $selectedRoleNr)) {
      $interfaceStr = escapeHtmlAttrStr(escapeURI($interface['name']));
      $conceptStr = escapeHtmlAttrStr(escapeURI($interface['srcConcept']));
      echo "\n<div class=MenuItem interface='$interfaceStr' concept='$conceptStr'>"
      .'<div><span class=TextContent>'.htmlSpecialChars($interface['srcConcept'])
      .' ('.htmlSpecialChars($interface['name']).')</span></div></div>'; // extra div is for renaming menu entries
    }
  }
  echo '</div>';
}

function generateInterfaceMap() {
  global $allInterfaceObjects;
  global $selectedRoleNr;
  
  echo 'function getInterfacesMap() {'; // TODO: use Json for this
  echo '  var interfacesMap = new Array();';
  foreach($allInterfaceObjects as $interface) {
    if (isInterfaceForRole($interface, $selectedRoleNr)) {
      $conceptOrSpecs = array_merge(array($interface['srcConcept']), getSpecializations($interface['srcConcept']));
      
      foreach ($conceptOrSpecs as $concept) 
        echo '  mapInsert(interfacesMap, '.showHtmlAttrStr($concept).', '.showHtmlAttrStr($interface['name']).');';
    }
  }
  echo '  return interfacesMap;';
  echo '}';
}

function generateInterface($interface, $srcAtom, $isRef=false) {
/*
 *  <Interface label='interface label' isRef=true'/'false'>
 *   <Label>interface label</Label>
 *   <AtomList concept=.. [relation=..  relationIsFlipped=..]>
 *     ..
 *     for each $tgtAtom in codomain of relation of $interface
 *     <AtomRow rowType=Normal>         <DeleteStub/> <AtomListElt> generateAtomInterfaces($interface, $tgtAtom) </AtomListElt> </AtomRow>
 *     ..
 *     
 *     <AtomRow rowType=NewAtomTemplate> <DeleteStub/> <AtomListElt> generateAtomInterfaces($interface, null) </AtomListElt>     </AtomRow>
 *     
 *     <AtomRow rowType=InsertAtomStub> <DeleteStub/> <InsertStub>Insert new .. </InsertStub>                                  </AtomRow>
 *   </AtomList>
 * </Interface> 
 */
  
  $html = "";
  emit($html, '<div class=Interface label='.showHtmlAttrStr($interface['name']).
                                  ' isRef='.showHtmlAttrBool($isRef).'>');
  emit($html, "<div class=Label>".htmlSpecialChars($interface['name']).'</div>');
  
  if ($srcAtom == null)
    $codomainAtoms = array (); // in case the table would contain (null, some atom)  
  else
    $codomainAtoms = array_filter(getCoDomainAtoms($srcAtom, $interface['expressionSQL']), 'notNull'); // filter, in case table contains ($srcAtom, null)
  if (count($codomainAtoms)==0 && isset($interface['min']) && $interface['min']=='One') // 'min' is only defined for editable relations  
    $codomainAtoms[] = ""; // if there should be at least one field, we add an empty field.
  
  $codomainAtoms[] = null; // the null is presented as a NewAtomTemplate (which is cloned when inserting a new atom)
  
  $nrOfAtoms = count($codomainAtoms)-1; // disregard the null for the NewAtomTemplate
  
  $relationAttrs = $interface['relation']=='' ? '' : ' relation='.showHtmlAttrStr($interface['relation']).' relationIsFlipped='.showHtmlAttrBool($interface['relationIsFlipped'])
                                                    .' min='.showHtmlAttrStr($interface['min']).' max='.showHtmlAttrStr($interface['max'])
                                                    .' nrOfAtoms='.showHtmlAttrStr($nrOfAtoms); // 
  emit($html, '<div class="AtomList" concept='.showHtmlAttrStr($interface['tgtConcept']).$relationAttrs.'>');
  
  foreach($codomainAtoms as $i => $tgtAtom) { // null is the NewAtomTemplate
    emit($html, '<div class=AtomRow rowType='.($tgtAtom===null ?'NewAtomTemplate': 'Normal').'><div class=DeleteStub>&nbsp;</div>'.
                  '<div class=AtomListElt>');
    emit($html, generateAtomInterfaces($interface, $tgtAtom));
    emit($html,'</div></div>');  
  }
  
  emit($html, '<div class=AtomRow rowType=InsertAtomRow><div class=DeleteStub>&nbsp;</div>'.
                '<div class=InsertStub>Insert new '.htmlSpecialChars($interface['tgtConcept']).'</div></div>');
  
  emit($html, '</div></div>'); // close .AtomList and .Interface
  return $html;
}

function generateAtomInterfaces($interface, $atom, $isTopLevelInterface=false) {
/* if $interface is a top-level interface, we only generate for $interface itself
 * otherwise, we generate for its subinterfaces 
 * 
 *  <Atom atom='atom name'>
 *   <AtomName>atom name</AtomName>
 *   <InterfaceList>
 *     ..
 *     for each subInterface in $interface: generateInterface($interface, $atom)        (or $interface, if $isTopLevelInterface)
 *     ..
 *   </InterfaceList>
 * </Atom>
 * 
 * if $atom is null, we are presenting a template. Because ""==null and "" denotes an empty atom, we check with === (since "" !== null)
 */
  global $selectedRoleNr;
  global $allInterfaceObjects;
  
  
  $html = "";
  $subInterfaceIsRef = false;
  
  if ($isTopLevelInterface)
  	$subInterfaces = array ($interface);
  else
  	if (isset($interface['boxSubInterfaces']))
  	  $subInterfaces = $interface['boxSubInterfaces'];
  	else 
      if (isset($interface['refSubInterface'])) {
        $subInterfaces = array ($allInterfaceObjects[$interface['refSubInterface']]);
        $subInterfaceIsRef = true;
      }
      else
        $subInterfaces = array ();
  
  // note that the assignments below are about interfaces for the atom, not about the subinterfaces 
  $nrOfInterfaces = count(getTopLevelInterfacesForConcept($interface['tgtConcept'], $selectedRoleNr));
  $hasInterfaces = $nrOfInterfaces == 0 ? '' : ' hasInterface=' . ($nrOfInterfaces == 1 ? 'single' : 'multiple');
  
  emit($html, '<div class=Atom atom='.showHtmlAttrStr($atom).$hasInterfaces.
                             ' status='.($atom!==null?'unchanged':'new').
                             ' atomic='.showHtmlAttrBool(count($subInterfaces)==0).'>');
  
  $atomName = showViewAtom($atom, $interface['tgtConcept']); 
  // TODO: can be done more efficiently if we query the concept atoms once for each concept

  emit($html, "<div class=AtomName>".$atomName.'</div>');
  if (count($subInterfaces) > 0) {
    emit($html, '<div class=InterfaceList>');
    foreach($subInterfaces as $interface) {
      emit($html, generateInterface($interface, $atom, $subInterfaceIsRef));
    }
    emit($html, '</div>'); // div class=InterfaceList
  }
  emit($html, '</div>'); // div class=Atom
  return $html;
}

function genSignalLogWindow($selectedRoleNr) {
  echo "<div class=LogWindow id=SignalLog minimized=false><div class=MinMaxButton></div><div class=Title>".
       ($selectedRoleNr==-1 ? "All signals" : "Signals for ".getRoleName($selectedRoleNr)).
       "</div>";
  checkRoleRules($selectedRoleNr);
  echo "</div>";
}

function genEditableConceptInfo($interface) {
  global $allInterfaceObjects;
  global $relationTableInfo;
  
  $editableConcepts = $allInterfaceObjects[$interface]['editableConcepts'];
  
  $atomViewMap = array ();
  foreach ($editableConcepts as $editableConcept) {
    $allAtoms = getAllConceptAtoms($editableConcept);
    $atomsAndViews = array ();
    foreach ($allAtoms as $atom) {
      $atomsAndViews[] = array ('atom' => $atom, 'view' => showViewAtom($atom, $editableConcept));
    }
    $atomViewMap[$editableConcept] = array ('hasView' => getView($editableConcept)!=null, 'atomViewMap' => $atomsAndViews);
  }
  $atomKeyMapJson = json_encode( $atomViewMap );
  echo "\n\nfunction getEditableConceptInfo() {\n";
  echo "  return $atomKeyMapJson;\n";
  echo "}\n";
  
}
?>
