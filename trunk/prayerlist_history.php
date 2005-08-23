<?
//****************************************************************************
//* File:	prayerlist_history.php
//* Author:	G.A. Heath
//* Date: 	July 9, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 23, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "common.inc.php";
//***template loads***********************************************************
$MAIN=loadtmplate (main);
$PRAYERLIST=loadtmplate (prayerlist);
$APP="prayerlist";

//===Functions================================================================
//***function showlist ()*****************************************************
function showlist () {
global $HTTP_GET_VARS, $user, $list_prefix, $MAIN, $LINKS, $PRAYERLIST;
//lets set our content to be blank.
$CONTENT="";
//lets see if the user has specified how many requests per page.
   if (isset ($HTTP_GET_VARS['perpage']))
      $perpage=$HTTP_GET_VARS['perpage'];
   else
      $perpage=3;
//lets see if the user has specified to show all requests on a single page.
   if (isset ($HTTP_GET_VARS['onepage']))
      $onepage=1;
   else
      $onepage=0;
//lets see what page we are on
   if (!isset ($HTTP_GET_VARS['page']))
      $page=1;
   else
      $page=$HTTP_GET_VARS['page'];
//lets calculate our start position for our query if needed.
  $start=($page-1)*$perpage;
 //lets calculate our query
  $sql="SELECT * FROM ".$list_prefix."prayer_list WHERE `expired` = '1'";
   if ($onepage == 0)
      $sql.=" LIMIT ".$start." , ".$perpage.";";
   else
      $sql.=";";
//now lets show the prayerlist entries.
   $result=mysql_query($sql);
   $rows = mysql_num_rows($result);
   if ($rows != 0) {
      $i=0;
      while ($i < $rows) {
         //lets fetch our prayer request from the database.
         $row = mysql_fetch_array($result);
         //lets insert the prayerrequest into our working copy of this template.
         $WORK=insert_into_template ($PRAYERLIST, "{REQUESTFOR}", striphtml ($row['request_for']));
         $WORK=insert_into_template ($WORK, "{REQUEST}", striphtml ($row['request']));
         $WORK=insert_into_template ($WORK, "{DATE}", date ("m/d/Y", $row['postdate']));
         $WORK=insert_into_template ($WORK, "{USERNAME}", $row['username']);
         if ($user['admin'] == 1)
            $WORK=insert_into_template ($WORK, "{DELETE}", "<a href='prayerlist.php?delete=".$row['id']."'>Delete</a>");
         $i++;
         //now lets add this request to the CONTENT.
         $WORK=insert_into_template ($WORK, "{REQUESTID}", $row['id']);
         $CONTENT.=$WORK;
      }
      //lets work on multiple pages if need be.
      $sql="SELECT * FROM ".$list_prefix."prayer_list WHERE `expired` = '1';";
      $result=mysql_query($sql);
      $rows = mysql_num_rows($result);
      $pages=($rows-($rows%$perpage))/$perpage; //this is the number of complete pages.
      if (($rows%$perpage) > 0)
         $pages++; //this will take care of incomplete pages.
      //lets list a previous page link if needed.
      if (($pages > 1) && ($onepage == 0)) {
         $i=0;
         if ($page != 1)
            $CONTENT.="<a href='prayerlist_history.php?page".($page-1)."'>prev</a> \r\n";
         //lets list all pages a user can click on.
         while ($i < $pages) {
            $i++;
            if ($i != $page)
               $CONTENT.="<a href='prayerlist_history.php?page=".$i."'>".$i."</a> \r\n";
            else
               $CONTENT.=$i." ";
         }
         //lets create a next page link if needed
         if ($page != $pages)
            $CONTENT.="<a href='prayerlist_history.php?page=".($page+1)."'>next</a>\r\n";
         $CONTENT.="<div align=\"right\"><a href='prayerlist.php?onepage=1'>Show all requests on one page.</a></div><br />\r\n";
      }
   } else {
      $CONTENT.="There are no history prayer requests at this time.<BR>\r\n";
   }
   //now lets output our prayer requests.
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
   $WORK=filltemplate ($WORK, "Prayer List");
   //when we output this lets make sure that the output is stripped of any template elements that are not used.
   printf ("%s", striptemplate ($WORK));
}
//***function delete_request ($id)********************************************
function delete_request ($id) {
global $list_prefix;
   $sql="DELETE FROM ".$list_prefix."prayer_list WHERE `id`=".$id.";";
   $result=mysql_query($sql);
   if ($result)
      $CONTENT="The selected request has been deleted.<br /\r\n";
   else
      $CONTENT="ERROR: unable to delete request.<br />\r\n";
   //now lets output our prayer requests.
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
   $WORK=filltemplate ($WORK, "Prayer List");
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
   if ($HTTP_GET_VARS['delete'])
      if ($user['admin'] == 1)
         delete_request ($HTTP_GET_VARS['delete']);
      else {
         $CONTENT="You must be logged in as the administrator to delete prayer requests.<BR>\r\n";
         //now lets output our results.
         $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
         $WORK=filltemplate ($WORK, "Prayer List");
         //when we output this lets make sure that the output is stripped of any template elements that are not used.
         printf ("%s", striptemplate ($WORK));
      }
   else
      showlist ();
?>