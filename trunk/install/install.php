<?
//****************************************************************************
//* File:	install.php[B
//* Author:	G.A. Heath
//* Date: 	July 14, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	September 4, 2005
//****************************************************************************
$VERSION="0.2.1";
//===Functions================================================================
//***function draw_install_form ()********************************************
function draw_install_form () {
global $_SERVER;
$FORM['sitename']=$_SERVER['HTTP_HOST'];
$FORM['sitedescription']="A FishCMS powered site.";
$FORM['copyright']="FishCMS is licensed under the GNU <a href='http://www.gnu.org/licenses/gpl.html'>General Public License</a><BR>&copy; 2005 by G.A. Heath and Michael Rice.";
   printf ("<form method='post' action='install.php?submit=1'>\r\n");
   printf ("<CENTER><H2>Installation</H2></CENTER>\r\n");
   printf ("<h3>MySQL configuration:</h3>\r\n");
   printf ("<table width='100%%' borders='0'>\r\n");
   printf ("<TR><TD width=50%%>Hostname:</TD><TD><input type='text' name='db_host' size='20' value='localhost'></TD></TR>\r\n");
   printf ("<TR><TD>Database name:</TD><TD><input type='text' name='db_database' size='20'><BR></TD></TR>\r\n");
   printf ("<TR><TD>Database username:</TD><TD><input type='text' name='db_username' size='20'></TD></TR>\r\n");
   printf ("<TR><TD>Database password:</TD><TD><input type='password' name='db_password' size='20'></TD></TR>\r\n");
   printf ("<TR><TD>Database prefix:</TD><TD><input type='text' name='list_prefix' size='20' value='FishCMS_'></TD></TR>\r\n");
   printf ("</table>");
   printf ("<H3>Site information:</H3>\r\n");
   printf ("<table width='100%%' borders='0'>\r\n");
   printf ("<TR><TD width=50%%>Site name:</TD><TD><input type='text' name='sitename' size='20' value='%s'></TD></TR>\r\n", $FORM['sitename']);
   printf ("<TR><TD>Site Description:</TD><TD><input type='text' name='sitedescription' size='20' value='%s'></TD></TR>\r\n", $FORM['sitedescription']);
   printf ("<TR><TD>Administrators email:</TD><TD><input type='text' name='admin_email' size='20'></TD></TR>\r\n");
   printf ("<TR><TD>Copyright Notice:</TD><TD><textarea name='copyright' rows='4' cols='60'>%s</textarea></TD></TR>\r\n", $FORM['copyright']);
   printf ("</table>\r\n");
   printf ("<H3>Optional install functions:</H3>\r\n");
   printf ("<P>\r\n");
   printf ("These options are not required, however the developers are extremely\r\n");
   printf ("interested in what sites use this software and would like to offer\r\n");
   printf ("you a chance to recieve developement and security announcements from\r\n");
   printf ("them via the mailing list.<BR><B>Your email address will not be share, sold, or SPAMMED.</B><BR><BR>\r\n");
   printf ("<input type='checkbox' name='email_devs' checked> Send a message to the developers informing them about this site.<BR>\r\n");
   printf ("<input type='checkbox' name='join_list' checked> Join the FishCMS mailing list.<BR>\r\n");
   printf ("</P>\r\n");
   printf ("<input type='submit' value='Submit'><BR>\r\n");
   printf ("</form>\r\n");
}
//***function redo_form ()****************************************************
function redo_form ($dberr) {
global $HTTP_POST_VARS;
   printf ("<form method='post' action='install.php?submit=1'>\r\n");
   printf ("<CENTER><H2>%s</H2></CENTER>\r\n", $dberr);
   printf ("<h3>MySQL configuration:</h3>\r\n");
   printf ("<table width='100%%' borders='0'>\r\n");
   printf ("<TR><TD>Hostname:</TD><TD><input type='text' name='db_host' size='20' value='%s'></TD></TR>\r\n", $HTTP_POST_VARS['db_host']);
   printf ("<TR><TD>Database name:</TD><TD><input type='text' name='db_database' size='20' value='%s'><BR></TD></TR>\r\n", $HTTP_POST_VARS['db_database']);
   printf ("<TR><TD>Database username:</TD><TD><input type='text' name='db_username' size='20' value='%s'></TD></TR>\r\n", $HTTP_POST_VARS['db_username']);
   printf ("<TR><TD>Database password:</TD><TD><input type='password' name='db_password' size='20'></TD></TR>\r\n");
   printf ("<TR><TD>Database prefix:</TD><TD><input type='text' name='list_prefix' size='20' value='%s'></TD></TR>\r\n", $HTTP_POST_VARS['list_prefix']);
   printf ("</table>");
   printf ("<CENTER><H2>Site information</H2></CENTER>\r\n");
   printf ("<table width='100%%' borders='0'>\r\n");
   printf ("<TR><TD>Site name:</TD><TD><input type='text' name='sitename' size='20' value='%s'></TD></TR>\r\n", $HTTP_POST_VARS['sitename']);
   printf ("<TR><TD>Site Description:</TD><TD><input type='text' name='sitedescription' size='20' value='%s'></TD></TR>\r\n",$HTTP_POST_VARS['sitedescription']);
   printf ("<TR><TD>Administrators email:</TD><TD><input type='text' name='admin_email' size='20' value='%s'></TD></TR>\r\n", $HTTP_POST_VARS['admin_email']);
   printf ("<TR><TD>Copyright Notice:</TD><TD><textarea name='copyright' rows='4' cols='60'>%s</textarea></TD></TR>\r\n",$HTTP_POST_VARS['copyright']);
   printf ("</table>\r\n");
   if (isset ($HTTP_POST_VARS['join_list']))
      $joinlist="checked";
   if (isset ($HTTP_POST_VARS['email_devs']))
      $email_devs="checked";
   printf ("<CENTER><H2>Optional functions.</H2></CENTER>\r\n");
   printf ("<P>\r\n");
   printf ("These options are not required, however the developers are extremely\r\n");
   printf ("interested in what sites use this software and would like to offer\r\n");
   printf ("you a chance to recieve developement and security announcements from\r\n");
   printf ("them via the mailing list.<BR><B>Your email address will not be share, sold, or SPAMMED.</B><BR><BR>\r\n");
   printf ("<input type='checkbox' name='email_devs' %s> Send a message to the developers informing them about this site.<BR>\r\n", $email_devs);
   printf ("<input type='checkbox' name='join_list' %s> Join the FishCMS mailing list.<BR>\r\n", $joinlist);
   printf ("</P>\r\n");
   printf ("<input type='submit' value='Submit'><BR>\r\n");
   printf ("</form>\r\n");
}
//***function joinlist ($email)***********************************************
function joinlist ($email) {
   $xtra="From: ".$email."\r\nX-Mailer: FishCMS.\r\nReturn-Path: ".$email."\r\n";
   $message="";
   $result = @mail("fishlist-suscribe@fishcms.com", "Suscribe", $message, $xtra);
}
//***function joinlist ($email)***********************************************
function emaildevs ($email) {
   $xtra="From: ".$email."\r\nX-Mailer: FishCMS.\r\nReturn-Path: ".$email."\r\n";
   $message="A new FishCMS site has been installed.\r\n";
   $message.="The url is ".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "", $_SERVER['PHP_SELF']);
   $result = @mail("fishdevs@fishcms.com", "A new FishCMS site!", $message, $xtra);
}

