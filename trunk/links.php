<?
//****************************************************************************
//* File:	index.php
//* Author:	G.A. Heath
//* Date: 	July 8, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 13, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "common.inc.php";

//===Functions================================================================

//===Main code================================================================
//lets figure out what our categories are..
$MAIN=loadtmplate ("main");
$CATEGORIES=loadtmplate ("linkcats");
$LINKS=loadtmplate ("linklist");
$WORK="";
   $sql="SELECT * FROM `".$list_prefix ."category` WHERE `id` > '0' ORDER BY `order`;";
   $result=mysql_query($sql);
   if ($result)  //if its in the db we will go with the db's configured value.
      $rows = mysql_num_rows($result);
   else
      $rows = 0;
   $i=0;
   while ($i < $rows) {
      $row = mysql_fetch_array($result);
      $i++;
      $sql="SELECT * FROM `".$list_prefix ."links` WHERE `category` = '".$row['id']."' ORDER BY `order`;";
      $result2=mysql_query($sql);
      if ($result2)  //if its in the db we will go with the db's configured value.
         $rows2 = mysql_num_rows($result2);
      else
         $rows2 = 0;
      $j = 0;
      if ($rows2 > 0)
         $WORK.=insert_into_template ($CATEGORIES, "{CATEGORY}", $row['name']);
      $CONTENT="";
      while ($j < $rows2) {
         $row2 = mysql_fetch_array($result2);
         $CONTENT.=insert_into_template ($LINKS, "{LINKURL}", $row2['url']);
         $CONTENT=insert_into_template ($CONTENT, "{LINKTITLE}", $row2['title']);
         $j++;
      }
      $WORK=insert_into_template ($WORK, "{LINKS}", $CONTENT);
   }
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
   $WORK=filltemplate ($WORK, "Links");
   printf ("%s", striptemplate ($WORK));
?>