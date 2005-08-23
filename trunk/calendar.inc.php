<?
//****************************************************************************
//* File:	calendar.inc.php
//* Author:	G.A. Heath
//* Date: 	August 22, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 22, 2005
//****************************************************************************

//***function dayofweek ($month, $day, $year)*********************************
function dayofweek ($month, $day, $year) {
//lets do the math to find what dotw we have
   $C=100*floor($year/100);
   $Y=$year-$C;
   if ($month < 3)
      $Y=$Y-1;
   if ($month == 1)
      $m=11;
   elseif ($month == 2)
      $m=12;
   else
      $m=$month-2;
   $k=$day;
   $W = ($k + floor((2.6*$m) - 0.2) - (2*$C) + $Y + floor($Y/4) + floor($C/4))%7;
//now lets convert it to 0 = sunday, 1 = monday, 2 = tuesday .... 6 = saturday.   
   if ($W < 0)
      $W *= -1;
   switch ($W) {
      case (0):
         return 0;
         break;
      case (6):
         return 1;
         break;
      case (5):
         return 2;
         break;
      case (4):
         return 3;
         break;
      case (3):
         return 4;
         break;
      case (2):
         return 5;
         break;
      case (1):
         return 6;
         break;
   }
}

//***function day_name ($day)*************************************************
function day_name ($day) {
   switch ($day) {
      case (0):
         return "Sunday";
         break;
      case (1):
         return "Monday";
         break;
      case (2):
         return "Tuesday";
         break;
      case (3):
         return "Wednesday";
         break;
      case (4):
         return "Thursday";
         break;
      case (5):
         return "Friday";
         break;
      case (6):
         return "Saturday";
         break;
   }
}

//***function daysinmonth ($month, $year)*************************************
function daysinmonth ($month, $year) {
   $twentyeight=" 02 ";
   $thirty=" 04 06 09 11 ";
   $thirtyone=" 01 03 05 07 08 10 12 ";
   if (strstr ($twentyeight, $month))
      if (0 == ($year % 4))
         return 29;
      else
         return 28;
   if (strstr ($thirtyone, $month))
      return 31;
   if (strstr ($thirty, $month))
      return 30;
}

//***function monthname ($month)**********************************************
function monthname ($month) {
   switch ($month) {
      case (1):
         return "January";
         break;
      case (2):
         return "February";
         break;
      case (3):
         return "March";
         break;
      case (4):
         return "April";
         break;
      case (5):
         return "May";
         break;
      case (6):
         return "June";
         break;
      case (7):
         return "July";
         break;
      case (8):
         return "August";
         break;
      case (9):
         return "September";
         break;
      case (10):
         return "October";
         break;
      case (11):
         return "November";
         break;
      case (12):
         return "December";
         break;
   }
}
