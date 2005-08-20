<b>Select an news item</b>
<form method='post' action='news.php?mode=select'>
<table width=80% border=1>
   <tr>
      <td>
         {NEWSLIST}
      </td>
      <td>
         <input type='submit' value='Edit News'>
      </td>
   </tr>
</table>
</form>

<b>Delete an news item</b>
<form method='post' action='news.php?mode=delete'>
<table width=80% border=1>
   <tr>
      <td>
         {NEWSLIST}
      </td>
      <td>
         Check to delete <input type='checkbox' name='delete_yes'>
      </td>
      <td>
         <input type='submit' value='Delete News'>
      </td>
   </tr>
</table>
</form>

<b>Edit news item</b>
<form method='post' action='news.php?mode=edit'>
<input type='hidden' name='newsid' value='{NEWSID}'>
<table width=80% border=1>
   <tr>
      <td>
         Save as a new news item <input type='checkbox' name='newnews' {NEWCHECK}>
      </td>
   </tr>
   <tr>
      <td>
         Category {CATLIST}
      </td>
   </tr>
   <tr>
      <td>
         News title: <input type='text' name='newstitle' size='60' value='{NEWSTITLE}'>
      </td>
   </tr>
   <tr>
       <td>
         News teaser:
         <textarea name='teaser' rows='4' cols='100'>{TEASER}</textarea>
      </td>
   </tr>
   <tr>
      <td>
         Extended content
         <textarea name='news' rows='10' cols='100'>{NEWS}</textarea>
         <br>(this follows the teaser)
      </td>
   </tr>
   <tr>
      <td>
         Byline: <input type='text' name='byline' size='20' value='{BYLINE}'>
      </td>
   </tr>
   <tr>
      <td>
         <input type='submit' value='Save News'>
      </td>
   </tr>
</table>
</form>
