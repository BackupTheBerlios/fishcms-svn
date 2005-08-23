<?
//****************************************************************************
//* File:	common.inc.php
//* Author:	G.A. Heath
//* Date: 	July 7, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 22, 2005
//****************************************************************************

//===common code that should be run each time=================================
//Lets load the config file.
include ("fishcms-config.php");
//lets see if the config is valid, if not lets start the installer.
if (!isset ($db_host)) {
$action="install/install.php";
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
   die ($redirect);
}
//lets see if the installer remains. if so we die.
$installer="install/";
if (file_exists($installer))
   die ("SECURITY ERROR: You must remove the directory: install/");
//Lets access the database
@ $db=mysql_pconnect ($db_host, $db_username, $db_password);
if (!$db)
   die ("ERROR: UNABLE TO CONNECT TO DATABASE@<BR>\r\n");
elseif (!mysql_select_db ($db_database))
   die ("ERROR: UNABLE TO SELECT DATABASE!<BR>\r\n");

//common includes should go here.
include "phpbbauth.php";
include "tpl_engine.inc.php";

//thats it for common code.   
   
//***function striphtml ($content)********************************************
function striphtml ($content) {  //this function will strip the html out of any string it is given.
//lets initialize the variables.
$i=0;
$j=0;
$returned="";
$copy=1;
//we want to restrict finding the length to one time rather than each time the loop cycles.
$content_length=strlen ($content);
//now lets strip any html from the content
   while ($i < $content_length) {
      if (($content[$i] != '<') && ($content[$i] != '>') && ($copy == 1)) {
         $returned.=$content[$i];
         $j++;
      } elseif ($content[$i] == '<')
         $copy=0;
      elseif ($content[$i] == '>')
         $copy = 1;
      $i++; 
   }
   return $returned;
}

//***function GETNAVLINKS ()**************************************************
function GETNAVLINKS () { //this function will return all links in category 0.
global $list_prefix;
$LINKS=loadtmplate ("links");
//lets get started
//lets ask the db for all the links.
   $sql="SELECT * FROM ".$list_prefix."links WHERE `category` = '0' ORDER BY `order`;";
   $result=mysql_query($sql);
//lets initialize our variables we will need to process the db results.
   $rows = mysql_num_rows($result);
   $i=0;
   $NAVLINKS="";
//let loop through the db results.
   while ($i < $rows) {
   //read a single result
      $row=mysql_fetch_array($result);
   //insert that result into our links template.
      $WORK=insert_into_template ($LINKS, "{LINKURL}", $row['url']);
      $WORK=insert_into_template ($WORK, "{LINKTITLE}", $row['title']);
   //lets add this temporary template to the results we will return.
      $NAVLINKS.=$WORK;
      $i++;
   }
   return $NAVLINKS;
}

//***function GETPLAPPLINKS ()************************************************
function GETPLAPPLINKS ($USER) {
global $APP, $REGISTER, $GETPASS;
$LINK=loadtmplate ("links");
$APPLINKS="";
//if our APP variable is not set lets set it to the default.
   if (!isset($APP))
      $APP="default";
//now we will test to see what APP we are dealing with.
   if (0 == strcmp ($APP, "prayerlist")) {  //we are in the prayerlist
      $WORK=insert_into_template ($LINK, "{LINKURL}", "plsubmission.php");
      $WORK=insert_into_template ($WORK, "{LINKTITLE}", "Submit a Prayer Request.");
      $APPLINKS.=$WORK;
      $WORK=insert_into_template ($LINK, "{LINKURL}", "prayerlist_history.php");
      $WORK=insert_into_template ($WORK, "{LINKTITLE}", "View expired prayer requests");
      $APPLINKS.=$WORK;
      if ($USER['admin'] == 1) { //if the user is an admin lets let them have access to the admin panel.
         $WORK=insert_into_template ($LINK, "{LINKURL}", "/admin/");
         $WORK=insert_into_template ($WORK, "{LINKTITLE}", "Administrator tools");
         $APPLINKS.=$WORK;
      }
   } else {  //we are in our default result.
      $WORK=insert_into_template ($LINK, "{LINKURL}", $REGISTER);
      $WORK=insert_into_template ($WORK, "{LINKTITLE}", "Signup");
      $APPLINKS.=$WORK;
      $WORK=insert_into_template ($LINK, "{LINKURL}", $GETPASS);
      $WORK=insert_into_template ($WORK, "{LINKTITLE}", "I lost my password.");
      $APPLINKS.=$WORK;
      if ($USER['admin'] == 1) { //if the user is an admin lets let them have access to the admin panel.
         $WORK=insert_into_template ($LINK, "{LINKURL}", "/admin/");
         $WORK=insert_into_template ($WORK, "{LINKTITLE}", "Administrator tools");
         $APPLINKS.=$WORK;
      }
   }
   return $APPLINKS;
}

