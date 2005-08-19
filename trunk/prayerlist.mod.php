<?
//****************************************************************************
//* File:	prayerlist.mod.php
//* Author:	G.A. Heath
//* Date: 	August 1, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 13, 2005
//****************************************************************************

//===common code that should be run each time=================================
//***includes*****************************************************************
//===Functions================================================================
//***function showlist ()*****************************************************
function showlist ($perpage) {
global $list_prefix;
$PRAYERLISTMOD=loadtmplate ("prayerlist.mod");
//lets set our content to be blank.
$CONTENT="";
 //lets setup our query
  $sql="SELECT * FROM ".$list_prefix."prayer_list WHERE `expired` = '0' LIMIT 0, ".$perpage.";";
//now lets show the prayerlist entries.
   $result=mysql_query($sql);
@   $rows = mysql_num_rows($result);
   if ($rows != 0) {
      $j=0;
      while ($j < $rows) {
         //lets fetch our prayer request from the database.
         $row = mysql_fetch_array($result);
         //lets insert the prayerrequest into our working copy of this template.
         $WORK=insert_into_template ($PRAYERLISTMOD, "{REQUESTFOR}", striphtml ($row['request_for']));
         $WORK=insert_into_template ($WORK, "{REQUEST}", striphtml ($row['request']));
         if ($admin == 1)
            $WORK=insert_into_template ($WORK, "{DELETE}", "<a href='prayerlist.php?delete=".$row['id']."'>Delete</a>");
         $j++;
         //now lets add this request to the CONTENT.
         $WORK=insert_into_template ($WORK, "{REQUESTID}", $row['id']);
         $CONTENT.=$WORK;
      }
   } else {
      $CONTENT.="There are no active prayer requests at this time.<BR>\r\n";
   }
   //when we output this lets make sure that the output is stripped of any template elements that are not used.
   return striptemplate ($CONTENT);
}

//***function mailuser ($email, $request)*************************************
function mailuser ($email, $request) {
global $list_prefix;
//lets figure out who to send the message as.
   $sql="SELECT * FROM ".$list_prefix."prayer_list WHERE `key` = 'email';";
   $result=mysql_query($sql);
   @ $rows=mysql_num_rows($result);
   if($rows > 0) {
      $row = mysql_fetch_array($result);
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
function prayerlist_mod ($perpage) {
global $list_prefix;
//this is a module so we should already know if the user is logged in.
   //start main code here.
   //lets search for request that expire prior to right now.
   $sql="SELECT * FROM ".$list_prefix."prayer_list WHERE `expired` = '0' AND `expiredate` < '".time ()."';";
   $result=mysql_query($sql);
   @ $rows=mysql_num_rows($result);
   $j=0;
   while ($j < $rows) {
      $row = mysql_fetch_array($result);
      //mail the user here if they are not anonymous
      if (0 != strcmp ($row['requested_by'], 'anonymous')) 
         mailuser ($row['requested_by'], $row);
//We need to change this to make the request historical rather than deleting it.
      $sql = "UPDATE ".$list_prefix."prayer_list SET `expired` = '1' WHERE `id`='".$row['id']."';";
//end of change.
      $result2 = mysql_query($sql);
      $j++;
   }
   $MOD['title']="Prayer Request";
   $MOD['content']=showlist ($perpage);
   return $MOD;
}

//code run because we are who we are.
$MOD = prayerlist_mod ($perpage);
?>
