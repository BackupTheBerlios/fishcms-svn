<?
//****************************************************************************
//* File:	admin/index.php
//* Author:	G.A. Heath
//* Date: 	July 8, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 19, 2005
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
//===Main code================================================================
   if (checkadminlogin () == 1) //if the user is logged in
      printf ("admin_content");
   else
      loginbox ();
?>