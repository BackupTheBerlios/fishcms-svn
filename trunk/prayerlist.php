<?
//****************************************************************************
//* File:	prayerlist.php
//* Author:	G.A. Heath
//* Date: 	July 7, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	September 11, 2005
//****************************************************************************

//===common code that should be run each time=================================
//***includes*****************************************************************
include "common.inc.php";
//***template loads***********************************************************
$MAIN=loadtmplate (main);
$PRAYERLIST=loadtmplate (prayerlist);
$APP="prayerlist";
//===Functions================================================================
//***function showlist ()*****************************************************
function showlist () {
global $HTTP_GET_VARS, $user, $list_prefix, $MAIN, $PRAYERLIST;
//lets set our content to be blank.
$CONTENT="";
//lets see if the user has specified how many requests per page.
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
   if ((!isset ($HTTP_GET_VARS['page'])) || (is_numeric ($HTTP_GET_VARS['page'])))
      $page=1;
   else
      $page=$HTTP_GET_VARS['page'];
//lets calculate our start position for our query if needed.
  $start=($page-1)*$perpage;
 //lets calculate our query
   $sql="SELECT * FROM ".$list_prefix."prayer_list WHERE `expired` = '0'";
   if ($onepage == 0)
      $sql.=" LIMIT ".$start." , ".$perpage.";";
    else
      $sql.=";";
//now lets show the prayerlist entries.
   $result=db_query($sql);
   $rows = db_num_rows($result);
   if ($rows != 0) {
      $i=0;
      while ($i < $rows) {
         //lets fetch our prayer request from the database.
         $row = db_fetch_array($result);
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
      $sql="SELECT * FROM ".$list_prefix."prayer_list WHERE `expired` = '0';";
      $result=db_query($sql);
      $rows = db_num_rows($result);
      $pages=($rows-($rows%$perpage))/$perpage; //this is the number of complete pages.
      if (($rows%$perpage) > 0)
         $pages++; //this will take care of incomplete pages.
      //lets list a previous page link if needed.
      if (($pages > 1) && ($onepage == 0)) {
         $i=0;
         if ($page != 1)
            $CONTENT.="<a href='prayerlist.php?page".($page-1)."'>prev</a> \r\n";
         //lets list all pages a user can click on.
         while ($i < $pages) {
            $i++;
            if ($i != $page)
               $CONTENT.="<a href='prayerlist.php?page=".$i."'>".$i."</a> \r\n";
            else
               $CONTENT.=$i." ";
         }
         //lets create a next page link if needed
         if ($page != $pages)
            $CONTENT.="<a href='prayerlist.php?page=".($page+1)."'>next</a>\r\n";
         $CONTENT.="<div align=\"right\"><a href='prayerlist.php?onepage=1'>Show all requests on one page.</a></div><br />\r\n";
      }
   } else {
      $CONTENT.="There are no active prayer requests at this time.<BR>\r\n";
   }
   //now lets output our prayer requests.
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
   $WORK=filltemplate ($WORK, "Prayer List");
   //when we output this lets make sure that the output is stripped of any template elements that are not used.
   printf ("%s", striptemplate ($WORK));
}
//***function delete_request ($id)********************************************
function delete_request ($id) {
global $list_prefix, $MAIN;
   $sql="DELETE FROM ".$list_prefix."prayer_list WHERE `id`=".$id.";";
   $result=db_query($sql);
   if ($result)
      $CONTENT="The selected request has been deleted.<br /\r\n";
   else
      $CONTENT="ERROR: unable to delete request.<br />\r\n";
   //now lets output our results.
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
   $WORK=filltemplate ($WORK, "Prayer List");
   //when we output this lets make sure that the output is stripped of any template elements that are not used.
   printf ("%s", striptemplate ($WORK));
}

//***function mailuser ($email, $request)*************************************
function mailuser ($email, $request) {
global $list_prefix;
//lets figure out who to send the email as.
   $sql="SELECT * FROM ".$list_prefix."prayer_list WHERE `key` = 'email';";
   $result=db_query($sql);
   @ $rows=db_num_rows($result);
   if($rows > 0) {
      $row = db_fetch_array($result);
   } else {
      $row['value']="DBERROR@SOMEHOST.TLD";
   }
//lets code our message
   $xtra="From: ".$row['value']."\r\nX-Mailer: FishCMS mailer.\r\nReturn-Path: ".$row['value']."\r\n";
   $message="This message is from the FishCMS Prayer List manager to\r\n";
   $message.="inform you that your prayer request has expired.  The details of\r\n";
   $message.="request follow: \r\n";
   $message.="The request was for: ".$request['request_for']."\r\n";
   $message.="The request was:\r\n".$request['request']."\r\n";
   $message.="\r\nYou may resubmit the request at any time.\r\n";
   $message.="        Thank you, and may God bless you.\r\n";
//lets send it.
   $result = @mail($email, "A prayer request you submitted has expired.", $message, $xtra);
}

//===Main code================================================================
//check to see if the user is logged in.
$user = getuserinfo ();
   if (0 == strcmp ($user['email'] , "anonymous"))
      $logged_in = 0;
   else
      $logged_in = 1;
   //start main code here.
   //lets search for request that expire prior to right now.
   $sql="SELECT * FROM ".$list_prefix."prayer_list WHERE `expired` = '0' AND `expiredate` < '".time ()."';";
   $result=db_query($sql);
   $rows = db_num_rows($result);
   while ($i < $rows) {
      $row = db_fetch_array($result);
      //mail the user here if they are not anonymous
      if (0 != strcmp ($row['requested_by'], 'anonymous')) 
         mailuser ($row['requested_by'], $row);
//We need to change this to make the request "historical rather than deleting it.
      $sql = "UPDATE ".$list_prefix."prayer_list SET `expired` = '1' WHERE `id`='".$row['id']."';";
//end of change.
      $result2 = db_query($sql);
      $i++;
   }
   //lets handle the user interaction here.
   if (($HTTP_GET_VARS['delete']) && (is_numeric ($HTTP_GET_VARS['delete'])))
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
