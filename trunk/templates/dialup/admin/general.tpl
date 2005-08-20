<b>Site information</b>
<form method='post' action='general.php?mode=site'>
   <table width=80% border=1>
      <tr>
         <td>
            Site name:
         </td>
         <td>
            <input type='text' name='sitename' size='40' value='{SITENAME}'>
         </td>
      </tr>
      <tr>
         <td>
            Site description:
         </td>
         <td>
            <input type='text' name='sitedescription' size='40' value='{SITEDESCRIPTION}'>
         </td>
      </tr>
      <tr>
         <td>
            Site email address:
         </td>
         <td>
            <input type='text' name='email' size='40' value='{EMAIL}'>
         </td>
      </tr>
      <tr>
         <td>
            Copyright notice:
         </td>
         <td>
            <textarea name='copyright' rows='4' cols='60'>{COPYRIGHT}</textarea>
         </td>
      </tr>
   </table>
   <input type='submit' value='Submit Changes'>
</form>
<BR>

<b>Index page</b>
<form method='post' action='general.php?mode=index'>
   <table width=80% border=1>
      <tr>
         <td>
            <INPUT TYPE=RADIO NAME="redir_mod" VALUE="module" {MODCHECKED}> Use Modules
         </td>
         <td>
            <INPUT TYPE=RADIO NAME="redir_mod" VALUE="redirect" {REDIRCHECKED}> Redirect
         </td>
         <td>
            Redirect to: <input type='text' name='redirect' value='{REDIRECT}'>
         </td>
      </tr>
   </table>
   <input type='submit' value='Submit Changes'>
</form>

<b>Active Index modules</b>
<form method='post' action='general.php?mode=amodules'>
   <table width=80% border=1>
      <tr>
         <td colspan=2>
            {MODULE_LIST}
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

<b>Inactive Index modules</b>
<form method='post' action='general.php?mode=imodules'>
   {INACTIVE_MODULE_LIST} <input type='submit' value='Activate'>
</form>
