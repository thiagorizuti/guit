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

	$template->pageData['mainBody'].= "<div id='badges'>";
	$badges =  getEarnedBadges($userID);
	$template->pageData['mainBody'] .= "<h2>Badges You Have Earned</h2><br>";
	if($badges != false){
		foreach($badges as $b)
		{
			$badgeID = 	($b['badge_id']);
			$template->pageData['mainBody'] .= "<img src='img/badge_{$badgeID}.png' width=100px  /> <br>{$b['about']}<br>";
		}
	}
	else{
		$template->pageData['mainBody'] .= "You do not have any badges yet.";
	}
	$template->pageData['mainBody'] .= "<br><br>";


	$template->pageData['mainBody'] .= "<h2>Available Badges</h2><br>";
	$badges =  getBadges();
	foreach($badges as $b)
	{
		$badgeID = $b['id'];
		$template->pageData['mainBody'] .= "<img src='img/badge_{$badgeID}.png' width=100px  /> <br>{$b['about']}<br>";
		$template->pageData['mainBody'] .= "Points needed to earn: {$b['points']}<br><br>";
		if(totalPoints($userID) >= $b['points']){
			if(!userHasBadge($userID,$badgeID)){
				userEarnsBadge($userID,$badgeID);
			}
		}
	}
	$template->pageData['mainBody'].= "</div>";

	$template->pageData['logoutLink'] = loginBox($uinfo);
	
	


}

//if(error_get_last()==null)
    echo $template->render();
//else
//    echo "<p>Not rendering template to avoid hiding error messages.</p>";


?>