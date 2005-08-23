<?
//****************************************************************************
//* File:	admin/blocks.php
//* Author:	G.A. Heath
//* Date: 	August 20, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 22, 2005
//****************************************************************************

//===common code that should be run each time=================================
include "../common.inc.php";

//===Functions================================================================

//***function fixorder ($order, $blockset)************************************
function fixorder ($order, $blockset) {
global $list_prefix;
//lets see if there are any entries for this order number
   $sql="SELECT * FROM `".$list_prefix ."blocks` WHERE (`blockset` = '".$blockset."' AND `order` = '".$order."');";
   $result=mysql_query($sql);
   if ($result)
      $rows = mysql_num_rows($result);
   else
      $rows=0;
//if there are not lets see if there are any above it.
   if ($rows == 0) {
      $sql="SELECT * FROM `".$list_prefix ."blocks` WHERE (`blockset` = '".$blockset."' AND `order` > '".$order."') ORDER BY `order`;";
      $result=mysql_query($sql);
      if ($result)
         $rows = mysql_num_rows($result);
      else
         $rows=0;
//if there entries above us lets set the first one to this order and then recurse with order+1;
      if ($rows != 0) {
         $row = mysql_fetch_array($result);
         $sql="UPDATE `".$list_prefix ."blocks` SET `order` = '".$order."' WHERE `id` = '".$row['id']."';";
         $result=mysql_query($sql);
         fixorder ($order+1, $blockset);
      }
   } else //if there are is a match lets just recurse to the next order.
      fixorder ($order+1, $blockset);
}

//***function loginbox ()*****************************************************
function loginbox () {
   printf ("<form method='post' action='index.php?login=1'>\r\n");
   printf ("Username: <input type='text' name='adminuser' size='20'><BR>");
   printf ("Password: <input type='password' name='adminpass' size='20'><BR>");
   printf ("<input type='submit' value='Login'><BR>");
   printf ("</form>\r\n");
}

//***function list_inactive_blocks ()*****************************************
function list_inactive_blocks () {
global $list_prefix;
$BLOCK_LIST="<select name='inactive_blocks'>\r\n";
$block_dir="../blocks/";
$inactive=0;
   if ($dir_handle=opendir($block_dir)) {
      while ($entry = readdir ($dir_handle))
         if ((!is_dir ($block_dir.$entry)) && (0 != strcmp($entry, ".")) && (0 != strcmp($entry, ".."))) {
            $sql="SELECT * FROM ".$list_prefix ."blocks WHERE `name` = '".str_replace (".php", "", $entry)."';";
            $result=mysql_query($sql);
            if ($result)
               $rows=mysql_num_rows($result);
            else
               $rows=0;
            if ($rows == 0) {
               $BLOCK_LIST.="<option value='".str_replace (".php", "", $entry)."'>".str_replace (".php", "", $entry)."</option>\r\n";
               $inactive++;
            }
         }
      closedir ($dir_handle);
   }
   if ($inactive == 0) {
      $BLOCK_LIST.="<option value='-'>No Inactive Modules</option>\r\n";
   }
   $BLOCK_LIST.="</select>\r\n";
   return $BLOCK_LIST;
}
//***function list_active_blocks ()*******************************************
function list_active_blocks () {
global $list_prefix;
   $BLOCK_LIST="<select name='active_blocks'>\r\n";

   $sql="SELECT * FROM `".$list_prefix ."blocks` ORDER by `order`;";
   $result=mysql_query($sql);
   if ($result)
      $rows=mysql_num_rows($result);
   else
      $rows=0;
   $i=0;
   while ($i < $rows) {
      $row=mysql_fetch_array($result);
      $BLOCK_LIST.="<option value='".$row['name']."'>".$row['name']."</option>\r\n";
      $i++;
   }
   if ($rows == 0)
      $BLOCK_LIST.="<option value='-'>No Active Blocks</option>\r\n";
   $BLOCK_LIST.="</select>\r\n";
   return $BLOCK_LIST;
}

//***function move_area_list ()***********************************************
function move_area_list () {
   $AREA_LIST="<select name='block_area'>\r\n";
   $AREA_LIST.="<option value='-'>Don't move</option>\r\n";
   $AREA_LIST.="<option value='1'>Area 1</option>\r\n";
   $AREA_LIST.="<option value='2'>Area 2</option>\r\n";
   $AREA_LIST.="<option value='3'>Area 3</option>\r\n";
   $AREA_LIST.="<option value='4'>Area 4</option>\r\n";
   $AREA_LIST.="</select>\r\n";
   return $AREA_LIST;
}

//***function area_list ()****************************************************
function area_list () {
   $AREA_LIST="<select name='block_area'>\r\n";
   $AREA_LIST.="<option value='1'>Area 1</option>\r\n";
   $AREA_LIST.="<option value='2'>Area 2</option>\r\n";
   $AREA_LIST.="<option value='3'>Area 3</option>\r\n";
   $AREA_LIST.="<option value='4'>Area 4</option>\r\n";
   $AREA_LIST.="</select>\r\n";
   return $AREA_LIST;
}

