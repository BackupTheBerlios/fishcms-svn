<?
//****************************************************************************
//* File:	admin/articles.php
//* Author:	G.A. Heath
//* Date: 	August 17, 2005.
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
//***function articlelist ()**************************************************
function articlelist () {
global $list_prefix;
   $ARTICLELIST="<select name='article'>\r\n";
   $sql="SELECT * from `".$list_prefix."articles` WHERE 1;";
   $result=mysql_query($sql);
   if ($result)
      $rows = mysql_num_rows($result);
   else
      $rows=0;
   $i=0;
   while ($i < $rows) {
      $row=mysql_fetch_array($result);
      $ARTICLELIST.="<option value='".$row['id']."'>".$row['article_title']."</option>\r\n";
      $i++;
   }
   if ($rows == 0)
      $ARTICLELIST.="<option value='-'>No articles found</option>\r\n";
   $ARTICLELIST.="</select>\r\n";
   return $ARTICLELIST;
}

//***function catlist ($cat)**************************************************
function catlist ($cat) {
global $list_prefix;
   $CATLIST="<select name='category'>\r\n";
   $sql="SELECT * from `".$list_prefix."category` WHERE 1;";
   $result=mysql_query($sql);
   if ($result)
      $rows = mysql_num_rows($result);
   else
      $rows=0;
   $i=0;
   while ($i < $rows) {
      $row=mysql_fetch_array($result);
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
$ARTICLES=loadadmintmplate("articles");
   $WORK=$ARTICLES;
   if (0 == strcmp ($HTTP_GET_VARS['mode'], "select")) { //if we are to edit an article
   //lets get the article from the db
      $sql="SELECT * FROM `".$list_prefix."articles` WHERE `id` = '".$HTTP_POST_VARS['article']."';";
      $result=mysql_query($sql);
      if ($result)
         $rows = mysql_num_rows($result);
      else
         $rows=0;
      if ($rows == 0) { //lets make sure that the article exists
         $WORK=insert_into_template ($WORK, "{NEWCHECK}", "checked");
         $WORK=insert_into_template ($WORK, "{CATLIST}", catlist (0));
      } else { //if it does we will read it from the db and add it to our output.
         $row=mysql_fetch_array($result);
         $WORK=insert_into_template ($WORK, "{ARTICLEID}", $row['id']);
         $WORK=insert_into_template ($WORK, "{CATLIST}", catlist ($row['category']));
         $WORK=insert_into_template ($WORK, "{ARTICLETITLE}", $row['article_title']);
         $WORK=insert_into_template ($WORK, "{TEASER}", $row['teaser']);
         $WORK=insert_into_template ($WORK, "{ARTICLE}", $row['article']);
         $WORK=insert_into_template ($WORK, "{BYLINE}", $row['byline']);
      }
   } else { //if we are not editing an article lets prepare the form for a new article.
      $WORK=insert_into_template ($WORK, "{NEWCHECK}", "checked");
      $WORK=insert_into_template ($WORK, "{CATLIST}", catlist (0));
   }
//lets delete an article if its selected   
   if ((0 == strcmp ($HTTP_GET_VARS['mode'], "delete")) && (isset ($HTTP_POST_VARS['delete_yes']))) {
      $sql="DELETE FROM `".$list_prefix."articles` WHERE `id` = '".$HTTP_POST_VARS['article']."';";
      $result=mysql_query($sql);
   } elseif (0 == strcmp ($HTTP_GET_VARS['mode'], "delete"))
      $WORK="You must check the confirmation box to delete an article.<br>\r\n".$WORK;
   
   if (0 == strcmp ($HTTP_GET_VARS['mode'], "edit")) {
      $user=admincookie ();
      $posted_by=$user['user_id'];
      if (isset ($HTTP_POST_VARS['newarticle'])) { //its a new article being saved.
         $sql="INSERT INTO ".$list_prefix."articles VALUES ('', '".addslashes ($HTTP_POST_VARS['articletitle'])."', '".addslashes ($HTTP_POST_VARS['teaser'])."', '".addslashes ($HTTP_POST_VARS['article'])."', '".$posted_by."', '".addslashes ($HTTP_POST_VARS['byline'])."', '".time ()."', '".$HTTP_POST_VARS['category']."');";
         $result=mysql_query($sql);
      } elseif (isset ($HTTP_POST_VARS['articleid'])) { //its an old article being saved
         $sql="UPDATE `".$list_prefix ."articles` SET `article_title` = '".addslashes ($HTTP_POST_VARS['articletitle'])."', `teaser` = '".addslashes ($HTTP_POST_VARS['teaser'])."', `article` = '".addslashes ($HTTP_POST_VARS['article'])."', `byline` = '".addslashes ($HTTP_POST_VARS['byline'])."', `category` = '".$HTTP_POST_VARS['category']."' WHERE `id` = '".$HTTP_POST_VARS['articleid']."';";
         $result=mysql_query($sql);
      } else
         $WORK="You must check the new article box to save a new article<br>\r\n".$WORK;
   }
   //lets output our article cp.
   $WORK=insert_into_template ($WORK, "{ARTICLELIST}", articlelist ());
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