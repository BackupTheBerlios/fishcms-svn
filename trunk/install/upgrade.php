<?
//****************************************************************************
//* File:	upgrade.php
//* Author:	G.A. Heath
//* Date: 	August 19, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 23, 2005
//****************************************************************************
$VERSION="0.2.0";

//===Includes=================================================================
include_once "../fishcms-config.php";

//===Code that gets run each time=============================================
   @ $db=mysql_pconnect ($db_host, $db_username, $db_password);
   if (!$db) //if we can't access the db lets allow the user to fix it.
      die ("ERROR: UNABLE TO ACCESS DATABASE, perhaps you should try installing FishCMS");
   elseif (!mysql_select_db ($db_database))
      die ("ERROR: UNABLE TO SELECT DATABASE!"); //if we can't select the db lets allow the user to fix it.
   //lets get the current version
   $sql="SELECT * FROM `".$list_prefix."config` WHERE `key` = 'version';";
   $result=mysql_query($sql);
   if ($result) {
      $rows=mysql_num_rows($result);
      if ($rows == 0) //no need to go further if there is no result then it can only be version 0.1.0
         $OLD_VERSION="0.1.0";
      else {
         $row = mysql_fetch_array($result);
         $OLD_VERSION=$row['value'];
      }
   } else
      die ("ERROR: GENERAL DATABASE ERROR.");
//===Functions================================================================
//***function upgrade_from010 ()**********************************************
function upgrade_from010 () {
global $list_prefix, $_SERVER;
$error=0;
//lets update the db
   $sql="INSERT INTO ".$list_prefix."config VALUES ('url', '".$_SERVER['HTTP_HOST'].str_replace ("install.php", "", $_SERVER['PHP_SELF'])."', '');";
   $result=mysql_query($sql);
   if (!$result)
      $error=1;
   $sql="CREATE TABLE `".$list_prefix."blocks` (`id` TINYINT NOT NULL AUTO_INCREMENT , `name` VARCHAR( 64 ) NOT NULL , `blockset` TINYINT DEFAULT '0' NOT NULL , `order` TINYINT NOT NULL , PRIMARY KEY (`id`));";
   $result=mysql_query($sql);
   if (!$result)
      $error=1;
   //we will be updating the version later so lets just use the installed version to get the entry into the table.
   $sql="INSERT INTO ".$list_prefix."config VALUES ('version', '0.1.0', '');";
   $result=mysql_query($sql);
   if (!$result)
      $error=1;
   $sql="ALTER TABLE `".$list_prefix."praise_list` ADD `username` VARCHAR( 56 ) NOT NULL AFTER `left_by` ;";
   $result=mysql_query($sql);
   if (!$result)
      $error=1;
   $sql="ALTER TABLE `".$list_prefix."prayer_list` ADD `username` VARCHAR( 56 ) NOT NULL AFTER `requested_by` ;";
   $result=mysql_query($sql);
   if (!$result)
      $error=1;
   $sql="CREATE TABLE `".$list_prefix."calendar` (`id` TINYINT NOT NULL AUTO_INCREMENT ,`weekly` TINYINT DEFAULT '7' NOT NULL ,`monthly` VARCHAR( 3 ) NOT NULL ,`yearly` VARCHAR( 5 ) NOT NULL ,`date` VARCHAR( 9 ) NOT NULL ,`time` VARCHAR( 5 ) NOT NULL ,`description` TEXT NOT NULL ,PRIMARY KEY ( `id` ));";
   $result=mysql_query($sql);
   if (!$result)
      $error=1;
   
//lets die if there were errors
   if ($error) {
      die ("ERROR: Unable to (completely?) upgrade database from 0.1.0");
   }
}

//===Main code================================================================
//lets do the upgrades
   if (0 == strcmp ($OLD_VERSION, "0.1.0")) //if we are dealing with the first version
      upgrade_from010 ();
//lets update the version in the DB to the current version number
   $sql="UPDATE `".$list_prefix ."config` SET `value` = '".$VERSION."' WHERE `key` = 'version';";
   $result=mysql_query($sql);
   if (!$result)
      die ("ERROR: Unable to UPDATE VERSION INFO IN THE DATABASE.");
?>