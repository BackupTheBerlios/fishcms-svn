<?
//****************************************************************************
//* File:	month.php
//* Author:	G.A. Heath
//* Date: 	August 22, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 22, 2005
//****************************************************************************

include_once "calendar.inc.php";

//===main code================================================================
//now lets do our calendar
//lets figure out what the Month ($month) and Year ($year) are.
$month = date ("m");
$year = date ("Y");
//now lets find out what day of the week the first of the month is , and how many days are in the month.
$daysinmonth = daysinmonth ($month, $year)."\r\n";
$firstofmonth = dayofweek ($month, "01", $year);
$monthname = monthname ($month);
// lets padd the content for the week before the first.
//-----------------------------------------------------------------------------
//---the code below may be converted to a function and placed in calendar.inc.php
//-----------------------------------------------------------------------------
$i=0;
//lets calculate the month and day for next month
$nextmonth=$month+1;
$nextyear=$year;
$prevmonth=$month-1;
$prevyear=$year;
if ($nextmonth > 12) {
   $nextmonth=1;
   $nextyear+=1;
}
if ($prevmonth < 1) {
   $prevmonth=12;
   $prevyear-=1;
}
//lets load the month template
$MONTH=loadblocktmplate ("month");
$WEEK=loadblocktmplate ("week");
$DAY=loadblocktmplate ("calday");
$LINK_DAY=loadblocktmplate ("callink_day");

//lets do the calendar
   $DAYS="";
   $WEEKS="";
   while ($i < $firstofmonth) {
      $DAYS.=$DAY;
      $i++;
   }
   $iday=0;
//lets put the days into our calendar
   while ($iday < $daysinmonth) {
      $iday ++;
      //check the db for anything that may be on this day
      //if there is activity on this day we will do
      $event=0;
      $sql="SELECT * FROM ".$list_prefix ."calendar WHERE `weekly`='".dayofweek ($month, $iday, $year)."';";
      $result=mysql_query($sql);
      if ($result)
         $rows=mysql_num_rows($result);
      else
         $rows=0;
      if ($rows > 0)
         $event=1;
      if ($iday < 10)
         $D="0".$iday;
      else
         $D=$iday;
      if ($month < 10)
         $M="0".(1*$month);
      else
         $M=$month;
      $sql="SELECT * FROM ".$list_prefix ."calendar WHERE `monthly`='".$D."';";
      $result=mysql_query($sql);
      if ($result)
         $rows=mysql_num_rows($result);
      else
         $rows=0;
      if ($rows > 0)
         $event=1;
      $sql="SELECT * FROM ".$list_prefix ."calendar WHERE `yearly`='".$M.$D."';";
      $result=mysql_query($sql);
      if ($result)
         $rows=mysql_num_rows($result);
      else
         $rows=0;
      if ($rows > 0)
         $event=1;
      $sql="SELECT * FROM ".$list_prefix ."calendar WHERE `date`='".$year.$M.$D."';";
      $result=mysql_query($sql);
      if ($result)
         $rows=mysql_num_rows($result);
      else
         $rows=0;
      if ($rows > 0)
         $event=1;
      if ($event != 0) {
         $WORK=insert_into_template ($LINK_DAY, "{MONTH}", $month);
         $WORK=insert_into_template ($WORK, "{DAY}", $iday);
         $DAYS.=insert_into_template ($WORK, "{YEAR}", $year);
      } else {
         $DAYS.=insert_into_template ($DAY, "{DAY}", $iday);
      }
      if (6 == dayofweek ($month, $iday, $year)) { //we will append these DAYS to WEEKS and empty DAYS
         $WEEKS.=insert_into_template ($WEEK, "{DAYS}", $DAYS);
         $DAYS="";
      }
   }
   //now lets pad the last week of the calendar
   $lastday=dayofweek ($month, $iday, $year);
   while ($lastday < 6) {
      $DAYS.=$DAY;
      $lastday++;
   }
   //now lets close out the calendar.
   $WEEKS.=insert_into_template ($WEEK, "{DAYS}", $DAYS);
   //lets add the month, year, prev/next month and year
   $WORK=insert_into_template ($MONTH, "{PREVMONTH}", $prevmonth);
   $WORK=insert_into_template ($WORK, "{PREVYEAR}", $prevyear);
   $WORK=insert_into_template ($WORK, "{NEXTMONTH}", $nextmonth);
   $WORK=insert_into_template ($WORK, "{NEXTYEAR}", $nextyear);
   $WORK=insert_into_template ($WORK, "{MONTH}", $monthname);
   $WORK=insert_into_template ($WORK, "{YEAR}", $year);
   //lets add our weeks to the calendar
   $BLOCK['title']="Calendar";
   $BLOCK['content']=insert_into_template ($WORK, "{WEEKS}", $WEEKS);
   //now lets output the calendar
?>