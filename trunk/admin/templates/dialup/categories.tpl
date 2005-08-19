<b>Add a category</b>
<form method='post' action='categories.php?mode=add'>
<table width=80% border=1>
   <tr>
      <td>
         Name: <input type='text' name='catname' size='20' value=''>
      </td>
      <td>
         <input type='submit' value='Add category'>
      </td>
   </tr>
</table>
</form>
<BR>
<b>Edit a category</b>
<form method='post' action='categories.php?mode=edit'>
<table width=80% border=1>
   <tr>
      <td>
         {CATLIST}
      </td>
      <td>
         New name: <input type='text' name='catname' size='20' value=''>
      </td>
      <td>
         <input type='submit' value='Submit'>
      </td>
   </tr>
   <tr>
      <td>
         <INPUT TYPE=RADIO NAME="position" VALUE="same" checked> Don't move
      </td>
      <td>
         <INPUT TYPE=RADIO NAME="position" VALUE="up"> Move up
      </td>
      <td>
         <INPUT TYPE=RADIO NAME="position" VALUE="down"> Move down
      </td>
   </tr>
</table>
</form>
<BR>
<b>Delete a category</b>
<form method='post' action='categories.php?mode=delete'>
<table width=80% border=1>
   <tr>
      <td>
         Check to delete <input type='checkbox' name='delete_yes'>
      <td>
      <td>
         {CATLIST}
      </td>
      <td>
         <input type='submit' value='Delete'>
      </td>
   </tr>
</table>
</form>
