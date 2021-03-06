<?
//****************************************************************************
//* File:	day.php
//* Author:	G.A. Heath
//* Date: 	August 23, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	September 11, 2005
//****************************************************************************

include "common.inc.php";
include "calendar.inc.php";

//===functions================================================================

//===main code================================================================
$MAIN=loadtmplate("main");
$DAY=loadtmplate("day");
$EVENTS=loadtmplate("events");

//now lets get to work.
   if ((isset ($HTTP_GET_VARS['month'])) && (isset ($HTTP_GET_VARS['day'])) && (isset ($HTTP_GET_VARS['year'])) && (is_numeric ($HTTP_GET_VARS['month'])) && (is_numeric ($HTTP_GET_VARS['day'])) && (is_numeric ($HTTP_GET_VARS['year']))) {
   //lets initialize our variables
      $CONTENT="";
      $day=$HTTP_GET_VARS['day'];
      $month=$HTTP_GET_VARS['month'];
      $year=$HTTP_GET_VARS['year'];
      if ($day < 10)
         $day="0".(1*$day);
      if ($month < 10)
         $month="0".(1*$month);
   //lets get the weekly events
      $sql="SELECT * FROM ".$list_prefix ."calendar WHERE (`weekly`='".dayofweek ($month, $day, $year)."' OR `monthly`='".$day. "' OR `yearly`='".$month.$day. "' OR `date`='".$year.$month.$day. "') ORDER BY `time`;";
      $result=db_query($sql);
      if ($result)
         $rows=db_num_rows($result);
      else
         $rows=0;
      if ($rows > 0) {
         $i=0;
         $WORK="";
         while ($i < $rows) {
            $row=db_fetch_array($result);
         //calculate the time
            $time=$row['time'];
            if ($time[0] > 0)
               $hour=$time[0].$time[1];
            else
               $hour=$time[1];
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
            $WORK.=insert_into_template ($EVENTS, "{TIME}", $time);
            $WORK=insert_into_template ($WORK, "{DESCRIPTION}", $row['description']);
            $i++;
         }
         $WORK=insert_into_template ($DAY, "{EVENTS}", $WORK);
         $CONTENT.=insert_into_template ($WORK, "{EVENT_CLASS}", "Daily events");
      }
   
   } else
      $CONTENT="<b>ERROR: A valid date was not presented to this program</b>";
   //now lets output the content
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
   $WORK=filltemplate ($WORK, $monthname);
   printf ("%s", striptemplate (stripslashes ($WORK)));
?>