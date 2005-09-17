<?
//****************************************************************************
//* File:	install.php
//* Author:	G.A. Heath
//* Date: 	September 11, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	September 11, 2005
//****************************************************************************
//===Pre-defined variables====================================================
$VERSION="0.2.2";
$INSTALLDIR="../";
$CONFIGFILE="fishcms-config.php";

//===Functions================================================================

//***function install_header ()***********************************************
function install_header () {
global $VERSION;
$header="<!DOCTYPE HTML SYSTEM>
<html>
   <head>
      <title>FishCMS installer.</title>
      <META http-equiv='Pragma' content='no-cache'>
   </head>
   <body bgcolor='#ffffff' text='#000000' link='#ff0000' alink='#ff0000' vlink='#0000ff'>
   <div align='center'><h1>Install FishCMS Version ".$VERSION."</h1></div><br>
";
   printf ("%s", $header);
}

//***function install_footer ()***********************************************
function install_footer () {
$footer="   </body>
</html>
";
   printf ("%s", $footer);
}

//***function pre_install ()**************************************************
function pre_install () {
global $CONFIGFILE, $INSTALLDIR;
$fatal=0;  //lets start out without any fatal errors.
   //lets see if we can write the fishcms-config.php file
   printf ("<h2>Preinstallation tests</h2>\r\n");
   if (is_writable ($INSTALLDIR.$CONFIGFILE))
      printf ("CONFIGURATION FILE IS <font color='#00ff00'>WRITABLE.</font><br>\r\n");
   else {
      printf ("CONFIGURATION FILE IS <font color='#ff0000'>NOT WRITABLE.</font><br>\r\n");
      $fatal=1;
   }
   //lets see if we can make directories
   if (is_writable ($INSTALLDIR))
      printf ("INSTALL DIRECTORY IS <font color='#00ff00'>WRITABLE.</font><br>\r\n");
   else {
      printf ("INSTALL DIRECTORY IS <font color='#ff0000'>NOT WRITABLE.</font><br>\r\n");
      $fatal=1;
   }
   //lets see if wget is usable
   $retvar=0;
   system ("wget -V > /dev/null", $retvar);
   if ($retvar == 0)
      printf ("WGET IS <font color='#0000ff'>INSTALLED</font><br>\r\n");
   else
      printf ("WGET IS <font color='#ff0000'>NOT INSTALLED</font> **not fatal**<br>\r\n");
   //lets see if unzip is usable
   system ("unzip -v > /dev/null", $retvar);
   if ($retvar == 0)
      printf ("UNZIP IS <font color='#0000ff'>INSTALLED</font><br>\r\n");
   else
      printf ("UNZIP IS <font color='#ff0000'>NOT INSTALLED</font> **not fatal**<br>\r\n");
   //now lets send the user to the next step
   if ($fatal) {
      printf ("The fishcms-config.php file and the installation directory must be writable by the webserver.<BR>\r\n");
      printf ("Please change the permissions and click <a href='install.php'>here</a> to retry.<br>\r\n");
   } else {
      printf ("<form method='post' action='install.php?mode=selectdb'>\r\n");
      printf ("   <input type='submit' value='Next &gt;&gt;'>\r\n");
      printf ("</form>\r\n");
   }
}