//***function getblocks ($blockset)*******************************************
function getblocks ($blockset) {
global $list_prefix;
$BLOCK_TEMPLATE=loadtmplate ("block");
   $sql="SELECT * FROM `".$list_prefix."blocks` WHERE `blockset` = '".$blockset."' ORDER BY `order`;";
   $result=mysql_query($sql);
   if ($result)
      $rows = mysql_num_rows($result);
   else
      $rows=0;
   $blocks=0;
   $CONTENT="";
   while ($blocks < $rows) {
      $row=mysql_fetch_array($result);
      if ($row['name'] == "test"){ 
         $WORK=insert_into_template ($BLOCK_TEMPLATE, "{BLOCK_TITLE}", "TEST BLOCK");
         $WORK=insert_into_template ($WORK, "{BLOCK_CONTENT}", "this is a test block");
      } else {
         include "blocks/".$row['name'].".php";
         $WORK=insert_into_template ($BLOCK_TEMPLATE, "{BLOCK_TITLE}", $BLOCK['title']);
         $WORK=insert_into_template ($WORK, "{BLOCK_CONTENT}", $BLOCK['content']);
      }
      $CONTENT.=$WORK;
      $blocks++;
   }
   return $CONTENT;
}

//***function filltemplate ($TEMPLATE, $TITLE)********************************
function filltemplate ($TEMPLATE, $TITLE) { //this function will consolidate code in most pages.
global $list_prefix;
//lets process the page title.
   $WORK=insert_into_template ($TEMPLATE, "{TITLE}", $TITLE);
//lets process the links 
   $WORK=insert_into_template ($WORK, "{NAVLINKS}", GETNAVLINKS () );
   $WORK=insert_into_template ($WORK, "{APPLINKS}", GETPLAPPLINKS (getuserinfo ()));
//lets process the sitename
   $sql="SELECT * FROM ".$list_prefix ."config WHERE `key` = 'sitename';";
   $result=mysql_query($sql);
   $rows = mysql_num_rows($result);
   if ((isset($rows)) && ($rows > 0)) {
      $row = mysql_fetch_array($result);
      $VALUE=$row['value'];
   } else
      $VALUE="localhost";         
   $WORK=insert_into_template ($WORK, "{SITENAME}", $VALUE);
//lets process the site description
   $sql="SELECT * FROM ".$list_prefix ."config WHERE `key` = 'sitedescription';";
   $result=mysql_query($sql);
   $rows = mysql_num_rows($result);
   if ((isset($rows)) && ($rows > 0)) {
      $row = mysql_fetch_array($result);
      $VALUE=$row['value'];
   } else
      $VALUE="Another FishCMS site.";            
   $WORK=insert_into_template ($WORK, "{SITEDESCRIPTION}", $VALUE);
//lets process the site copyright notice.
   $sql="SELECT * FROM ".$list_prefix ."config WHERE `key` = 'copyright';";
   $result=mysql_query($sql);
   $rows = mysql_num_rows($result);
   if ((isset($rows)) && ($rows > 0)) {
      $row = mysql_fetch_array($result);
      $VALUE=$row['value'];
   } else
      $VALUE="FishCMS is licensed under the GNU Public License<BR>\n&copy; 2005 by G.A. Heath and Michael Rice.";
   $WORK=insert_into_template ($WORK, "{COPYRIGHT}", $VALUE);
//lets process the site email address.
   $sql="SELECT * FROM ".$list_prefix ."config WHERE `key` = 'email';";
   $result=mysql_query($sql);
   $rows = mysql_num_rows($result);
   if ((isset($rows)) && ($rows > 0)) {
      $row = mysql_fetch_array($result);
      $VALUE=$row['value'];
   } else
      $VALUE="FishCMS is licensed under the GNU Public License<BR>\n&copy; 2005 by G.A. Heath and Michael Rice.";
   $WORK=insert_into_template ($WORK, "{EMAIL}", $VALUE);
//lets process the blocks
//for now we will support 4 sets of blocks in the templates, later this will be dynamic.
   //block 1
   $WORK=insert_into_template ($WORK, "{BLOCKS1}", getblocks(1));
   //block 2
   $WORK=insert_into_template ($WORK, "{BLOCKS2}", getblocks(2));
   //block 3
   $WORK=insert_into_template ($WORK, "{BLOCKS3}", getblocks(3));
   //block 4
   $WORK=insert_into_template ($WORK, "{BLOCKS4}", getblocks(4));
//   $WORK=insert_into_template ($WORK, "", );
   return $WORK;
}

//***function getcatname ($catid)*********************************************
function getcatname ($catid) {
global  $list_prefix;
   if ($catid == 0)
      $CATNAME="SYSTEM";
   else {
      $sql="SELECT * FROM `".$list_prefix."category` WHERE `id` = '".$catid."';";
      $result=mysql_query($sql);
      if ($result)
         $rows = mysql_num_rows($result);
      else
         $rows=0;
      if ($rows > 0) {
         $row = mysql_fetch_array($result);
         $CATNAME=$row['name'];
      } else
         $CATNAME="INVALID CATEGORY";
   }
   return $CATNAME;
}
?>