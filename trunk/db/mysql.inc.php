<?
//****************************************************************************
//* File:	mysql.inc.php
//* Author:	G.A. Heath
//* Date: 	September 10, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	September 14, 2005
//****************************************************************************

//----------------------------------------------------------------------------
// Comments: In all honesty these functions are simply wrappers for the mysql
//db functions in php.  Other DB packages that we will support will have code
//to adapt the mysql query to their own format.
//----------------------------------------------------------------------------

//===common code that should be run each time=================================
//lets access the db first.
@ $db=mysql_pconnect ($db_host, $db_username, $db_password);
if (!$db)
   die ("ERROR: UNABLE TO CONNECT TO DATABASE!<BR>\r\n");
elseif (!mysql_select_db ($db_database))
   die ("ERROR: UNABLE TO SELECT DATABASE!<BR>\r\n");
//now lets create our mysql wrappers.

//***function db_query ($query)************************************************
function db_query ($query) {
return @mysql_query($sql);
}

//***function db_num_rows($result)*********************************************
function db_num_rows($result) {
return @mysql_num_rows($result);
}

//***funtion db_fetch_array($result)*******************************************
function db_fetch_array($result) {
return @mysql_fetch_array($result);
}

?>