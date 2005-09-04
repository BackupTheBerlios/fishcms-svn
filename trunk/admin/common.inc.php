<?
//****************************************************************************
//* File:       admin/common.inc.php
//* Author:     G.A. Heath
//* Date:       September 4, 2005.
//* License:    GNU Public License (GPL)
//* Last edit:  September 4, 2005
//****************************************************************************

//***function loginbox ()*****************************************************
function loginbox () {
   printf ("<form method='post' action='index.php?login=1'>\r\n");
   printf ("Username: <input type='text' name='adminuser' size='20'><BR>");
   printf ("Password: <input type='password' name='adminpass' size='20'><BR>");
   printf ("<input type='submit' value='Login'><BR>");
   printf ("</form>\r\n");
}
               
?>