//***function selectdb ()*****************************************************
function selectdb () {
//for now we support mysql and postgresql, well kinda support postgresql.
//we need to know dbms type, hostname, dbname, db_username, db_password, list_prefix
   printf ("<h2>Database Configuration</h2>\r\n");
   printf ("<form method='post' action='install.php?mode=selectauth'>\r\n");
   printf ("<table border=0>\r\n");
   printf ("<tr><td>Database type:</td><td><select name='dbtype'>\r\n");
   printf ("   <option value='mysql'>MySQL</option>\r\n");
   printf ("   <option value='pgsql'>PostgreSQL</option>\r\n");
   printf ("</select></td></tr>\r\n");
   printf ("<tr><td>Database server:</td><td><input type='text' name='db_host' size='20' value='localhost'></td></tr>\r\n");
   printf ("<tr><td>Database name:</td><td><input type='text' name='db_database' size='20'></td></tr>\r\n");
   printf ("<tr><td>Database username:</td><td><input type='text' name='db_username' size='20'></td></tr>\r\n");
   printf ("<tr><td>Database password:</td><td><input type='password' name='db_password' size='20'></td></tr>\r\n");
   printf ("<tr><td>DB table prefix:</td><td><input type='text' name='list_prefix' size='20' value='FishCMS_'></td></tr>\r\n");
   printf ("</table>\r\n");
   printf ("<input type='submit' value='Next &gt;&gt;'>\r\n");
   printf ("</form>\r\n");
}
//***function install_mysql ()************************************************
function install_mysql () {
global $HTTP_POST_VARS;
//lets set our variables.
$username=$HTTP_POST_VARS['db_username'];
$password=$HTTP_POST_VARS['db_password'];
$hostname=$HTTP_POST_VARS['db_host'];
$database=$HTTP_POST_VARS['db_database'];
$prefix=$HTTP_POST_VARS['list_prefix'];
//here we will create the mysql query strings for our installation.
$sql['articles']    = "CREATE TABLE `".$prefix."articles` (`id` tinyint(4) NOT NULL auto_increment, `article_title` varchar(128) NOT NULL default '', `teaser` text NOT NULL, `article` text NOT NULL, `posted_by` tinyint(4) NOT NULL default '0', `byline` varchar(255) NOT NULL default '', `date` int(11) NOT NULL default '0', `category` tinyint(4) NOT NULL default '0', PRIMARY KEY  (`id`));";
$sql['blocks']      = "CREATE TABLE `".$prefix."blocks` (`id` tinyint(4) NOT NULL auto_increment, `name` varchar(64) NOT NULL default '', `blockset` tinyint(4) NOT NULL default '0', `order` tinyint(4) NOT NULL default '0', PRIMARY KEY  (`id`));";
$sql['calendar']    = "CREATE TABLE `".$prefix."calendar` (`id` tinyint(4) NOT NULL auto_increment, `weekly` tinyint(4) NOT NULL default '7', `monthly` char(3) NOT NULL default '', `yearly` varchar(5) NOT NULL default '', `date` varchar(9) NOT NULL default '', `time` varchar(5) NOT NULL default '', `description` text NOT NULL, PRIMARY KEY  (`id`));";
$sql['category']    = "CREATE TABLE `".$prefix."category` (`id` tinyint(4) NOT NULL auto_increment, `name` varchar(128) NOT NULL default '', `order` tinyint(4) NOT NULL default '0', PRIMARY KEY  (`id`));";
$sql['config']      = "CREATE TABLE `".$prefix."config` (`key` varchar(25) NOT NULL default '', `value` varchar(255) NOT NULL default '', `order` tinyint(4) NOT NULL default '0');";
$sql['links']       = "CREATE TABLE `".$prefix."links` (`id` tinyint(4) NOT NULL auto_increment, `category` tinyint(4) NOT NULL default '0', `title` varchar(56) NOT NULL default '', `url` varchar(255) NOT NULL default '', `order` tinyint(4) NOT NULL default '0', PRIMARY KEY  (`id`));";
$sql['news']        = "CREATE TABLE `".$prefix."news` (`id` tinyint(4) NOT NULL auto_increment, `news_title` varchar(128) NOT NULL default '', `teaser` text NOT NULL, `news` text NOT NULL, `posted_by` tinyint(4) NOT NULL default '0', `byline` varchar(255) NOT NULL default '', `date` int(11) NOT NULL default '0', `category` tinyint(4) NOT NULL default '0', PRIMARY KEY  (`id`));";
$sql['praise_list'] = "CREATE TABLE `".$prefix."praise_list` (`id` int(11) NOT NULL auto_increment, `request` int(11) NOT NULL default '0', `praise` text NOT NULL, `postdate` int(11) NOT NULL default '0', `left_by` varchar(255) NOT NULL default '', `username` varchar(56) NOT NULL default '', PRIMARY KEY  (`id`));";
$sql['prayer_list'] = "CREATE TABLE `".$prefix."prayer_list` (`id` int(11) NOT NULL auto_increment, `request_for` varchar(80) NOT NULL default '', `request` text NOT NULL, `postdate` int(11) NOT NULL default '0', `expiredate` int(11) NOT NULL default '0', `requested_by` varchar(255) NOT NULL default '', `username` varchar(56) NOT NULL default '', `expired` tinyint(4) NOT NULL default '0', PRIMARY KEY  (`id`));";
//lets tell the user whats happening.
   printf ("Testing MySQL configuration: ");
//now lets see if we can connect and select database.
   $errror=0;
   @ $db=mysql_pconnect ($hostname, $username, $password);
   if (!$db) {
      $errror=1;
   } elseif (!mysql_select_db ($database))
      $errror=1;
   if ($errror == 0) { //we were able to connect.
      printf ("<font color='#00ff00'>OK</font><br>\r\n");
      printf ("Installing database tables: ");
      $error=0;
      if (@mysql_query($sql['articles']) == 0)
         $error++;
      if (@mysql_query($sql['blocks']) == 0)
         $error++;
      if (@mysql_query($sql['calendar']) == 0)
         $error++;
      if (@mysql_query($sql['category']) == 0)
         $error++;
      if (@mysql_query($sql['config']) == 0)
         $error++;
      if (@mysql_query($sql['links']) == 0)
         $error++;
      if (@mysql_query($sql['news']) == 0)
         $error++;
      if (@mysql_query($sql['praise_list']) == 0)
         $error++;
      if (@mysql_query($sql['prayer_list']) == 0)
         $error++;
      echo mysql_query($sql['prayer_list']);
      //lets report our results.
      if ($errors == 0)
         printf ("<font color='#00ff00'>OK</font><br>\r\n");
      else
         printf ("<font color='#ff0000'>%d ERRORS</font><br>\r\n", $errors);
      return 1;  //we will return 1 as the tables may already be installed.      
   } else { //we were not able to connect.
      printf ("<font color='#ff0000'>ERROR</font><br>\r\n");
      printf ("<h2>Database Configuration</h2>\r\n");
      printf ("<form method='post' action='install.php?mode=selectauth'>\r\n");
      printf ("<table border=0>\r\n");
      printf ("<tr><td>Database type:</td><td><select name='dbtype'>\r\n");
      printf ("   <option value='mysql' SELECTED>MySQL</option>\r\n");
      printf ("   <option value='pgsql'>PostgreSQL</option>\r\n");
      printf ("</select></td></tr>\r\n");
      printf ("<tr><td>Database server:</td><td><input type='text' name='db_host' size='20' value='%s'></td></tr>\r\n", $hostname);
      printf ("<tr><td>Database name:</td><td><input type='text' name='db_database' size='20' value='%s'></td></tr>\r\n", $database);
      printf ("<tr><td>Database username:</td><td><input type='text' name='db_username' size='20' value='%s'></td></tr>\r\n", $username);
      printf ("<tr><td>Database password:</td><td><input type='password' name='db_password' size='20'></td></tr>\r\n");
      printf ("<tr><td>DB table prefix:</td><td><input type='text' name='list_prefix' size='20' value='%s'></td></tr>\r\n", $prefix);
      printf ("</table>\r\n");
      printf ("<input type='submit' value='Next &gt;&gt;'>\r\n");
      printf ("</form>\r\n");
      return 0;
   }
}

