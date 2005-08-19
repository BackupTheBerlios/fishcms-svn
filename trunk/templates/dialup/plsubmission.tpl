<div align='center'>
   <form method='post' action='plsubmission.php?submit=1'>
   <P>
      {LOGGEDIN}
   </P>
   <P>
      <input type='checkbox' name='anonymous'> Make request anonymously.<BR>
   </P>
   <P>
      I would like to submit a prayer request for:<BR>
      <input type='text' name='request_for' size='50' value='{REQUESTFOR}'><BR>
      My prayer request is:<BR>
      <textarea name='request' rows='4' cols='60'>{REQUEST}</textarea><BR>
      I would like this request to expire in:
      <select name='expire_date'>
         <option value='1w'>One Week</option>
         <option SELECTED value='2w'>Two Weeks</option>
         <option value='30d'>Thirty Days</option>
         <option value='90d'>Ninety Days</option>
         <option value='1y'>One Year</option>
      </select>
      <BR>
      <input type='submit' value='Submit'><BR>
   </P>
   </form>
</div>
