<?
//****************************************************************************
//* File:	admin/calendar.php
//* Author:	G.A. Heath
//* Date: 	August 23, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 23, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "../common.inc.php";
include "../calendar.inc.php";

//===Functions================================================================

//***function loginbox ()*****************************************************
function loginbox () {
   printf ("<form method='post' action='index.php?login=1'>\r\n");
   printf ("Username: <input type='text' name='adminuser' size='20'><BR>");
   printf ("Password: <input type='password' name='adminpass' size='20'><BR>");
   printf ("<input type='submit' value='Login'><BR>");
   printf ("</form>\r\n");
}

//***function listevents ()***************************************************
function listevents () {
global $list_prefix;
$EVENTS="<select name='deletelist'>\r\n";
$count=0;
   //now we will list weekly events
   $sql="SELECT * FROM ".$list_prefix ."calendar WHERE `weekly` < '7';";
   $result=mysql_query($sql);
   if ($result)
      $rows=mysql_num_rows($result);
   else
      $rows=0;
   if ($rows > 0) {
      $i=0;
      while ($i < $rows) {
         $row=mysql_fetch_array($result);
      //calculate the time
         $time=$row['time'];
         $hour=$time[0].$time[1];
         $min=$time[2].$time[3];
         if ($hour < 12) { //we are in the am.
            if ($hour == 0)
               $hour="12";
            $time=$hour.":".$min."am";
         } else {  //we are in the pm
            $hour-=12;
            if ($hour == 0)
               $hour="12";
            $time=$hour.":".$min."pm";
         }
         $EVENTS.="<option value='".$row['id']."'>".day_name ($row['weekly'])." at ".$time."</option>\r\n";
         $count++;
         $i++;
      }
   }
   //we will do the monthly events
   $sql="SELECT * FROM ".$list_prefix ."calendar WHERE `monthly` NOT LIKE '';";
   $result=mysql_query($sql);
   if ($result)
      $rows=mysql_num_rows($result);
   else
      $rows=0;
   if ($rows > 0) {
      $i=0;
      while ($i < $rows) {
         $row=mysql_fetch_array($result);
      //calculate the time
         $time=$row['time'];
         $hour=$time[0].$time[1];
         $min=$time[2].$time[3];
         if ($hour < 12) { //we are in the am.
            if ($hour == 0)
               $hour="12";
            $time=$hour.":".$min."am";
         } else {  //we are in the pm
            $hour-=12;
            if ($hour == 0)
               $hour="12";
            $time=$hour.":".$min."pm";
         }
         $EVENTS.="<option value='".$row['id']."'>Monthly on the ".$row['monthly']." at ".$time."</option>\r\n";
         $count++;
         $i++;
      }
   }
   //we will do yearly events
   $sql="SELECT * FROM ".$list_prefix ."calendar WHERE `yearly` NOT LIKE '';";
   $result=mysql_query($sql);
   if ($result)
      $rows=mysql_num_rows($result);
   else
      $rows=0;
   if ($rows > 0) {
      $i=0;
      while ($i < $rows) {
         $row=mysql_fetch_array($result);
      //calculate the time
         $time=$row['time'];
         $hour=$time[0].$time[1];
         $min=$time[2].$time[3];
         if ($hour < 12) { //we are in the am.
            if ($hour == 0)
               $hour="12";
            $time=$hour.":".$min."am";
         } else {  //we are in the pm
            $hour-=12;
            if ($hour == 0)
               $hour="12";
            $time=$hour.":".$min."pm";
         }
         $t=$row['yearly'];
         $t=monthname ($t[0].$t[1])." ".$t[2].$t[3];
         $EVENTS.="<option value='".$row['id']."'>Yearly on ".$t." at ".$time."</option>\r\n";
         $count++;
         $i++;
      }
   }
   //now we will work scheduled events
   $sql="SELECT * FROM ".$list_prefix ."calendar WHERE `date` NOT LIKE '';";
   $result=mysql_query($sql);
   if ($result)
      $rows=mysql_num_rows($result);
   else
      $rows=0;
   if ($rows > 0) {
      $i=0;
      while ($i < $rows) {
         $row=mysql_fetch_array($result);
      //calculate the time
         $time=$row['time'];
         $hour=$time[0].$time[1];
         $min=$time[2].$time[3];
         if ($hour < 12) { //we are in the am.
            if ($hour == 0)
               $hour="12";
            $time=$hour.":".$min."am";
         } else {  //we are in the pm
            $hour-=12;
            if ($hour == 0)
               $hour="12";
            $time=$hour.":".$min."pm";
         }
         $t=$row['date'];
         $t=monthname ($t[4].$t[5])." ".$t[6].$t[7].", ".$t[0].$t[1].$t[2].$t[3];
         $EVENTS.="<option value='".$row['id']."'>".$t." at ".$time."</option>\r\n";
         $count++;
         $i++;
      }
   }
   if ($count == 0)
      $EVENTS.="<option value='-'>Nothing to delete</option>\r\n";
   $EVENTS.="</select>";
   return $EVENTS;
}

