<?
//****************************************************************************
//* File:	tpl_engine.inc.php
//* Author:	G.A. Heath
//* Date: 	July 31, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 22, 2005
//****************************************************************************

//===common code that should be run each time=================================
//for now we will hardwire our template location
$sql="SELECT * FROM ".$list_prefix ."config WHERE `key` = 'template';";
$result=mysql_query($sql);
//lets determine what template to use.
if (!$result)
   $templatedir="templates/default/";
else {
   $rows = mysql_num_rows($result);
   if ($rows == 0)
      $templatedir="templates/default/";
   else {
      $row=mysql_fetch_array($result);
      $templatedir="templates/".$row['value']."/";
   }
}
//***function loadtmplate ($specific)*****************************************
function loadtmplate ($specific) {
global $templatedir;
//lets return the template, failing at that we will return an empty string.
   if ($file= @fopen($templatedir.$specific.".tpl", "r"))
      return fread ($file, filesize($templatedir.$specific.".tpl"));
   else
      return "";
}
//***function loadblocktmplate ($specific)************************************
function loadblocktmplate ($specific) {
global $templatedir;
//lets return the template, failing at that we will return an empty string.
   if ($file= @fopen($templatedir."blocks/".$specific.".tpl", "r"))
      return fread ($file, filesize($templatedir."blocks/".$specific.".tpl"));
   else
      return "";
}
//***function loadadmintmplate ($specific)************************************
function loadadmintmplate ($specific) {
global $templatedir;
//lets return the template, failing at that we will return an empty string.
   if ($file= @fopen("../".$templatedir."admin/".$specific.".tpl", "r"))
      return fread ($file, filesize("../".$templatedir."admin/".$specific.".tpl"));
   else
      return "";
}
//***function striptemplate ($template)***************************************
function striptemplate ($template) {
//lets initialize the variables.
$i=0;
$j=0;
$returned="";
$copy=1;
//we want to restrict finding the length to one time rather than each time the loop cycles.
$template_length=strlen ($template);
//now lets strip any html from the template
   while ($i < $template_length) {
      if (($template[$i] != '{') && ($template[$i] != '}') && ($copy == 1)) {
         $returned.=$template[$i];
         $j++;
      } elseif ($template[$i] == '{')
         $copy=0;
      elseif ($template[$i] == '}')
         $copy = 1;
      $i++; 
   }
   return $returned;
}
//***function insert_into_template ($template, $replaced, $replacewith)*******
function insert_into_template ($template, $replaced, $replacewith) {
//lets copy the template to our working version so we don't corrupt the
//template if its needed later.
$work=$template;
//lets do the replacing.
   return str_replace ($replaced, $replacewith, $template);
}

?>