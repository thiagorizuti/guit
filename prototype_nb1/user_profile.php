<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<body>

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

$template->pageData['pagetitle'] = 'University of Glasgow '; //# append conference name
$template->pageData['homeURL'] = $_SERVER['PHP_SELF'];
$template->pageData['breadcrumb'] = "<a href='http://www.gla.ac.uk/'>University of Glasgow</a> | <a href='http://www.gla.ac.uk/services/learningteaching/'>Learning & Teaching Centre</a> ";
//$template->pageData['breadcrumb'] .= '| <a href="index.php">Abstracts</a> | <a href="admin.php">Admin home</a>';
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


	$user = dataConnection::runQuery("SELECT * FROM user WHERE id = '$profile';");
	

	if($user != false)
	    {
		    foreach($user as $u)
		    {
		    	$points = $u['points_likes'] + $u['points_liked'] + $u['points_comments'] + $u['points_bundles'];
		    	 			    
		    	 			$template->pageData['mainBody'] .= "<h2>User Information</h2><ul>";

                $d = dir("images");
                $image = 0;

                while (false !== ($entry = $d->read()))

                {

                    if (strpos($entry, $userID) !== false)

                    {

                        $template->pageData['mainBody'] .= "<img src='images/{$entry}' height='100' width='100'/>";
                        $image = 1;

                    }
                }

                if ($image == 0) $template->pageData['mainBody'] .= "<br><img src='http://www.gamerguides.com/assets/images/profiles/3/83673-71555-profil-300x189gif.jpg' height='100' width='100'/>";


                if ($u['username'] != "") $template->pageData['mainBody'] .= "<li>Username: {$u['username']} </li><br>";
		    	 			
		    	 			if ($u['name'] != "") $template->pageData['mainBody'] .= "<li>Name: {$u['name']} </li><br>";
		    	 			
		    	 			if ($u['lastname'] != "") $template->pageData['mainBody'] .= "<li>Last Name: {$u['lastname']} </li><br>";
		    	 			
		    	 			if ($u['specialization'] != "") $template->pageData['mainBody'] .= "<li>Specialization: {$u['specialization']} </li><br>"; 
		    	 			
		    	 			if ($u['email'] != "") $template->pageData['mainBody'] .= "<li>Email: {$u['email']} </li><br>";   
		    	 			
		    	 			if ($u['phonenumber'] != "") $template->pageData['mainBody'] .= "<li>Phone Number: {$u['phonenumber']} </li><br>";
		    	 			
		    	 			if ($u['username'] != "") $template->pageData['mainBody'] .= "<li>Total Points: $points </li><br>";
		    	 			 			
		    	 			if ($u['joindate'] != "") $template->pageData['mainBody'] .= "<li>Join Date: {$u['joindate']} </li><br>";
		    	 			
		    	 			if ($u['lastaccess'] != "") $template->pageData['mainBody'] .= "<li>Last Access: {$u['lastaccess']} </li><br>";
						     
		    }    
	    }
	    $template->pageData['mainBody'] .= "</ul>";
	    
	    
	if ($profile == $userID) {
	
		$template->pageData['mainBody'] .= "<h2>Update Information</h2><br><ul>";
		/*?>
	

		<form action="" method="post">
		First Name: <input type="text" name="name"><br>
		Last Name: <input type="text" name="lastname"><br>
		Specialization: <input type="text" name="specialization"><br>
		Email: <input type="text" name="email"><br>
		Phone Number: <input type="text" name="phonenumber"><br>
		<input type="submit">
		</form>

	
		<?php*/
	
		if (isset($_POST["name"])) dataConnection::runQuery("UPDATE user SET name = {$_POST['name']} WHERE id = $userID;");
		if (isset($_POST["lastname"])) dataConnection::runQuery("UPDATE user SET lastname = {$_POST['lastname']} WHERE id = $userID;");
		if (isset($_POST["specialization"])) dataConnection::runQuery("UPDATE user SET specialization = {$_POST['specialization']} WHERE id = $userID;");
		if (isset($_POST["email"])) dataConnection::runQuery("UPDATE user SET email = {$_POST['email']} WHERE id = $userID;");
		if (isset($_POST["phonenumber"])) dataConnection::runQuery("UPDATE user SET phonenumber = {$_POST['phonenumber']} WHERE id = $userID;");
		
		
		 $template->pageData['mainBody'] .= "<h3><a href='upload.php'>Upload Avatar</a></h3>";	
	}	
	
	$template->pageData['logoutLink'] = loginBox($uinfo);
		
	$template->pageData['sideInfo'] = "<h2> Menu </h2> <br> <a href='/prototype_nb1/index.php'>Main Page</a> <br> <a href='/prototype_nb1/badges.php'>Badges</a>";


}

//if(error_get_last()==null)
    echo $template->render();
//else
//    echo "<p>Not rendering template to avoid hiding error messages.</p>";


?>

</body>
</html>