//***function install_pgsql ()************************************************
function install_pgsql () {
global $HTTP_POST_VARS;
//lets set our variables.
   $username=$HTTP_POST_VARS['db_username'];
   $password=$HTTP_POST_VARS['db_password'];
   $hostname=$HTTP_POST_VARS['db_host'];
   $database=$HTTP_POST_VARS['db_database'];
   $prefix=$HTTP_POST_VARS['list_prefix'];
//here we will create the pgsql query strings for our installation.
$sql_seq['articles']    = "CREATE SEQUENCE ".$prefix."articles_id_seq start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;";
$sql_seq['blocks']      = "CREATE SEQUENCE ".$prefix."blocks_id_seq start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;";
$sql_seq['calendar']    = "CREATE SEQUENCE ".$prefix."calendar_id_seq start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;";
$sql_seq['category']    = "CREATE SEQUENCE ".$prefix."category_id_seq start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;";
$sql_seq['links']       = "CREATE SEQUENCE ".$prefix."links_id_seq start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;";
$sql_seq['news']        = "CREATE SEQUENCE ".$prefix."news_id_seq start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;";
$sql_seq['praise_list'] = "CREATE SEQUENCE ".$prefix."praise_list_id_seq start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;";
$sql_seq['prayer_list'] = "CREATE SEQUENCE ".$prefix."prayer_list_id_seq start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1;";

$sql['articles']    = "CREATE TABLE ".$prefix."articles ( id int2 DEFAULT nextval('articles_id_seq'::text) NOT NULL, article_title varchar(128) NOT NULL default '', teaser text NOT NULL, article text NOT NULL, posted_by int2 default '0', byline varchar(255) NOT NULL default '', \"date\" int4 default '0', category int2 default '0', CONSTRAINT ".$prefix."articles_pkey PRIMARY KEY (id) );";
$sql['blocks']      = "CREATE TABLE ".$prefix."blocks ( id int2 DEFAULT nextval('blocks_id_seq'::text) NOT NULL, name varchar(64) NOT NULL default '', blockset int2 default '0', \"order\" int2 default '0', CONSTRAINT ".$prefix."blocks_pkey PRIMARY KEY (id) );";
$sql['calendar']    = "CREATE TABLE ".$prefix."calendar ( id int2 DEFAULT nextval('calendar_id_seq'::text) NOT NULL, weekly int2 default '7', monthly char(3) NOT NULL default '', yearly varchar(5) NOT NULL default '', \"date\" varchar(9) NOT NULL default '', \"time\" varchar(5) NOT NULL default '', description text NOT NULL, CONSTRAINT ".$prefix."calendar_pkey PRIMARY KEY (id) );";
$sql['category']    = "CREATE TABLE ".$prefix."category ( id int2 DEFAULT nextval('category_id_seq'::text) NOT NULL, name varchar(128) NOT NULL default '', \"order\" int2 default '0', CONSTRAINT ".$prefix."category_pkey PRIMARY KEY (id) );";
$sql['config']      = "CREATE TABLE ".$prefix."config ( \"key\" varchar(25) NOT NULL default '', \"value\" varchar(255) NOT NULL default '', \"order\" int2 default '0' );";
$sql['links']       = "CREATE TABLE ".$prefix."links ( id int2 DEFAULT nextval('links_id_seq'::text) NOT NULL, category int2 default '0', title varchar(56) NOT NULL default '', url varchar(255) NOT NULL default '', \"order\" int2 default '0', CONSTRAINT ".$prefix."links_pkey PRIMARY KEY (id) );";
$sql['news']        = "CREATE TABLE ".$prefix."news ( id int2 DEFAULT nextval('news_id_seq'::text) NOT NULL, news_title varchar(128) NOT NULL default '', teaser text NOT NULL, news text NOT NULL, posted_by int2 default '0', byline varchar(255) NOT NULL default '', \"date\" int4 default '0', category int2 default '0', CONSTRAINT ".$prefix."news_pkey PRIMARY KEY (id) );";
$sql['praise_list'] = "CREATE TABLE ".$prefix."praise_list ( id int4 DEFAULT nextval('praise_list_id_seq'::text) NOT NULL, request int4 default '0', praise text NOT NULL, postdate int4 default '0', left_by varchar(255) NOT NULL default '', username varchar(56) NOT NULL default '', CONSTRAINT ".$prefix."praise_list_pkey PRIMARY KEY (id) );";
$sql['prayer_list'] = "CREATE TABLE ".$prefix."prayer_list ( id int4 DEFAULT nextval('prayer_list_id_seq'::text) NOT NULL, request_for varchar(80) NOT NULL default '', request text NOT NULL, postdate int4 default '0', expiredate int4 default '0', requested_by varchar(255) NOT NULL default '', username varchar(56) NOT NULL default '', expired int2 default '0', CONSTRAINT ".$prefix."prayer_list_pkey PRIMARY KEY (id) );";
//lets tell the user whats happening.
   printf ("Testing PostgreSQL configuration: ");
//lets create our connect string.
   $pgconnect="";
   if ($hostname != '') {
      $pgconnect.="host=".$hostname." ";
   }
   if ($database != '') {
      $pgconnect.="dbname=".$database." ";
   }
   if ($username != '') {
      $pgconnect.="user=".$username." ";
   }
   if ($password != '') {
      $pgconnect.="password=".$password." ";
   }
//now lets see if we can connect.
   if (@ $db=pg_pconnect ($pgconnect)) { //we were able to connect.
      printf ("<font color='#00ff00'>OK</font><br>\r\n");
      printf ("Installing database tables: ");
      //now we will install the db tables.
      $errors=0;
      if (@pg_query ($sql_seq['articles']) == 0)
         $errors++;
      if (@pg_query ($sql_seq['blocks']) == 0)
         $errors++;
      if (@pg_query ($sql_seq['calendar']) == 0)
         $errors++;
      if (@pg_query ($sql_seq['category']) == 0)
         $errors++;
      if (@pg_query ($sql_seq['links']) == 0)
         $errors++;
      if (@pg_query ($sql_seq['news']) == 0)
         $errors++;
      if (@pg_query ($sql_seq['praise_list']) == 0)
         $errors++;
      if (@pg_query ($sql_seq['prayer_list']) == 0)
         $errors++;
      if (@pg_query ($sql['articles']) == 0)
         $errors++;
      if (@pg_query ($sql['blocks']) == 0)
         $errors++;
      if (@pg_query ($sql['calendar']) == 0)
         $errors++;
      if (@pg_query ($sql['category']) == 0)
         $errors++;
      if (@pg_query ($sql['config']) == 0)
         $errors++;
      if (@pg_query ($sql['links']) == 0)
         $errors++;
      if (@pg_query ($sql['news']) == 0)
         $errors++;
      if (@pg_query ($sql['praise_list']) == 0)
         $errors++;
      if (@pg_query ($sql['prayer_list']) == 0)
         $errors++;
      //now lets report the results.
      if ($errors == 0)
         printf ("<font color='#00ff00'>OK</font><br>\r\n");
      else
         printf ("<font color='#ff0000'>%d ERRORS</font><br>\r\n", $errors);
      return 1;  //we will return 1 as the tables may already be installed.
   } else { //we were not able to connect.
      printf ("<font color='#ff0000'>ERROR</font><br>\r\n");
      printf ("<h2>Database Configuration</h2>\r\n");
      printf ("<form method='post' action='install.php?mode=selectauth'>\r\n");
      printf ("<table border=0>\r\n");
      printf ("<tr><td>Database type:</td><td><select name='dbtype'>\r\n");
      printf ("   <option value='mysql'>MySQL</option>\r\n");
      printf ("   <option value='pgsql' SELECTED>PostgreSQL</option>\r\n");
      printf ("</select></td></tr>\r\n");
      printf ("<tr><td>Database server:</td><td><input type='text' name='db_host' size='20' value='%s'></td></tr>\r\n", $hostname);
      printf ("<tr><td>Database name:</td><td><input type='text' name='db_database' size='20' value='%s'></td></tr>\r\n", $database);
      printf ("<tr><td>Database username:</td><td><input type='text' name='db_username' size='20' value='%s'></td></tr>\r\n", $username);
      printf ("<tr><td>Database password:</td><td><input type='password' name='db_password' size='20'></td></tr>\r\n");
      printf ("<tr><td>DB table prefix:</td><td><input type='text' name='list_prefix' size='20' value='%s'></td></tr>\r\n", $prefix);
      printf ("</table>\r\n");
      printf ("<input type='submit' value='Next &gt;&gt;'>\r\n");
      printf ("</form>\r\n");
      return 0;
   }
}