//***function content ()******************************************************
function content () {
global $HTTP_POST_VARS, $HTTP_GET_VARS, $list_prefix;
$MAIN=loadadmintmplate ("main");
$BLOCKS=loadadmintmplate ("blocks");
   //we will process changes here
   if (0 == strcmp($HTTP_GET_VARS['mode'],"ablocks")) { //process active blocks
      if (isset ($HTTP_POST_VARS['disable'])) { //if we are to disable the block
         $sql="DELETE FROM `".$list_prefix ."blocks` WHERE `name` = '".$HTTP_POST_VARS['active_blocks']."';";
         $result=mysql_query($sql);
      } else { //otherwise we will process all the fields.
         //lets read the db info for the block, we will set the default order also
         $sql="SELECT * FROM `".$list_prefix ."blocks` WHERE `name` = '".$HTTP_POST_VARS['active_blocks']."';";
         $result=mysql_query($sql);
         if ($result)
            $rows=mysql_num_rows($result);
         else
            $rows=0;
         if ($rows != 0) {
            $row=mysql_fetch_array($result);
            $order=$row['order'];
            $id=$row['id'];
         } else 
            $order=1;  //we will default to order of 1.
         //lets determine if there are any moves
         if (0 == strcmp ($HTTP_POST_VARS['position'], "up")) { //if it moves up
         //now we will find the new value for $order to move to
            $sql="SELECT * FROM `".$list_prefix ."blocks` WHERE `blockset` = '".$row['blockset']."' AND `order` < '".$order."' ORDER by `order` DESC;";
            $result=mysql_query($sql);
            if ($result)
               $rows=mysql_num_rows($result);
            else
               $rows=0;
            if ($rows != 0) { //if we have no rows we don't move it up, but if there are rows we want to trade places with the one above.
               $row2=mysql_fetch_array($result);
               //now we will set $row2 to $row's order
               $sql="UPDATE ".$list_prefix."blocks SET `order` = '".$order."' WHERE `id` = '".$row2['id']."';";
               $result=mysql_query($sql);
               if ($result) //if we succeeded we will now change $order to $row2's previous order
                  $order=$row2['order'];
            }
         } elseif (0 == strcmp ($HTTP_POST_VARS['position'], "down")) { //if it doesn't move.
         //now we will find the new value for $order to move to
            $sql="SELECT * FROM `".$list_prefix ."blocks` WHERE `blockset` = '".$row['blockset']."' AND `order` > '".$order."' ORDER by `order`;";
            $result=mysql_query($sql);
            if ($result)
               $rows=mysql_num_rows($result);
            else
               $rows=0;
            if ($rows != 0) { //if we have no rows we don't move it down, but if there are rows we want to trade places with the one below.
               $row2=mysql_fetch_array($result);
               //now we will set $row2 to $row's order
               $sql="UPDATE ".$list_prefix."blocks SET `order` = '".$order."' WHERE `id` = '".$row2['id']."';";
               $result=mysql_query($sql);
               if ($result) //if we succeeded we will now change $order to $row2's previous order
                  $order=$row2['order'];
            }
         }
         //now lets see if we are moving the block to a new blockset
         if (0 != strcmp ($HTTP_POST_VARS['block_area'],"-")) { //we are moving the blockset
            $blockset=$HTTP_POST_VARS['block_area'];
            //if we are moving to a new block set we need to make 100% sure that we don't break the order so we will put this on the end of that blockset.
            $sql="SELECT * FROM `".$list_prefix ."blocks` WHERE `blockset` = '".$blockset."' ORDER by `order` DESC;";
            $result=mysql_query($sql);
            if ($result)
               $rows=mysql_num_rows($result);
            else
               $rows=0;
            if (0 != $rows) {
               $row=mysql_fetch_array($result);
               $order=$row['order']+1;
            } else { //there are no blocks in this blockset so we will be the first.
               $order=1;
            }
         } else { //we are not moving the blockset
            $blockset=$row['blockset'];
         }
         //here is where we will update the db with the new values for block.
         $sql="UPDATE ".$list_prefix."blocks SET `blockset` = '".$blockset."', `order` = '".$order."' WHERE `id` = '".$id."';";
         $result=mysql_query($sql);
      }
      //here we will run fix order to correct any issues in the order of the modules.
      fixorder (1, 1);
      fixorder (1, 2);
      fixorder (1, 3);
      fixorder (1, 4);
   }
   if (0 == strcmp($HTTP_GET_VARS['mode'],"iblocks")) { //process inactive blocks
   //lets figure out what our order and blockset are.   
      $blockset=$HTTP_POST_VARS['block_area'];
      $sql="SELECT * FROM `".$list_prefix ."blocks` WHERE `blockset` = '".$blockset."' ORDER by `order` DESC;";
      $result=mysql_query($sql);
      if ($result)
         $rows=mysql_num_rows($result);
      else
         $rows=0;
      if (0 != $rows) {
         $row=mysql_fetch_array($result);
         $order=$row['order']+1;
      } else { //there are no blocks in this blockset so we will be the first.
         $order=1;
      }
   //now lets prepare our sql query
      $sql="INSERT INTO ".$list_prefix."blocks VALUES ('', '".$HTTP_POST_VARS['inactive_blocks']."', '".$blockset."', '".$order."');";
      $result = mysql_query($sql);
   }
   //now we will handle our output.
   $WORK=insert_into_template ($BLOCKS, "{INACTIVE_BLOCK_LIST}", list_inactive_blocks ());
   $WORK=insert_into_template ($WORK, "{ACTIVE_BLOCK_LIST}", list_active_blocks ());
   $WORK=insert_into_template ($WORK, "{MOVE_AREA_LIST}", move_area_list ());
   $WORK=insert_into_template ($WORK, "{AREA_LIST}", area_list ());
   $WORK=insert_into_template ($MAIN, "{CONTENT}", $WORK);
   printf ("%s", striptemplate ($WORK));
}

//===Main code================================================================
   if (checkadminlogin () == 1) //if the user is logged in
      content ();
   else
      loginbox ();
?>