<p>Add a weekly event (this events happens on a specific weekday every week).<b></br>
<form method='post' action='calendar.php?mode=dow'>
Day of the week: <select name='dow'>
   <option value='0'>Sunday</option>
   <option value='1'>Monday</option>
   <option value='2'>Tuesday</option>
   <option value='3'>Wednesday</option>
   <option value='4'>Thursday</option>
   <option value='5'>Friday</option>
   <option value='6'>Saturday</option>
</select>
Hour: <select name='hour'>
   <option value='01'>1</option>
   <option value='02'>2</option>
   <option value='03'>3</option>
   <option value='04'>4</option>
   <option value='05'>5</option>
   <option value='06'>6</option>
   <option value='07'>7</option>
   <option value='08'>8</option>
   <option value='09'>9</option>
   <option value='10'>10</option>
   <option value='11'>11</option>
   <option value='12'>12</option>
</select>
Minutes: <select name='tmin'>
   <option value='0'>0</option>
   <option value='1'>1</option>
   <option value='2'>2</option>
   <option value='3'>3</option>
   <option value='4'>4</option>
   <option value='5'>5</option>
</select>
<select name='omin'>
   <option value='0'>0</option>
   <option value='1'>1</option>
   <option value='2'>2</option>
   <option value='3'>3</option>
   <option value='4'>4</option>
   <option value='5'>5</option>
   <option value='6'>6</option>
   <option value='7'>7</option>
   <option value='8'>8</option>
   <option value='9'>9</option>
</select>
 Am/Pm: <select name='ampm'>
   <option value='a'>Am</a>
   <option value='p'>Pm</a>
</select> <BR>
Event description:<BR>
<textarea name='description' rows='4' cols='60'></textarea><BR>
<input type='submit' value='Submit'>
</form></p>

<p>Add a monthly event (happens on a specific day every month).</b><br>
<form method='post' action='calendar.php?mode=dom'>
Day of the month: <select name='dom'>
   <option value='01'>1</option>
   <option value='02'>2</option>
   <option value='03'>3</option>
   <option value='04'>4</option>
   <option value='05'>5</option>
   <option value='06'>6</option>
   <option value='07'>7</option>
   <option value='08'>8</option>
   <option value='09'>9</option>
   <option value='10'>10</option>
   <option value='11'>11</option>
   <option value='12'>12</option>
   <option value='13'>13</option>
   <option value='14'>14</option>
   <option value='15'>15</option>
   <option value='16'>16</option>
   <option value='17'>17</option>
   <option value='18'>18</option>
   <option value='19'>19</option>
   <option value='20'>20</option>
   <option value='21'>21</option>
   <option value='22'>22</option>
   <option value='23'>23</option>
   <option value='24'>24</option>
   <option value='25'>25</option>
   <option value='26'>26</option>
   <option value='27'>27</option>
   <option value='28'>28</option>
   <option value='29'>29</option>
   <option value='30'>30</option>
   <option value='31'>31</option>
</select>
Hour: <select name='hour'>
   <option value='01'>1</option>
   <option value='02'>2</option>
   <option value='03'>3</option>
   <option value='04'>4</option>
   <option value='05'>5</option>
   <option value='06'>6</option>
   <option value='07'>7</option>
   <option value='08'>8</option>
   <option value='09'>9</option>
   <option value='10'>10</option>
   <option value='11'>11</option>
   <option value='12'>12</option>
</select>
Minutes: <select name='tmin'>
   <option value='0'>0</option>
   <option value='1'>1</option>
   <option value='2'>2</option>
   <option value='3'>3</option>
   <option value='4'>4</option>
   <option value='5'>5</option>
</select>
<select name='omin'>
   <option value='0'>0</option>
   <option value='1'>1</option>
   <option value='2'>2</option>
   <option value='3'>3</option>
   <option value='4'>4</option>
   <option value='5'>5</option>
   <option value='6'>6</option>
   <option value='7'>7</option>
   <option value='8'>8</option>
   <option value='9'>9</option>
</select>
Am/Pm: <select name='ampm'>
   <option value='a'>Am</a>
   <option value='p'>Pm</a>
</select> <br>
Event Description:<br>
<textarea name='description' rows='4' cols='60'></textarea><br>
<input type='submit' value='Submit'>
</form></p>

<p>Add a yearly event (happens every year on a specific month and day).</b><br>
<form method='post' action='calendar.php?mode=moy'>
Month: <select name='moy'>
   <option value='01'>January</option>
   <option value='02'>February</option>
   <option value='03'>March</option>
   <option value='04'>April</option>
   <option value='05'>May</option>
   <option value='06'>June</option>
   <option value='07'>July</option>
   <option value='08'>August</option>
   <option value='09'>September</option>
   <option value='10'>October</option>
   <option value='11'>November</option>
   <option value='12'>December</option>
