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

$template->pageData['pagetitle'] = 'University of Glasgow ';
$template->pageData['homeURL'] = $_SERVER['PHP_SELF'];
$template->pageData['breadcrumb'] = "<a href='http://www.gla.ac.uk/'>University of Glasgow</a> | <a href='http://www.gla.ac.uk/services/learningteaching/'>Learning & Teaching Centre</a> ";

$template->pageData['sideInfo'] .= "<h2> Menu </h2>";
$template->pageData['sideInfo'] .= "<a class='menuItem' href='/index.php'>Home</a> ";
$template->pageData['sideInfo']	.= "<a class='menuItem' href='/browse.php'>Browse Teaching Practices</a>";
$template->pageData['sideInfo']	.= "<a class='menuItem' href='/addbundle.php'>New Teaching Practice</a>";
$template->pageData['sideInfo'] .= "<a class='menuItem' href='/user_profile.php?profile=$userID'>My Profile</a> ";
$template->pageData['sideInfo'] .= "<a class='menuItem' href='/badges.php'>Badges</a>";
if ($dbUser->isadmin == true) {
	$template->pageData['sideInfo'] .= "<a href='/manageusers.php'>Manage Users</a>";
}

if($uinfo==false)
{
	header("Location: index.php");
	exit();
}
else
{
	$username = $uinfo['uname'];
	$query = "SELECT * FROM badge, user_earns_badge, user WHERE user_earns_badge.user_id = user.id AND user.username = '$username' AND badge.id = user_earns_badge.badge_id;";
	$badges =  dataConnection::runQuery($query);

	if($badges != false)
	{
	    $template->pageData['mainBody'] .= "<h2>List Of Badges You Have Earned</h2><ul>";
		    foreach($badges as $b)
		    {
		        $template->pageData['mainBody'] .= "<img src = {$b['url']} alt = 'badge'> You earned this badge on: {$b['time']} <br><br>"; 
			// $template->pageData['mainBody'] .= '<pre>'.print_r($b,1).'<pre>';
		    }
		    $template->pageData['mainBody'] .= "</ul><br>";
	    }
	    
	    

	$template->pageData['mainBody'] .= "<h2>List Of Badges Available</h2><ul>";
	$query = "SELECT * FROM badge;";
	$badges =  dataConnection::runQuery($query);

	if($badges !== false)
	    {
		    foreach($badges as $b)
		    {
		        $template->pageData['mainBody'] .= "<img src = {$b['url']} alt = 'badge'> {$b['about']} <br><br>"; 
			// $template->pageData['mainBody'] .= '<pre>'.print_r($b,1).'<pre>';
		    }
	    }
	    $template->pageData['mainBody'] .= "</ul>";


	$template->pageData['logoutLink'] = loginBox($uinfo);
	
	


}

//if(error_get_last()==null)
    echo $template->render();
//else
//    echo "<p>Not rendering template to avoid hiding error messages.</p>";


?>