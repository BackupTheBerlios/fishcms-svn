<?
//****************************************************************************
//* File:	admin/templates.php
//* Author:	G.A. Heath
//* Date: 	August 15, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:  September 4, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "../common.inc.php";
include "common.inc.php";

//***function themelist ($template)*******************************************
function themelist ($template) {
$template_dir="../templates/";
   if (is_dir($template_dir)) 
      if ($dir_handle=opendir($template_dir)) {
         $THEMELIST="<select name='template'>\r\n";
         while ($entry = readdir ($dir_handle))
            if ((is_dir($template_dir.$entry)) && (0 != strcmp($entry, ".")) && (0 != strcmp($entry, "..")))
               if (0 == strcmp ($entry, $template))
                  $THEMELIST.="<option value='".$entry."' selected>".$entry."</option>\r\n";
               else
                  $THEMELIST.="<option value='".$entry."'>".$entry."</option>\r\n";
         $THEMELIST.="</select>\r\n";
         closedir ($dir_handle);
      } else {
         $THEMELIST="<select name='category'><option value='-'>ERROR!!!</option></select>\r\n";
      }
   return $THEMELIST;
}

//***function content ()******************************************************
function content () {
global $HTTP_POST_VARS, $HTTP_GET_VARS, $list_prefix;
$MAIN=loadadmintmplate ("main");
$TEMPLATES=loadadmintmplate("templates");
   if (isset ($HTTP_GET_VARS['set'])) { //if we are supposed to set the template
      //set the template here
      $sql="UPDATE `".$list_prefix ."config` SET `value` = '".$HTTP_POST_VARS['template']."' WHERE `key` = 'template';";
      $result=mysql_query($sql);
      if ($result)
         $CONTENT="The theme was successfully changed to ".$HTTP_POST_VARS['template']."<BR>\r\n";
      else
         $CONTENT="ERROR: I was unable to change the theme!<BR>\r\n";
      $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
      $WORK=filltemplate ($WORK, "{SITENAME} Administration panel");
      printf ("%s", striptemplate ($WORK));
   } else { //else we will draw the form for the user to change the template.
      //first lets read the template from the configuration
      $sql="SELECT * FROM ".$list_prefix ."config WHERE `Key` = 'template';";
      $result=mysql_query($sql);
      if (!$result)
         $template="default";
      else {
         $rows = mysql_num_rows($result);
         if ($rows == 0)
            $template="default";
         else {
            $row=mysql_fetch_array($result);
            $template=$row['value'];
         }
      }
      $WORK=insert_into_template ($TEMPLATES, "{TEMPLATE}", $template);
      $WORK=insert_into_template ($WORK, "{THEMELIST}", themelist ($template));
      $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
      $WORK=filltemplate ($WORK, "{SITENAME} Administration panel");
      printf ("%s", striptemplate ($WORK));
   }
}
//===Main code================================================================
   if (checkadminlogin () == 1) //if the user is logged in
      content ();
   else
      loginbox ();
?>