//***function selectauth ()***************************************************
function selectauth () {
global $HTTP_POST_VARS, $INSTALLDIR, $CONFIGFILE;
   //lets test our db access.
   if ($HTTP_POST_VARS['dbtype'] == "mysql")
      $cont=install_mysql ();
   elseif ($HTTP_POST_VARS['dbtype'] == "pgsql")
      $cont=install_pgsql ();
   else
      die ("ERROR: UNKNOWN DBMS SELECTED.");
   //if ($cont != 0) we will draw the selectauth form.
   if ($cont != 0) {
//now we will create the config file contents
      printf ("Writing config file: ");
      $config_contents="<?\r\n";
      $config_contents.="//****************************************************************************\r\n";
      $config_contents.="//*     This file was generated by the fishCMS installer.\r\n";
      $config_contents.="//*     PLEASE DO NOT EDIT THE CONTENTS OF THIS FILE UNLESS YOU ARE 100% SURE!\r\n";
      $config_contents.="//****************************************************************************\r\n";
      $config_contents.="\$db_type='".$HTTP_POST_VARS['dbtype']."';\r\n";
      $config_contents.="\$db_host='".$HTTP_POST_VARS['db_host']."';\r\n";
      $config_contents.="\$db_username='".$HTTP_POST_VARS['db_username']."';\r\n";
      $config_contents.="\$db_password='".$HTTP_POST_VARS['db_password']."';\r\n";
      $config_contents.="\$db_database='".$HTTP_POST_VARS['db_database']."';\r\n";
      $config_contents.="\$list_prefix='".$HTTP_POST_VARS['list_prefix']."';\r\n";
      $config_contents.="?>\r\n";
      if ($file= @fopen($INSTALLDIR.$CONFIGFILE, "w")) {
         fwrite ($file, $config_contents);
         fclose ($file);
         printf ("<font color='#00ff00'>OK</font><br>\r\n");
      } else {
         printf ("<font color='#ff0000'>ERROR</font><br>\r\n");
         printf ("Please copy and paste the following text in your FishCMS directory as %s<BR>\r\n", $CONFIGFILE);
         printf ("<pre>%s</pre>\r\n", htmlspecialchars ($config_contents));
         printf ("<BR><BR>");            
      }
//now we will draw the authentication table
      printf ("<h2>Authentication module selection.</h2>\r\n");
      printf ("<form method='post' action='install.php?mode=siteconfig'>\r\n");
      printf ("<table border=0>\r\n");
      printf ("<tr><td>Authentication type:</td><td><select name='authtype'>\r\n");
      printf ("   <option value='fishauth'>FishCMS</option>\r\n");
      printf ("   <option value='phpbbauth'>phpBB2</option>\r\n");
      printf ("</select></td></tr>\r\n");
      printf ("</table>\r\n");
      printf ("<input type='submit' value='Next &gt;&gt;'>\r\n");
      printf ("</form>\r\n");   
   }
}

