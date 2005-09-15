<?
//****************************************************************************
//* File:	admin/index.php
//* Author:	G.A. Heath
//* Date: 	August 17, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:  September 11, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "../common.inc.php";
include "common.inc.php";
//***function newslist ()**************************************************
function newslist () {
global $list_prefix;
   $NEWSLIST="<select name='news'>\r\n";
   $sql="SELECT * from `".$list_prefix."news` WHERE 1;";
   $result=db_query($sql);
   if ($result)
      $rows = db_num_rows($result);
   else
      $rows=0;
   $i=0;
   while ($i < $rows) {
      $row=db_fetch_array($result);
      $NEWSLIST.="<option value='".$row['id']."'>".$row['news_title']."</option>\r\n";
      $i++;
   }
   if ($rows == 0)
      $NEWSLIST.="<option value='-'>No news found</option>\r\n";
   $NEWSLIST.="</select>\r\n";
   return $NEWSLIST;
}

//***function catlist ($cat)**************************************************
function catlist ($cat) {
global $list_prefix;
   $CATLIST="<select name='category'>\r\n";
   $sql="SELECT * from `".$list_prefix."category` WHERE 1;";
   $result=db_query($sql);
   if ($result)
      $rows = db_num_rows($result);
   else
      $rows=0;
   $i=0;
   while ($i < $rows) {
      $row=db_fetch_array($result);
      if ($cat == $row['id'])
         $CATLIST.="<option value='".$row['id']."' selected>".$row['name']."</option>\r\n";
      else
         $CATLIST.="<option value='".$row['id']."'>".$row['name']."</option>\r\n";
      $i++;
   }
   if ($rows == 0)
      $CATLELIST.="<option value='0 '>SYSTEM</option>\r\n";
   $CATLIST.="</select>\r\n";
   return $CATLIST;
}
//***function content ()******************************************************
function content () {
global $HTTP_POST_VARS, $HTTP_GET_VARS, $list_prefix;
$MAIN=loadadmintmplate ("main");
$NEWS=loadadmintmplate("news");
   $WORK=$NEWS;
   if (0 == strcmp ($HTTP_GET_VARS['mode'], "select")) { //if we are to edit an news
   //lets get the news from the db
      $sql="SELECT * FROM `".$list_prefix."news` WHERE `id` = '".$HTTP_POST_VARS['news']."';";
      $result=db_query($sql);
      if ($result)
         $rows = db_num_rows($result);
      else
         $rows=0;
      if ($rows == 0) { //lets make sure that the news exists
         $WORK=insert_into_template ($WORK, "{NEWCHECK}", "checked");
         $WORK=insert_into_template ($WORK, "{CATLIST}", catlist (0));
      } else { //if it does we will read it from the db and add it to our output.
         $row=db_fetch_array($result);
         $WORK=insert_into_template ($WORK, "{NEWSID}", $row['id']);
         $WORK=insert_into_template ($WORK, "{CATLIST}", catlist ($row['category']));
         $WORK=insert_into_template ($WORK, "{NEWSTITLE}", $row['news_title']);
         $WORK=insert_into_template ($WORK, "{TEASER}", stripslashes ($row['teaser']));
         $WORK=insert_into_template ($WORK, "{NEWS}", stripslashes ($row['news']));
         $WORK=insert_into_template ($WORK, "{BYLINE}", $row['byline']);
      }
   } else { //if we are not editing an news lets prepare the form for a new news.
      $WORK=insert_into_template ($WORK, "{NEWCHECK}", "checked");
      $WORK=insert_into_template ($WORK, "{CATLIST}", catlist (0));
   }
//lets delete an news if its selected   
   if ((0 == strcmp ($HTTP_GET_VARS['mode'], "delete")) && (isset ($HTTP_POST_VARS['delete_yes']))) {
      $sql="DELETE FROM `".$list_prefix."news` WHERE `id` = '".$HTTP_POST_VARS['news']."';";
      $result=db_query($sql);
   } elseif (0 == strcmp ($HTTP_GET_VARS['mode'], "delete"))
      $WORK="You must check the confirmation box to delete a news item.<br>\r\n".$WORK;
   
   if (0 == strcmp ($HTTP_GET_VARS['mode'], "edit")) {
      $user=admincookie ();
      $posted_by=$user['user_id'];
      if (isset ($HTTP_POST_VARS['newnews'])) { //its a new news being saved.
         $sql="INSERT INTO ".$list_prefix."news VALUES ('', '".addslashes ($HTTP_POST_VARS['newstitle'])."', '".addslashes ($HTTP_POST_VARS['teaser'])."', '".addslashes ($HTTP_POST_VARS['news'])."', '".$posted_by."', '".addslashes ($HTTP_POST_VARS['byline'])."', '".time ()."', '".$HTTP_POST_VARS['category']."');";
         $result=db_query($sql);
      } elseif (isset ($HTTP_POST_VARS['newsid'])) { //its an old news being saved
         $sql="UPDATE `".$list_prefix ."news` SET `news_title` = '".addslashes ($HTTP_POST_VARS['newstitle'])."', `teaser` = '".addslashes ($HTTP_POST_VARS['teaser'])."', `news` = '".addslashes ($HTTP_POST_VARS['news'])."', `byline` = '".addslashes ($HTTP_POST_VARS['byline'])."', `category` = '".$HTTP_POST_VARS['category']."' WHERE `id` = '".$HTTP_POST_VARS['newsid']."';";
         $result=db_query($sql);
      } else
         $WORK="You must check the new news box to save a new news<br>\r\n".$WORK;
   }
   //lets output our news cp.
   $WORK=insert_into_template ($WORK, "{NEWSLIST}", newslist ());
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
   $WORK=filltemplate ($WORK, "{SITENAME} Administration panel");
   printf ("%s", striptemplate ($WORK));
}
//===Main code================================================================
   if (checkadminlogin () == 1) //if the user is logged in
      content ();
   else
      loginbox ();
?>