<?
//****************************************************************************
//* File:	plsubmission.php
//* Author:	G.A. Heath
//* Date: 	July 7, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 13, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "common.inc.php";
//***template loads***********************************************************
$MAIN=loadtmplate (main);
$PRAISE=loadtmplate (praise);
$SUBMISSION=loadtmplate(plsubmission);
$APP="prayerlist";

//===Functions================================================================
//***function submissionform ()***********************************************
function submissionform () {
global $logged_in, $user, $MAIN, $SUBMISSION;
   $WORK=loggedin ();
   //lets draw the submission form
   $WORK=insert_into_template ($SUBMISSION, "{LOGGEDIN}", $WORK);
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
   $WORK=filltemplate ($WORK, "Submit a Prayer Request");
   printf ("%s", striptemplate ($WORK));
}
//***function submissionform_redo ()******************************************
function submissionform_redo () {
global $logged_in, $user, $HTTP_POST_VARS, $SUBMISSION, $MAIN;
   $WORK=loggedin ();
   //lets redraw the submission form.
   $WORK=insert_into_template ($SUBMISSION, "{LOGGEDIN}", $WORK);
   $WORK=insert_into_template ($WORK, "{REQUESTFOR}", $HTTP_POST_VARS['request_for']);
   $WORK=insert_into_template ($WORK, "{REQUEST}", $HTTP_POST_VARS['request']);
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
   $WORK=filltemplate ($WORK, "Submit a Prayer Request");
   printf ("%s", striptemplate ($WORK));

}
//***function processsubmission ()***********************************************
function processsubmission () {
global $logged_in, $user, $HTTP_POST_VARS, $list_prefix, $MAIN;
//lets make sure anonymous requests are accepted as "logged in".
   if (isset ($HTTP_POST_VARS['anonymous'])) {
      $logged_in=1;
      $email='anonymous';
   } else
      $email=$user['email'];
//lets accept request from users who are not cookied but are logging in.
   if ((!$logged_in) && (isset ($HTTP_POST_VARS['user']))) {
      $user=userlogin ($HTTP_POST_VARS['user'], $HTTP_POST_VARS['pass'], $HTTP_POST_VARS['automatic']);
      if (0 != strcmp ($user['email'], "anonymous")) {
         $logged_in=1;
         $email=$user['email'];
      }
   }
//lets see if our user is logged in
   if (!$logged_in) { //if our user is not logged in we will redo the form for them with the data pre-entered.
      submissionform_redo ();
   } else { //if they are logged in we will process the request.
      $req_date=time();
      switch ($HTTP_POST_VARS['expire_date']) {
         case ('1w'):
               $expire=$req_date+(60 * 60 *24 * 7);
            break;
         case ('2w'):
               $expire=$req_date+(2*(60 * 60 *24 * 7));
            break;
         case ('30d'):
               $expire=$req_date+(60 * 60 *24 * 30);
            break;
         case ('90d'):
               $expire=$req_date+(60 * 60 *24 * 90);
            break;
         case ('1y'):
               $expire=$req_date+(60 * 60 *24 * 365.25);
            break; 
      }
      $sql="INSERT INTO ".$list_prefix."prayer_list (request_for, request, postdate, expiredate, requested_by) VALUES ('".$HTTP_POST_VARS['request_for']."', '".$HTTP_POST_VARS['request']."', '".$req_date."', '".$expire."', '".addslashes($email)."');";
      $result=mysql_query($sql);
      if ($result)
         $WORK="Your prayer request has been processed.<BR>\r\n";
      else {
         $WORK="ERROR: the server was unable to process your prayer request at this time.<BR>\r\n";
         $WORK.="The SQL query was: ".$sql."<BR>\r\n";
      }
      $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
      $WORK=filltemplate ($WORK, "Submit a Prayer Request");
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