//***function siteconfig ()***************************************************
function siteconfig () {
global $HTTP_POST_VARS;
//lets set our default values here.
$FORM['sitename']=$_SERVER['HTTP_HOST'];
$FORM['sitedescription']="A FishCMS powered site.";
$FORM['copyright']="FishCMS is licensed under the GNU <a href='http://www.gnu.og/licenses/gpl.html'>General Public License</a><BR>&copy; 2005 by G.A. Heath and Michael Rice.";
$FORM['authtype']=$HTTP_POST_VARS['authtype'];
$FORM['user']="";
$FORM['email']="";
//if we have alternate values set lets use them (in the event of a form redo).
   if (isset($HTTP_POST_VARS['sitename']))
      $FORM['sitename']=$HTTP_POST_VARS['sitename'];
   if (isset($HTTP_POST_VARS['sitedescription']))
      $FORM['sitedescription']=$HTTP_POST_VARS['sitedescription'];
   if (isset($HTTP_POST_VARS['copyright']))
      $FORM['copyright']=$HTTP_POST_VARS['copyright'];
   if (isset($HTTP_POST_VARS['email']))
      $FORM['email']=$HTTP_POST_VARS['email'];
   if (isset($HTTP_POST_VARS['user']))
      $FORM['user']=$HTTP_POST_VARS['user'];
   //first lets draw the form.  we will hide the auth module value in here and insert it in to the db in the next stage.
   printf ("<h2>General site configuration.</h2>\r\n");
   printf ("<form method='post' action='install.php?mode=optional'>\r\n");
   printf ("<table border=0>\r\n");
   //general site options.
   printf ("<TR><TD colspan='2' bgcolor='#0000ff'><font color='#ffff00'>General Configuration</font></TD></TR>\r\n");
   printf ("<TR><TD>Site name:</TD><TD><input type='text' name='sitename' size='20' value='%s'></TD></TR>\r\n", $FORM['sitename']);
   printf ("<TR><TD>Site Description:</TD><TD><input type='text' name='sitedescription' size='20' value='%s'></TD></TR>\r\n", $FORM['sitedescription']);
   printf ("<TR><TD>Administrators email:</TD><TD><input type='text' name='admin_email' size='20' value='%s'></TD></TR>\r\n", $FORM['email']);
   printf ("<TR><TD>Copyright Notice:</TD><TD><textarea name='copyright' rows='4' cols='60'>%s</textarea></TD></TR>\r\n", $FORM['copyright']);
   //authentication options
   if ($FORM['authtype'] == "fishauth") {
      printf ("<TR><TD colspan='2' bgcolor='#0000ff'><font color='#ffff00'>Administrative user</font></TD></TR>\r\n");
      printf ("<TR><TD>Username:</TD><TD><input type='text' name='user' size='20' value='%s'></TD></TR>\r\n", $FORM['user']);
      printf ("<TR><TD>Password:</TD><TD><input type='password' name='userpass1' size='20'></TD></TR>\r\n");
      printf ("<TR><TD>Verify password:</TD><TD><input type='password' name='userpass2 size='20'></TD></TR>\r\n");
   }
   //end of form.
   printf ("</table>\r\n");
   printf ("<input type='hidden' name='authtype' value='%s'>\r\n", $FORM['authtype']);
   printf ("<input type='submit' value='Next &gt;&gt;'>\r\n");
   printf ("</form>\r\n");   
}

