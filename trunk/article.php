<?
//****************************************************************************
//* File:	article.php
//* Author:	G.A. Heath
//* Date: 	August 1, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 17, 2005
//****************************************************************************

//===common code that should be run each time=================================
//***includes*****************************************************************
include "common.inc.php";

//***template loads***********************************************************
$MAIN=loadtmplate("main");
$ARTICLE=loadtmplate ("article");
$NAVLINKS=GETNAVLINKS ();

//===Functions================================================================
//***function showarticle ($id)***********************************************
function showarticle ($id) {
global $list_prefix, $ARTICLE, $MAIN;
   $sql="SELECT * FROM ".$list_prefix ."articles WHERE id = '".$id ."';";
   $result=mysql_query($sql);
   $rows = mysql_num_rows($result);
   if ($rows != 0) {
      $row = mysql_fetch_array($result);
      $postedby=getuser ($row['posted_by']);
      //lets insert the prayerrequest into our working copy of this template.
      $WORK=insert_into_template ($ARTICLE, "{ARTICLETITLE}", stripslashes ($row['article_title']));
      $WORK=insert_into_template ($WORK, "{TEASER}", stripslashes ($row['teaser']));
      $WORK=insert_into_template ($WORK, "{ARTICLEID}", $row['id']);
      $WORK=insert_into_template ($WORK, "{POSTEDBY}", $postedby);
      $WORK=insert_into_template ($WORK, "{BYLINE}", $row['byline']);
      $WORK=insert_into_template ($WORK, "{DATE}", date ("m/d/Y", $row['date']));
      $WORK=insert_into_template ($WORK, "{CATEGORY}", getcatname ($row['category']));
      $WORK=insert_into_template ($WORK, "{ARTICLE}", stripslashes ($row['article']));
      //now lets add this request to the CONTENT.
      $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
      $WORK=filltemplate ($WORK, striphtml ($row['article_title']));
      printf ("%s", striptemplate ($WORK));
   }
}
//===Main code================================================================
   if ((isset($HTTP_GET_VARS['article'])) && (is_numeric ($HTTP_GET_VARS['article'])))
      showarticle ($HTTP_GET_VARS['article']);
   else {
      $CONTENT="ERROR: An invalid article was requested.";
      $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
      $WORK=filltemplate ($WORK, "ERROR INVALID ARTICLE.");
      //when we output this lets make sure that the output is stripped of any template elements that are not used.
      printf ("%s", striptemplate ($WORK));
   }
?>