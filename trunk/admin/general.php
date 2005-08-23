<?
//****************************************************************************
//* File:	admin/general.php
//* Author:	G.A. Heath
//* Date: 	August 15, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 22, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "../common.inc.php";

//===Functions================================================================

//***function loginbox ()*****************************************************
function loginbox () {
   printf ("<form method='post' action='index.php?login=1'>\r\n");
   printf ("Username: <input type='text' name='adminuser' size='20'><BR>");
   printf ("Password: <input type='password' name='adminpass' size='20'><BR>");
   printf ("<input type='submit' value='Login'><BR>");
   printf ("</form>\r\n");
}

//***function list_active_modules ()******************************************
function list_active_modules () {
global $list_prefix;
   $MODULE_LIST="<select name='active_modules'>\r\n";

   $sql="SELECT * FROM `".$list_prefix ."config` WHERE `key` = 'indexmodule' ORDER by `order`;";
   $result=mysql_query($sql);
   if ($result)
      $rows=mysql_num_rows($result);
   else
      $rows=0;
   $i=0;
   while ($i < $rows) {
      $row=mysql_fetch_array($result);
      $MODULE_LIST.="<option value='".$row['value']."'>".$row['value']."</option>\r\n";
      $i++;
   }
   if ($rows == 0)
      $MODULE_LIST.="<option value='-'>No Active Modules</option>\r\n";
   $MODULE_LIST.="</select>\r\n";
   return $MODULE_LIST;
}
//***function list_inactive_modules ()****************************************
function list_inactive_modules () {
global $list_prefix;
$mod_dir="../";
$MODULE_LIST="<select name='inactive_modules'>\r\n";
   $inactive=0;
   if ($dir_handle=opendir($mod_dir)) {
      while ($entry = readdir ($dir_handle))
         if ((!is_dir ($mod_dir.$entry)) && (0 != strcmp($entry, ".")) && (0 != strcmp($entry, "..")))
            if (strstr ($entry, ".mod.php")) {               
               $sql="SELECT * FROM `".$list_prefix ."config` WHERE (`key` = 'indexmodule' AND `value` = '".str_replace (".mod.php", "", $entry)."');";
               $result=mysql_query($sql);
               if ($result)
                  $rows=mysql_num_rows($result);
               else
                  $rows=0;
               if ($rows == 0) {
                  $MODULE_LIST.="<option value='".str_replace (".mod.php", "", $entry)."'>".str_replace (".mod.php", "", $entry)."</option>\r\n";
                  $inactive++;
               }
            }
      closedir ($dir_handle);
   }
   if ($inactive == 0) {
      $MODULE_LIST.="<option value='-'>No Inactive Modules</option>\r\n";
   }
   $MODULE_LIST.="</select>\r\n";
   return $MODULE_LIST;
}
//***function fixorder ($order)***********************************************
function fixorder ($order) {
global $list_prefix;
   $sql="SELECT * FROM `".$list_prefix ."config` WHERE (`key` = 'indexmodule' AND `order` = '".$order."');";
   $result=mysql_query($sql);
   if ($result)
      $rows = mysql_num_rows($result);
   else
      $rows=0;
   if ($rows == 0) {
      $sql="SELECT * FROM `".$list_prefix ."config` WHERE (`key` = 'indexmodule' AND `order` > '".$order."') ORDER BY `order`;";
      $result=mysql_query($sql);
      if ($result)
         $rows = mysql_num_rows($result);
      else
         $rows=0;
      if ($rows != 0) {
         $row = mysql_fetch_array($result);
         $sql="UPDATE `".$list_prefix ."config` SET `order` = '".$order."' WHERE `key` = '".$row['key']."' and `value` = '".$row['value']."';";
         $result=mysql_query($sql);
         fixorder ($order+1);
      }
   } else
      fixorder ($order+1);
}
//***function content ()******************************************************
function content () {
global $HTTP_POST_VARS, $HTTP_GET_VARS, $list_prefix;
$MAIN=loadadmintmplate ("main");
$GENERAL=loadadmintmplate ("general");
   $CONTENT="";
   //we will process changes here.
   if (0 == strcmp ($HTTP_GET_VARS['mode'], "site")) { //changes to the site info
      $sql="UPDATE ".$list_prefix ."config SET `value` = '".$HTTP_POST_VARS['sitename']."' WHERE `key` = 'sitename';";
      $result = mysql_query($sql);
      $sql="UPDATE ".$list_prefix ."config SET `value` = '".$HTTP_POST_VARS['sitedescription']."' WHERE `key` = 'sitedescription';";
      $result = mysql_query($sql);
      $sql="UPDATE ".$list_prefix ."config SET `value` = '".$HTTP_POST_VARS['email']."' WHERE `key` = 'email';";
      $result = mysql_query($sql);
      $sql="UPDATE ".$list_prefix ."config SET `value` = '".$HTTP_POST_VARS['copyright']."' WHERE `key` = 'copyright';";
      $result = mysql_query($sql);
      $RESULT="Changes to site configuration saved.<BR>\r\n";
   } elseif (0 == strcmp ($HTTP_GET_VARS['mode'], "index")) { //changes to the index page
      if (0 == strcmp ($HTTP_POST_VARS['redir_mod'], "module")) {
         $sql="UPDATE ".$list_prefix ."config SET `value` = 'modules' WHERE `key` = 'index';";
         $result = mysql_query($sql);
         $RESULT="The index page will now use the modules for content.";
      } elseif (0 == strcmp ($HTTP_POST_VARS['redir_mod'], "redirect")) //if we have checked the redirect
         if (isset ($HTTP_POST_VARS['redirect'])) { //and if we know where to redirect the user to...
            $sql="UPDATE ".$list_prefix ."config SET `value` = '".$HTTP_POST_VARS['redirect']."' WHERE `key` = 'index';";
            $result = mysql_query($sql);
            $RESULT="The index page will now redirect users to <a href='".$HTTP_POST_VARS['redirect']."'>".$HTTP_POST_VARS['redirect']."</a>.<br>\r\n";
         } else
            $RESULT="<B>ERROR</B>: You must tell me where to redirect the user to!<BR>\r\n";
   } elseif (0 == strcmp ($HTTP_GET_VARS['mode'], "amodules")) { //changes to the active modules
      if (isset ($HTTP_POST_VARS['disable'])) { //if we are to make the module inactive lets do it.
         $sql="DELETE FROM `".$list_prefix ."config` WHERE `key` = 'indexmodule' AND `value` = '".$HTTP_POST_VARS['active_modules']."';";
         $result = mysql_query($sql);
         $RESULT="The module ".$HTTP_POST_VARS['active_modules']." Has been deactivated.<BR>\r\n";
      } elseif (0 == strcmp($HTTP_POST_VARS['position'], "up")) {
         $sql="SELECT * FROM `".$list_prefix ."config` WHERE (`key` = 'indexmodule' AND `value` = '".$HTTP_POST_VARS['active_modules']."');";
         $result = mysql_query($sql);
         if ($result)
            $rows = mysql_num_rows($result);
         else
            $rows=0;
         if ($rows > 0) {
            $row = mysql_fetch_array($result);
            $sql="SELECT * FROM `".$list_prefix ."config` WHERE (`key` = 'indexmodule' AND `order` < '".$row['order']."') ORDER BY `order` DESC;";
            $result = mysql_query($sql);
            if ($result)
               $rows = mysql_num_rows($result);
            else
               $rows=0;
            if ($rows > 0) {
               $row2 = mysql_fetch_array($result);
               $sql="UPDATE `".$list_prefix ."config` SET `order` = '".$row['order']."' WHERE `key` = 'indexmodule' and `value` = '".$row2['value']."';";
               $result=mysql_query($sql);
               $sql="UPDATE `".$list_prefix ."config` SET `order` = '".$row2['order']."' WHERE `key` = 'indexmodule' and `value` = '".$row['value']."';";
               $result=mysql_query($sql);
               $RESULT="All possible module positions have been changed as requested.<BR>\r\n";
            } else
               $RESULT="The module ".$HTTP_POST_VARS['active_modules']." appears to already be at the top.<BR>\r\n";
         } else
            $RESULT="ERROR: Unable to change the modules position.<BR>\r\n";
      } elseif (0 == strcmp($HTTP_POST_VARS['position'], "down")) {
         $sql="SELECT * FROM `".$list_prefix ."config` WHERE (`key` = 'indexmodule' AND `value` = '".$HTTP_POST_VARS['active_modules']."');";
         $result = mysql_query($sql);
         if ($result)
            $rows = mysql_num_rows($result);
         else
            $rows=0;
         if ($rows > 0) {
            $row = mysql_fetch_array($result);
            $sql="SELECT * FROM `".$list_prefix ."config` WHERE (`key` = 'indexmodule' AND `order` > '".$row['order']."') ORDER BY `order`;";
            $result = mysql_query($sql);
            if ($result)
               $rows = mysql_num_rows($result);
            else
               $rows=0;
            if ($rows > 0) {
               $row2 = mysql_fetch_array($result);
               $sql="UPDATE `".$list_prefix ."config` SET `order` = '".$row['order']."' WHERE `key` = 'indexmodule' and `value` = '".$row2['value']."';";
               $result=mysql_query($sql);
               $sql="UPDATE `".$list_prefix ."config` SET `order` = '".$row2['order']."' WHERE `key` = 'indexmodule' and `value` = '".$row['value']."';";
               $result=mysql_query($sql);
               $RESULT="All possible module positions have been changed as requested.<BR>\r\n";
            } else
               $RESULT="The module ".$HTTP_POST_VARS['active_modules']." appears to already be at the bottom.<BR>\r\n";
         } else
            $RESULT="ERROR: Unable to change the modules position.<BR>\r\n";
      }
      fixorder (1);
   } elseif (0 == strcmp ($HTTP_GET_VARS['mode'], "imodules")) { //changes to the inactive modules
      $sql="SELECT * FROM `".$list_prefix ."config` WHERE `key` = 'indexmodule' ORDER BY `order` DESC;";
      $result = mysql_query($sql);
      if ($result)
         $rows = mysql_num_rows($result);
      else
         $rows=0;
      if ($rows > 0) {
         $row = mysql_fetch_array($result);
         $order=$row['order']+1;
      } else
         $order=1;
      $sql="INSERT INTO ".$list_prefix."config VALUES ('indexmodule', '".$HTTP_POST_VARS['inactive_modules']."', '".$order."');";
      $result = mysql_query($sql);
      $RESULT="The module ".$HTTP_POST_VARS['inactive_modules']." has been activated.<BR>\r\n";
   }
   //output will be added to $CONTENT.
   $CONTENT.=$RESULT.$GENERAL;
   //We don't have to read the site info from the db and place it into the
   //template because the filltemplate function will do it for us automagically.
   //we will read and output the index page settings here.
   $sql="SELECT * FROM ".$list_prefix ."config WHERE `key` = 'index';";
   $result=mysql_query($sql);
   if ($result) {  //if its in the db we will go with the db's configured value
      $rows = mysql_num_rows($result);
      $row = mysql_fetch_array($result);
      $action=$row['value'];
   } else
      $action="modules";
   if (strcmp ($action, "modules") != 0) 
      $CHECKED="{REDIRCHECKED}";
   else {
      $CHECKED="{MODCHECKED}";
      $action="";
   }
   $CONTENT=insert_into_template ($CONTENT, $CHECKED, "checked");//This is backards to how we normally do things but it works!
   $CONTENT=insert_into_template ($CONTENT, "{REDIRECT}", $action);
   //we will read and output the active index modules here
   $CONTENT=insert_into_template ($CONTENT, "{MODULE_LIST}", list_active_modules ());
   //we will read and output the inactive index modules here
   $CONTENT=insert_into_template ($CONTENT, "{INACTIVE_MODULE_LIST}", list_inactive_modules ());
   //now we will output our work.
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
   $WORK=filltemplate ($WORK, "{SITENAME} Administration panel");
   printf ("%s", striptemplate ($WORK));
}
//===Main code================================================================
   if (checkadminlogin () == 1) //if the user is logged in
      content ();
   else
      loginbox ();
?>