<?
//****************************************************************************
//* File:	leavepraise.php
//* Author:	G.A. Heath
//* Date: 	July 9, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 23, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "common.inc.php";
//***template loads***********************************************************
$MAIN=loadtmplate (main);
$LEAVEPRAISE=loadtmplate (leavepraise);
$APP="prayerlist";

//===Functions================================================================
//***function submissionform ()***********************************************
function submissionform () {
global $logged_in, $user, $HTTP_POST_VARS, $HTTP_GET_VARS, $MAIN, $LINKS, $LEAVEPRAISE;
   $WORK=loggedin ();
   //lets draw the request form.
   $WORK=insert_into_template ($LEAVEPRAISE, "{LOGGEDIN}", $CONTENT);
   $WORK=insert_into_template ($WORK, "{REQUESTID}", $HTTP_GET_VARS['request']);
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
   $WORK=filltemplate ($WORK, "Leave Praise");
   printf ("%s", striptemplate ($WORK));
}
//***function submissionform_redo ()******************************************
function submissionform_redo () {
global $logged_in, $user, $HTTP_POST_VARS, $HTTP_GET_VARS, $MAIN;
   $WORK=loggedin ();
   //lets redraw the request form.
   $WORK=insert_into_template ($LEAVEPRAISE, "{LOGGEDIN}", $CONTENT);
   $WORK=insert_into_template ($WORK, "{REQUESTID}", $HTTP_GET_VARS['request']);
   $WORK=insert_into_template ($WORK, "{PREFILL}", $HTTP_POST_VARS['praise']);
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
   $WORK=filltemplate ($WORK, "Leave Praise");
   printf ("%s", striptemplate ($WORK));
}
//***function processsubmission ()***********************************************
function processsubmission () {
global $logged_in, $user, $HTTP_POST_VARS, $list_prefix, $HTTP_GET_VARS, $MAIN;
//lets make sure anonymous requests are accepted as "logged in".
   if (isset ($HTTP_POST_VARS['anonymous'])) {
      $logged_in=1;
      $email='anonymous';
      $username='anonymous';
   } else {
      $email=$user['email'];
      $username=$user['username'];
   }
//lets accept request from users who are not cookied but are logging in.
   if ((!$logged_in) && (isset ($HTTP_POST_VARS['user']))) {
      $user=userlogin ($HTTP_POST_VARS['user'], $HTTP_POST_VARS['pass'], $HTTP_POST_VARS['automatic']);
      if (0 != strcmp ($user['email'], "anonymous")) {
         $logged_in=1;
         $email=$user['email'];
         $username=$user['username'];
      }
   }
//lets see if our user is logged in
   if (!$logged_in) { //if our user is not logged in we will redo the form for them with the data pre-entered.
      submissionform_redo ();
   } else { //if they are logged in we will process the request.
      $req_date=time();
      $sql="INSERT INTO ".$list_prefix."praise_list (request, praise, postdate, left_by, username) VALUES ('".$HTTP_GET_VARS['request']."', '".$HTTP_POST_VARS['praise']."', '".$req_date."',  '".addslashes($email)."', '".$username."');";
      $result=mysql_query($sql);
      if ($result)
         $CONTENT="Your praise been processed.<BR>\r\n";
      else {
         $CONTENT="ERROR: the server was unable to process your praise at this time.<BR>\r\n";
         $CONTENT.="The SQL query was: ".$sql."<BR>\r\n";
      }
      $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
      $WORK=filltemplate ($WORK, "Leave Praise");
      printf ("%s", striptemplate ($WORK));
   }
}

//===Main code================================================================
//check to see if the user is logged in.
$user = getuserinfo ();
   if (0 == strcmp ($user['email'] , "anonymous"))
      $logged_in = 0;
   else
      $logged_in = 1;
//lets do the submission code.
   if (isset ($HTTP_GET_VARS['submit']))//are we submitting a request?
      processsubmission ();
   else //if not lets allow them to submit.
      submissionform ();
?>