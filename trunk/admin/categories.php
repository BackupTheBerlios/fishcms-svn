<?
//****************************************************************************
//* File:	admin/categories.php
//* Author:	G.A. Heath
//* Date: 	August 14, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 15, 2005
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

//***function content ()******************************************************
function content () {
global $HTTP_POST_VARS, $HTTP_GET_VARS, $list_prefix;
$MAIN=loadtmplate ("main");
$CATEGORIES=loadtmplate("categories");
   //we can choose to edit, add, or delete a category.
   if (0 == strcmp ($HTTP_GET_VARS['mode'], "delete")) {
      if (isset ($HTTP_POST_VARS['delete_yes']))
         if ($HTTP_POST_VARS['category'] > 0) {
            $sql="DELETE FROM `".$list_prefix ."category` WHERE `id` = ".$HTTP_POST_VARS['category'].";";
            $result=mysql_query($sql);
            if ($result)
               $CONTENT="The selected category has been deleted.<BR><BR>";
            else
               $CONTENT="The selected category could not be deleted.<BR><BR>";
         } else
            $CONTENT="You can not delete the SYSTEM category.<BR><BR>";
      else
         $CONTENT="You must click the checkbox to delete a category.<BR><BR>";
      $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
      $WORK=filltemplate ($WORK, "{SITENAME} Administration panel");
      printf ("%s", striptemplate ($WORK));
   } elseif (0 == strcmp ($HTTP_GET_VARS['mode'], "add")) {
      //we will do a search of the categories in the db in reverse sort on order.
      $sql="SELECT * FROM `".$list_prefix ."category` ORDER BY `order` DESC limit 1;";
      $result=mysql_query($sql);
      $rows = mysql_num_rows($result);
      //we will add +1 to that for the new entry's order.
      if ($rows == 0)
         $order=1;
      else {
      //we will add +1 to that for the new entry's order.
         $row = mysql_fetch_array($result);
         $order=$row['order']+1;
      }
      //then we will insert the new category and its order value into the db.
      //we will then report success or failure and draw the page.
      if (isset ($HTTP_POST_VARS['catname'])) {
         $sql="INSERT INTO ".$list_prefix."category VALUES ('', '".$HTTP_POST_VARS['catname']."', '".$order."');";
         $result=mysql_query($sql);
         if ($result)
            $CONTENT="The category ".$HTTP_POST_VARS['catname']." has been added to the database<BR><BR>\r\n";
         else
            $CONTENT="Sorry there was an ERROR while adding the category to the database.<BR><BR>\r\n";
      } else
         $CONTENT="Sorry but the category must have a name!<BR><BR>\r\n";
      //lets output the results.
      $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
      $WORK=filltemplate ($WORK, "{SITENAME} Administration panel");
      printf ("%s", striptemplate ($WORK));
   } elseif (0 == strcmp ($HTTP_GET_VARS['mode'], "edit")) {
   //first we must make sure that our category is valid and not category 0
      if ($HTTP_POST_VARS['category'] > 0) {
         $sql="SELECT * FROM `".$list_prefix ."category` WHERE `id` = '".$HTTP_POST_VARS['category']."';";
         $result=mysql_query($sql);
         if ($result)
            $rows = mysql_num_rows($result);
         else
            $rows=0;
         if ($rows > 0) {
            $row = mysql_fetch_array($result);
         //lets figure out if we need to change the name or leave it the same.
            if (isset ($HTTP_POST_VARS['catname']) && ($HTTP_POST_VARS['catname'] != ""))
               $name=$HTTP_POST_VARS['catname'];
            else
               $name=$row['name'];
         //lets figure out if there's a change to the order.
            if (isset ($HTTP_POST_VARS['position'])) {
               if (0 == strcmp ($HTTP_POST_VARS['position'], "up")) {
                  $sql="SELECT * FROM ".$list_prefix ."category WHERE `order` < '".$row['order']."' ORDER BY `order` DESC;";
                  $result=mysql_query($sql);
                  if ($result)
                     $rows = mysql_num_rows($result);
                  else
                     $rows=0;
                  if ($rows > 0) {
                     $row2 = mysql_fetch_array($result);
                     $sql="UPDATE ".$list_prefix ."category SET `order` = '".$row['order']."' WHERE `id` = '".$row2['id']."';";
                     $result=mysql_query($sql);
                     $order=$row2['order'];
                  } else
                     $order=$row['order'];
               } elseif (0 == strcmp ($HTTP_POST_VARS['position'], "down")) {
                  $sql="SELECT * FROM ".$list_prefix ."category WHERE `order` > '".$row['order']."' ORDER BY `order`;";
                  $result=mysql_query($sql);
                  if ($result)
                     $rows = mysql_num_rows($result);
                  else
                     $rows=0;
                  if ($rows > 0) {
                     $row2 = mysql_fetch_array($result);
                     $sql="UPDATE ".$list_prefix ."category SET `order` = '".$row['order']."' WHERE `id` = '".$row2['id']."';";
                     $result=mysql_query($sql);
                     $order=$row2['order'];
                  } else
                     $order=$row['order'];
                  
               } else //the order will stay the same by default.
                  $order=$row['order'];
            }
            $sql="UPDATE `".$list_prefix ."category` SET ";
            $sql.="`name` = '".$name."', `order` = '".$order."' ";
            $sql.="WHERE `id` = '".$HTTP_POST_VARS['category']."';";
            $result=mysql_query($sql);
            if ($result)
               $CONTENT="The changes made have been saved.<BR><BR>\r\n";
            else
               $CONTENT="ERROR: Unable to make the changes requested.<BR><BR>\r\n";
         } else
            $CONTENT="ERROR: Unable to alter a category that does not exist.<BR><BR>\r\n";
      }
      $WORK=insert_into_template ($MAIN, "{CONTENT}", $CONTENT);
      $WORK=filltemplate ($WORK, "{SITENAME} Administration panel");
      printf ("%s", striptemplate ($WORK));
   } else {
      //here we will read the categories from the db and let the user choose to delete or edit them.
      //we will include a form to optionally add a category.
      //category 0 is always present and can not be deleted.
      //to delete a category the user must choose it from a list, enter the name in a box and click "Delete"
      $CONTENT="<select name='category'>";
      $sql="SELECT * FROM ".$list_prefix ."category WHERE `id` > 0 ORDER BY `order`;";
      $result=mysql_query($sql);
      $rows = mysql_num_rows($result);
      if ($rows == 0)
         $CONTENT.="<option value='-'>No categories found</option>";
      else {
         $i=0;
         while ($i<$rows) {
            $row = mysql_fetch_array($result);
            $CONTENT.="<option value='".$row['id']."'>".$row['name']."</option>";
            $i++;
         }
      }
      $CONTENT.="</select>";
      //lets output the results.
      $WORK=insert_into_template ($CATEGORIES, "{CATLIST}", $CONTENT);
      $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
      $WORK=filltemplate ($WORK, "{SITENAME} Administration panel");
      printf ("%s", striptemplate ($WORK));
   }
}
//===Main code================================================================
   if (checkadminlogin () == 1) //if the user is logged in
      content ();
   else
      loginbox ();
?>