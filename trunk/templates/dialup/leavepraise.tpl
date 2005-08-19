<div align='center'>
   <form method='post' action='leavepraise.php?request={REQUESTID}&submit=1'>
      <P>
         {LOGGEDIN}<BR>
      </P>
      <P>
         <input type='checkbox' name='anonymous'> Make request anonymously.<BR>
      </P>
      <P>
         I would like to leave the following praise:<BR>
         <textarea name='praise' rows='4' cols='60'>{PREFILL}</textarea><BR>
         <input type='submit' value='Submit'><BR>
      </P>
   </form>
</div>
