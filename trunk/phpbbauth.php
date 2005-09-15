<?
//****************************************************************************
//* File:	phpbbauth.php
//* Author:	G.A. Heath
//* Date: 	July 14, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	September 11, 2005
//****************************************************************************


//phpbb specific configuration variables.
$table_prefix = "phpbb_";
$path="/forums/";
$REGISTER=$path."profile.php?mode=register";
$GETPASS=$path."profile.php?mode=sendpassword";

//***function getuserinfo ()**************************************************
function getuserinfo () {
global $HTTP_COOKIE_VARS, $table_prefix;
//***Lets preconfill our user data structure here
$user['username']="anonymous";
$user['password']="";
$user['admin']="0"; //we want this to default to the user not being an administrator.
$user['email']="anonymous"; 
//***end preconfill
//!!!code time!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//lets get the cookie name.
   $sql="select * from ".$table_prefix."config WHERE config_name = 'cookie_name'";
   $result=db_query($sql);
   $rows = db_num_rows($result);
   if ($rows != 1) //if there is more than 1 result or if there is no result we can't trust the db.
      return $user;
   else
      $row = db_fetch_array($result);
   $cookiename=$row['config_value'];
   if (!isset($HTTP_COOKIE_VARS[$cookiename."_data"]))
      return $user;
//now lets get the cookie from the browser if its set.
   $sessiondata = isset($HTTP_COOKIE_VARS[$cookiename . '_data']) ? unserialize(stripslashes($HTTP_COOKIE_VARS[$cookiename . '_data'])) : '';  //this line was ripped from phpBB.
//now lets decode the cookie.
   $userid=$sessiondata['userid'];
   $autologinid=$sessiondata['autologinid'];
//now lets read the users info from the database.
   $query="select * from ".$table_prefix."users WHERE user_id = ".$userid;
   $result=db_query($query);
   if ($result)
      $rows = db_num_rows($result);
   else
      $rows = 0;
   if ($rows != 1)//we must have exactly 1 user with this userid number.
      return $user;
   $userdata = db_fetch_array($result);
//now lets compare the user info to the database info.
   if ($autologinid != $userdata['user_password'])
      return $user;
//now we will determine if the user is an administrator.
   if ($userdata['user_level'] == "1")
      $user['admin'] = 1;
//now lets return the user's email address.
   $user['username']=$userdata['username'];
   $user['password']=$userdata['user_password'];
   $user['email']=$userdata['user_email'];
   return $user;
}

//***function userlogin ($username, $pass, $automatic)************************************
function userlogin ($username, $pass, $automatic) {
global $HTTP_COOKIE_VARS, $table_prefix;
$pass = md5 ($pass);//lets encrypt the pass so we can compare it.
//***Lets preconfill our user data structure here
$user['username']="anonymous";
$user['password']="";
$user['admin']="0"; //we want this to default to the user not being an administrator.
$user['email']="anonymous"; 
//***end preconfill

//!!!code time!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//lets search the database for one username and encrypted password that match ours.
   $sql="select * from ".$table_prefix."users WHERE username = '".$username."' AND user_password = '".$pass."';";
   $result=db_query($sql);
   $rows = db_num_rows($result);
   if ($rows != 1) //if there is more than 1 result or if there is no result we can't trust the db.
      return $user;
   else
      $userdata = db_fetch_array($result);
//now we will determine if the user is an administrator.
   if ($userdata['user_level'] == "1")
      $user['admin'] = 1;
//now lets return the user's email address.
   $user['username']=$userdata['username'];
   $user['password']=$userdata['user_password'];
   $user['email']=$userdata['user_email'];
//now lets set the cookie
   $sessiondata['userid']=$userdata['user_id'];
   $sessiondata['autologinid']=$userdata['user_password'];
   $sql="select * from ".$table_prefix."config WHERE config_name = 'cookie_name'";
   $result=db_query($sql);
   $rows = db_num_rows($result);
   if ($rows != 1) //if there is more than 1 result or if there is no result we can't trust the db.
      return $user;
   else
      $row = db_fetch_array($result);
   $cookiename=$row['config_value'];
   if ((0 != strcmp ($user['username'], "anonymous")) && ($user['admin'] == 1)) {
      $cookiedata=serialize($sessiondata);
      if (isset ($automatic))
         setcookie ($cookiename, $cookiedata, time+(60 * 60 *24 * 365.25));
      else
         setcookie ($cookiename, $cookiedata);
   }
//now lets end the program
   return $user;
}

//***function getuser ($uid)**************************************************
function getuser ($uid) {
global $table_prefix;
   $sql="select * from ".$table_prefix."users WHERE user_id = ".$uid;
   $result=db_query($sql);
   $rows = db_num_rows($result);
   if ($rows > 0) {
      $userdata = db_fetch_array($result);
      return $userdata['username'];
   } else {
      return "anonymous";
   }
}

