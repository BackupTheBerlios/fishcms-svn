<b>Active Blocks</b>
<form method='post' action='blocks.php?mode=ablocks'>
   <table width=80% border=1>
      <tr>
         <td>
            {ACTIVE_BLOCK_LIST}
         </td>
         <td>
            Move to {MOVE_AREA_LIST}
         </td>
         <td>
            <input type='checkbox' name='disable'> Make inactive
         </td>
      </tr>
      <tr>
         <td>
            <INPUT TYPE=RADIO NAME="position" value="same" checked> Don't move
         </td>
         <td>
            <INPUT TYPE=RADIO NAME="position" value="up"> Move up
         </td>
         <td>
            <INPUT TYPE=RADIO NAME="position" value="down"> Move down
         </td>
      </tr>
   </table>
   <input type='submit' value='Submit Changes'>
</form>

<b>Inactive blocks</b>
<form method='post' action='blocks.php?mode=iblocks'>
   Activate {INACTIVE_BLOCK_LIST} in area {AREA_LIST} <input type='submit' value='Activate'>
</form>
