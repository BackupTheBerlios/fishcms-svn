<?
//****************************************************************************
//* File:	admin/index.php
//* Author:	G.A. Heath
//* Date: 	August 17, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 22, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "../common.inc.php";

//===Functions================================================================
//***function loginbox ()*****************************************************
function loginbox () {
   printf ("<form method='post' action='index.php?login=1'>\r\n");
   printf ("Username: <input type='text' name='adminuser' size='20'><BR>");
   printf ("Password: <input type='password' name='adminpass' size='20'><BR>");
   printf ("<input type='submit' value='Login'><BR>");
   printf ("</form>\r\n");
}

//***function linkslist ()**************************************************
function linkslist () {
global $list_prefix;
   $LINKSLIST="<select name='links'>\r\n";
   $sql="SELECT * from `".$list_prefix."links` WHERE 1 ORDER by `order`;";
   $result=mysql_query($sql);
   if ($result)
      $rows = mysql_num_rows($result);
   else
      $rows=0;
   $i=0;
   while ($i < $rows) {
      $row=mysql_fetch_array($result);
      $LINKSLIST.="<option value='".$row['id']."'>".$row['title']."</option>\r\n";
      $i++;
   }
   if ($rows == 0)
      $LINKSLIST.="<option value='-'>No links found</option>\r\n";
   $LINKSLIST.="</select>\r\n";
   return $LINKSLIST;
}