//***function admincookie ()**************************************************
function admincookie () {
global $table_prefix, $HTTP_COOKIE_VARS;
//***Lets preconfill our user data structure here
$user['username']="anonymous";
$user['password']="";
$user['admin']="0"; //we want this to default to the user not being an administrator.
$user['email']="anonymous"; 
$user['user_id']=0;
//***end preconfill
//we are hardwiring the admincookie name.
$cookiename="phpbbauth_admincookie";
//now lets read the cookie
   $sessiondata = isset($HTTP_COOKIE_VARS[$cookiename]) ? unserialize(stripslashes($HTTP_COOKIE_VARS[$cookiename])) : '';  //this line is adapted from above.
   $userid=$sessiondata['userid'];
   $autologinid=$sessiondata['autologinid'];
//now lets read the users info from the database.
   $query="select * from ".$table_prefix."users WHERE user_id = ".$userid;
   $result=db_query($query);
   if ($result)
      $rows = db_num_rows($result);
   else
      return $user;
   if ($rows != 1)//we must have exactly 1 user with this userid number.
      $admin=0;
   $userdata = db_fetch_array($result);
//now lets compare the user info to the database info.
   if ($autologinid != $userdata['user_password'])
      return $user;
//now we will determine if the user is an administrator.
   if ($userdata['user_level'] == "1")
      $user['admin'] = 1;
//now lets return the user's email address.
   $user['username']=$userdata['username'];
   $user['password']=$userdata['user_password'];
   $user['email']=$userdata['user_email'];
   $user['user_id']=$userdata['user_id'];
   return $user;
}

//***function checkadminlogin ()**********************************************
function checkadminlogin () {
global $table_prefix, $HTTP_COOKIE_VARS;
//lets see if we are logged in already.
   $admin=admincookie ();
   if ((0 != strcmp ($admin['username'], "anonymous")) && ($admin['admin'] == 1))
      return 1;
   else
      return 0;
}

//***function adminlogin ($username, $password)*******************************
function adminlogin ($username, $password) {
global $table_prefix, $HTTP_COOKIE_VARS;
$password = md5 ($password);//lets encrypt the pass so we can compare it.
//***Lets preconfill our user data structure here
$user['username']="anonymous";
$user['password']="";
$user['admin']="0"; //we want this to default to the user not being an administrator.
$user['email']="anonymous"; 
$user['user_id']=0;
//***end preconfill
//we are hardwiring the admincookie name.
$cookiename="phpbbauth_admincookie";
//now lets verify our user is loggedin
   $sql="select * from ".$table_prefix."users WHERE username = '".$username."' AND user_password = '".$password."';";
   $result=db_query($sql);
   $rows = db_num_rows($result);
   if ($rows != 1) //if there is more than 1 result or if there is no result we can't trust the db.
      return 0;
   else
      $userdata = db_fetch_array($result);
//now we will determine if the user is an administrator.
   if ($userdata['user_level'] == "1")
      $user['admin'] = 1;
//now lets return the user's email address.
   $user['username']=$userdata['username'];
   $user['email']=$userdata['user_email'];
   $sessiondata['userid']=$userdata['user_id'];
   $sessiondata['autologinid']=$userdata['user_password'];
      
//now lets log the user in with a cookie if they are really an admin.
   if ((0 != strcmp ($user['username'], "anonymous")) && ($user['admin'] == 1)) {
      $cookiedata=serialize($sessiondata);  //this line is adapted from above.
      setcookie ($cookiename, $cookiedata);
      return 1;
   } else {
      return 0;
   }
}

//***function loggedin ()*****************************************************
function loggedin () { //this function allows the login to be part of another form.
//eventually we want to phase this one out except when the prayerlist is in standalone mode.
global $user, $HTTP_POST_VARS;
//lets make sure that if our user is logged in or logging in that we know it.
   if (!isset ($user))
      $user=getuserinfo ();
   if ((0 == strcmp ($user['username'], "anonymous")) && (isset ($HTTP_POST_VARS['user'])))
      $user=userlogin ($HTTP_POST_VARS['user'], $HTTP_POST_VARS['pass'], $HTTP_POST_VARS['automatic']);
//lets see if we need to present the user with a login box
   if (0 == strcmp ($user['username'], "anonymous")) {
      $WORK="Please Enter your username and password to login:<BR>";
      $WORK.="Username: <input type='text' name='user' size='20'><BR>\r\n";
      $WORK.="Password: <input type='password' name='pass' size='20'><BR>\r\n";
      $WORK.="Log me on automatically each visit: <input type='checkbox' name='automatic' checked><BR>\r\n";
   } else {
      $WORK="You are currently logged in as ".$user['username'].".<BR>\r\n";
   }
   return $WORK;
}

//***function login ()*****************************************************
function login () { //this function draws a complete login form
global $user, $HTTP_POST_VARS;
//lets make sure that if our user is logged in or logging in that we know it.
   if (!isset ($user))
      $user=getuserinfo ();
   if ((0 == strcmp ($user['username'], "anonymous")) && (isset ($HTTP_POST_VARS['user'])))
      $user=userlogin ($HTTP_POST_VARS['user'], $HTTP_POST_VARS['pass'], $HTTP_POST_VARS['automatic']);
//lets see if we need to present the user with a login box
   if (0 == strcmp ($user['username'], "anonymous")) {
      if (iset($_SERVER["QUERY_STRING"]))
         $url=$_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"]."&login=1";
      else
         $url=$_SERVER["SCRIPT_NAME"]."?login=1";            
      $WORK="Please Enter your username and password to login:<BR>\r\n";
      $WORK.="<form method='post' action='".$url."'>\r\n";
      $WORK.="Username: <input type='text' name='user' size='20'><BR>\r\n";
      $WORK.="Password: <input type='password' name='pass' size='20'><BR>\r\n";
      $WORK.="Log me on automatically each visit: <input type='checkbox' name='automatic' checked><BR>\r\n";
      $WORK.="<input type='submit' value='Login'>\r\n";
      $WORK.="</form>\r\n";
   } else {
      $WORK="You are currently logged in as ".$user['username'].".<BR>\r\n";
   }
   return $WORK;
}

?>