</select>
Day: <select name='domoy'>
   <option value='01'>1</option>
   <option value='02'>2</option>
   <option value='03'>3</option>
   <option value='04'>4</option>
   <option value='05'>5</option>
   <option value='06'>6</option>
   <option value='07'>7</option>
   <option value='08'>8</option>
   <option value='09'>9</option>
   <option value='10'>10</option>
   <option value='11'>11</option>
   <option value='12'>12</option>
   <option value='13'>13</option>
   <option value='14'>14</option>
   <option value='15'>15</option>
   <option value='16'>16</option>
   <option value='17'>17</option>
   <option value='18'>18</option>
   <option value='19'>19</option>
   <option value='20'>20</option>
   <option value='21'>21</option>
   <option value='22'>22</option>
   <option value='23'>23</option>
   <option value='24'>24</option>
   <option value='25'>25</option>
   <option value='26'>26</option>
   <option value='27'>27</option>
   <option value='28'>28</option>
   <option value='29'>29</option>
   <option value='30'>30</option>
   <option value='31'>31</option>
</select>
Hour: <select name='hour'>
   <option value='01'>1</option>
   <option value='02'>2</option>
   <option value='03'>3</option>
   <option value='04'>4</option>
   <option value='05'>5</option>
   <option value='06'>6</option>
   <option value='07'>7</option>
   <option value='08'>8</option>
   <option value='09'>9</option>
   <option value='10'>10</option>
   <option value='11'>11</option>
   <option value='12'>12</option>
</select>
Minutes: <select name='tmin'>
   <option value='0'>0</option>
   <option value='1'>1</option>
   <option value='2'>2</option>
   <option value='3'>3</option>
   <option value='4'>4</option>
   <option value='5'>5</option>
</select>
<select name='omin'>
   <option value='0'>0</option>
   <option value='1'>1</option>
   <option value='2'>2</option>
   <option value='3'>3</option>
   <option value='4'>4</option>
   <option value='5'>5</option>
   <option value='6'>6</option>
   <option value='7'>7</option>
   <option value='8'>8</option>
   <option value='9'>9</option>
</select>
Am/Pm<select name='ampm'>
   <option value='a'>Am</a>
   <option value='p'>Pm</a>
</select> <BR>
Event Description<br>
<textarea name='description' rows='4' cols='60'></textarea><br>
<input type='submit' value='Submit'>
</form></p>

<p>Add a scheduled event (normal schedule).</b><br>
<form method='post' action='calendar.php?mode=norm'>
Month: <select name='month'>
   <option value='01'>January</option>
   <option value='02'>February</option>
   <option value='03'>March</option>
   <option value='04'>April</option>
   <option value='05'>May</option>
   <option value='06'>June</option>
   <option value='07'>July</option>
   <option value='08'>August</option>
   <option value='09'>September</option>
   <option value='10'>October</option>
   <option value='11'>November</option>
   <option value='12'>December</option>
</select>
Day: <select name='day'>
   <option value='01'>1</option>
   <option value='02'>2</option>
   <option value='03'>3</option>
   <option value='04'>4</option>
   <option value='05'>5</option>
   <option value='06'>6</option>
   <option value='07'>7</option>
   <option value='08'>8</option>
   <option value='09'>9</option>
   <option value='10'>10</option>
   <option value='11'>11</option>
   <option value='12'>12</option>
   <option value='13'>13</option>
   <option value='14'>14</option>
   <option value='15'>15</option>
   <option value='16'>16</option>
   <option value='17'>17</option>
   <option value='18'>18</option>
   <option value='19'>19</option>
   <option value='20'>20</option>
   <option value='21'>21</option>
   <option value='22'>22</option>
   <option value='23'>23</option>
   <option value='24'>24</option>
   <option value='25'>25</option>
   <option value='26'>26</option>
   <option value='27'>27</option>
   <option value='28'>28</option>
   <option value='29'>29</option>
   <option value='30'>30</option>
   <option value='31'>31</option>
</select>
Year {YEARMENU}<br>
Hour: <select name='hour'>
   <option value='01'>1</option>
   <option value='02'>2</option>
   <option value='03'>3</option>
   <option value='04'>4</option>
   <option value='05'>5</option>
   <option value='06'>6</option>
   <option value='07'>7</option>
   <option value='08'>8</option>
   <option value='09'>9</option>
   <option value='10'>10</option>
   <option value='11'>11</option>
   <option value='12'>12</option>
</select>
Minutes: <select name='tmin'>
   <option value='0'>0</option>
   <option value='1'>1</option>
   <option value='2'>2</option>
   <option value='3'>3</option>
   <option value='4'>4</option>
   <option value='5'>5</option>
</select>
<select name='omin'>
   <option value='0'>0</option>
   <option value='1'>1</option>
   <option value='2'>2</option>
   <option value='3'>3</option>
   <option value='4'>4</option>
   <option value='5'>5</option>
   <option value='6'>6</option>
   <option value='7'>7</option>
   <option value='8'>8</option>
   <option value='9'>9</option>
</select>
Am/PM: <select name='ampm'>
   <option value='a'>Am</a>
   <option value='p'>Pm</a>
</select> <BR>
Event Description:<br>
<textarea name='description' rows='4' cols='60'></textarea><BR>
<input type='submit' value='Submit'>
</form></p>
<br>
<p><form method='post' action='calendar.php?mode=delete'>
   {DELETE_LIST} 
   click to delete<input type='checkbox' name='delete_yes'>
   <input type='submit' value='Delete'>
</form></p>