//===Main code================================================================
$list_prefix = $HTTP_POST_VARS['list_prefix'];
$make_tables_sql['praise_list']="CREATE TABLE `".$list_prefix."praise_list` ( `id` int(11) NOT NULL auto_increment, `request` int(11) NOT NULL default '0', `praise` text NOT NULL, `postdate` int(11) NOT NULL default '0', `left_by` varchar(255) NOT NULL default '', `username` VARCHAR( 56 ) NOT NULL default '', PRIMARY KEY  (`id`));";
$make_tables_sql['prayer_list']="CREATE TABLE `".$list_prefix."prayer_list` ( `id` int(11) NOT NULL auto_increment, `request_for` varchar(80) NOT NULL default '', `request` text NOT NULL, `postdate` int(11) NOT NULL default '0', `expiredate` int(11) NOT NULL default '0', `requested_by` varchar(255) NOT NULL default '', `username` VARCHAR( 56 ) NOT NULL default '',`expired` tinyint(4) NOT NULL default '0', PRIMARY KEY  (`id`));";
$make_tables_sql['links']="CREATE TABLE `".$list_prefix."links` ( `id` TINYINT NOT NULL AUTO_INCREMENT , `category` TINYINT DEFAULT '0' NOT NULL , `title` VARCHAR( 56 ) NOT NULL , `url` VARCHAR( 255 ) NOT NULL , `order` TINYINT DEFAULT '0' NOT NULL, PRIMARY KEY ( `id` ));";
$make_tables_sql['config']="CREATE TABLE `".$list_prefix."config` ( `key` varchar(25) NOT NULL default '', `value` varchar(255) NOT NULL default '', `order` tinyint NOT NULL default 0);";
$make_tables_sql['articles']="CREATE TABLE `".$list_prefix."articles` (`id` tinyint(4) NOT NULL auto_increment, `article_title` varchar(128) NOT NULL default '', `teaser` text NOT NULL, `article` text NOT NULL, `posted_by` tinyint(4) NOT NULL default '0', `byline` varchar(255) NOT NULL default '', `date` int(11) NOT NULL default '0', `category` tinyint(4) NOT NULL default '0', PRIMARY KEY (`id`));";
$make_tables_sql['news']="CREATE TABLE `".$list_prefix."news` ( `id` tinyint(4) NOT NULL auto_increment, `news_title` varchar(128) NOT NULL default '', `teaser` text NOT NULL, `news` text NOT NULL, `posted_by` tinyint(4) NOT NULL default '0', `byline` varchar(255) NOT NULL default '', `date` int(11) NOT NULL default '0', `category` tinyint(4) NOT NULL default '0', PRIMARY KEY (`id`));";
$make_tables_sql['category']="CREATE TABLE `".$list_prefix."category` ( `id` TINYINT NOT NULL AUTO_INCREMENT, `name` VARCHAR(128) NOT NULL, `order` TINYINT DEFAULT '0' NOT NULL, PRIMARY KEY (`id`));";
$make_tables_sql['blocks']="CREATE TABLE `".$list_prefix."blocks` (`id` TINYINT NOT NULL AUTO_INCREMENT , `name` VARCHAR( 64 ) NOT NULL , `blockset` TINYINT DEFAULT '0' NOT NULL , `order` TINYINT NOT NULL , PRIMARY KEY (`id`));";
$make_tables_sql['calendar']="CREATE TABLE `".$list_prefix."calendar` (`id` TINYINT NOT NULL AUTO_INCREMENT ,`weekly` TINYINT DEFAULT '7' NOT NULL ,`monthly` VARCHAR( 3 ) NOT NULL ,`yearly` VARCHAR( 5 ) NOT NULL ,`date` VARCHAR( 9 ) NOT NULL ,`time` VARCHAR( 5 ) NOT NULL ,`description` TEXT NOT NULL ,PRIMARY KEY ( `id` ));";

