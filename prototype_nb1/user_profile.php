<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');
require_once('lib/database.php');
require_once('lib/sharedfunctions.php');
require_once('corelib/dataaccess.php');

$template = new templateMerge($TEMPLATE);

$uinfo = checkLoggedInUser();
$dbUser = getUserRecord($uinfo);
$userID = $dbUser->id;

$template->pageData['pagetitle'] = 'University of Glasgow GUIT';
$template->pageData['homeURL'] = $_SERVER['PHP_SELF'];
$template->pageData['breadcrumb'] = "<a href='http://www.gla.ac.uk/'>University of Glasgow</a> | <a href='http://www.gla.ac.uk/services/learningteaching/'>Learning & Teaching Centre</a> ";


if($uinfo==false)
{
	header("Location: index.php");
	exit();
}
else
{
	if(isset($_GET['profile']))
	    $profile = $_GET['profile'];
	else
		header("Location: index.php");

	//Menu
	$template->pageData['sideInfo'] .= "<h2> Menu </h2>";
	$template->pageData['sideInfo'] .= "<a class='menuItem' href='/index.php'>Home</a> ";
	$template->pageData['sideInfo']	.= "<a class='menuItem' href='/browse.php'>Browse Teaching Practices</a>";
	$template->pageData['sideInfo']	.= "<a class='menuItem' href='/addbundle.php'>New Teaching Practice</a>";
	$template->pageData['sideInfo'] .= "<a class='menuItem' href='/user_profile.php?profile=$userID'>My Profile</a> ";
	$template->pageData['sideInfo'] .= "<a class='menuItem' href='/badges.php'>Badges</a>";
	if ($dbUser->isadmin == true) {
		$template->pageData['sideInfo'] .= "<a class='menuItem' href='/manageusers.php'>Manage Users</a>";
	}

	//Profile
	$template->pageData['mainBody'] .= "<h2>User Information</h2><ul>";
	$user = dataConnection::runQuery("SELECT * FROM user WHERE id = '$profile';");

	$template->pageData['mainBody'] .= "<div id='profile'>";
	if($user != false) {
		foreach($user as $u) {
			$points = totalPoints($u['id']);
			$template->pageData['mainBody'] .= "<img src=" . getAvatarURL($userID) . "  />";
			if ($profile == $userID) $template->pageData['mainBody'] .= "<form action='uploadAvatar.php' method='post' enctype='multipart/form-data'><input type='hidden' name='userID' value='$userID'/><input type='file' name='avatar' size='25' /><br/><input type='submit' name='upload' value='Upload Avatar'/><sub>(size less than 1MB)</sub></form>";
			$template->pageData['mainBody'] .= "<table>";
			$template->pageData['mainBody'] .= "<tr class='alt'><td>Username</td><td>{$u['username']}</td></tr>";
			$template->pageData['mainBody'] .= "<tr><td>Name</td><td>{$u['name']}</td></tr>";
			$template->pageData['mainBody'] .= "<tr class='alt'><td>Last Name</td><td>{$u['lastname']}</td></tr>";
			$template->pageData['mainBody'] .= "<tr><td>Specialization</td><td>{$u['specialization']}</td></tr>";
			$template->pageData['mainBody'] .= "<tr class='alt'><td>Email</td><td>{$u['email']}</td></tr>";
			$template->pageData['mainBody'] .= "<tr><td>Phone Number</td><td>{$u['phonenumber']}</td></tr>";
			$template->pageData['mainBody'] .= "<tr class='alt'><td>Total Points</td><td>{$points}</td></tr>";
			$template->pageData['mainBody'] .= "<tr><td>Join Date</td><td>".date("d/m/Y",strtotime($u["joindate"]))."</td></tr>";
			$template->pageData['mainBody'] .= "<tr class='alt'><td>Last Access</td><td>".date("d/m/Y",strtotime($u["lastaccess"]))."</td></tr>";
			$template->pageData['mainBody'] .= "<tr><td></td><td></td></tr>";
			$template->pageData['mainBody'] .= "</table>";
		}
	}
	$template->pageData['mainBody'] .= "</div>";

	/*
	if ($profile == $userID) {

		$template->pageData['mainBody'] .= "<h2>Update Information</h2><br><ul>";
		?>


		<form action="" method="post">
		First Name: <input type="text" name="name"><br>
		Last Name: <input type="text" name="lastname"><br>
		Specialization: <input type="text" name="specialization"><br>
		Email: <input type="text" name="email"><br>
		Phone Number: <input type="text" name="phonenumber"><br>
		<input type="submit">
		</form>


		<?php*

		if (isset($_POST["name"])) dataConnection::runQuery("UPDATE user SET name = {$_POST['name']} WHERE id = $userID;");
		if (isset($_POST["lastname"])) dataConnection::runQuery("UPDATE user SET lastname = {$_POST['lastname']} WHERE id = $userID;");
		if (isset($_POST["specialization"])) dataConnection::runQuery("UPDATE user SET specialization = {$_POST['specialization']} WHERE id = $userID;");
		if (isset($_POST["email"])) dataConnection::runQuery("UPDATE user SET email = {$_POST['email']} WHERE id = $userID;");
		if (isset($_POST["phonenumber"])) dataConnection::runQuery("UPDATE user SET phonenumber = {$_POST['phonenumber']} WHERE id = $userID;");


		 $template->pageData['mainBody'] .= "<h3><a href='upload.php'>Upload Avatar</a></h3>";

	}
	*/
	$template->pageData['logoutLink'] = loginBox($uinfo);


}

//if(error_get_last()==null)
    echo $template->render();
//else
//    echo "<p>Not rendering template to avoid hiding error messages.</p>";


?>

</body>
</html>