//***function optional ()*****************************************************
function optional () {
global $HTTP_POST_VARS, $INSTALLDIR, $CONFIGFILE, $VERSION;
//lets connect to, and prepare to use, our database.
include ($INSTALLDIR.$CONFIGFILE); //this loads the config file previously created.
include ($INSTALLDIR."db/".$db_type.".inc.php"); //this should load the db "layer".
//lets insert the default values into our database (including the default article, news, ect.)
printf ("Inserting default database values: ");
$errors=0;
$sql="INSERT INTO ".$list_prefix."config VALUES ('template', 'default');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."config VALUES ('index', 'modules');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."config VALUES ('indexmodule', 'articles', '1');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."config VALUES ('indexmodule', 'news', '2');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."config VALUES ('indexmodule', 'prayerlist', '3');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."config VALUES ('url', '".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "", $_SERVER['PHP_SELF'])."');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."config VALUES ('version', '".$VERSION."');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."news VALUES ('1', 'Welcome to FishCMS', 'The developers would like to thank you for trying <a href=\'http://fishcms.com\'>FishCMS</a>.', 'FishCMS is being developed by the webmasters at <a href=\'http://believewith.us/\'>BelieveWith.US</a>.', '2', 'FishCMS developers', '".time ()."', '1');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."articles VALUES ('1', 'A new FishCMS website', '".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "", $_SERVER['PHP_SELF'])." has just installed <a href=\'http://fishcms.com\'>FishCMS</a>...', '<a href=\'http://fishcms.com\'>FishCMS</a> is being developed by the webmasters at <a href=\'http://believewith.us/\'>BelieveWith.US</a>. and is distributed under the GNU Public License (AKA GPL).', '2', 'FishCMS developers', '".time ()."', '1');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."links VALUES ('1', '0', 'Home', 'http://".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "", $_SERVER['PHP_SELF'])."', '1');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."links VALUES ('2', '0', 'Prayer List', 'http://".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "prayerlist.php", $_SERVER['PHP_SELF'])."', '2');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."links VALUES ('3', '0', 'News', 'http://".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "news.php", $_SERVER['PHP_SELF'])."', '3');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."links VALUES ('4', '0', 'Articles', 'http://".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "articles.php", $_SERVER['PHP_SELF'])."', '4');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."links VALUES ('5', '0', 'Links', 'http://".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "links.php", $_SERVER['PHP_SELF'])."', '5');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."category VALUES ('6', 'General', '1');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."blocks VALUES ('1', 'verse_of_the_day', '1', '1');";
if (db_query ($sql) == 0)
   $errors++;
$sql="INSERT INTO ".$list_prefix."blocks VALUES ('2', 'calendar', '1', '2');";
if (db_query ($sql) == 0)
   $errors++;
