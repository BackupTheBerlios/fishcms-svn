<?
//****************************************************************************
//* File:	articles.mod.php
//* Author:	G.A. Heath
//* Date: 	August 1, 2005.
//* License:	GNU Public License (GPL)
//* Last edit:	August 17, 2005
//****************************************************************************

//===Functions================================================================
//*** function getarticles ($perpage)***************************************
function getarticles ($perpage) {
global $list_prefix;
$ARTICLES=loadtmplate ("articles.mod");
$CONTENT="";
 //lets calculate our query
   $sql="SELECT * FROM ".$list_prefix. "articles LIMIT 0 , ".$perpage.";";
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
         $WORK=insert_into_template ($ARTICLES, "{ARTICLETITLE}", stripslashes ($row['article_title']));
         $WORK=insert_into_template ($WORK, "{TEASER}", stripslashes ($row['teaser']));
         $WORK=insert_into_template ($WORK, "{ARTICLEID}", $row['id']);
         $WORK=insert_into_template ($WORK, "{POSTEDBY}", $postedby);
         $WORK=insert_into_template ($WORK, "{BYLINE}", $row['byline']);
         $WORK=insert_into_template ($WORK, "{DATE}", date ("m/d/Y", $row['date']));
         $WORK=insert_into_template ($WORK, "{CATEGORY}", getcatname ($row['category']));
         $j++;
         //now lets add this request to the CONTENT.
         $CONTENT.=$WORK;
      }
   } else {
      $CONTENT.="There are no active articles at this time.<BR>\r\n";
   }
   //when we output this lets make sure that the output is stripped of any template elements that are not used.
   return striptemplate ($CONTENT);
   }
//===Main code================================================================
//check to see if the user is logged in.
   //start main code here.
   $MOD['title']="Articles";
   $MOD['content']=getarticles ($perpage);
?>