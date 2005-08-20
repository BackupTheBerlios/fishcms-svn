<?
//****************************************************************************
//* File:	admin/index.php
//* Author:	G.A. Heath
//* Date: 	August 19, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 17, 2005
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
//***function content ()******************************************************
function content () {
$MAIN=loadadmintmplate ("main");
$CONTENT="
   <p>
      For now there isn't much here in the way of content.  please keep in mind
      that this project is still in its very early stages.
   </p>
   <p>
      <a href='http://fishcms.com'>FishCMS</a> is intended to be a simple, clean,
      and easy to use Content Management System targeted at Christian websites
      such as <a href='http://believewith.us'>BelieveWith.US</a>.  FishCMS
      started out as an extension of the prayerlist program used at BelieveWith.US
   </p>
   <p>
      From this Admin Control Panel you will be able to edit, configure, and
      control your FishCMS site.
   </p>
";   
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
   $WORK=filltemplate ($WORK, "{SITENAME} Administration panel");
   printf ("%s", striptemplate ($WORK));
}
//===Main code================================================================
   if (checkadminlogin () == 1) //if the user is logged in
      content ();
   elseif (isset ($HTTP_GET_VARS['login']))
      if (adminlogin ($HTTP_POST_VARS['adminuser'], $HTTP_POST_VARS['adminpass']))
         content ();
      else {
         printf ("Incorrect username or password.<BR>");
         loginbox ();
      }
   else
      loginbox ();
?>