//***function yearmenu ()*****************************************************
function yearmenu () {
$year=date ("Y");
   $MENU="<select name='yearlist'>\r\n";
   $MENU.="   <option value='".($year-1)."'>".($year-1)."</option>\r\n";
   $MENU.="   <option value='".$year."' selected>".$year."</option>\r\n";
   $MENU.="   <option value='".($year+1)."'>".($year+1)."</option>\r\n";
   $MENU.="   <option value='".($year+2)."'>".($year+2)."</option>\r\n";
   $MENU.="   <option value='".($year+3)."'>".($year+3)."</option>\r\n";
   $MENU.="   <option value='".($year+4)."'>".($year+4)."</option>\r\n";
   $MENU.="</select>";
   return $MENU;
}

//***function usertime ($hour, $tmin, $omin, $ampm)***************************
function usertime ($hour, $tmin, $omin, $ampm) {
   if (0 == strcmp ($ampm, "a"))
      $utime=$hour;
   else
      $utime=$hour+12;
   $utime.=$tmin.$omin;
   return $utime;
}

//***function content ()******************************************************
function content () {
global $HTTP_POST_VARS, $HTTP_GET_VARS, $list_prefix;
$MAIN=loadadmintmplate ("main");
$CALENDAR=loadadmintmplate ("calendar");
$CONTENT="";
   //first lets see if we are deleting an event
   if (0 == strcmp ($HTTP_GET_VARS['mode'], "delete")) { //we are deleting this event.
      if (isset ($HTTP_POST_VARS['delete_yes'])) {
         $sql="DELETE FROM `".$list_prefix ."calendar` WHERE `id` = '".$HTTP_POST_VARS['deletelist']."';";
         $result=mysql_query($sql);
      } else
         $CONTENT.="You must check the checkbox to confirm deleting this event.<BR>\r\n";
   }
   //lets see if we are adding a weekly event
   if (0 == strcmp ($HTTP_GET_VARS['mode'], "dow")) { //we are adding a event
      $utime=usertime ($HTTP_POST_VARS['hour'], $HTTP_POST_VARS['tmin'], $HTTP_POST_VARS['omin'], $HTTP_POST_VARS['$ampm']);
      $sql="INSERT INTO `".$list_prefix."calendar` ( `id` , `weekly` , `monthly` , `yearly` , `date` , `time` , `description` ) VALUES ( '', '".$HTTP_POST_VARS['dow']."', '', '', '', '".$utime."', '".$HTTP_POST_VARS['description']."' );";
      $result=mysql_query($sql);
   }
   //lets see if we are adding a monthly event
   if (0 == strcmp ($HTTP_GET_VARS['mode'], "dom")) { //we are adding a event
      $utime=usertime ($HTTP_POST_VARS['hour'], $HTTP_POST_VARS['tmin'], $HTTP_POST_VARS['omin'], $HTTP_POST_VARS['$ampm']);
      $sql="INSERT INTO `".$list_prefix."calendar` ( `id` , `weekly` , `monthly` , `yearly` , `date` , `time` , `description` ) VALUES ( '', '7', '".$HTTP_POST_VARS['dom']."', '', '', '".$utime."', '".$HTTP_POST_VARS['description']."' );";
      $result=mysql_query($sql);   
   }
   //lets see if we are adding a yearly event
   if (0 == strcmp ($HTTP_GET_VARS['mode'], "moy")) { //we are adding a event
      $utime=usertime ($HTTP_POST_VARS['hour'], $HTTP_POST_VARS['tmin'], $HTTP_POST_VARS['omin'], $HTTP_POST_VARS['$ampm']);
      $sql="INSERT INTO `".$list_prefix."calendar` ( `id` , `weekly` , `monthly` , `yearly` , `date` , `time` , `description` ) VALUES ( '', '7', '', '".$HTTP_POST_VARS['moy'].$HTTP_POST_VARS['domoy']."', '', '".$utime."', '".$HTTP_POST_VARS['description']."' );";
      $result=mysql_query($sql);   
   }
   //lets see if we are adding a scheduled event
   if (0 == strcmp ($HTTP_GET_VARS['mode'], "norm")) { //we are adding a event
      $utime=usertime ($HTTP_POST_VARS['hour'], $HTTP_POST_VARS['tmin'], $HTTP_POST_VARS['omin'], $HTTP_POST_VARS['$ampm']);
      $sql="INSERT INTO `".$list_prefix."calendar` ( `id` , `weekly` , `monthly` , `yearly` , `date` , `time` , `description` ) VALUES ( '', '7', '', '', '".$HTTP_POST_VARS['yearlist'].$HTTP_POST_VARS['month'].$HTTP_POST_VARS['day']."', '".$utime."', '".$HTTP_POST_VARS['description']."' );";
      $result=mysql_query($sql);   
   }
   //lets draw our interface now.
   $CONTENT.=insert_into_template ($CALENDAR, "{DELETE_LIST}", listevents ());
   $CONTENT=insert_into_template ($CONTENT, "{YEARMENU}", yearmenu ());
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
   $WORK=filltemplate ($WORK, "{SITENAME} Administration panel");
   printf ("%s", striptemplate ($WORK));

}

//===Main code================================================================
   if (checkadminlogin () == 1) //if the user is logged in
      content ();
   else
      loginbox ();
?>