//***function catlist ($cat)**************************************************
function catlist ($cat) {
global $list_prefix;
   $CATLIST="<select name='category'>\r\n";
   $CATLIST.="<option value='0' selected>system</option>\r\n";
   $sql="SELECT * from `".$list_prefix."category` WHERE 1;";
   $result=mysql_query($sql);
   if ($result)
      $rows = mysql_num_rows($result);
   else
      $rows=0;
   $i=0;
   while ($i < $rows) {
      $row=mysql_fetch_array($result);
      if ($cat == $row['id'])
         $CATLIST.="<option value='".$row['id']."' selected>".$row['name']."</option>\r\n";
      else
         $CATLIST.="<option value='".$row['id']."'>".$row['name']."</option>\r\n";
      $i++;
   }
   if ($rows == 0)
      $CATLELIST.="<option value='0 '>SYSTEM</option>\r\n";
   $CATLIST.="</select>\r\n";
   return $CATLIST;
}
//***function fixorder ($order, $cat)***********************************************
function fixorder ($order, $cat) {
global $list_prefix;
   $sql="SELECT * FROM `".$list_prefix ."links` WHERE (`category` = '".$cat."' AND `order` = '".$order."');";
   $result=mysql_query($sql);
   if ($result)
      $rows = mysql_num_rows($result);
   else
      $rows=0;
   if ($rows == 0) {
      $sql="SELECT * FROM `".$list_prefix ."links` WHERE (`category` = '".$cat."' AND `order` > '".$order."') ORDER BY `order`;";
      $result=mysql_query($sql);
      if ($result)
         $rows = mysql_num_rows($result);
      else
         $rows=0;
      if ($rows != 0) {
         $row = mysql_fetch_array($result);
         $sql="UPDATE `".$list_prefix ."links` SET `order` = '".$order."' WHERE `id` = '".$row['id']."';";
         $result=mysql_query($sql);
         fixorder ($order+1, $cat);
      } else
         fixorder ($order+1, $cat);
   }
}
//***function catorder ()**************************************************
function catorder () {
global $list_prefix;
   fixorder (1, 0);
   $sql="SELECT * from `".$list_prefix."category` WHERE 1;";
   $result=mysql_query($sql);
   if ($result)
      $rows = mysql_num_rows($result);
   else
      $rows=0;
   $i=0;
   while ($i < $rows) {
      $row=mysql_fetch_array($result);
      fixorder (1, $row['id']);
      $i++;
   }
   if ($rows == 0)
      $CATLELIST.="<option value='0 '>SYSTEM</option>\r\n";
   $CATLIST.="</select>\r\n";
   return $CATLIST;
}
//***function content ()******************************************************
function content (){
global $HTTP_POST_VARS, $HTTP_GET_VARS, $list_prefix;
$MAIN=loadadmintmplate ("main");
$LINKS=loadadmintmplate("links");
   $WORK=$LINKS;
   if (0 == strcmp ($HTTP_GET_VARS['mode'], "select")) { //if we are to edit a link
   //lets get the links from the db
      $sql="SELECT * FROM `".$list_prefix."links` WHERE `id` = '".$HTTP_POST_VARS['links']."';";
      $result=mysql_query($sql);
      if ($result)
         $rows = mysql_num_rows($result);
      else
         $rows=0;
      if ($rows == 0) { //lets make sure that the news exists
         $WORK=insert_into_template ($WORK, "{NEWCHECK}", "checked");
         $WORK=insert_into_template ($WORK, "{CATLIST}", catlist (0));
      } else { //if it does we will read it from the db and add it to our output.
         $row=mysql_fetch_array($result);
         $WORK=insert_into_template ($WORK, "{LINKID}", $row['id']);
         $WORK=insert_into_template ($WORK, "{CATLIST}", catlist ($row['category']));
         $WORK=insert_into_template ($WORK, "{LINKTITLE}", $row['title']);
         $WORK=insert_into_template ($WORK, "{LINKURL}", $row['url']);
      }
   } else { //if we are not editing an news lets prepare the form for a new news.
      $WORK=insert_into_template ($WORK, "{NEWCHECK}", "checked");
      $WORK=insert_into_template ($WORK, "{CATLIST}", catlist (0));
   }
//lets delete a link if its selected   
   if ((0 == strcmp ($HTTP_GET_VARS['mode'], "delete")) && (isset ($HTTP_POST_VARS['delete_yes']))) {
      $sql="DELETE FROM `".$list_prefix."links` WHERE `id` = '".$HTTP_POST_VARS['links']."';";
      $result=mysql_query($sql);
   } elseif (0 == strcmp ($HTTP_GET_VARS['mode'], "delete"))
      $WORK="You must check the confirmation box to delete a link.<br>\r\n".$WORK;
//lets edit/add a link if thats our job.
   if ((0 == strcmp ($HTTP_GET_VARS['mode'], "edit"))) {
      if (isset($HTTP_POST_VARS['newlink'])) { //we are adding a new link
         $sql="SELECT * FROM `".$list_prefix ."links` WHERE `category` = '".$HTTP_POST_VARS['category']."' ORDER BY `order` DESC limit 1;";
         $result=mysql_query($sql);
         if ($result)
            $rows=mysql_num_rows($result);
         else
            $rows=0;
         if ($rows > 0) {
            $row = mysql_fetch_array($result);
            $order=$row['order']+1;
         } else
            $order=1;
         if ((isset($HTTP_POST_VARS['linktitle'])) && (isset($HTTP_POST_VARS['linkurl']))) {
            $sql="INSERT INTO ".$list_prefix."links VALUES ('', '".$HTTP_POST_VARS['category']."', '".$HTTP_POST_VARS['linktitle']."', '".$HTTP_POST_VARS['linkurl']."', '".$order."');";
            $result=mysql_query($sql);
         }
      } else { //we are editing an existing link
         if (isset ($HTTP_POST_VARS['linkid'])) { //we must know the links linkid to work on it.
            //lets get our existing db entry
            $sql="SELECT * FROM `".$list_prefix ."links` WHERE `id` = '".$HTTP_POST_VARS['linkid']."' ORDER BY `order` DESC limit 1;";
            $result=mysql_query($sql);
            $row=mysql_fetch_array($result);
            //lets figure out our order
            $order=1;
            if (0 ==strcmp ($HTTP_POST_VARS['position'], "same")) //no change to the order.
               $order=$row['order'];
            elseif (0 ==strcmp ($HTTP_POST_VARS['position'], "up")) {//it needs to move up
               $sql="SELECT * FROM `".$list_prefix ."links` WHERE `order` < '".$row['order']."' ORDER BY `order` DESC;";
               $result=mysql_query($sql);
               if ($result)
                  $rows = mysql_num_rows($result);
               else
                  $rows = 0;
               if ($rows > 0) {
                  $row2 = mysql_fetch_array($result);
                  $sql="UPDATE `".$list_prefix ."links` SET `order` = '".$row['order']."' WHERE `id` = '".$row2['id']."';";
                  $result=mysql_query($sql);
                  $order=$row2['order'];
               }
            } elseif (0 ==strcmp ($HTTP_POST_VARS['position'], "down")) {// it needs to move down
               $sql="SELECT * FROM ".$list_prefix ."links WHERE `order` > '".$row['order']."' ORDER BY `order`;";
               $result=mysql_query($sql);
               if ($result)
                  $rows = mysql_num_rows($result);
               else
                  $rows = 0;
               if ($rows > 0) {
                  $row2 = mysql_fetch_array($result);
                  $sql="UPDATE ".$list_prefix ."links SET `order` = '".$row['order']."' WHERE `id` = '".$row2['id']."';";
                  $result=mysql_query($sql);
                  $order=$row2['order'];
               }
            }
            //now we have the correct order, category, name, and url lets update the db
            if ($row['category'] != $HTTP_POST_VARS['category']) {  //if we are moving to a NEW category lets make this the last link present.
               $sql="SELECT * FROM ".$list_prefix ."links WHERE `category` = '".$HTTP_POST_VARS['category']."' ORDER BY `order` DESC;";
               $result=mysql_query($sql);
               if ($result)
                  $rows=mysql_num_rows($result);
               else
                  $rows=0;
               if ($rows > 0) {
                  $row=mysql_fetch_array($result);
                  $order=$row['order']+1;
               } else
                  $order=1;
            }
            //now lets save our changes
            $sql="UPDATE ".$list_prefix ."links SET `category` = '".$HTTP_POST_VARS['category']."', `title` = '".$HTTP_POST_VARS['linktitle']."', `url` = '".$HTTP_POST_VARS['linkurl']."', `order` = '".$order."' WHERE `id` = '".$HTTP_POST_VARS['linkid']."';";
            $result=mysql_query($sql);
         } else
            $WORK="ERROR: you must check 'Save as a new link' to make a new link.<BR>\r\n".$WORK;
      }
      catorder ();
   }
   //lets output our news cp.
   $WORK=insert_into_template ($WORK, "{LINKSLIST}", linkslist ());
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
   $WORK=filltemplate ($WORK, "{SITENAME} Administration panel");
   printf ("%s", striptemplate ($WORK));

}

//===Main code================================================================
   if (checkadminlogin () == 1) //if the user is logged in
      content ();
   else
      loginbox ();
?>