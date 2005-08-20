<b>Select a link</b>
<form method='post' action='links.php?mode=select'>
<table width=80% border=1>
   <tr>
      <td>
         {LINKSLIST}
      </td>
      <td>
         <input type='submit' value='Edit Link'>
      </td>
   </tr>
</table>
</form>

<b>Delete a link</b>
<form method='post' action='links.php?mode=delete'>
<table width=80% border=1>
   <tr>
      <td>
         {LINKSLIST}
      </td>
      <td>
         Check to delete <input type='checkbox' name='delete_yes'>
      </td>
      <td>
         <input type='submit' value='Delete Link'>
      </td>
   </tr>
</table>
</form>

<b>Edit Link</b>
<form method='post' action='links.php?mode=edit'>
<input type='hidden' name='linkid' value='{LINKID}'>
<table width=80% border=1>
   <tr>
      <td>
         Save as a new link <input type='checkbox' name='newlink' {NEWCHECK}>
      </td>
      <td colspan='2'>
         Category {CATLIST}
      </td>
   </tr>
   <tr>
      <td colspan='3'>
         Link title: <input type='text' name='linktitle' size='60' value='{LINKTITLE}'>
      </td>
   </tr>
   <tr>
      <td colspan='3'>
         Link URL: <input type='text' name='linkurl' size='60' value='{LINKURL}'>
      </td>
   </tr>
   <tr>
      <td>
         <INPUT TYPE=RADIO NAME="position" value="same" checked> Don't move      </td>
      <td>
         <INPUT TYPE=RADIO NAME="position" value="up"> Move up
      </td>
      <td>
         <INPUT TYPE=RADIO NAME="position" value="down"> Move down
      </td>
   </tr>
</table>
<input type='submit' value='Save Link'>
</form>
