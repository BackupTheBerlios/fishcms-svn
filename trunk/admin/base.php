<?
//****************************************************************************
//* File:	admin/index.php
//* Author:	G.A. Heath
//* Date: 	July 8, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:  September 4, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "../common.inc.php";
include "common.inc.php";
//===Main code================================================================
   if (checkadminlogin () == 1) //if the user is logged in
      printf ("admin_content");
   else
      loginbox ();
?>