//lets report our results.
   if ($errors == 0)
      printf ("<font color='#00ff00'>OK</font><br>\r\n");
   else
      printf ("<font color='#ff0000'>%d ERRORS</font><br>\r\n", $errors);
//lets insert the user specified configuration options.
   $errors=0;
   printf ("Inserting user configured values: ");
   $sql="INSERT INTO ".$list_prefix."config VALUES ('sitename', '".$HTTP_POST_VARS['sitename']."');";
   if (db_query ($sql) == 0)
      $errors++;
   $sql="INSERT INTO ".$list_prefix."config VALUES ('sitedescription', '".$HTTP_POST_VARS['sitedescription']."');";
   if (db_query ($sql) == 0)
      $errors++;
   $sql="INSERT INTO ".$list_prefix."config VALUES ('email', '".$HTTP_POST_VARS['admin_email']."');";
   if (db_query ($sql) == 0)
      $errors++;
   $sql="INSERT INTO ".$list_prefix."config VALUES ('copyright', '".addslashes ($HTTP_POST_VARS['copyright'])."');";
   if (db_query ($sql) == 0)
      $errors++;
   $sql="INSERT INTO ".$list_prefix."config VALUES ('authtype', '".addslashes ($HTTP_POST_VARS['authtype'])."');";
   if (db_query ($sql) == 0)
      $errors++;
   //here we will add support for our FishCMS auth module.
//----------------------------------------------------------------------------
//---FishCMS AUTH MODULE SUPPORT GOES HERE.
//----------------------------------------------------------------------------   
   //lets report our results.
   if ($errors == 0)
      printf ("<font color='#00ff00'>OK</font><br>\r\n");
   else
      printf ("<font color='#ff0000'>%d ERRORS</font><br>\r\n", $errors);
//lets draw the optional functions.
   printf ("<h2>Optional Installation features.</h2>\r\n");
   printf ("<form method='post' action='install.php?mode=finalize'>\r\n");
   //first lets offer to download and start the installation of phpBB2 (opt in).
   printf ("Downloading phpBB2 is optional.  If you chose to use the phpBB2 authentication module then you");
   printf ("<b>MUST</b> install phpBB2 either manually or by using FishCMS to start the process.<br>");
   printf ("<input type='checkbox' name='install_phpbb2'> Have FishCMS download and help me install phpBB2.<BR></p>\r\n");
   //lets offer to automatically join the mailing list (opt out).
   printf ("<p>Enabling or Disabling the following options will not affect the operation of FishCMS.");
   printf ("The developers would like to present you with the option to join the mailing list so that");
   printf (" you can recieve release announcements and/or security information.<br>\r\nThe developers are");
   printf (" also interested in learning where the software is being used.<br>\r\n<b>The developers will");
   printf (" do their best to protect your email address and will never knowingly send you SPAM.</b> The");
   printf (" developers will not share, sell, publish, or otherwise distribute your email address.  By");
   printf (" enabling the 'Send a message to the developers informing them about this site.' option you");
   printf (" agree that the developers may link to your site.<br>");
   printf (" <input type='checkbox' name='join_list' checked> Join the FishCMS mailing list.<BR>\r\n");
   //lets offer to send a message to the developers.
   printf ("<input type='checkbox' name='email_devs' checked> Send a message to the developers informing them about this site.<BR></p>\r\n");
   printf ("<input type='submit' value='Next &gt;&gt;'>\r\n");
   printf ("</form>\r\n");   
}

//***function joinlist ($email)***********************************************
function joinlist ($email) {
   $xtra="From: ".$email."\r\nX-Mailer: FishCMS.\r\nReturn-Path: ".$email."\r\n";
   $message="";
   $result = @mail("fishlist-suscribe@fishcms.com", "Suscribe", $message, $xtra);
return $result;
}

//***function emaildevs ($email)***********************************************
function emaildevs ($email) {
   $xtra="From: ".$email."\r\nX-Mailer: FishCMS.\r\nReturn-Path: ".$email."\r\n";
   $message="A new FishCMS Version ".$VERSION." site has been installed.\r\n";
   $message.="The url is ".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "", $_SERVER['PHP_SELF']);
   $result = @mail("fishdevs@fishcms.com", "A new FishCMS site!", $message, $xtra);
return $result;
}

