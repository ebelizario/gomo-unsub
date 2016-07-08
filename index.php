<html>
<title>Driscoll / Unsubscribe</title>
<h2>Driscoll / Unsubscribe</h2>
<form enctype="multipart/form-data" action="unsubscribe.php" method="POST">
    <div>
        <h3>SELECT TYPE</h3>
        <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
        <input type="radio" name="subtype" value="mobile" id="mobile" /><label for="mobile">Mobile</label><br />
        <input type="radio" name="subtype" value="id" id="id" /><label for="id">ID</label><br />
        <input type="radio" name="subtype" value="email" id="email"/><label for="email">Email</label><br />
    </div>
    <div>
        <h3>ENTER CID (Optional)</h3>
        Category ID: <input type="text" name="cid" placeholder="Optional" id="cid" /><br />
        <i>Comma separated for multiple CIDs (ex: 234,456,122)</i><br />
    </div>
    <div>
        <h3>IMPORT CSV</h3>
        <input type="file" name="list">
    </div>
    <div>
        <br/><br/>
        <input type="submit">
    </div>
</form>
</html>
