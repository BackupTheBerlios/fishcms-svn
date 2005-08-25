<?
//****************************************************************************
//* File:	praise.php
//* Author:	G.A. Heath
//* Date: 	July 8, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 23, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "common.inc.php";
//***template loads***********************************************************
$MAIN=loadtmplate (main);
$PRAISE=loadtmplate (praise);
$PRAYERLIST=loadtmplate(prayerlist);
$APP="prayerlist";
//===Functions================================================================
//***function showlist ()*****************************************************
function showlist () {
global $HTTP_GET_VARS, $user, $list_prefix, $MAIN, $LINK, $PRAISE, $PRAYERLIST;
//lets see if the user has specified how many requests per page.
   if ((isset ($HTTP_GET_VARS['perpage'])) && (is_numeric ($HTTP_GET_VARS['perpage'])))
      $perpage=$HTTP_GET_VARS['perpage'];
   else
      $perpage=10;
//lets see if the user has specified to show all requests on a single page.
   if (isset ($HTTP_GET_VARS['onepage']))
      $onepage=1;
   else
      $onepage=0;
//lets see what page we are on
   if ((!isset ($HTTP_GET_VARS['page'])) || ($HTTP_GET_VARS['page']))
      $page=1;
   else
      $page=$HTTP_GET_VARS['page'];
//lets calculate our start position for our query if needed.
  $start=($page-1)*$perpage;
 //lets calculate our query
  $sql="SELECT * FROM ".$list_prefix."praise_list WHERE `request` = '".$HTTP_GET_VARS['request']."'";
   if ($onepage == 0)
      $sql.=" LIMIT ".$start." , ".$perpage.";";
   else
      $sql.=";";
//now lets show the prayerlist entries.
   $result=mysql_query($sql);
   $rows = mysql_num_rows($result);
   $sql="SELECT * FROM ".$list_prefix."prayer_list WHERE `id` = '".$HTTP_GET_VARS['request']."';";
   $result2=mysql_query($sql);
   $rows2 = mysql_num_rows($result2);
   //prayer request shown here.
   if ($rows2 != 1) 
      $CONTENT="ERROR: Unable to locate the exact prayer request.<BR>\r\n";
   else { 
      $row = mysql_fetch_array($result2);
      $WORK=insert_into_template ($PRAYERLIST, "{REQUESTFOR}", $row['request_for']);
      $WORK=insert_into_template ($WORK, "{REQUEST}", $row['request']);
      $WORK=insert_into_template ($WORK, "{REQUESTID}", $row['id']);
      $WORK=insert_into_template ($WORK, "{DATE}", date ("m/d/Y", $row['postdate']));
      $WORK=insert_into_template ($WORK, "{USERNAME}", $row['username']);
      $CONTENT=insert_into_template ($WORK, "{DELETE}", "<a href='prayerlist.php?delete=".$row['id']."'>Delete</a>");
   }
   if ($rows != 0) {
      $i=0;
      while ($i < $rows) {
         $row = mysql_fetch_array($result);
         $WORK=insert_into_template ($PRAISE, "{PRAISE}", $row['praise']); 
         if ($user['admin'] == 1)
            $WORK=insert_into_template ($WORK, "{DELETE}", "<a href='praise.php?delete=".$row['id']."'>Delete</a>");
            $WORK=insert_into_template ($WORK, "{DATE}", date ("m/d/Y", $row['postdate']));
            $WORK=insert_into_template ($WORK, "{USERNAME}", $row['username']);
         $i++;
         $CONTENT.=$WORK;
      }
      //lets work on multiple pages if need be.
      $sql="SELECT * FROM ".$list_prefix."praise_list  WHERE `request` = '".$HTTP_GET_VARS['request']."';";
      $result=mysql_query($sql);
      $rows = mysql_num_rows($result);
      $pages=($rows-($rows%$perpage))/$perpage; //this is the number of complete pages.
      if (($rows%$perpage) > 0)
         $pages++; //this will take care of incomplete pages.
      //lets list a previous page link if needed.
      if (($pages > 1) && ($onepage == 0)) {
         $i=0;
         $WORK="";
         if ($page != 1)
            $WORK.="<a href='praise.php?request=".$HTTP_GET_VARS['request']."&page=".($page-1)."'>prev</a> \r\n";
         //lets list all pages a user can click on.
         while ($i < $pages) {
            $i++;
            if ($i != $page) {
               $WORK.="<a href='praise.php?request=".$HTTP_GET_VARS['request']."&page=".$i."'>".$i."</a> \r\n";
            } else {
               $WORK.=$i." ";
            }
         }
         //lets create a next page link if needed
         if ($page != $pages) {
            $WORK.="<a href='praise.php?request=".$HTTP_GET_VARS['request']."&page=".($page+1)."'>next</a>\r\n";
         }
         $WORK.="<div align=\"right\"><a href='praise.php?request=".$HTTP_GET_VARS['request']."&onepage=1'>Show all requests on one page.</a></div><br />\r\n";
         $CONTENT.=$WORK;
      }
   } else {
      $CONTENT.="There is no praise associated with this prayer request at this time.<br />\r\n";
   }
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
   $WORK=filltemplate ($WORK, "Praise");
   //when we output this lets make sure that the output is stripped of any template elements that are not used.
   printf ("%s", striptemplate ($WORK));
}
//***function delete_praise ($id)********************************************
function delete_praise ($id) {
global $list_prefix, $MAIN;
   $sql="DELETE FROM ".$list_prefix."praise_list WHERE `id`=".$id.";";
   $result=mysql_query($sql);
   if ($result)
      $CONTENT="The selected request has been deleted.<br />\r\n";
   else
      $CONTENT="ERROR: unable to delete request.<br />\r\n";
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
   //when we output this lets make sure that the output is stripped of any template elements that are not used.
   $WORK=filltemplate ($WORK, "Praise");
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
   
   if (($HTTP_GET_VARS['delete']) && (is_numeric ($HTTP_GET_VARS['delete'])))
      if ($user['admin'] == 1)
         delete_praise ($HTTP_GET_VARS['delete']);
      else {
         $CONTENT="You must be logged in as the administrator to delete prayer requests.<br />\r\n";
         $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
         $WORK=filltemplate ($WORK, "DELETE ERROR");
         //when we output this lets make sure that the output is stripped of any template elements that are not used.
         printf ("%s", striptemplate ($WORK));
      }
   else
      if (($HTTP_GET_VARS['request']) && (is_numeric ($HTTP_GET_VARS['request'])))
         showlist ();
      else {
         $CONTENT="This page can only show praise associated with a prayer request.<br />\r\n";
         $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
         //when we output this lets make sure that the output is stripped of any template elements that are not used.
         $WORK=filltemplate ($WORK, "ASSOCIATION ERROR");
         printf ("%s", striptemplate ($WORK));
      }
//include "footer.inc";
?>