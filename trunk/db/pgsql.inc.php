<?
//****************************************************************************
//* File:       pgsql.inc.php
//* Author:     G.A. Heath
//* Date:       September 4, 2005.
//* License:    GNU Public License (GPL)
//* Last edit:  September 14, 2005
//****************************************************************************

//connect to database.
@ $db=pg_pconnect ("host=".$db_host." dbname=".$db_database." user=".$db_username." password=".$db_password);
//if we are unable to connect we will die.
if (!$db)
   die ("ERROR: Unable to connect to or select Database!");

//now lets declare our wrapper functions.
//***function db_query ($query)************************************************
function db_query ($query) {
$query= str_replace("`", "\"", $query);
$query= preg_replace("/LIMIT ([0-9]+),([ 0-9]+)/", "LIMIT \\2 OFFSET \\1", $query);
//echo $query."<br>";
   return @pg_query($query);
}

//***function db_num_rows($result)*********************************************
function db_num_rows($result) {
return @pg_num_rows ($result);
}

//***funtion db_fetch_array($result)*******************************************
function db_fetch_array($result) {
return pg_fetch_array ($result);
}

?>