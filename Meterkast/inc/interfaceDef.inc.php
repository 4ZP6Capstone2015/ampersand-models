<?php
// interfaceDef.inc.php
// Generated with ADL vs. 1.1-647

// this file contains large chunks of HTML code to improve code readability and reuse


/**************************************************************\
* writeHead: code to write the page and HTML-document headers. *
* If extra JavaScript is needed, or to get a title,            *
* use the $extraheaders argument to pass extra headers         *
\**************************************************************/
session_start();
function writeHead($extraHeaders=""){
  ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- jQuery -->
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<!-- Extra Headers -->
<?php echo $extraHeaders; ?>
<!-- Screen Stylesheets -->
<link href="css/reset.css"  rel="stylesheet" type="text/css" media="screen" />
<link href="css/screen.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<div id="error"></div>
<div id="container">
  <div id="header">
    <!-- End #logo -->
    <div id="decoration"></div>
    <!-- End #decoration -->
  </div>
  <!-- End #header -->
  <div id="menu">
    <div class="primairy">
      <ul><li>
      <a href="<?php echo serviceref('Ops');?>" TITLE="Toon alle Ops" class="menuItem">
        Ops
      </a>
      <a href="<?php echo serviceref('Sessions');?>" TITLE="Toon alle Sessions" class="menuItem">
        Sessions
      </a>
    <?php if (isset($_SESSION["home"])) { //$_SESSION["home"] can be set by the parent CONTEXT application like Meterkast is in the relation with Atlas
      echo '<a HREF="'.$_SESSION["home"].'" TITLE="Back to main page" class="menuItem" >
      Back to main page
      </a>';} ?>
      </li></ul>
    </div>
    <!-- End .primairy -->
  </div>
  <!-- End #menu -->
  <div id="content">
<?php
}
function writeTail($buttons=""){
?>
  </div>
  <!-- End #content -->
  <div id="buttons">
    <ul><?php echo $buttons; ?></ul>
  </div>
  <!-- End #buttons -->
  <div id="notice">
    <span title="generated with ADL vs. 1.1-647">Layout V3.0 (Milan Interface)</span>
  </div>
  <!-- End #notice -->
<!-- ********** Javascript ********** -->
<!-- Cufon Font Replacement (http://cufon.shoqolate.com/generate)-->
<script src="js/cufon-yui.js" type="text/javascript"></script>
<script src="fonts/myriad_pro_700.font.js" type="text/javascript"></script>
<script type="text/javascript">
  Cufon.replace('#menu .primairy ul li a, h1, h2, .FloaterHeader, h3, h4, h5, h6', {
    fontFamily: 'Myriad Pro',
 hover: 'true'
  });
</script>
<!-- jQuery -->
<script type="text/javascript">
$(".GotoArrow").hover(
  function () {
 $(this).addClass("GotoArrowHover");
  },

  function () {
 $(this).removeClass("GotoArrowHover");
  }
);
</script>
</body>
</html>
<?php
}
function serviceref($svc,$env=array() ) {
  $ref = 'Meterkast.php?content='.$svc;
  foreach($GLOBALS['ctxenv'] as $key => $value){ //CONTEXT wide variables
     $ref = $ref.'&'.$key.'='.$value;
  }
  foreach($env as $key => $value){
     $ref = $ref.'&'.$key.'='.$value;
  }
  return $ref;
}
function ifaceButton($url,$tag,$descr=""){
return '
  <li><a href="'.$url.'" class="button" title="'.htmlspecialchars($descr).'">
      '.htmlspecialchars($tag).'</a></li>';
}
?>