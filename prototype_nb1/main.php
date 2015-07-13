<!DOCTYPE HTML> 
<html>
<head>
</head>
<body style="background:#80BFFF">
<center>
<h2>Main Page</h2>
<img src="http://www.communitycare.co.uk/blogs/wp-content/uploads/mt/communitycareweb/blogs/social-work-blog/2011/02/17/Glasgow%20University%20(cropped).jpg", alt="no image" width="304" height="228">

<?php

//include 'connection.php'; // connects to the database

?>

<h2>Badge Page</h2>
<form method="post" action="/prototype_nb1/badges.php">
<input type="submit" id="badges" value="Badges" />
</form>

<h2>Create Teaching Practice</h2>
<form method="post" action="/create_tpp.php">
<input type="submit" id="bundle" value="Create New Teaching Practice" />
</form>

<h2>Main Page</h2>
<form method="post" action="/prototype_nb1/main.php">
<input type="submit" id="main page" value="Main Page" />
</form>


<?php
/*
mysql_close($connection);
*/
?>

</center>
</body>
</html>