<?
//****************************************************************************
//* File:	news.mod.php
//* Author:	G.A. Heath
//* Date: 	August 1, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 22, 2005
//****************************************************************************

//===common code that should be run each time=================================
//***includes*****************************************************************

//===Functions================================================================
//*** function getnews ($perpage)***************************************
function getnews ($perpage) {
global $list_prefix;
$NEWS=loadtmplate ("news.mod");
$CONTENT="";
 //lets calculate our query
   $sql="SELECT * FROM ".$list_prefix. "news ORDER BY `date` DESC LIMIT 0 , ".$perpage.";";
//now lets show the prayerlist entries.
   $result=mysql_query($sql);
@   $rows = mysql_num_rows($result);
   if ($rows != 0) {
      $j=0;
      while ($j < $rows) {
         //lets fetch our prayer request from the database.
         $row = mysql_fetch_array($result);
         $postedby=getuser ($row['posted_by']);
         //lets insert the prayerrequest into our working copy of this template.
         $WORK=insert_into_template ($NEWS, "{NEWSTITLE}", stripslashes ($row['news_title']));
         $WORK=insert_into_template ($WORK, "{TEASER}", stripslashes ($row['teaser']));
         $WORK=insert_into_template ($WORK, "{NEWSID}", $row['id']);
         $WORK=insert_into_template ($WORK, "{POSTEDBY}", $postedby);
         $WORK=insert_into_template ($WORK, "{BYLINE}", $row['byline']);
         $WORK=insert_into_template ($WORK, "{CATEGORY}", getcatname ($row['category']));
         $WORK=insert_into_template ($WORK, "{DATE}", date ("m/d/Y", $row['date']));
         $j++;
         //now lets add this request to the CONTENT.
         $CONTENT.=$WORK;
      }
   } else {
      $CONTENT.="There are no active news items at this time.<BR>\r\n";
   }
   //when we output this lets make sure that the output is stripped of any template elements that are not used.
   return striptemplate ($CONTENT);
   }
//===Main code================================================================
//check to see if the user is logged in.
   //start main code here.
   $MOD['title']="News";
   $MOD['content']=getnews ($perpage);
?>