$preset_values_sql['config1']="INSERT INTO ".$list_prefix."config VALUES ('template', 'default', '');";
$preset_values_sql['config2']="INSERT INTO ".$list_prefix."config VALUES ('index', 'modules', '');";
$preset_values_sql['config3']="INSERT INTO ".$list_prefix."config VALUES ('indexmodule', 'articles', '1');";
$preset_values_sql['config4']="INSERT INTO ".$list_prefix."config VALUES ('indexmodule', 'news', '2');";
$preset_values_sql['config5']="INSERT INTO ".$list_prefix."config VALUES ('indexmodule', 'prayerlist', '3');";
$preset_values_sql['config6']="INSERT INTO ".$list_prefix."config VALUES ('url', '".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "", $_SERVER['PHP_SELF'])."', '');";
$preset_values_sql['config7']="INSERT INTO ".$list_prefix."config VALUES ('version', '".$VERSION."', '');";
$preset_values_sql['news']="INSERT INTO ".$list_prefix."news VALUES ('', 'Welcome to FishCMS', 'The developers would like to thank you for trying <a href=\'http://fishcms.com\'>FishCMS</a>.', 'FishCMS is being developed by the webmasters at <a href=\'http://believewith.us/\'>BelieveWith.US</a>.', '2', 'FishCMS developers', '".time ()."', '1');";
$preset_values_sql['articles']="INSERT INTO ".$list_prefix."articles VALUES ('', 'A new FishCMS website', '".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "", $_SERVER['PHP_SELF'])." has just installed <a href=\'http://fishcms.com\'>FishCMS</a>...', '<a href=\'http://fishcms.com\'>FishCMS</a> is being developed by the webmasters at <a href=\'http://believewith.us/\'>BelieveWith.US</a>. and is distributed under the GNU Public License (AKA GPL).', '2', 'FishCMS developers', '".time ()."', '1');";
$preset_values_sql['links1']="INSERT INTO ".$list_prefix."links VALUES ('', '', 'Home', 'http://".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "", $_SERVER['PHP_SELF'])."', '1');";
$preset_values_sql['links2']="INSERT INTO ".$list_prefix."links VALUES ('', '', 'Prayer List', 'http://".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "prayerlist.php", $_SERVER['PHP_SELF'])."', '2');";
$preset_values_sql['links3']="INSERT INTO ".$list_prefix."links VALUES ('', '', 'News', 'http://".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "news.php", $_SERVER['PHP_SELF'])."', '3');";
$preset_values_sql['links4']="INSERT INTO ".$list_prefix."links VALUES ('', '', 'Articles', 'http://".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "articles.php", $_SERVER['PHP_SELF'])."', '4');";
$preset_values_sql['links5']="INSERT INTO ".$list_prefix."links VALUES ('', '', 'Links', 'http://".$_SERVER['HTTP_HOST'].str_replace ("install/install.php", "links.php", $_SERVER['PHP_SELF'])."', '5');";
$preset_values_sql['category']="INSERT INTO ".$list_prefix."category VALUES ('', 'General', '1');";
$preset_values_sql['blocks1']="INSERT INTO ".$list_prefix."blocks VALUES ('', 'verse_of_the_day', '1', '1');";
$preset_values_sql['blocks2']="INSERT INTO ".$list_prefix."blocks VALUES ('', 'calendar', '1', '2');";

   //if we have our values lets test the db.
   if ((isset ($HTTP_POST_VARS['db_host'])) && (isset ($HTTP_POST_VARS['db_username'])) && (isset ($HTTP_POST_VARS['db_password'])) && (isset ($HTTP_POST_VARS['db_database'])) && (isset ($HTTP_POST_VARS['list_prefix']))) {
      $db_host = $HTTP_POST_VARS['db_host'];
      $db_username = $HTTP_POST_VARS['db_username'];
      $db_password = $HTTP_POST_VARS['db_password'];
      $db_database = $HTTP_POST_VARS['db_database'];
      $list_prefix = $HTTP_POST_VARS['list_prefix'];
      @ $db=mysql_pconnect ($db_host, $db_username, $db_password);
      if (!$db) //if we can't access the db lets allow the user to fix it.
         redo_form ("Invalid MySQL username, password, or hostname provided.");
      elseif (!mysql_select_db ($db_database))
         redo_form ("Invalid MySQL database name or permissions."); //if we can't select the db lets allow the user to fix it.
      else  { //since we can access the db lets write our config file and create the tables in the db.
         printf ("<h3>Creating the configuration file</h3>\r\n");
         $config_name="fishcms-config.php";
         $config_contents="<?\r\n";
         $config_contents.="//****************************************************************************\r\n";
         $config_contents.="//*     This file was generated by the fishCMS installer.\r\n";
         $config_contents.="//*     PLEASE DO NOT EDIT THE CONTENTS OF THIS FILE UNLESS YOU ARE 100% SURE!\r\n";
         $config_contents.="//****************************************************************************\r\n";
         $config_contents.="\$db_host='".$HTTP_POST_VARS['db_host']."';\r\n";
         $config_contents.="\$db_username='".$HTTP_POST_VARS['db_username']."';\r\n";
         $config_contents.="\$db_password='".$HTTP_POST_VARS['db_password']."';\r\n";
         $config_contents.="\$db_database='".$HTTP_POST_VARS['db_database']."';\r\n";
         $config_contents.="\$list_prefix='".$HTTP_POST_VARS['list_prefix']."';\r\n";
         $config_contents.="?>\r\n";
         if ($file= @fopen("../".$config_name, "w")) {
            fwrite ($file, $config_contents);
            fclose ($file);
         } else {
            printf ("ERROR: Unable tp create file %s<BR>\r\n", $config_name);
            printf ("Please copy and paste the following text in your FishCMS directory as %s<BR>\r\n", $config_name);
            printf ("<pre>%s</pre>\r\n", htmlspecialchars ($config_contents));
            printf ("<BR><BR>");            
         }
         printf ("<h3>Creating database tables</h3>\r\n");
         $error=0;
         //if we can't create the tables tell the user we created the config file and warn them there may be issues with the DB tables.
         $result = @mysql_query($make_tables_sql['praise_list']);
         if (!$result)
            $error=1;
         $result = @mysql_query($make_tables_sql['prayer_list']);
         if (!$result)
            $error=1;
         $result = @mysql_query($make_tables_sql['links']);
         if (!$result)
            $error=1;
         $result = @mysql_query($make_tables_sql['config']);
         if (!$result)
            $error=1;
         $result = @mysql_query($make_tables_sql['articles']);
         if (!$result)
            $error=1;
         $result = @mysql_query($make_tables_sql['news']);
         if (!$result)
            $error=1;
         $result = @mysql_query($make_tables_sql['category']);
         if (!$result)
            $error=1;
         $result = @mysql_query($make_tables_sql['blocks']);
         if (!$result)
            $error=1;
         if ($error == 0) {
            //here we will insert default values into the db tables.
            $result = @mysql_query($preset_values_sql['config1']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['config2']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['config3']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['config4']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['config5']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['news']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['articles']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['links1']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['links2']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['links3']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['links4']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['links5']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['category']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['blocks1']);
            if (!$result)
               $error=1;
            $result = @mysql_query($preset_values_sql['blocks2']);
            if (!$result)
               $error=1;
            if ($error==1)
               printf ("ERROR: I was unable to insert all of the default values into the database.<BR>\r\n<BR>\r\n");
            //here we will insert user configured values into the tables.
            $sql="INSERT INTO ".$list_prefix."config VALUES ('sitename', '".$HTTP_POST_VARS['sitename']."', '');";
            $result = @mysql_query($sql);
            if (!$result)
               printf ("ERROR: I was unable to insert all of the user configured values<BR>\r\n");
            $sql="INSERT INTO ".$list_prefix."config VALUES ('sitedescription', '".$HTTP_POST_VARS['sitedescription']."', '');";
            $result = @mysql_query($sql);
            if (!$result)
               printf ("ERROR: I was unable to insert all of the user configured values<BR>\r\n");
            $sql="INSERT INTO ".$list_prefix."config VALUES ('email', '".$HTTP_POST_VARS['admin_email']."', '');";
            $result = @mysql_query($sql);
            if (!$result)
               printf ("ERROR: I was unable to insert all of the user configured values<BR>\r\n");
            $sql="INSERT INTO ".$list_prefix."config VALUES ('copyright', '".addslashes ($HTTP_POST_VARS['copyright'])."', '');";
            $result = @mysql_query($sql);
            if (!$result)
               printf ("ERROR 4: I was unable to insert all of the user configured values<BR>\r\n");
            //now we will send any email we need to send.
            if (isset ($HTTP_POST_VARS['join_list']))
               joinlist ($HTTP_POST_VARS['admin_email']);
            if (isset ($HTTP_POST_VARS['email_devs']))
               emaildevs ($HTTP_POST_VARS['admin_email']);
         } else
            printf ("ERROR: I could not create the correct tables in the database<BR>\r\n<BR>\r\n");
         //else tell them to do (or we do) post-install clean up.
         printf ("<h3>Final steps:</h3>\r\n");
         printf ("You should delete this install.php script and set permissions to readonly for the file: %s<BR>\r\n", $config_name);
      }
   //let the user input the DB configuration.   
   } else
      draw_install_form ();
//end program
?>