//***function fetch_phpbb2 ()*************************************************
function fetch_phpbb2 () {
global $INSTALLDIR;
   printf ("Attempting to determine the most recent version of phpBB2: ");
   if ($fsock = @fsockopen('www.phpbb.com', 80, $errno, $errstr)) {
      @fputs($fsock, "GET /updatecheck/20x.txt HTTP/1.1\r\n");
      @fputs($fsock, "HOST: www.phpbb.com\r\n");
      @fputs($fsock, "Connection: close\r\n\r\n");

      $get_info = false;
      while (!@feof($fsock)) {
         if ($get_info) {
            $version_info .= @fread($fsock, 1024);
         } else {
            if (@fgets($fsock, 1024) == "\r\n") {
               $get_info = true;
            }
         }
      }
      @fclose($fsock);

      $version_info = explode("\n", $version_info);
      $latest_head_revision = (int) $version_info[0];
      $latest_minor_revision = (int) $version_info[2];
      $latest_version = (int) $version_info[0] . '.' . (int) $version_info[1] . '.' . (int) $version_info[2];
      printf ("<font color='#00ff00'>%s</font><br>\r\n", $latest_version);
      printf ("Checking for an existing installation: ");
      if ((file_exists ($INSTALLDIR."phpBB2/")) || (file_exists ($INSTALLDIR."forums/")))
         printf ("<font color='#00ff00'>FOUND</font><br>\r\n");
      else {
         printf ("<font color='#00ff00'>NOT FOUND</font><br>\r\n");
         printf ("Attempting to fetch phpBB-%s.zip: ", $latest_version);
         if (file_exists ("./phpBB-".$latest_version.".zip"))
            unlink ("./phpBB-".$latest_version.".zip");
         system  ("wget http://phpbb.com/files/releases/phpBB-".$latest_version.".zip");
         if (file_exists ("./phpBB-".$latest_version.".zip")) {
            printf ("<font color='#00ff00'>OK</font><br>\r\n");
            printf ("Decompressing phpBB-%s.zip: \r\n", $latest_version);
            system ("unzip -d ".$INSTALLDIR." -n phpBB-".$latest_version.".zip > /dev/null");
         } else
            printf ("<font color='#ff0000'>ERROR</font><br>\r\n");
         if (file_exists ($INSTALLDIR."phpBB2/")) {
            printf ("<font color='#00ff00'>OK</font><br>\r\n");
            rename ($INSTALLDIR."phpBB2", $INSTALLDIR."forums");
            printf ("You must <a href='http://".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "forums/", $_SERVER['PHP_SELF'])."' target='_blank'>now follow this link</a> to finish installing phpBB2.<br>\r\n");
         } else
            printf ("<font color='#ff0000'>ERROR</font><br>\r\n");
      }
   } else {
      printf ("<font color='#ff0000'>ERROR</font><br>\r\n");
   }

}

//***function finalize ()*****************************************************
function finalize () {
global $HTTP_POST_VARS, $INSTALLDIR, $CONFIGFILE;
//lets get the admin email address from the database.
include ($INSTALLDIR.$CONFIGFILE); //this loads the config file previously created.
include ($INSTALLDIR."db/".$db_type.".inc.php"); //this should load the db "layer".
   $sql="SELECT * FROM `".$list_prefix."config WHERE `key` = 'email';";
   $result=db_query ($sql);
   if ($result)
      $rows=db_num_rows($result);
   else
      $rows=0;
   if ($rows > 0) {
      $row=db_fetch_array($result);
      $email=$row['value'];
   } else
      $email="noemail@nowhere.com";
   printf ("<h2>Finalizing FishCMS installation.</h2>\r\n");
//lets see if we need to join the mailing list.
   if (isset ($HTTP_POST_VARS['join_list']))
      if (joinlist ($email))
      if (emaildevs ($email))
         printf ("Emailing the developers: <font color='#00ff00'>OK</font><br>\r\n");
      else
         printf ("Emailing the developers: <font color='#ff0000'>ERROR</font><br>\r\n");
//lets see we need to notify the developers.
   if (isset ($HTTP_POST_VARS['email_devs']))
      if (emaildevs ($email))
         printf ("Emailing the developers: <font color='#00ff00'>OK</font><br>\r\n");
      else
         printf ("Emailing the developers: <font color='#ff0000'>ERROR</font><br>\r\n");
//lets see if we need to begin the install of phpBB2.
   if (isset ($HTTP_POST_VARS['install_phpbb2']))
      fetch_phpbb2 ();
      
   printf ("<BR><BR>Your site should now be running FishCMS.  For security reasons you");
   printf (" must now delete the install directory.  If you are installing phpBB2 from");
   printf (" FishCMS you should follow the instructions in the phpBB2 installer regarding");
   printf (" deleting directories and setting permissions.<BR>");
}

//===Main code================================================================
//lets write the header.
   install_header ();
//now we will perform the install steps.
   if (!isset ($HTTP_GET_VARS['mode']))
      pre_install ();
   elseif ($HTTP_GET_VARS['mode'] =="selectdb")
      selectdb ();
   elseif ($HTTP_GET_VARS['mode'] =="selectauth")
      selectauth ();
   elseif ($HTTP_GET_VARS['mode'] =="siteconfig")
      siteconfig ();
   elseif ($HTTP_GET_VARS['mode'] =="optional")
      optional ();
   elseif ($HTTP_GET_VARS['mode'] =="finalize")
      finalize ();
/*   else
      unknown ();
*/
//now we write the footer.
   install_footer ();
?>