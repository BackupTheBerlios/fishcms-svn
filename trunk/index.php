<?
//****************************************************************************
//* File:	index.php
//* Author:	G.A. Heath
//* Date: 	July 8, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	September 11, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "common.inc.php";

//===Functions================================================================
//***function redirect ($action)**********************************************
function redirect ($action) {
//lets code the content that will handle the redirect here.
$redirect="
<html>
   <head>
      <title>Redirecting...</title>
      <script language='JavaScript'>
         self.location.href='".$action ."';
      </script>
   </head>
   <body>
      If you see this for more than a second please click <a href='".$action ."'>here</a><BR>
   </body>
</html>";
//lets output the redirect now.
   printf ("%s", $redirect);
}
//***function modules ()******************************************************
function modules () {
global $list_prefix;
$MAIN=loadtmplate ("main"); 
//lets get our module list from the DB.
   $sql="SELECT * FROM ".$list_prefix ."config WHERE `key` = 'indexmodule' ORDER BY `order`;";
   $result=db_query($sql);
   if ($result) {
//lets see how many modules we have and initialize our variables.
      $rows=db_num_rows($result);
      $i=0;
      $CONTENT="";
      $perpage=3;
//lets read our modules, load them, add their content to our main content.
      while ($i<$rows) {
         $row = db_fetch_array($result);
         include_once $row['value'].".mod.php";
         $CONTENT.="<H2>".$MOD['title']."</H2><BR>\r\n";
         $CONTENT.=$MOD['content'];
         $i++;
      }
//lets insert our content into the template.
      $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
      $WORK=filltemplate ($WORK, "{SITENAME}");  //this is an ugly hack but it works.
      //when we output this lets make sure that the output is stripped of any template elements that are not used.
      printf ("%s", striptemplate ($WORK));
   }
}
//===Main code================================================================
//lets figure out how we are going to draw the index page.
   $sql="SELECT * FROM ".$list_prefix ."config WHERE `key` = 'index';";
   $result=db_query($sql);
   if ($result) {  //if its in the db we will go with the db's configured value.
      $rows = db_num_rows($result);
      $row = db_fetch_array($result);
      $action=$row['value'];
   } else //if for some reason the db doesn't know what we are supposed to do we will go with modules by default.
      $action="modules";
//now lets redirect the user or draw the content from the modules.
   if (strcmp ($action, "modules") != 0)
      redirect ($action);
   else
      modules ();
?>