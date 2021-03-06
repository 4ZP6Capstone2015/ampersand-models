<?php

  if (!isset($_SERVER['AUTH_USER'])|| $_SERVER['AUTH_USER']=='') {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
      header('WWW-Authenticate: Basic realm="Ampersand - Bedrijfsregels"');
      echo 'Just enter a name without password. Refresh the page to retry...';
      exit;
    } else {
      DEFINE("USER","PHP_".str_replace(" ","",$_SERVER['PHP_AUTH_USER']));
    }
  } else {
    DEFINE("USER", str_replace("\\", "_", $_SERVER['AUTH_USER']));
  }
  DEFINE("IMGPATH","");
  DEFINE("FILEPATH","comp/".USER."/");
  DEFINE("COMPILATIONS_PATH","comp/".USER."/");
 // DEFINE("ADL_PATH_ABS","/Users/basj/ADL_ou/");
 // passthru ('PATH=$PATH:'.ADL_PATH_ABS);// or exit('error:Cannot set PATH directive using exec');
  session_start();
  require "inc/Session.inc.php";
  require "inc/Bestand.inc.php";
  require "inc/Actie.inc.php";
  require "inc/Operatie.inc.php";
  require "inc/connectToDataBase.inc.php";
  $ses  = readSession($_SESSION["adlsessie"]) or exit('error:Cannot find session, ensure cookies are enabled');
//  if($ses->get_ip()!=$_SERVER['REMOTE_ADDR']){
//    exit('error:Cannot find a session for your IP, ensure cookies are enabled');
//  }
  $file = readBestand($ses->get_file()) or exit('error:Cannot find the file to compile. Upload a file first');
  if(!isset($_REQUEST['op'])) exit('error:Variable \'op\' not given'); else $op = $_REQUEST['op'];
  $opr = readOperatie($op) or exit('error:Operation op=\''.$op.'\' unknown');
  $target = escapeshellcmd(COMPILATIONS_PATH.$file->getId().'_'.$op.'/');
  $source = escapeshellcmd(FILEPATH.$file->getId().'.adl');
  $compileurl = ''.sprintf($opr->get_outputURL(),$target,$source,$file->getId(),USER);
//  function ok($i){
//    return '<A HREF="'.COMPILATIONS_PATH.$i.'/"><IMG SRC="'.IMGPATH.'ok.png" /></A>';
//  }
//  foreach($file->get_compilations() as $c){
//    if($op==$c['operatie']){
//      // check the progress of the compilation
//     if ($c->get_compiled()){
//        exit('ok:'.linkoutput($compileurl)); //the command output and error streams will disappear from screen
//      }else{
//        sleep(5); // wait some time before checking again
//        exit('hold:op='.$op);
//      }
//    }
//  }
//  $running=true;
//  set_time_limit(1);
//  function shutdown ($running,$compileurl,$op) {
//     if($running){
//        if ($compileurl=='NULL'){
//           echo ('ok:<small><A href="JavaScript:compile(\''.$op.'\');">TIMEOUT ERROR. Nogmaals proberen?</A></small>');
//        } else {
//           echo ('ok:<A HREF="'.$compileurl.'">TIMEOUT WARNING: Resultaat achter link is niet gegarandeerd. <IMG SRC="'.IMGPATH.'warning.png" /></A>');
//	}
//     }
 //   echo ('hold:op='.$op);
//  }
//  register_shutdown_function('shutdown',$running,$compileurl,$op);
  if(!is_dir($target)) mkdir($target) or exit('error:could not create directory '.$target);
  $act = new Actie(null,$file->getId(),$op,false);
  if($act->save()!==false){
    // in linux:
    // exec(sprintf($opr->get_call(),$target,$source,$file->getId()).';touch '.COMPILATIONS_PATH.$op.'/done');
    // but this has to be run on a windows machine, so:
    // |sort| is used as ;
    // type nul > is used as touch
    // (both methods work in linux as well)
    $str = ''.sprintf($opr->get_call(),$target,$source,$file->getId(),USER);//.' |sort| type nul > "'.$target.'done"';
    $descriptorspec = array(
      0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
      //1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
      1 => array("file", $target."verbose.txt", "a"),  // stdout is a pipe that the child will write to
      //2 => array("pipe", "w") // stderr is a pipe that the child will write to
      2 => array("file", $target."error.txt", "a") // stderr is a pipe that the child will write to
  //    2 => array("file", "/error-output.txt" ,"a") // stderr is a file to write to
    );
    $process = proc_open($str, $descriptorspec, $pipes, getcwd());
    if (is_resource($process)) {
       fclose($pipes[0]);
       //fclose($pipes[2]);
       //$perr = stream_get_contents($pipes[2]);
       //$pout = stream_get_contents($pipes[1]);
       //fclose($pipes[1]);
       //fclose($pipes[2]);
       // It is important that you close any pipe before calling
       // proc_close in order to avoid a deadlock
       $return_value = proc_close($process);
    }
  //  set_time_limit(30);
  //  $running=false;
       
    if (file_exists($target.'error.txt')) 
	    foreach( file ( escapeshellcmd($target.'error.txt')) as $line) //}
		    {$err = $err.'<p>'.$line.'</p>'; }
//       if (file_exists($target.'err1.txt')) 
//	    {$err1 = file_get_contents ( escapeshellcmd($target.'err1.txt'));}
    if (file_exists($target.'verbose.txt')) 
	    foreach( file ( escapeshellcmd($target.'verbose.txt')) as $line) //}
		    {$verbose = $verbose.'<p>'.$line.'</p>'; }
    if (file_exists($target.'verbose_cmd.txt')) 
	    foreach( file ( escapeshellcmd($target.'verbose_cmd.txt')) as $line) //}
		    {$verbose = $verbose.'<p>'.$line.'</p>'; }
	  
    if ($err) {$outstr = 'error:';} 
    else {$outstr = 'ok:'.linkoutput($compileurl);}
       
    if ($err || $verbose) $outstr = $outstr.'<P>COMMAND: '.$str.'</P>';
    if ($verbose) $outstr = $outstr.'<P>VERBOSE:</P><P>'.($verbose).'</P>';
    if ($err) $outstr = $outstr.'<P>ERROR:</P><P>'.($err).'</P>';

    if ($err) $act->set_error($err);
    $act->set_compiled(true);
    if ($act->save()!=false) exit($outstr);
    else exit('error:Could not save the action status into the database');
  } else exit('error:Could not save the action into the database');


  function linkoutput($compileurl){
    if ($compileurl=='NULL'){
       return '<P>De bewerking is succesvol uitgevoerd.</P>';
    } else {
       return '<A HREF="'.$compileurl.'"><IMG SRC="'.IMGPATH.'ok.png" /></A>';
    }
  }
  ?>
