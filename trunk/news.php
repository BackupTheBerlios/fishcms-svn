<?
//****************************************************************************
//* File:	news.php
//* Author:	G.A. Heath
//* Date: 	August 1, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	September 11, 2005
//****************************************************************************

//===common code that should be run each time=================================
//***includes*****************************************************************
include "common.inc.php";

//***template loads***********************************************************
$MAIN=loadtmplate("main");
$NEWS=loadtmplate ("news");
$NAVLINKS=GETNAVLINKS ();

//===Functions================================================================
//*** function shownews ($category)***************************************
function shownews ($category) {
global $HTTP_GET_VARS, $NEWS, $list_prefix, $MAIN;
$CONTENT="";
   if ((isset ($HTTP_GET_VARS['perpage'])) && (is_numeric ($HTTP_GET_VARS['perpage'])))
      $perpage=$HTTP_GET_VARS['perpage'];
   else
      $perpage=3;
//lets see if the user has specified to show all requests on a single page.
   if (isset ($HTTP_GET_VARS['onepage']))
      $onepage=1;
   else
      $onepage=0;
//lets see what page we are on
   if ((!isset ($HTTP_GET_VARS['page'])) || (!is_numeric($HTTP_GET_VARS['page'])))
      $page=1;
   else
      $page=$HTTP_GET_VARS['page'];
//lets calculate our start position for our query if needed.
   $start=($page-1)*$perpage;
 //lets calculate our query
   $sql="SELECT * FROM ".$list_prefix."news";
   if ($category != 0)
      $sql.=" WHERE category = '". $category."'";
   if ($onepage == 0)
      $sql.=" ORDER BY `date` DESC LIMIT ".$start." , ".$perpage.";";
  else
      $sql.=" ORDER BY `date` DESC;";
//now lets show the prayerlist entries.
   $result=db_query($sql);
   $rows = db_num_rows($result);
   if ($rows != 0) {
      $i=0;
      while ($i < $rows) {
         //lets fetch our prayer request from the database.
         $row = db_fetch_array($result);
         $postedby=getuser ($row['posted_by']);
         //lets insert the prayerrequest into our working copy of this template.
         $WORK=insert_into_template ($NEWS, "{NEWSTITLE}", stripslashes ($row['news_title']));
         $WORK=insert_into_template ($WORK, "{TEASER}", stripslashes ($row['teaser']));
         $WORK=insert_into_template ($WORK, "{NEWSID}", $row['id']);
         $WORK=insert_into_template ($WORK, "{POSTEDBY}", $postedby);
         $WORK=insert_into_template ($WORK, "{BYLINE}", $row['byline']);
         $WORK=insert_into_template ($WORK, "{DATE}", date ("m/d/Y", $row['date']));
         $WORK=insert_into_template ($WORK, "{CATEGORY}", getcatname ($row['category']));
         $i++;
         //now lets add this request to the CONTENT.
         $CONTENT.=$WORK;
      }
      $sql="SELECT * FROM ".$list_prefix."news;";
      $result=db_query($sql);
      $rows = db_num_rows($result);
      $pages=($rows-($rows%$perpage))/$perpage; //this is the number of complete pages.
      if (($rows%$perpage) > 0)
         $pages++; //this will take care of incomplete pages.
      //lets list a previous page link if needed.
      if (($pages > 1) && ($onepage == 0)) {
         $i=0;
         if ($page != 1)
            $CONTENT.="<a href='news.php?page".($page-1)."'>prev</a> \r\n";
         //lets list all pages a user can click on.
         while ($i < $pages) {
            $i++;
            if ($i != $page)
               $CONTENT.="<a href='news.php?page=".$i."'>".$i."</a> \r\n";
            else
               $CONTENT.=$i." ";
         }
         //lets create a next page link if needed
         if ($page != $pages)
            $CONTENT.="<a href='news.php?page=".($page+1)."'>next</a>\r\n";
         $CONTENT.="<div align=\"right\"><a href='news.php?onepage=1'>Show all requests on one page.</a></div><br />\r\n";
      }
   } else {
      $CONTENT.="There are no active news at this time.<BR>\r\n";
   }
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
   $WORK=filltemplate ($WORK, "News");
   //when we output this lets make sure that the output is stripped of any template elements that are not used.
   printf ("%s", striptemplate ($WORK));

}
//===Main code================================================================
//check to see if the user is logged in.
$user = getuserinfo ();
   if (0 == strcmp ($user['email'] , "anonymous"))
      $logged_in = 0;
   else
      $logged_in = 1;
   //start main code here.
   //lets handle the user interaction here.
   if ((isset($HTTP_GET_VARS['category'])) && (is_numeric ($HTTP_GET_VARS['category'])))
      $category=$HTTP_GET_VARS['category'];
   else
      $category=0;
   shownews ($category);
?>