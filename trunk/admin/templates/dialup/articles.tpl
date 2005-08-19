<b>Select an article</b>
<form method='post' action='articles.php?mode=select'>
<table width=80% border=1>
   <tr>
      <td>
         {ARTICLELIST}
      </td>
      <td>
         <input type='submit' value='Edit Article'>
      </td>
   </tr>
</table>
</form>

<b>Delete an article</b>
<form method='post' action='articles.php?mode=delete'>
<table width=80% border=1>
   <tr>
      <td>
         {ARTICLELIST}
      </td>
      <td>
         Check to delete <input type='checkbox' name='delete_yes'>
      </td>
      <td>
         <input type='submit' value='Delete Article'>
      </td>
   </tr>
</table>
</form>

<b>Edit article</b>
<form method='post' action='articles.php?mode=edit'>
<input type='hidden' name='articleid' value='{ARTICLEID}'>
<table width=80% border=1>
   <tr>
      <td>
         Save as a new article <input type='checkbox' name='newarticle' {NEWCHECK}>
      </td>
   </tr>
   <tr>
      <td>
         Category {CATLIST}
      </td>
   </tr>
   <tr>
      <td>
         Article title: <input type='text' name='articletitle' size='60' value='{ARTICLETITLE}'>
      </td>
   </tr>
   <tr>
       <td>
         Article teaser:
         <textarea name='teaser' rows='4' cols='100'>{TEASER}</textarea>
      </td>
   </tr>
   <tr>
      <td>
         Extended content
         <textarea name='article' rows='10' cols='100'>{ARTICLE}</textarea>
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
         <input type='submit' value='Save article'>
      </td>
   </tr>
</table>
</form>